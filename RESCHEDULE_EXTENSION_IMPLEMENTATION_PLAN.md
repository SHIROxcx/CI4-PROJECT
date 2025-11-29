# Booking Reschedule & Extension Implementation Plan

## 📋 Overview

This document outlines the implementation plan for two new features:
1. **Reschedule Booking** - Change booking to a different date/time
2. **Same-Day Extension** - Extend the duration on the same day

---

## 🎯 Feature 1: Reschedule Booking

### What It Does
Allows users to change their booking to a different date or time while keeping everything else the same (facility, plan, equipment, etc.)

### Business Rules

#### Who Can Reschedule?
- **Users/Faculty**: Can request reschedule (needs admin approval)
- **Students**: Can request reschedule (needs admin approval)
- **Admin**: Can reschedule directly (no approval needed)

#### When Can They Reschedule?
- ✅ Only bookings with status: `confirmed`
- ✅ Must be at least **48 hours** before the original event date
- ✅ New date/time must not conflict with existing bookings
- ✅ Equipment must be available on the new date
- ❌ Cannot reschedule if event has already started
- ❌ Cannot reschedule `cancelled` or `completed` bookings

#### Reschedule Process Flow
```
User/Student                    Admin                    System
    |                             |                         |
    | Request Reschedule          |                         |
    |----------------------------->|                         |
    |                             | Review Request          |
    |                             |------------------------>|
    |                             |                         | Check availability
    |                             |                         | Check equipment
    |                             |<------------------------|
    |                             | Approve/Reject          |
    |<----------------------------|                         |
    | Notification                |                         |
```

---

## 🎯 Feature 2: Same-Day Extension

### What It Does
Allows users to extend their booking duration on the same day (e.g., extend from 4 hours to 6 hours)

### Business Rules

#### Who Can Extend?
- **Users/Faculty/Students**: Can request extension (needs admin approval)
- **Admin**: Can approve/reject extension requests

#### When Can They Extend?
- ✅ Only bookings with status: `confirmed`
- ✅ Can request extension **up to 2 hours before** the event ends
- ✅ Facility must be available for the extended time
- ✅ Must not conflict with next booking
- ❌ Cannot extend more than **4 additional hours**
- ❌ Cannot extend if event is already completed

#### Extension Pricing
- **Internal (Students)**: Still FREE
- **External (Users/Faculty)**: Charge additional cost based on hourly rate
  - Calculate: `(hourly_rate × additional_hours) + potential_overtime_fee`

#### Extension Process Flow
```
User                            Admin                    System
 |                                |                         |
 | Request Extension (X hours)    |                         |
 |-------------------------------->|                         |
 |                                | Review Request          |
 |                                |------------------------>|
 |                                |                         | Check if facility available
 |                                |                         | Check next booking
 |                                |                         | Calculate new cost
 |                                |<------------------------|
 |                                | Approve/Reject          |
 |<-------------------------------|                         |
 | Pay additional fee (if external)|                        |
```

---

## 💾 Database Changes Required

### 1. New Table: `booking_reschedules`
```sql
CREATE TABLE `booking_reschedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `original_date` DATE NOT NULL,
  `original_time` TIME NOT NULL,
  `new_date` DATE NOT NULL,
  `new_time` TIME NOT NULL,
  `reason` TEXT,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `requested_by` INT NOT NULL,
  `requested_at` DATETIME NOT NULL,
  `reviewed_by` INT NULL,
  `reviewed_at` DATETIME NULL,
  `review_notes` TEXT NULL,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`requested_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. New Table: `booking_extensions`
```sql
CREATE TABLE `booking_extensions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `original_duration` INT NOT NULL COMMENT 'Original duration in hours',
  `additional_hours` INT NOT NULL COMMENT 'Additional hours requested',
  `new_duration` INT NOT NULL COMMENT 'Total new duration',
  `additional_cost` DECIMAL(10,2) DEFAULT 0.00,
  `reason` TEXT,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `requested_by` INT NOT NULL,
  `requested_at` DATETIME NOT NULL,
  `reviewed_by` INT NULL,
  `reviewed_at` DATETIME NULL,
  `review_notes` TEXT NULL,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`requested_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Add Column to `bookings` Table
```sql
ALTER TABLE `bookings`
ADD COLUMN `is_rescheduled` BOOLEAN DEFAULT FALSE,
ADD COLUMN `is_extended` BOOLEAN DEFAULT FALSE,
ADD COLUMN `reschedule_count` INT DEFAULT 0 COMMENT 'Track how many times booking was rescheduled',
ADD COLUMN `original_event_date` DATE NULL COMMENT 'Original date before any reschedules',
ADD COLUMN `original_event_time` TIME NULL COMMENT 'Original time before any reschedules';
```

---

## 🔧 Backend Implementation

### New Models Needed

#### 1. `BookingRescheduleModel.php`
```php
- getByBookingId($bookingId)
- getPendingReschedules()
- createRescheduleRequest($data)
- approveReschedule($rescheduleId, $adminId, $notes)
- rejectReschedule($rescheduleId, $adminId, $reason)
```

#### 2. `BookingExtensionModel.php`
```php
- getByBookingId($bookingId)
- getPendingExtensions()
- createExtensionRequest($data)
- calculateAdditionalCost($bookingId, $additionalHours)
- approveExtension($extensionId, $adminId, $notes)
- rejectExtension($extensionId, $adminId, $reason)
```

### New API Endpoints

#### Reschedule Endpoints
```
POST   /api/bookings/{id}/request-reschedule
GET    /api/bookings/{id}/reschedule-requests
POST   /api/bookings/reschedules/{id}/approve (Admin only)
POST   /api/bookings/reschedules/{id}/reject (Admin only)
GET    /api/admin/reschedule-requests (Admin - view all pending)
```

#### Extension Endpoints
```
POST   /api/bookings/{id}/request-extension
GET    /api/bookings/{id}/extension-requests
POST   /api/bookings/extensions/{id}/approve (Admin only)
POST   /api/bookings/extensions/{id}/reject (Admin only)
GET    /api/admin/extension-requests (Admin - view all pending)
POST   /api/bookings/extensions/{id}/calculate-cost
```

### Controller Methods Needed

#### In `BookingApiController.php`
```php
requestReschedule($bookingId)      // User requests reschedule
requestExtension($bookingId)       // User requests extension
getRescheduleRequests($bookingId)  // Get reschedule history
getExtensionRequests($bookingId)   // Get extension history
```

#### New `RescheduleController.php` (Admin)
```php
getPendingReschedules()            // List all pending reschedules
approveReschedule($rescheduleId)   // Approve reschedule request
rejectReschedule($rescheduleId)    // Reject reschedule request
```

#### New `ExtensionController.php` (Admin)
```php
getPendingExtensions()             // List all pending extensions
approveExtension($extensionId)     // Approve extension request
rejectExtension($extensionId)      // Reject extension request
calculateExtensionCost($bookingId, $additionalHours) // Calculate cost
```

---

## 🎨 Frontend Implementation

### User Dashboard Updates

#### 1. Booking Details Page - Add Action Buttons
```
┌────────────────────────────────────────┐
│  Booking #BK001                        │
│  Status: Confirmed                     │
│  Date: Dec 15, 2024                    │
│  Time: 10:00 AM - 2:00 PM             │
│                                        │
│  [📅 Reschedule] [⏱️ Request Extension] │
└────────────────────────────────────────┘
```

#### 2. Reschedule Modal
```
┌─────────────────────────────────────────────┐
│  Reschedule Booking                         │
│  ─────────────────────────────────────────  │
│  Current Date: Dec 15, 2024                 │
│  Current Time: 10:00 AM                     │
│                                             │
│  New Date:     [Calendar Picker]            │
│  New Time:     [Time Picker]                │
│                                             │
│  Reason:       [Text area]                  │
│                                             │
│  ⚠️ Note: Reschedule requires admin approval│
│  and must be at least 48 hours in advance  │
│                                             │
│  [Cancel]  [Submit Request]                 │
└─────────────────────────────────────────────┘
```

#### 3. Extension Modal
```
┌─────────────────────────────────────────────┐
│  Request Extension                          │
│  ─────────────────────────────────────────  │
│  Current Duration: 4 hours                  │
│  Current End Time: 2:00 PM                  │
│                                             │
│  Additional Hours: [1] [2] [3] [4]          │
│  New End Time: 6:00 PM                      │
│                                             │
│  Additional Cost: ₱2,500.00                 │
│  (Including overtime fee)                   │
│                                             │
│  Reason: [Text area]                        │
│                                             │
│  [Cancel]  [Submit Request]                 │
└─────────────────────────────────────────────┘
```

### Admin Dashboard Updates

#### 1. New Menu Item
```
Admin Sidebar:
  - Bookings
    └─ Pending Approvals
    └─ Reschedule Requests  ⭐ NEW
    └─ Extension Requests   ⭐ NEW
```

#### 2. Reschedule Requests Page
```
┌────────────────────────────────────────────────────────────┐
│  Pending Reschedule Requests (5)                           │
├────────────────────────────────────────────────────────────┤
│  Booking  │ User      │ Original Date │ New Date │ Action │
│  ───────────────────────────────────────────────────────── │
│  #BK001   │ John Doe  │ Dec 15, 10AM │ Dec 20   │ [View] │
│  #BK002   │ Jane Smith│ Dec 18, 2PM  │ Dec 22   │ [View] │
└────────────────────────────────────────────────────────────┘
```

#### 3. Reschedule Approval Modal
```
┌─────────────────────────────────────────────┐
│  Review Reschedule Request                  │
│  ─────────────────────────────────────────  │
│  Booking: #BK001 - University Auditorium    │
│  Requested by: John Doe                     │
│  Requested on: Dec 10, 2024                 │
│                                             │
│  Original: Dec 15, 2024 at 10:00 AM        │
│  New Date: Dec 20, 2024 at 10:00 AM        │
│                                             │
│  Reason: "Need to postpone due to..."      │
│                                             │
│  ✅ Facility is available                   │
│  ✅ Equipment is available                  │
│  ✅ No conflicts found                      │
│                                             │
│  Review Notes: [Text area]                  │
│                                             │
│  [Reject]  [Approve]                        │
└─────────────────────────────────────────────┘
```

---

## ⚡ Implementation Steps (Priority Order)

### Phase 1: Database & Models (Week 1)
1. ✅ Create migration files for new tables
2. ✅ Create `BookingRescheduleModel`
3. ✅ Create `BookingExtensionModel`
4. ✅ Update `BookingModel` with new fields
5. ✅ Test models with sample data

### Phase 2: Backend API (Week 2)
1. ✅ Create reschedule API endpoints
2. ✅ Create extension API endpoints
3. ✅ Add validation logic (date/time conflicts, availability)
4. ✅ Add cost calculation for extensions
5. ✅ Test all API endpoints

### Phase 3: Admin Interface (Week 3)
1. ✅ Create reschedule requests admin page
2. ✅ Create extension requests admin page
3. ✅ Add approval/rejection modals
4. ✅ Add notifications for admins
5. ✅ Test admin workflows

### Phase 4: User Interface (Week 4)
1. ✅ Add reschedule button to booking details
2. ✅ Add extension button to booking details
3. ✅ Create reschedule modal
4. ✅ Create extension modal
5. ✅ Add status indicators for pending requests
6. ✅ Test user workflows

### Phase 5: Notifications & Polish (Week 5)
1. ✅ Email notifications for requests
2. ✅ Email notifications for approvals/rejections
3. ✅ In-app notifications
4. ✅ History tracking
5. ✅ Final testing & bug fixes

---

## 🔒 Security Considerations

1. **Authorization**
   - Users can only reschedule/extend their own bookings
   - Admin can manage all requests
   - Validate user permissions on every request

2. **Validation**
   - Prevent reschedule less than 48 hours before event
   - Prevent extension after event completion
   - Validate date/time conflicts
   - Validate equipment availability

3. **Rate Limiting**
   - Limit reschedule requests per booking (max 2 times?)
   - Limit extension requests per booking (max 1 time?)

4. **Audit Trail**
   - Log all reschedule/extension requests
   - Track who approved/rejected
   - Keep history for reporting

---

## 📊 Additional Features to Consider

### Nice-to-Have Features
1. **Auto-approval for certain conditions**
   - If rescheduling within same week
   - If no conflicts detected

2. **Partial refunds for reschedules**
   - If rescheduling reduces duration

3. **Reschedule fees**
   - Charge a small fee for rescheduling (external bookings only)

4. **Bulk reschedules**
   - Allow admin to reschedule multiple bookings at once

5. **Recurring booking reschedules**
   - If you implement recurring bookings later

---

## 🎯 Success Metrics

Track these metrics after implementation:
- Number of reschedule requests per month
- Reschedule approval rate
- Number of extension requests per month
- Extension approval rate
- Average time to approve requests
- Revenue from extensions

---

## 📝 Notes

- Keep the original booking data for reporting/analytics
- Consider timezone handling for date/time
- Test edge cases (midnight bookings, year-end dates, etc.)
- Consider mobile responsiveness for modals
- Add loading states for all async operations

---

## 🤔 Questions to Decide

Before implementation, please decide:

1. **Reschedule Limits**
   - How many times can a user reschedule the same booking?
   - Should there be a deadline (e.g., 48 hours before event)?

2. **Extension Limits**
   - Maximum additional hours allowed? (suggestion: 4 hours)
   - Can users request multiple extensions?

3. **Pricing**
   - Should rescheduling have a fee?
   - How to calculate overtime for extensions?

4. **Approval**
   - Should some reschedules be auto-approved?
   - Who can approve? (just admins or also facilitators?)

5. **Notifications**
   - Email notifications for every request?
   - SMS notifications?

---

**Ready to implement?** Let me know which phase you'd like to start with!
