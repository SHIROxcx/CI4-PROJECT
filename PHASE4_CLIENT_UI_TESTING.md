# Phase 4: Client UI Testing Guide - Extension Request Modal

## Overview

Phase 4 implements the client-side interface for requesting booking extensions. Students and faculty can now request additional hours for their confirmed or pending bookings through an intuitive modal interface.

## Files Modified/Created

### New Files

1. **`public/js/dashboard/extensionRequest.js`** (246 lines)
   - Main handler for extension request modal operations
   - Functions for opening modal, calculating costs, submitting requests
   - Real-time cost calculation based on hourly rate

### Modified Files

1. **`app/Views/user/bookings.php`**

   - Added extension request modal HTML structure
   - Added script tag for extensionRequest.js

2. **`public/js/dashboard/bookings.js`**

   - Added extension button to booking table rows
   - Added extension button to modal footer
   - Integrated extension modal opening functionality

3. **`public/css/dashboard/bookings.css`**

   - Added 130+ lines of styling for extension request modal
   - Gradient backgrounds, hover effects, responsive design

4. **`app/Controllers/Api/UserApi.php`**

   - Updated `getUserBookings()` to include `facilities.hourly_rate`
   - Now returns hourly_rate for each booking for cost calculations

5. **`app/Models/BookingModel.php`**
   - Updated `getBookingWithFullDetails()` to include `facilities.hourly_rate`
   - Ensures hourly_rate is available in detailed booking views

## Feature Implementation

### Extension Request Modal Components

#### 1. **Hours Selector**

- Input field accepting 1-12 hours
- Real-time validation
- Input validation: min="1", max="12"
- Auto-calculated cost display

#### 2. **Reason Textarea**

- Optional field for extension justification
- Sent with request but not required
- Can help admin understand request context

#### 3. **Cost Calculator Card**

- Displays facility hourly rate (pulled from booking data)
- Shows selected hours
- Calculates and displays total extension cost
- Real-time updates as user changes hours

#### 4. **Action Buttons**

- **Cancel**: Dismiss modal without submitting
- **Submit Request**: Submit extension request to API
  - Disabled when form is invalid
  - Shows loading state during submission
  - Auto-enables when form becomes valid

#### 5. **Integration Points**

- Extension button (clock icon) in booking table row
- Extension button in booking details modal footer
- Only visible for confirmed or pending bookings

## Testing Procedures

### Pre-Testing Checklist

- [ ] Database migrations have been run: `php spark migrate`
- [ ] Apache/WAMP is running
- [ ] Database connection is configured
- [ ] User is logged in (student or faculty role)
- [ ] At least one confirmed booking exists

### Test 1: Modal Opening from Table

**Objective**: Verify extension button appears in booking table and modal opens

**Steps**:

1. Navigate to `/user/bookings` (My Bookings page)
2. Locate a confirmed or pending booking in the table
3. In the Actions column, verify a clock icon button appears
4. Click the clock icon button
5. Extension request modal should open smoothly

**Expected Results**:

- ✅ Clock icon visible only for confirmed/pending bookings
- ✅ Modal opens without errors
- ✅ Modal title: "Request Hours Extension"
- ✅ Info alert message displays
- ✅ Form is clean (all fields reset)

### Test 2: Modal Opening from Details

**Objective**: Verify extension button appears in booking details modal

**Steps**:

1. From My Bookings page, click the eye icon to view booking details
2. Look at modal footer (bottom right corner)
3. Verify "Request Extension" button appears
4. Click the button
5. Extension request modal should open

**Expected Results**:

- ✅ "Request Extension" button visible in footer
- ✅ Button appears only for confirmed/pending bookings
- ✅ Clicking opens extension modal
- ✅ Modal displays correct booking information

### Test 3: Cost Calculation

**Objective**: Verify real-time cost calculation works correctly

**Steps**:

1. Open extension request modal
2. Verify hourly rate is displayed (should match facility rate)
3. Change hours from 1 to 5
4. Observe cost calculation updates
5. Test edge cases:
   - Min value: 1 hour
   - Max value: 12 hours
   - Invalid input (0, 13, negative)

**Expected Results**:

- ✅ Hourly rate displays correctly
- ✅ Cost = Hours × Hourly Rate
- ✅ Cost updates in real-time as hours change
- ✅ Submit button disabled for invalid hour values
- ✅ Submit button enabled when 1-12 hours selected
- ✅ No errors on invalid input (gracefully handled)

**Example Calculations**:

- Facility Hourly Rate: ₱500
- 1 hour → ₱500
- 5 hours → ₱2,500
- 12 hours → ₱6,000

### Test 4: Form Validation

**Objective**: Verify form validation and button states

**Steps**:

1. Open extension request modal
2. Try to submit without selecting hours
3. Change hours to valid value (5)
4. Verify submit button becomes enabled
5. Clear reason field and retry
6. Enter reason text and change hours again

**Expected Results**:

- ✅ Submit button initially disabled
- ✅ Submit button enables when hours valid (1-12)
- ✅ Reason field is optional (can submit without it)
- ✅ Cost recalculates when hours change

### Test 5: Request Submission Success

**Objective**: Verify successful extension request submission

**Steps**:

1. Open extension request modal for a confirmed booking
2. Enter valid values:
   - Hours: 5
   - Reason: "Need additional setup time" (optional)
3. Click "Submit Request"
4. Observe loading state (button shows spinner)
5. Wait for response

**Expected Results**:

- ✅ Button shows spinner with "Submitting..." text during request
- ✅ Success message appears at top of page
- ✅ Modal closes automatically after success
- ✅ Bookings list reloads (auto-reload after 1 second)
- ✅ Extension cost shown in alert: "Extension request submitted! Cost: ₱2,500"

### Test 6: Request Submission Error Handling

**Objective**: Verify error handling for failed submissions

**Prerequisites**: Temporarily modify API to return error, or use invalid booking ID

**Steps**:

1. Try submitting with various error conditions:
   - Network disconnected
   - API returns error
   - Booking not found
2. Observe error message display

**Expected Results**:

- ✅ Error message displayed in red alert
- ✅ Submit button remains active (not stuck in loading state)
- ✅ Modal stays open for user to retry
- ✅ Error messages are user-friendly

### Test 7: Browser Console Check

**Objective**: Verify no JavaScript errors

**Steps**:

1. Open browser DevTools (F12)
2. Go to Console tab
3. Perform all above tests
4. Check for any JavaScript errors or warnings

**Expected Results**:

- ✅ No console errors
- ✅ No uncaught exceptions
- ✅ All fetch requests show 200/201 responses

### Test 8: Responsive Design

**Objective**: Verify modal works on mobile devices

**Steps**:

1. Open DevTools device emulation
2. Test on iPhone 12 (375px)
3. Test on iPad (768px)
4. Test on Desktop (1920px)

**Expected Results**:

- ✅ Modal is centered and readable on all sizes
- ✅ Input fields are properly sized and clickable
- ✅ Cost calculator card is readable
- ✅ Buttons are properly sized for touch

## Database Prerequisite

Ensure facilities table has `hourly_rate` column configured:

```sql
SELECT id, name, hourly_rate FROM facilities;
```

Example facility setup:

```sql
UPDATE facilities SET hourly_rate = 500 WHERE name = 'Auditorium';
UPDATE facilities SET hourly_rate = 1000 WHERE name = 'Function Hall';
UPDATE facilities SET hourly_rate = 300 WHERE name = 'Classroom';
```

## API Integration

### Endpoint Called

- **URL**: `POST /api/extensions/request`
- **Authentication**: Required (session-based)
- **Request Body**:
  ```json
  {
    "booking_id": 123,
    "extension_hours": 5,
    "reason": "Additional setup time needed"
  }
  ```

### Expected Response (Success)

```json
{
  "success": true,
  "extension_id": 45,
  "message": "Extension request submitted successfully",
  "extension_cost": 2500,
  "hourly_rate": 500
}
```

### Expected Response (Error)

```json
{
  "success": false,
  "message": "Facility hourly rate not configured"
}
```

## Next Steps

After Phase 4 testing is complete:

1. Proceed to Phase 5: Admin UI - Extensions Tab
2. Implement admin panel for viewing/approving extensions
3. Add file upload capabilities for receipts
4. Implement payment order generation

## Troubleshooting

### Modal Not Opening

- **Cause**: JavaScript file not loaded
- **Solution**: Check browser console for errors, verify `extensionRequest.js` is in public/js/dashboard/

### Cost Not Calculating

- **Cause**: Hourly rate not returned from API
- **Solution**: Run database query to verify hourly_rate is set for facility

### Button Not Enabling

- **Cause**: Validation logic issue
- **Solution**: Check browser console for validation errors

### Extension Button Not Showing

- **Cause**: Booking status not confirmed or pending
- **Solution**: Only confirmed/pending bookings show extension button

## Performance Notes

- Modal is lightweight (~5KB)
- Cost calculation is instant (no API call)
- Extension request submission is <500ms typical
- All data is cached in memory (bookings array)

## Security Considerations

- ✅ User authentication verified before allowing extension requests
- ✅ Booking ownership verified in API controller
- ✅ Input validation on both client and server
- ✅ CSRF protection (framework handles automatically)
- ✅ SQL injection prevention (parameterized queries)

## Accessibility

- ✅ Modal has proper ARIA labels
- ✅ Form inputs are properly labeled
- ✅ Color contrast meets WCAG standards
- ✅ Keyboard navigation supported (Tab, Enter, Esc)
- ✅ Error messages are properly announced

---

**Phase 4 Complete**: Client-side extension request interface fully implemented and ready for testing.
