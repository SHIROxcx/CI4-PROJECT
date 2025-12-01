# File Upload Backend Flow - Faculty Free Booking System

## Overview

The faculty free booking system handles file uploads in a two-step process:

1. **Booking Creation** - Creates the booking record first
2. **File Upload** - Uploads associated documents after booking confirmation

---

## 1. FRONTEND FLOW (faculty-book.js)

### Step 1: File Selection & Validation

**Location:** `public/js/faculty-book.js` - `handleFreeFileSelect()` function (Line 409)

```javascript
function handleFreeFileSelect(input, docType) {
  const file = input.files[0];

  // Validate file size (10MB limit)
  if (file.size > 10 * 1024 * 1024) {
    alert("File size must be less than 10MB");
    input.value = "";
    return;
  }

  // Update UI to show file is uploaded
  statusSpan.textContent = "Uploaded";
  statusSpan.style.color = "#16a34a";
  filenameDisplay.textContent = `File: ${file.name}`;
  uploadItem.style.background = "#f0fdf4";

  validateFreeForm(); // Check if form can be submitted
}
```

**Validations:**

- File size must be ≤ 10MB
- Three required files (Permission, Request, Approval)
- All form fields must be filled

### Step 2: Booking Submission

**Location:** `public/js/faculty-book.js` - `submitFreeBooking()` function (Line 453)

This function performs:

#### 2a. Conflict Check

```javascript
const conflictCheck = await fetch("/api/bookings/checkDateConflict", {
  method: "POST",
  body: JSON.stringify({
    facility_id: facilityId,
    event_date: eventDate,
    event_time: eventTime,
    duration: duration,
  }),
});
```

**Endpoint:** `/api/bookings/checkDateConflict`
**Purpose:** Verifies no existing bookings conflict with requested date/time

#### 2b. Create Booking

```javascript
const bookingData = {
  facility_id: parseInt(document.getElementById("freeFacilityId").value),
  plan_id: 1, // Free plan
  client_name: document.getElementById("freeClientName").value,
  email_address: document.getElementById("freeClientEmail").value,
  organization: document.getElementById("freeOrganization").value,
  contact_number: document.getElementById("freeContactNumber").value,
  address: document.getElementById("freeAddress").value || "",
  event_date: document.getElementById("freeEventDate").value,
  event_time: document.getElementById("freeEventTime").value,
  duration: parseInt(document.getElementById("freeDuration").value),
  attendees: parseInt(document.getElementById("freeAttendees").value) || null,
  event_title: document.getElementById("freeEventTitle").value,
  special_requirements:
    document.getElementById("freeSpecialRequirements").value || "",
  selected_equipment: equipmentSelections,
  booking_type: "faculty",
};

const bookingResponse = await fetch("/api/student/bookings/create", {
  method: "POST",
  body: JSON.stringify(bookingData),
});

const bookingResult = await bookingResponse.json();
const bookingId = bookingResult.booking_id; // Get ID for file upload
```

**Endpoint:** `/api/student/bookings/create`
**Returns:** `booking_id` - Required for file upload

#### 2c. File Upload (After Booking Created)

```javascript
if (hasFiles) {
  const formData = new FormData();
  if (permissionFile) formData.append("files[]", permissionFile);
  if (requestFile) formData.append("files[]", requestFile);
  if (approvalFile) formData.append("files[]", approvalFile);

  const uploadResponse = await fetch(
    `/api/student/bookings/${bookingId}/upload`,
    {
      method: "POST",
      body: formData, // FormData (NOT JSON)
    }
  );

  const uploadResult = await uploadResponse.json();
}
```

**Endpoint:** `/api/student/bookings/{bookingId}/upload`
**Method:** POST with FormData (multipart/form-data)
**Returns:** Success status and uploaded file info

---

## 2. BACKEND FLOW

### API Routes Configuration

**Location:** `app/Config/Routes.php` (Lines 156-159)

```php
// File upload route
$routes->post('(:num)/upload', 'BookingApiController::uploadFiles/$1');
$routes->get('(:num)/files', 'BookingApiController::getBookingFiles/$1');
$routes->get('(:num)/files/(:num)/download', 'BookingApiController::downloadFile/$1/$2');
$routes->delete('files/(:num)', 'BookingApiController::deleteFile/$1');
```

### Upload Handler: uploadFiles()

**Location:** `app/Controllers/Api/BookingApiController.php` (Line 1276)

```php
public function uploadFiles($bookingId)
{
    try {
        // Step 1: Verify booking exists
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Step 2: Create upload directory
        $uploadPath = WRITEPATH . 'uploads/booking_files/' . $bookingId . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedFiles = [];
        $files = $this->request->getFiles();

        if (empty($files)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files uploaded'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // Step 3: Process each file
        foreach ($files as $fileKey => $file) {
            if ($file->isValid() && !$file->hasMoved()) {

                // Validate file size (10MB max)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    continue; // Skip files larger than 10MB
                }

                // Step 4: Check if file type already exists
                $existingFile = $db->table('booking_files')
                                  ->where('booking_id', $bookingId)
                                  ->where('file_type', $fileKey)
                                  ->get()
                                  ->getRowArray();

                if ($existingFile) {
                    // Delete old file from disk
                    if (file_exists($existingFile['file_path'])) {
                        unlink($existingFile['file_path']);
                    }

                    // Delete old database record
                    $db->table('booking_files')->where('id', $existingFile['id'])->delete();
                }

                // Step 5: Generate unique filename and move file
                $newName = $file->getRandomName();

                if ($file->move($uploadPath, $newName)) {

                    // Step 6: Save file metadata to database
                    $fileData = [
                        'booking_id' => $bookingId,
                        'file_type' => $fileKey,
                        'original_filename' => $file->getClientName(),
                        'stored_filename' => $newName,
                        'file_path' => $uploadPath . $newName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                        'upload_date' => date('Y-m-d H:i:s')
                    ];

                    $fileId = $db->table('booking_files')->insert($fileData);

                    $uploadedFiles[] = [
                        'id' => $db->insertID(),
                        'file_type' => $fileKey,
                        'filename' => $file->getClientName(),
                        'size' => $file->getSize()
                    ];
                }
            }
        }

        if (empty($uploadedFiles)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files were successfully uploaded'
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => count($uploadedFiles) . ' files uploaded successfully',
            'files' => $uploadedFiles
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error uploading files: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload files'
        ])->setStatusCode(500);
    }
}
```

**Key Process Steps:**

1. Verify booking ID exists in database
2. Create directory: `writable/uploads/booking_files/{bookingId}/`
3. For each uploaded file:
   - Validate file size ≤ 10MB
   - Check if file type already exists for this booking
   - If exists, delete old file and database record
   - Generate random filename (security measure)
   - Move file to upload directory
   - Store metadata in `booking_files` table
4. Return response with uploaded file info

### Database Model: BookingFileModel

**Location:** `app/Models/BookingFileModel.php`

```php
protected $table = 'booking_files';
protected $primaryKey = 'id';
protected $allowedFields = [
    'booking_id',           // Link to booking
    'file_type',            // Permission, Request, Approval
    'original_filename',    // Original file name
    'stored_filename',      // Random generated name (security)
    'file_path',            // Full path to file
    'file_size',            // File size in bytes
    'mime_type',            // MIME type (PDF, JPG, PNG)
    'upload_date'           // Upload timestamp
];
```

### StudentBookingFileModel

**Location:** `app/Models/StudentBookingFileModel.php`

**Note:** This is a minimal model, mainly for backward compatibility.

---

## 3. DATABASE SCHEMA

### booking_files Table

```sql
CREATE TABLE booking_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    file_type VARCHAR(50),           -- e.g., "files[]"
    original_filename VARCHAR(255),
    stored_filename VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    mime_type VARCHAR(100),
    upload_date DATETIME,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
```

---

## 4. FILE STORAGE STRUCTURE

```
writable/
├── uploads/
│   └── booking_files/
│       └── {booking_id}/
│           ├── random_filename_1.pdf    (Permission file)
│           ├── random_filename_2.jpg    (Request file)
│           └── random_filename_3.png    (Approval file)
```

**Security Features:**

- Files stored in `writable/` (outside web root by default)
- Random filename generation (prevents direct URL guessing)
- Booking ID in path (organizes by booking)
- File size validation (10MB max)

---

## 5. COMPLETE FLOW DIAGRAM

```
Frontend (faculty-book.js)
    ↓
1. User selects 3 files → handleFreeFileSelect() validates
    ↓
2. User clicks "Submit Free Booking" → submitFreeBooking()
    ↓
3. Check date conflicts → /api/bookings/checkDateConflict
    ↓
4. Create booking record → /api/student/bookings/create
    ↓ (Get booking_id from response)
5. Upload files via FormData → /api/student/bookings/{bookingId}/upload
    ↓

Backend (BookingApiController::uploadFiles)
    ↓
1. Verify booking exists
    ↓
2. Create upload directory (writable/uploads/booking_files/{bookingId}/)
    ↓
3. For each file:
   a. Validate file size (≤10MB)
   b. Check if file_type already exists
   c. Delete old file if exists
   d. Generate random filename
   e. Move file to upload directory
   f. Save metadata to booking_files table
    ↓
4. Return JSON response with file IDs
    ↓
Frontend
    ↓
6. Show success toast and redirect to /faculty/bookings
```

---

## 6. REQUEST/RESPONSE EXAMPLES

### File Upload Request

```
POST /api/student/bookings/123/upload
Content-Type: multipart/form-data

FormData:
  files[]: [File object] - Permission file
  files[]: [File object] - Request file
  files[]: [File object] - Approval file
```

### Upload Response (Success)

```json
{
  "success": true,
  "message": "3 files uploaded successfully",
  "files": [
    {
      "id": 1,
      "file_type": "files[]",
      "filename": "permission_letter.pdf",
      "size": 245632
    },
    {
      "id": 2,
      "file_type": "files[]",
      "filename": "booking_request.jpg",
      "size": 1024000
    },
    {
      "id": 3,
      "file_type": "files[]",
      "filename": "approval_letter.png",
      "size": 512000
    }
  ]
}
```

### Upload Response (Error)

```json
{
  "success": false,
  "message": "File not found"
}
```

---

## 7. ERROR HANDLING

| Error                     | Status  | Cause                 | Solution                                |
| ------------------------- | ------- | --------------------- | --------------------------------------- |
| Booking not found         | 404     | Invalid booking ID    | Verify booking was created successfully |
| No files uploaded         | 400     | Empty file array      | Select files before submitting          |
| File size exceeds 10MB    | Skipped | File too large        | Reduce file size                        |
| Directory creation failed | 500     | Permission issue      | Check `writable/` permissions (0755)    |
| File move failed          | 500     | Disk space/permission | Ensure sufficient disk space            |
| Database insert failed    | 500     | Database issue        | Check database connection               |

---

## 8. KEY FILES INVOLVED

| File                                           | Purpose                                  |
| ---------------------------------------------- | ---------------------------------------- |
| `faculty_free_booking_modal.php`               | UI form for file selection               |
| `faculty-book.js`                              | Frontend logic (file validation, upload) |
| `app/Config/Routes.php`                        | API route definitions                    |
| `app/Controllers/Api/BookingApiController.php` | Backend upload handler                   |
| `app/Models/BookingFileModel.php`              | Database operations                      |
| `app/Models/StudentBookingFileModel.php`       | Alternate model (minimal)                |

---

## 9. IMPORTANT NOTES

- **No file type restriction in code**: The backend does NOT validate MIME types after upload. Frontend restricts to `.pdf,.jpg,.jpeg,.png`.
- **File type field inconsistency**: Frontend sends files as `files[]` but no grouping by document type (permission, request, approval) - all files have same `file_type`.
- **Update logic**: If same file_type is uploaded again, old file is deleted and replaced (one file per type).
- **No virus scanning**: No antivirus scanning implemented.
- **Permissions**: Directory created with `0755` permissions.
- **Database transaction**: No transaction wrapping for upload operations.
