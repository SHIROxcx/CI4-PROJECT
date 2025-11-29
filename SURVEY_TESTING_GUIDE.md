# Survey System Testing Guide

## Step 1: Run Database Migration

Before testing, you need to create the survey responses table.

### Terminal Command:

```bash
cd c:\wamp64\www\CI4-PROJECT-main
php spark migrate
```

This will create the `booking_survey_responses` table with all survey fields.

**Expected Output:**

```
Running all new migrations...
CodeIgniter Migration Batch 1 completed successfully.
```

---

## Step 2: Verify Database Table Creation

### Using Database Client (MySQL Workbench, phpMyAdmin, etc.):

```sql
-- Check if table exists
SHOW TABLES LIKE 'booking_survey_responses';

-- Check table structure
DESCRIBE booking_survey_responses;

-- Should show columns like:
-- id, booking_id, survey_token, staff_punctuality,
-- staff_courtesy_property, facility_level_expectations, etc.
```

---

## Step 3: Manual Testing Workflow

### **Test Scenario A: Complete Booking Approval & Survey Flow**

#### Step A1: Create/Select a Test Booking

1. Go to Admin Dashboard → **Booking Management**
2. Find a **Pending** booking or create a new test booking
3. Note the **Booking ID** (e.g., #BK001)

#### Step A2: Approve the Booking

1. Click **View** on the pending booking
2. Click **Approve Booking** button
3. Check the **Verification Checklist** items
4. Add optional approval notes
5. Click **Approve Booking**

**Expected Result:**

- ✅ Booking status changes to "Confirmed"
- ✅ Email sent to client's email address (check SMTP logs or email)
- ✅ Survey record created in database with unique token

#### Step A3: Check Database for Survey Record

```sql
-- Connect to your database
SELECT * FROM booking_survey_responses
WHERE booking_id = [YOUR_BOOKING_ID];

-- Should return 1 row with:
-- - booking_id: [your ID]
-- - survey_token: [64-character hash]
-- - All response fields: NULL (not yet submitted)
-- - created_at: current timestamp
```

#### Step A4: Access Survey via Email Link

1. **Check admin email** (or test email configured in `.env`)
2. Look for email subject: **"✅ Booking Confirmed"**
3. Find the **"Complete Facility Evaluation"** button or link
4. Copy the survey link (should be like: `http://yoursite.local/survey/[TOKEN]`)

#### Step A5: Test Survey Form Access

##### Test A5a: Valid Token

```
URL: http://localhost/CI4-PROJECT-main/survey/[VALID_TOKEN]
Expected: Beautiful survey form displays with booking information
```

##### Test A5b: Invalid Token

```
URL: http://localhost/CI4-PROJECT-main/survey/invalid_token_12345
Expected: "Invalid or Expired Link" error page
```

#### Step A6: Fill and Submit Survey

1. Complete the survey form by:

   - Select rating for **Staff Evaluation** (Excellent, Very Good, Good, Fair, Poor, N/A)
   - Select rating for **Facility Evaluation** (all sections)
   - Select rating for **Equipment Function** (all items)
   - Answer **Overall Experience** questions
   - Optionally add **Comments/Suggestions**

2. Click **Submit Survey** button

**Expected Result:**

- ✅ Loading indicator shows "Submitting your survey..."
- ✅ Success message: "Thank you! Your survey has been submitted successfully"
- ✅ Redirects to thank you page after 2 seconds
- ✅ Survey data saved to `booking_survey_responses` table

#### Step A7: Verify Survey in Booking Management

1. Go back to Admin → **Booking Management**
2. Find the booking you just surveyed
3. Click **View** to open booking details
4. Scroll to **"⭐ Facility Evaluation Survey"** section
5. Verify survey responses display correctly in tables

**Expected Display:**

- Status: "Completed" (green checkmark)
- Submitted timestamp
- All responses shown in formatted tables
- Comments/suggestions in highlighted box

---

## Step 4: Automated Database Verification

### SQL Queries for Testing

```sql
-- 1. Check all survey responses
SELECT
    bs.id,
    bs.booking_id,
    b.client_name,
    b.email_address,
    bs.staff_punctuality,
    bs.facility_level_expectations,
    bs.overall_would_recommend,
    bs.created_at
FROM booking_survey_responses bs
JOIN bookings b ON b.id = bs.booking_id
ORDER BY bs.created_at DESC;

-- 2. Check unsubmitted surveys (created but not filled)
SELECT
    bs.id,
    bs.booking_id,
    b.client_name,
    bs.created_at,
    'Not Yet Submitted' as status
FROM booking_survey_responses bs
JOIN bookings b ON b.id = bs.booking_id
WHERE bs.staff_punctuality IS NULL
ORDER BY bs.created_at DESC;

-- 3. Check submitted surveys with "Excellent" ratings
SELECT
    bs.booking_id,
    b.client_name,
    bs.staff_punctuality,
    bs.facility_level_expectations,
    bs.overall_would_recommend
FROM booking_survey_responses bs
JOIN bookings b ON b.id = bs.booking_id
WHERE bs.staff_punctuality = 'Excellent'
ORDER BY bs.created_at DESC;

-- 4. Count survey statistics
SELECT
    COUNT(*) as total_surveys,
    SUM(CASE WHEN staff_punctuality = 'Excellent' THEN 1 ELSE 0 END) as excellent_staff,
    SUM(CASE WHEN overall_would_recommend = 'Yes' THEN 1 ELSE 0 END) as would_recommend
FROM booking_survey_responses;
```

---

## Step 5: Edge Case Testing

### Test Case 1: Duplicate Survey Submission

**What it tests:** System prevents submitting the same survey twice

1. Complete and submit a survey once ✅
2. Go back to the same survey link in your browser
3. Try to submit again

**Expected Result:**

- Page shows: "Survey Already Submitted"
- Message: "Thank you for your response! We have already recorded your survey submission"
- New submission is rejected (no duplicate entries in DB)

### Test Case 2: Token Expiration/Invalidation

**What it tests:** Invalid or tampered tokens are rejected

```
Try accessing: http://localhost/CI4-PROJECT-main/survey/tampered_token_123
Expected: "Invalid or Expired Link" page
```

### Test Case 3: Missing Required Fields

**What it tests:** Form validation works

1. Open survey form
2. Try to submit without filling required fields
3. Click Submit

**Expected Result:**

- Error message: "Please fill in all required fields before submitting"
- Form remains open for user to complete

### Test Case 4: Booking Not Found

**What it tests:** System handles missing bookings gracefully

1. Create survey with booking_id = 99999 (non-existent)
2. Try to access that token

**Expected Result:**

- Shows invalid token page
- No errors in browser console

---

## Step 6: Email Testing

### Check Approval Email Contains Survey Link

The approval email should include:

```
Subject: ✅ Booking Confirmed - BK001

Body should contain:
- Booking confirmation details
- Guest registration link
- ⭐ FACILITY EVALUATION SURVEY section
- Survey completion link with unique token
```

### Test Email Sending

If SMTP not configured, check `writable/logs/` for email logs:

```bash
cd c:\wamp64\www\CI4-PROJECT-main\writable\logs
type [latest-log-file].log | findstr "booking\|survey\|email"
```

---

## Step 7: API Testing (Admin)

### Test Admin Survey Retrieval API

```bash
# Using PowerShell
$token = "YOUR_AUTH_TOKEN"
$bookingId = 1

$response = Invoke-WebRequest `
  -Uri "http://localhost/CI4-PROJECT-main/api/survey/$bookingId" `
  -Method GET `
  -Headers @{"Authorization" = "Bearer $token"}

$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

**Expected Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "booking_id": 1,
    "survey_token": "[64-char-hash]",
    "staff_punctuality": "Excellent",
    "facility_level_expectations": "Very Good",
    "overall_would_recommend": "Yes",
    "comments_suggestions": "Great service!",
    "created_at": "2025-11-27 10:30:45",
    "updated_at": "2025-11-27 10:30:45"
  }
}
```

---

## Step 8: Browser Developer Tools Verification

### Console Logs

Open browser **Developer Tools (F12)** → **Console** and look for:

```javascript
// Expected logs when submitting survey:
"Survey submitted for booking #1 with token: [hash]"

// In Network tab, check POST request to /survey/submit:
Status: 200 OK
Response: {"success": true, "message": "Thank you! Your survey..."}
```

---

## Step 9: Performance Testing

### Test with Multiple Surveys

```bash
# Run this PowerShell script to create multiple test surveys
$baseUrl = "http://localhost/CI4-PROJECT-main"
$testData = @(
    @{bookingId=1; clientName="Test Client 1"},
    @{bookingId=2; clientName="Test Client 2"},
    @{bookingId=3; clientName="Test Client 3"}
)

foreach($test in $testData) {
    # Check survey creation
    $response = Invoke-WebRequest "$baseUrl/api/survey/$($test.bookingId)" -Method GET
    Write-Host "Survey $($test.bookingId): $($response.StatusCode)"
}
```

---

## Quick Checklist ✓

- [ ] Database migration ran successfully
- [ ] Survey table exists with correct columns
- [ ] Can approve a booking
- [ ] Approval email sent (check logs/email)
- [ ] Email contains survey link
- [ ] Can access survey with valid token
- [ ] Invalid token shows error page
- [ ] Survey form displays correctly
- [ ] Can fill and submit survey
- [ ] Survey data saved to database
- [ ] Survey displays in booking management
- [ ] Cannot submit survey twice
- [ ] Admin API returns survey data
- [ ] No console errors in browser

---

## Troubleshooting

### Problem: Survey table not found

**Solution:**

```bash
php spark migrate
php spark migrate:status  # Verify migration ran
```

### Problem: Survey form not loading

**Solution:**

1. Check routes in `app/Config/Routes.php`
2. Verify `app/Views/survey/facilityEvaluation.php` exists
3. Check browser console for errors

### Problem: Email not sending

**Solution:**
Check `.env` file:

```
CI_ENVIRONMENT=development
app.baseURL = 'http://localhost/CI4-PROJECT-main/'
email.SMTPHost = 'smtp.mailtrap.io'  # or your email service
```

### Problem: Survey not appearing in booking management

**Solution:**

1. Check browser console (F12)
2. Verify API endpoint: `/api/survey/{bookingId}`
3. Ensure booking ID is correct
4. Check survey table has data:

```sql
SELECT * FROM booking_survey_responses WHERE booking_id = [YOUR_ID];
```

### Problem: CORS or Permission errors

**Solution:**

1. Ensure you're logged in as admin
2. Check auth filter on routes
3. Verify session is active

---

## Final Notes

- **Survey tokens are unique** - each booking gets one unique token
- **One submission per token** - prevents duplicates
- **No confirmation email** - survey submission is silent
- **All responses stored** - accessible in booking management admin panel
- **Production ready** - can go live after testing
