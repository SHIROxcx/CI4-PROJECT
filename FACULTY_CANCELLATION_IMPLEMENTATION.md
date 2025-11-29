# Faculty Booking Cancellation Implementation Summary

## Overview

Successfully implemented booking cancellation functionality for faculty bookings with conditional refund policy display. Faculty can now cancel confirmed paid bookings by submitting a cancellation letter and reason.

## Changes Made

### 1. **public/js/faculty/bookings.js**

#### Cancel Button Display Logic (Line ~82-88)

- **Updated**: Cancel button now shows for BOTH pending AND confirmed bookings, BUT only for paid bookings
- **Condition**: `(booking.status === "pending" || booking.status === "confirmed") && !isFreeBooking`
- **Parameters**: Pass `bookingId`, `status`, and `totalCost` to `confirmCancelBooking()`
- **Example**:
  ```javascript
  onclick =
    "confirmCancelBooking(${booking.id}, '${booking.status}', ${parseFloat(booking.total_cost || 0)})";
  ```

#### confirmCancelBooking() Function (Line ~780-810)

- **Enhancement**: Now accepts three parameters: `bookingId`, `status`, `totalCost`
- **Refund Policy Display**:
  - Shows refund policy section ONLY if `totalCost > 0` (paid bookings)
  - Hides refund policy for free bookings (`totalCost === 0`)
- **Form Reset**: Clears reason, notes, and previously uploaded letter
- **Removed Validation**: Removed check that limited cancellation to "pending" status only

#### cancelBooking() Function (Line ~813-885)

- **Enhancement**: Now handles file uploads using FormData instead of JSON
- **File Validation**:
  - Checks if cancellation letter file is selected
  - Shows error if file is missing
  - Returns error if reason not selected
- **API Endpoint**: Changed from `/user/bookings/cancel/{id}` to `/api/user/bookings/{id}/cancel`
- **Request Format**: Uses FormData with:
  - `reason`: Cancellation reason selected
  - `notes`: Additional notes (optional)
  - `cancel_letter`: File object from cancelLetterFile variable

#### File Handling Functions (Line ~1275-1335)

Added three new functions to manage cancellation letter uploads:

1. **handleCancelLetterSelect(fileInput)**

   - Validates file type: PDF, JPG, PNG only
   - Validates file size: Maximum 10MB
   - Stores file in `cancelLetterFile` variable
   - Shows file name in preview area
   - Updates submit button state

2. **removeCancelLetter()**

   - Clears the `cancelLetterFile` variable
   - Resets file input
   - Hides file preview area
   - Updates submit button state (disables if no file)

3. **updateCancelSubmitButtonState()**
   - Enables submit button ONLY when:
     - Cancellation reason is selected
     - Cancellation letter file is uploaded
   - Disables button otherwise

#### Variable Declaration (Line ~1-4)

- Added: `let cancelLetterFile = null;` to track selected file

### 2. **app/Views/faculty/bookings.php**

#### Cancel Booking Modal (Line ~364-428)

Added complete modal with:

**Modal Header**:

- Title: "Cancel Booking"
- Close button

**Modal Body**:

1. **Hidden Input**: `#cancelBookingId` - stores booking ID
2. **Refund Policy Section** (ID: `#refundPolicySection`):

   - Style: `display: none` by default
   - Shows only for paid bookings (`totalCost > 0`)
   - Displays three refund tiers:
     - 80% refund if 15+ working days before event
     - 50% refund if 5-14 working days before event
     - No refund if <5 working days before event

3. **Cancellation Reason** (ID: `#cancelReason`):

   - Required field (red asterisk)
   - Dropdown with options:
     - Schedule Conflict
     - No Longer Needed
     - Budget Constraints
     - Other
   - Triggers `updateCancelSubmitButtonState()` on change

4. **Additional Notes** (ID: `#cancelNotes`):

   - Optional text area
   - 3 rows height
   - Placeholder text for guidance

5. **Cancellation Letter Upload**:
   - Required field (red asterisk)
   - Upload area with drag-and-drop UI
   - Accepted formats: PDF, JPG, PNG
   - Maximum file size: 10MB
   - File preview section (hidden by default):
     - Shows file name
     - Remove button to delete selected file

**Modal Footer**:

- "Close" button (secondary)
- "Confirm Cancellation" button (danger, red):
  - Disabled until reason AND letter are provided
  - Calls `cancelBooking()` function

### 3. **Backend Integration Points**

#### Endpoint: `/api/user/bookings/{id}/cancel`

- **Controller**: `app/Controllers/Api/UserApi.php`
- **Method**: POST
- **Accepts**:
  - `reason`: Cancellation reason (form field)
  - `notes`: Additional notes (form field)
  - `cancel_letter`: File upload (multipart/form-data)
- **Supports**: Both pending and confirmed bookings
- **File Handling**:
  - Validates file type and size
  - Creates `/uploads/cancellations/` directory automatically
  - Stores file with timestamp-based filename
  - Saves `cancellation_letter_path` to database
- **Equipment Handling**:
  - For equipment bookings: calls `restoreEquipmentInventory()`
  - Restores equipment availability upon cancellation
- **Response**: JSON with `success` flag and message

## Key Features

### 1. **Conditional Refund Policy**

- ✅ Shows refund policy for paid bookings (`total_cost > 0`)
- ✅ Hides refund policy for free bookings (`total_cost === 0`)
- ✅ Displays three refund tiers based on cancellation timing

### 2. **Cancel Button Visibility**

- ✅ Shows for pending bookings (all types)
- ✅ Shows for confirmed PAID bookings only
- ✅ Hidden for free bookings (all statuses)

### 3. **File Management**

- ✅ Validates file type (PDF, JPG, PNG)
- ✅ Validates file size (max 10MB)
- ✅ Shows file preview with name
- ✅ Allows file removal before submission
- ✅ Stores file with cancellation record

### 4. **Form Validation**

- ✅ Requires cancellation reason
- ✅ Requires cancellation letter upload
- ✅ Disables submit button until both required fields are filled
- ✅ Shows appropriate error messages

### 5. **User Experience**

- ✅ Modal-based interface consistent with existing patterns
- ✅ Clear instructions and labels
- ✅ Loading state during submission
- ✅ Success/error notifications
- ✅ Automatic reload of bookings after successful cancellation

## Testing Checklist

### UI Testing

- [ ] Cancel button appears for pending paid bookings
- [ ] Cancel button appears for confirmed paid bookings
- [ ] Cancel button does NOT appear for free bookings (any status)
- [ ] Cancel button does NOT appear for declined bookings

### Modal Testing

- [ ] Modal opens when cancel button clicked
- [ ] Refund policy section visible for paid bookings
- [ ] Refund policy section hidden for free bookings
- [ ] Form fields reset when modal opens
- [ ] Previously uploaded file is cleared

### File Upload Testing

- [ ] PDF files upload successfully
- [ ] JPG files upload successfully
- [ ] PNG files upload successfully
- [ ] File validation rejects non-PDF/JPG/PNG files
- [ ] File validation rejects files >10MB
- [ ] File preview shows after selection
- [ ] Remove button clears file selection

### Form Submission Testing

- [ ] Submit button disabled until reason selected
- [ ] Submit button disabled until file uploaded
- [ ] Submit button enabled when both filled
- [ ] Error shown if reason not selected
- [ ] Error shown if file not uploaded
- [ ] Successful cancellation shows success message
- [ ] Modal closes after successful cancellation
- [ ] Bookings list reloads after cancellation

### Database Testing

- [ ] Cancellation reason saved correctly
- [ ] Additional notes saved correctly
- [ ] Cancellation letter file uploaded to `/uploads/cancellations/`
- [ ] `cancellation_letter_path` stored in bookings table
- [ ] Equipment inventory restored (for equipment bookings)
- [ ] Booking status updated to "cancelled"

## Database Schema Updates

### Bookings Table

Requires these columns (should already exist from student/user implementation):

- `cancellation_reason` VARCHAR(50)
- `cancellation_notes` TEXT
- `cancellation_letter_path` VARCHAR(255)
- `cancellation_date` TIMESTAMP

## API Response Format

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Booking cancelled successfully",
  "data": {
    "booking_id": 123,
    "cancelled_at": "2024-01-15 10:30:45",
    "refund_percentage": 80,
    "refund_amount": 4000.0
  }
}
```

### Error Response (400/500)

```json
{
  "success": false,
  "message": "Error message describing what went wrong"
}
```

## File Paths

| File                              | Purpose                                                                 |
| --------------------------------- | ----------------------------------------------------------------------- |
| `public/js/faculty/bookings.js`   | Client-side logic for faculty bookings, including cancel modal handling |
| `app/Views/faculty/bookings.php`  | Faculty bookings view with cancel modal HTML                            |
| `app/Controllers/Api/UserApi.php` | Backend endpoint for processing cancellations                           |
| `app/Config/Routes.php`           | API route definition for cancel endpoint                                |

## Consistency with Other User Roles

This implementation maintains consistency with:

- **Student Bookings** (`public/js/student/bookings.js`): Same modal structure, file handling, and refund policy display
- **User/Extern Bookings** (`public/js/dashboard/bookings.js`): Identical file handling and form validation logic

## Potential Issues & Solutions

### Issue: Cancel button doesn't appear for paid confirmed bookings

**Solution**: Verify that `isFreeBooking` is calculated correctly:

```javascript
const isFreeBooking = parseFloat(booking.total_cost || 0) === 0;
```

### Issue: Refund policy section always hidden

**Solution**: Ensure `refundPolicySection` element exists in modal and `totalCost > 0` check is correct

### Issue: File upload fails

**Solution**:

1. Verify `/uploads/cancellations/` directory is writable
2. Check file type/size validation in PHP backend
3. Ensure FormData is being sent (not JSON)

### Issue: Modal doesn't close after cancellation

**Solution**: Verify `cancelBookingModal` ID matches in HTML and JavaScript

## Future Enhancements

1. **Email Notifications**: Send confirmation email when booking cancelled
2. **Automatic Refund Processing**: Auto-calculate and process refunds
3. **Cancellation History**: Track all cancellations with audit trail
4. **Bulk Cancellation**: Allow cancelling multiple bookings at once
5. **Cancellation Policies**: Make refund percentages configurable per facility
6. **Payment Integration**: Track refund status in payment system

## Notes

- Faculty can only cancel their own bookings (verified via authentication filter)
- Free bookings cannot be cancelled (no refund policy shown, no cancel button)
- Equipment inventory is automatically restored when equipment booking is cancelled
- Cancellation letter is required and stored for compliance/records
- All changes are backward compatible with existing functionality
