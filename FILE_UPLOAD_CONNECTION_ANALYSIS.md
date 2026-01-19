# File Upload Connection Analysis - Issue & Solution

## Problem Statement

When a booking is created with files (internal booking form), the files are saved. However, when navigating to Booking Management and opening the upload section for that booking, the files do NOT appear - regardless of booking type (student/employee/user).

**Expected:** Files uploaded during booking creation should auto-populate in Booking Management upload modal
**Actual:** Upload modal shows empty file status

---

## Root Cause Analysis

### 1. File Storage (Database Table)

**Location:** Both controllers use the same table: `student_booking_files`

**Student/Employee Booking Creation** (Line 476-659 in StudentBookingApi.php)

- Files stored in: `student_booking_files` table
- File type mapping:
  ```php
  0 => 'permission_letter'
  1 => 'request_letter'
  2 => 'approval_letter'
  ```

**Admin Booking Management** (Line 1352-1530 in BookingApiController.php)

- Files stored in: `student_booking_files` table
- Same file type mapping for student/employee files
- Different file types for user bookings:
  ```php
  'receipt', 'moa', 'billing', 'equipment',
  'evaluation', 'inspection', 'orderofpayment'
  ```

✅ **Both use same table** - This is CORRECT

---

### 2. File Retrieval (API Endpoints)

#### Route Definitions (Routes.php)

**Admin Booking API** (Lines 155-169):

```php
$routes->group('api/bookings', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth'], function($routes) {
    // File operations (admin)
    $routes->post('(:num)/upload', 'BookingApiController::uploadFiles/$1');
    $routes->get('(:num)/files', 'BookingApiController::getBookingFiles/$1');
    $routes->delete('files/(:num)', 'BookingApiController::deleteFile/$1');
});
```

**Student Booking API** (Lines 305-322):

```php
$routes->group('api/student', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth'], function($routes) {
    $routes->post('bookings/(:num)/upload', 'StudentBookingApi::uploadStudentDocuments/$1');
    $routes->get('bookings/(:num)/files', 'StudentBookingApi::getStudentBookingFiles/$1');
});
```

#### Controller Methods

**BookingApiController::getBookingFiles()** (Line 1530):

```php
public function getBookingFiles($bookingId)
{
    // Queries: student_booking_files table ✓
    $files = $db->table('student_booking_files')
               ->where('booking_id', $bookingId)
               ->get()
               ->getResultArray();
}
```

**StudentBookingApi::getStudentBookingFiles()** (Line 662):

```php
public function getStudentBookingFiles($bookingId)
{
    // Queries: student_booking_files table ✓
    $studentFileModel = new StudentBookingFileModel();
    $files = $studentFileModel->where('booking_id', $bookingId)->findAll();
}
```

✅ **Both query correct table** - This is CORRECT

---

### 3. JavaScript API Calls (Booking Management)

#### Function: loadExistingStudentFilesForUpload()

**Location:** Line 3567 in bookingManagement.js

```javascript
async function loadExistingStudentFilesForUpload(bookingId) {
  // Calls: /api/bookings/{bookingId}/files
  const response = await fetch(`/api/bookings/${bookingId}/files`);

  if (data.success && data.files && data.files.length > 0) {
    // Updates UI with file info
  }
}
```

**ISSUE IDENTIFIED HERE!** ⚠️

The function is called but let's check WHERE it's called from:

#### Function: openStudentUploadModal()

**Location:** Line 3311 in bookingManagement.js

```javascript
function openStudentUploadModal(bookingId) {
  currentBookingId = bookingId;
  // ... setup code ...
  loadExistingStudentFilesForUpload(bookingId); // ← CALLED HERE
}
```

The function loads files, but we need to verify the endpoint return format matches the expected format.

---

## API Response Format Mismatch

### StudentBookingApi::getStudentBookingFiles() Response

```json
{
  "success": true,
  "files": [
    {
      "id": 123,
      "file_type": "permission_letter",
      "filename": "permission.pdf",      ← Returns 'filename'
      "size": 102400,
      "mime_type": "application/pdf",
      "upload_date": "2024-01-15 10:30:00"
    }
  ],
  "total_files": 1
}
```

### BookingApiController::getBookingFiles() Response

```json
{
  "success": true,
  "files": [
    {
      "id": 123,
      "file_type": "permission_letter",
      "original_filename": "permission.pdf",    ← Returns 'original_filename'
      "file_size": 102400,
      "mime_type": "application/pdf",
      "upload_date": "2024-01-15 10:30:00"
    }
  ],
  "count": 1
}
```

### JavaScript Parsing in loadExistingStudentFilesForUpload()

```javascript
data.files.forEach((file) => {
  uploadedFiles[fileType] = {
    fileId: file.id,
    name: file.filename, // ← EXPECTS 'filename'
    size: file.size, // ← EXPECTS 'size'
    type: file.mime_type,
    uploadDate: new Date(file.upload_date),
    isExisting: true,
  };

  status.textContent = `Uploaded: ${file.filename}`; // ← EXPECTS 'filename'
});
```

---

## The Problem Summary

When `BookingApiController::getBookingFiles()` is called for a booking created during the internal form (student/employee booking):

**Endpoint Called:** `GET /api/bookings/{bookingId}/files`
**Controller Method:** `BookingApiController::getBookingFiles()`
**Response Format:** Uses `original_filename` and `file_size`
**JavaScript Expects:** `filename` and `size`

**Result:** Data exists in database but UI cannot parse it correctly because:

1. JS looks for `file.filename` → Server returns `original_filename` ❌
2. JS looks for `file.size` → Server returns `file_size` ❌

---

## Solution

There are three possible fixes:

### Option 1: Normalize Response in BookingApiController (RECOMMENDED)

Modify `getBookingFiles()` to return consistent format:

```php
public function getBookingFiles($bookingId)
{
    $files = $db->table('student_booking_files')
               ->where('booking_id', $bookingId)
               ->get()
               ->getResultArray();

    // Normalize response format
    $formattedFiles = array_map(function($file) {
        return [
            'id' => $file['id'],
            'file_type' => $file['file_type'],
            'filename' => $file['original_filename'],  // ← Normalize
            'size' => $file['file_size'],              // ← Normalize
            'mime_type' => $file['mime_type'],
            'upload_date' => $file['upload_date']
        ];
    }, $files);

    return $this->response->setJSON([
        'success' => true,
        'files' => $formattedFiles,
        'count' => count($formattedFiles)
    ]);
}
```

### Option 2: Update JavaScript to Handle Both Formats

Modify `loadExistingStudentFilesForUpload()`:

```javascript
async function loadExistingStudentFilesForUpload(bookingId) {
  const response = await fetch(`/api/bookings/${bookingId}/files`);

  if (data.success && data.files && data.files.length > 0) {
    data.files.forEach((file) => {
      // Handle both formats
      const filename = file.filename || file.original_filename;
      const filesize = file.size || file.file_size;
      const fileType = file.file_type;

      uploadedFiles[fileType] = {
        fileId: file.id,
        name: filename,
        size: filesize,
        type: file.mime_type,
        uploadDate: new Date(file.upload_date),
        isExisting: true,
      };

      const uploadItem = document.querySelector(
        `[data-file-type="${fileType}"]`
      );
      if (uploadItem) {
        const status = uploadItem.querySelector(".file-upload-status");
        if (status) {
          status.className = "file-upload-status status-uploaded";
          status.textContent = `Uploaded: ${filename}`;
        }
        // Show download/cancel buttons...
      }
    });
    updateUploadProgress();
  }
}
```

### Option 3: Use StudentBookingApi Endpoint Instead

Modify `openStudentUploadModal()` to call the student API endpoint:

```javascript
function openStudentUploadModal(bookingId) {
  currentBookingId = bookingId;
  // ... setup code ...

  // Use StudentBookingApi endpoint instead of BookingApiController
  loadExistingStudentFilesForUpload(bookingId);
}

async function loadExistingStudentFilesForUpload(bookingId) {
  try {
    // Call the StudentBookingApi endpoint that returns correct format
    const response = await fetch(`/api/student/bookings/${bookingId}/files`);
    // Rest of code...
  } catch (error) {
    console.error("Error loading existing student files:", error);
  }
}
```

---

## Recommended Fix

**Option 1 is recommended** because:

1. ✅ Centralizes normalization in backend
2. ✅ Prevents issues if other code uses this endpoint
3. ✅ Consistent response format for all file endpoints
4. ✅ No changes needed in JavaScript
5. ✅ Works for both student/employee AND user bookings

---

## Testing the Fix

After implementing Option 1:

1. **Create a booking** with files using internal form
2. **Navigate to Booking Management**
3. **Click the booking row** → Upload modal opens
4. **Verify:** Files appear in the modal with:
   - ✅ File status shows "Uploaded: [filename]"
   - ✅ Download button is visible
   - ✅ Cancel button is visible
   - ✅ Upload progress bar shows 3/3 files

---

## Implementation Steps for Option 1

1. Open `app/Controllers/Api/BookingApiController.php`
2. Find `getBookingFiles()` method (Line 1530)
3. Add array_map transformation (see code above)
4. Test with curl/Postman to verify response format
5. Verify in browser - files should now appear

---

## Files Involved

- **Backend:**

  - `app/Controllers/Api/BookingApiController.php` (getBookingFiles method)
  - `app/Controllers/Api/StudentBookingApi.php` (uploadStudentDocuments, getStudentBookingFiles)
  - `app/Config/Routes.php` (routes definition)

- **Database:**

  - Table: `student_booking_files` (used by both student and admin uploads)

- **Frontend:**
  - `public/js/admin/bookingManagement.js` (openStudentUploadModal, loadExistingStudentFilesForUpload)
  - `app/Views/admin/bookingManagement.php` (upload modal HTML)
