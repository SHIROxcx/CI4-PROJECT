# File Download Debugging Guide

## Overview

Enhanced logging has been added to track file downloads, especially for the facility evaluation survey form. When you experience download issues, check both the browser console and server logs.

---

## 📊 Browser Console (JavaScript Logs)

### How to Access:

1. Press `F12` or `Ctrl+Shift+I` to open Developer Tools
2. Click on the **Console** tab
3. Try downloading the file again
4. Look for logs starting with `[Faculty Evaluation]`, `[Download]`, etc.

### What to Look For:

#### Faculty Evaluation Download Logs:

```
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] Survey files response OK: true
[Faculty Evaluation] Survey result: {success: true, files: Array(1), count: 1}
[Faculty Evaluation] Found survey evaluation file: {name: "CSPC_Evaluation_Booking_84_20251128020111.xlsx", ...}
[Faculty Evaluation] File name: CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] File URL: http://localhost:8080/api/bookings/evaluation-file/CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] File size: 15234 bytes
[Faculty Evaluation] File accessibility check status: 200
[Faculty Evaluation] Creating download link...
[Faculty Evaluation] Download element created with href: http://localhost:8080/api/bookings/evaluation-file/CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] Download click triggered
[Faculty Evaluation] Download element removed from DOM
[Faculty Evaluation] Survey evaluation file download completed: CSPC_Evaluation_Booking_84_20251128020111.xlsx
```

#### Common Issues in Console:

1. **No files found:**

   ```
   [Faculty Evaluation] No survey files found in response. Success: false, Files count: 0
   ```

   - **Cause**: Survey hasn't been submitted yet
   - **Solution**: Submit the survey first or generate blank template

2. **API not responding (404):**

   ```
   [Faculty Evaluation] Survey files API returned status 404
   ```

   - **Cause**: Survey API endpoint not found
   - **Solution**: Check if `/api/survey-files/{bookingId}` route exists

3. **File accessibility check fails:**

   ```
   [Faculty Evaluation] File may not be accessible (HTTP 404)
   ```

   - **Cause**: File doesn't exist in uploads directory
   - **Solution**: Regenerate the Excel file

4. **File URL construction error:**
   ```
   [Faculty Evaluation] Error fetching survey file: TypeError: Failed to fetch
   ```
   - **Cause**: Network issue or CORS problem
   - **Solution**: Check browser network tab for actual response

---

## 🔍 Server Logs

### Log Location:

```
writable/logs/
```

### How to Check Logs:

1. Open your terminal/command prompt
2. Navigate to the project folder
3. Run:
   ```powershell
   Get-Content writable/logs/* -Tail 100 -Wait  # Windows PowerShell
   tail -f writable/logs/*                       # Linux/Mac
   ```

### What to Look For:

#### Successful Download Flow:

```
=== EVALUATION FILE DOWNLOAD STARTED ===
Requested filename: CSPC_Evaluation_Booking_84_20251128020111.xlsx
Full filepath: C:\wamp64\www\CI4-PROJECT-main\writable/uploads/CSPC_Evaluation_Booking_84_20251128020111.xlsx
WRITEPATH: C:\wamp64\www\CI4-PROJECT-main\writable/
Uploads directory exists and is readable
File exists at: C:\wamp64\www\CI4-PROJECT-main\writable/uploads/CSPC_Evaluation_Booking_84_20251128020111.xlsx
File size: 15234 bytes
File readable: YES
File permissions: 0644
File extension: xlsx
Detected MIME type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
Reading file content...
File content read successfully. Content length: 15234 bytes
Setting response headers...
Response prepared successfully
=== EVALUATION FILE DOWNLOAD COMPLETED SUCCESSFULLY ===
```

#### Common Server Issues:

1. **File Not Found:**

   ```
   ERROR: Evaluation file not found at path: C:\...\CSPC_Evaluation_Booking_84_20251128020111.xlsx
   ERROR: Files in uploads directory: [".", "..", "other_file.xlsx"]
   ERROR: Similar files matching pattern "CSPC_Evaluation_*": []
   ```

   - **Cause**: Survey file wasn't generated or is in wrong location
   - **Solution**:
     - Check if survey was submitted successfully
     - Verify `writable/uploads/` directory exists and has write permissions
     - Look for the file manually in the directory

2. **Upload Directory Doesn't Exist:**

   ```
   ERROR: Uploads directory does not exist: C:\...\writable/uploads/
   ```

   - **Solution**: Create the directory manually or fix permissions

3. **File Not Readable:**

   ```
   ERROR: File is not readable: C:\...\CSPC_Evaluation_Booking_84_20251128020111.xlsx
   ERROR: File permissions: 0000
   ```

   - **Cause**: File permissions are restricted
   - **Solution**: Change file permissions to 0644 or 0755

4. **Content Length Mismatch:**

   ```
   WARNING: Content length mismatch - Expected: 15234, Got: 12000
   ```

   - **Cause**: File was corrupted during write
   - **Solution**: Regenerate the Excel file

5. **File Is Empty:**
   ```
   ERROR: File content is empty!
   ```
   - **Cause**: Excel file generation failed
   - **Solution**: Check Excel generation logs, see "Survey Submission Logs" section

---

## 📝 Survey Submission Logs

When the survey is submitted, look for these logs to verify file generation:

```
=== SURVEY SUBMIT START ===
Token received: abc123def456...
Survey found - Booking ID: 84
About to update survey - Data count: 15, Data: {...}
Excel evaluation form generated: C:\...\writable/uploads/CSPC_Evaluation_Booking_84_20251128020111.xlsx
=== SURVEY SUBMIT SUCCESS ===
```

### If Excel Generation Fails:

```
ERROR: Failed to generate Excel file: [Error message]
```

Common causes:

- `ExcelSurveyGenerator` helper file not found
- PHPOffice/PhpSpreadsheet library not installed
- Missing write permissions in `writable/uploads/`

---

## 🔧 Troubleshooting Steps

### Step 1: Check if the file was created

```powershell
# Windows PowerShell
Get-ChildItem -Path "writable/uploads/" -Filter "CSPC_Evaluation_Booking_84*" -Recurse

# Or
Test-Path "writable/uploads/CSPC_Evaluation_Booking_84_20251128020111.xlsx"
```

### Step 2: Check browser network activity

1. Open Developer Tools (`F12`)
2. Click **Network** tab
3. Try downloading again
4. Look for the `/api/bookings/evaluation-file/...` request
5. Click on it and check:
   - **Status**: Should be `200`
   - **Size**: Should match file size (not 0)
   - **Content-Type**: Should be `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
   - **Response**: Should show binary content (not JSON error)

### Step 3: Check route configuration

Open `app/Config/Routes.php` and verify:

```php
$routes->get('api/bookings/evaluation-file/(:any)', 'Survey::downloadEvaluationFile/$1', ['filter' => 'auth']);
```

### Step 4: Test the endpoint directly

Navigate to: `http://localhost:8080/api/bookings/evaluation-file/CSPC_Evaluation_Booking_84_20251128020111.xlsx`

Expected behavior:

- File starts downloading
- Or you see a JSON error with details

### Step 5: Check PHP error logs

```powershell
# Check PHP error log (if configured)
# Usually in php.ini or Apache/Nginx error log
```

---

## 📋 Checklist for Download Issues

- [ ] Survey was submitted successfully (check for "Thank you" message)
- [ ] Excel file was generated (check server logs for "Excel evaluation form generated")
- [ ] File exists in `writable/uploads/` directory
- [ ] File has read permissions (0644 or better)
- [ ] Route is registered in `Routes.php`
- [ ] Browser doesn't block downloads (check pop-up blocker)
- [ ] API endpoint returns HTTP 200 (check Network tab)
- [ ] File size is > 0 bytes (check Network tab Response Size)
- [ ] Correct MIME type is sent (check Response Headers)

---

## 📱 Log Format Reference

### JavaScript Console Format:

```
[<Feature>] <Action>: <Details>
[Faculty Evaluation] Starting download for booking #84
[Download] Created download element with href: http://...
[Error] Message: <error details>
```

### Server Log Format:

```
=== <OPERATION> START/COMPLETED/ERROR ===
INFO: <Operation details>
ERROR: <Error details>
WARNING: <Warning details>
DEBUG: <Debug details>
```

---

## 🚀 Next Steps if Issues Persist

1. **Create New Survey File**: Delete the old file from `writable/uploads/` and resubmit survey
2. **Check PHP Configuration**: Verify memory_limit and max_execution_time in php.ini
3. **Verify Permissions**: Run `chmod 755 writable/uploads/` on Linux
4. **Clear Browser Cache**: Press `Ctrl+Shift+Delete`
5. **Check Firewall**: Ensure download port is not blocked
6. **Test in Different Browser**: Verify it's not a browser-specific issue

---

## 📞 Additional Resources

- Browser Console: `F12` → Console tab
- Server Logs: `writable/logs/` directory
- Network Inspector: `F12` → Network tab
- Browser Download History: `Ctrl+J` or `Ctrl+Shift+Y`

---

**Last Updated**: November 28, 2025  
**Version**: 2.0 with Enhanced Logging
