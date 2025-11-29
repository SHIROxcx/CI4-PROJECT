# 🎯 Quick Console Log Reference Card

## Download Functions Log Prefixes

| Feature               | Prefix                     | Used For                |
| --------------------- | -------------------------- | ----------------------- |
| Faculty Evaluation    | `[Faculty Evaluation]`     | Survey/Evaluation forms |
| Inspection Evaluation | `[Inspection Evaluation]`  | Post-event inspections  |
| Order of Payment      | `[Order of Payment]`       | Payment documents       |
| Equipment Request     | `[Equipment Request Form]` | Equipment forms         |
| MOA                   | `[MOA]`                    | Memorandum of Agreement |
| Billing               | `[Billing Statement]`      | Cost breakdowns         |
| Generic Download      | `[Download]`               | File URL operations     |

---

## Log Message Types

### ✅ Success Flow

```
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] Found survey evaluation file: CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] File URL: http://localhost:8080/api/bookings/evaluation-file/...
[Faculty Evaluation] Download click triggered
[Faculty Evaluation] Download element removed from DOM
[Faculty Evaluation] Survey evaluation file download completed
```

### ❌ Error Flow

```
[Faculty Evaluation] Error fetching survey file: Error message
[Faculty Evaluation] Error message: Details here
[Faculty Evaluation] Falling back to template generation...
[Faculty Evaluation] Error initiating download: Error details
```

---

## Key Log Indicators

### What Each Log Tells You

| Log Message                    | Means                             | Status     |
| ------------------------------ | --------------------------------- | ---------- |
| `Starting download`            | Download process initiated        | ℹ️ Info    |
| `Fetching survey files`        | Checking for existing files       | ℹ️ Info    |
| `response status: 200`         | Server found the resource         | ✅ Good    |
| `response status: 404`         | Server couldn't find resource     | ❌ Bad     |
| `response status: 500`         | Server error                      | ❌ Bad     |
| `Found survey evaluation file` | File exists and can be used       | ✅ Good    |
| `No survey files found`        | Need to generate new file         | ⚠️ Warning |
| `File URL:`                    | URL being used for download       | ℹ️ Info    |
| `Download click triggered`     | Browser received download command | ✅ Good    |
| `Error:`                       | Something went wrong              | ❌ Bad     |

---

## Finding Logs in Console

### How to Search:

1. **Open DevTools**: Press `F12`
2. **Go to Console Tab**: Click the "Console" tab
3. **Search**: Press `Ctrl+F` to find logs
4. **Filter**: Type log prefix like `Faculty Evaluation`

### Search Tips:

- Search for `[Faculty Evaluation]` to see all Faculty Evaluation logs
- Search for `Error` to find all errors
- Search for `status: 404` to find missing files
- Search for `Download click triggered` to find successful clicks

---

## Response Status Codes

| Status | Meaning                   | Action                                       |
| ------ | ------------------------- | -------------------------------------------- |
| 200    | OK - File found           | Check console for "Download click triggered" |
| 404    | Not Found - File missing  | File doesn't exist in `writable/uploads/`    |
| 500    | Server Error              | Check server logs in `writable/logs/`        |
| 403    | Forbidden - Access denied | File pattern validation failed               |
| 0      | Network error             | Connection to server failed                  |

---

## Log Entry Breakdown

### Example Log:

```
[Faculty Evaluation] Found survey evaluation file: CSPC_Evaluation_Booking_84_20251128020111.xlsx
│                   │                                 │
Feature name        Description                       Value/Details
```

### Parts:

- **Feature Name** (in brackets): What feature is logging
- **Description**: What's happening
- **Details**: Specific information about the operation

---

## Common Log Patterns

### ✅ Everything Working:

```
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] Found survey evaluation file: CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] Download click triggered
```

### ⚠️ File Not Ready (Generation needed):

```
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] No survey files found in response
[Faculty Evaluation] Falling back to template generation...
[Faculty Evaluation] Download initiated for booking: 84
```

### ❌ API Not Found:

```
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 404
[Faculty Evaluation] Error fetching survey file: TypeError: Failed to fetch
```

### ❌ File Not Accessible:

```
[Faculty Evaluation] File accessibility check status: 404
[Faculty Evaluation] File may not be accessible (HTTP 404)
```

---

## Where to Look First

**If download is interrupted:**

1. Check browser console for errors
2. Look for `Error` keyword in logs
3. Note the HTTP status code (200, 404, 500)

**If nothing happens:**

1. Check for `Download click triggered` message
2. If missing, look for error before it
3. Check browser download settings

**If file is empty:**

1. Look in server logs for "File is empty!"
2. Check if Excel generation succeeded
3. Try resubmitting the survey

---

## Quick Troubleshooting

| Symptom               | Check For                     | Solution           |
| --------------------- | ----------------------------- | ------------------ |
| No logs appear        | Is DevTools open? Press F12   | Yes                |
| Download but empty    | Server logs: "File is empty!" | Regenerate file    |
| 404 Error             | File doesn't exist            | Resubmit survey    |
| "Interrupted" message | Check network tab             | See network status |
| Multiple errors       | Check first error in sequence | Fix that first     |

---

## Log Levels

### Info Logs (ℹ️)

- Regular operation messages
- Useful for tracking flow
- Example: `[Faculty Evaluation] Starting download for booking #84`

### Error Logs (❌)

- Errors and failures
- Cause of download interruption
- Example: `[Faculty Evaluation] Error: File not found`

### Warning Logs (⚠️)

- Unusual conditions
- Fallback behaviors
- Example: `[Faculty Evaluation] File may not be accessible (HTTP 404)`

---

## Real-World Example Sessions

### Session 1: Successful Download

```
User clicks "Download Faculty Evaluation"
↓
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] Found survey evaluation file: CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] File size: 15234 bytes
[Faculty Evaluation] Download click triggered
File starts downloading... ✅
```

### Session 2: File Not Yet Created

```
User clicks "Download Faculty Evaluation"
↓
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] No survey files found in response
[Faculty Evaluation] Falling back to template generation...
Blank template starts downloading... ✅
```

### Session 3: File Missing from Server

```
User clicks "Download Faculty Evaluation"
↓
[Faculty Evaluation] Starting download for booking #84
[Faculty Evaluation] Fetching survey files from: /api/survey-files/84
[Faculty Evaluation] Survey files response status: 200
[Faculty Evaluation] Found survey evaluation file: CSPC_Evaluation_Booking_84_20251128020111.xlsx
[Faculty Evaluation] File accessibility check status: 404
[Faculty Evaluation] Error: File not found
User sees: ❌ Failed to download Faculty Evaluation Form: File not found
```

---

## Pro Tips 💡

- **Copy Logs**: Right-click in console and select "Save as..."
- **Filter by Type**: Type `console.error` to see only errors
- **Timestamp**: Logs show exact time - helps identify when issue occurred
- **Context**: Each log has booking ID - helps verify you're looking at right booking
- **Stack Trace**: Error logs show where code failed - helps developers

---

**Reference Version**: 1.0  
**Last Updated**: November 28, 2025  
**Companion Doc**: `DOWNLOAD_DEBUGGING_GUIDE.md`
