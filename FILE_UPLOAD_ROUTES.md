# File Upload Routes & Flow Documentation

## Overview

When a user creates a booking through the internal facility booking system, files are uploaded through a multi-step process.

---

## 1. FRONTEND FILE UPLOAD FLOW

### JavaScript Files Involved:

- **[js/admin/student.js](public/js/admin/student.js)** - Main file handling upload logic
- **[js/admin/student-steps.js](public/js/admin/student-steps.js)** - Step navigation
- **[Views/admin/internal.php](app/Views/admin/internal.php)** - HTML form & upload UI

### Upload Process:

#### Step 1: Form Submission

**Function:** `submitStudentBooking()` (Line 374 in student.js)

1. Validates form data
2. Creates booking via POST request
3. Uploads files if any are selected

#### Step 2: File Collection

Files are stored in the global object:

```javascript
let uploadedStudentFiles = {
  permission: null, // "Approved Permission to Conduct" file
  request: null, // "Letter Request for Venue" file
  approval: null, // "Approval Letter of the Venue" file
};
```

Files are added via:

```javascript
function handleStudentFileSelect(fileInput, docType)
```

This stores File objects (from input elements) into `uploadedStudentFiles`.

#### Step 3: Booking Creation

**Endpoint:** `POST /api/student/bookings/create`

```javascript
const bookingResponse = await fetch("/api/student/bookings/create", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
  },
  body: JSON.stringify(bookingData),
});
```

Returns: `bookingResult.booking_id` (needed for file upload)

#### Step 4: File Upload

**Endpoint:** `POST /api/bookings/{bookingId}/upload`

Files are sent as FormData:

```javascript
const formData = new FormData();

// Add files
formData.append("files[]", uploadedStudentFiles.permission);
formData.append("files[]", uploadedStudentFiles.request);
formData.append("files[]", uploadedStudentFiles.approval);

const uploadResponse = await fetch(`/api/bookings/${bookingId}/upload`, {
  method: "POST",
  headers: {
    "X-Requested-With": "XMLHttpRequest",
  },
  body: formData,
});
```

---

## 2. BACKEND ROUTES

### Route Definitions (app/Config/Routes.php)

#### Student Booking API Routes (Lines 305-322):

```php
$routes->group('api/student', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth'], function($routes) {
    $routes->post('bookings/create', 'StudentBookingApi::createStudentBooking');

    // FILE UPLOAD ROUTES
    $routes->post('bookings/(:num)/upload', 'StudentBookingApi::uploadStudentDocuments/$1');
    $routes->post('bookings/(:num)/upload-files', 'StudentBookingApi::uploadStudentDocuments/$1');

    // Other routes...
});
```

#### Admin Booking API Routes (Lines 155-169):

```php
$routes->group('api/bookings', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth'], function($routes) {
    // File operations (admin)
    $routes->post('(:num)/upload', 'BookingApiController::uploadFiles/$1');
    $routes->get('(:num)/files', 'BookingApiController::getBookingFiles/$1');
    $routes->get('(:num)/files/(:num)/download', 'BookingApiController::downloadFile/$1/$2');
    $routes->delete('files/(:num)', 'BookingApiController::deleteFile/$1');
});
```

---

## 3. ENDPOINT SUMMARY

### Student Booking Creation

| Method | Endpoint                       | Controller                                | Purpose            |
| ------ | ------------------------------ | ----------------------------------------- | ------------------ |
| POST   | `/api/student/bookings/create` | `StudentBookingApi::createStudentBooking` | Create new booking |

### Student File Upload

| Method | Endpoint                                  | Controller                                  | Purpose                     |
| ------ | ----------------------------------------- | ------------------------------------------- | --------------------------- |
| POST   | `/api/bookings/{id}/upload`               | `StudentBookingApi::uploadStudentDocuments` | Upload booking documents    |
| POST   | `/api/student/bookings/{id}/upload`       | `StudentBookingApi::uploadStudentDocuments` | Alternative upload endpoint |
| POST   | `/api/student/bookings/{id}/upload-files` | `StudentBookingApi::uploadStudentDocuments` | Alternative upload endpoint |

### Admin File Upload

| Method | Endpoint                    | Controller                          | Purpose            |
| ------ | --------------------------- | ----------------------------------- | ------------------ |
| POST   | `/api/bookings/{id}/upload` | `BookingApiController::uploadFiles` | Admin upload files |

### File Retrieval

| Method | Endpoint                                     | Controller                                  | Purpose                   |
| ------ | -------------------------------------------- | ------------------------------------------- | ------------------------- |
| GET    | `/api/bookings/{id}/files`                   | `BookingApiController::getBookingFiles`     | Get all files for booking |
| GET    | `/api/bookings/{id}/files/{fileId}/download` | `BookingApiController::downloadFile`        | Download specific file    |
| GET    | `/api/student/bookings/{id}/files`           | `StudentBookingApi::getStudentBookingFiles` | Student get their files   |

### File Deletion

| Method | Endpoint                                    | Controller                                 | Purpose             |
| ------ | ------------------------------------------- | ------------------------------------------ | ------------------- |
| DELETE | `/api/bookings/files/{id}`                  | `BookingApiController::deleteFile`         | Delete file         |
| DELETE | `/api/student/bookings/{id}/files/{fileId}` | `StudentBookingApi::deleteStudentDocument` | Student delete file |

---

## 4. AUTHENTICATION & AUTHORIZATION

### Student Routes Filter: `'filter' => 'auth'`

- Requires user to be logged in
- Routes under `api/student` require authentication

### Admin Routes Filter: `'filter' => 'auth'`

- Requires admin/authorized user to be logged in
- Routes under `api/bookings` require authentication

---

## 5. KEY CONTROLLERS

### StudentBookingApi Controller

**Location:** `app/Controllers/Api/StudentBookingApi.php`

**Methods:**

- `createStudentBooking()` - Creates booking record
- `uploadStudentDocuments($bookingId)` - Handles file uploads
- `getStudentBookingFiles($bookingId)` - Returns file list
- `downloadStudentDocument($bookingId, $fileId)` - Downloads file
- `deleteStudentDocument($bookingId, $fileId)` - Deletes file

### BookingApiController

**Location:** `app/Controllers/Api/BookingApiController.php`

**Methods:**

- `uploadFiles($bookingId)` - Admin file upload
- `getBookingFiles($bookingId)` - Get files list
- `downloadFile($bookingId, $fileId)` - Download file
- `deleteFile($fileId)` - Delete file

---

## 6. FILE UPLOAD FORM ELEMENTS

**HTML Location:** [Views/admin/internal.php](app/Views/admin/internal.php#L340-L378)

### Upload Cards (Step 4):

```html
<!-- Permission Document -->
<input
  type="file"
  id="file-permission"
  accept=".pdf,.jpg,.jpeg,.png"
  onchange="handleStudentFileSelect(this, 'permission')"
/>

<!-- Request Letter -->
<input
  type="file"
  id="file-request"
  accept=".pdf,.jpg,.jpeg,.png"
  onchange="handleStudentFileSelect(this, 'request')"
/>

<!-- Approval Letter -->
<input
  type="file"
  id="file-approval"
  accept=".pdf,.jpg,.jpeg,.png"
  onchange="handleStudentFileSelect(this, 'approval')"
/>
```

### File Constraints:

- **Accepted formats:** PDF, JPG, JPEG, PNG
- **Max size:** 10MB per file
- **Form field names:** `files[]` (array)

---

## 7. DEBUG INFORMATION

### Browser Console

The `submitStudentBooking()` function logs detailed information:

- Booking creation status
- File FormData entries
- Network request details
- Upload response status
- File verification results

### Debug Panel (HTML)

- **ID:** `uploadDebugPanel` (lines 315-321)
- Shows upload progress with timestamps
- Color-coded messages (success, error, warning, info)
- Displays both client-side and server response logs

---

---

## 8. BOOKING MANAGEMENT PAGE - FILE UPLOADS

### Overview

The Booking Management page ([app/Views/admin/bookingManagement.php](app/Views/admin/bookingManagement.php)) allows admins to upload documents for existing bookings. This handles both **Student/Employee** bookings and **User/External** bookings.

### JavaScript File

- **[public/js/admin/bookingManagement.js](public/js/admin/bookingManagement.js)** - Main upload handler

### Upload Modal Functions

#### 1. Open Upload Modal - Student/Employee Booking

**Function:** `openStudentUploadModal(bookingId)` (Line 3311)

```javascript
function openStudentUploadModal(bookingId) {
  // Sets currentBookingId
  // Shows upload modal with 3 required files:
  // - Permission Letter (permission_letter)
  // - Request Letter (request_letter)
  // - Approval Letter (approval_letter)
}
```

**File Types for Student/Employee:**

- `permission_letter` - Official permission from organization adviser
- `request_letter` - Formal request letter for facility booking
- `approval_letter` - Pre-approval or recommendation letter

#### 2. Open Upload Modal - User/External Booking

**Function:** `openUploadModal(bookingId)` (Line 1785)

```javascript
async function openUploadModal(bookingId) {
  // Determines booking type: Student/Employee vs User/External
  // Shows appropriate file upload items
  // Sets total count: 3 for free bookings, 7 for user bookings
  // Loads existing files
}
```

**File Types for User/External (7 files):**

- `receipt` - Payment receipt
- `moa` - Memorandum of Agreement
- `billing` - Billing statement
- `equipment` - Equipment request form
- `evaluation` - Faculty evaluation
- `inspection` - Inspection evaluation
- `orderofpayment` - Order of payment

### File Upload Submission

#### For Student/Employee Bookings

**Function:** `saveFreeBookingUploadedFiles()` (Line 2151)

```javascript
async function saveFreeBookingUploadedFiles() {
  // Collects new files only (not existing ones)
  // Creates FormData with "files[]" array format
  // POSTs to: /api/bookings/{bookingId}/upload
  // Response includes: success, message, files array
}
```

**Request Format:**

```javascript
formData.append("files[]", fileData.file);
// Multiple files sent as array

POST /api/bookings/{bookingId}/upload
Headers: {
  "X-Requested-With": "XMLHttpRequest"
}
Body: FormData with "files[]"
```

#### For User/External Bookings

**Function:** `saveAdminUserUploadedFiles()` (Line 2061)

```javascript
async function saveAdminUserUploadedFiles() {
  // Collects new files only
  // Creates FormData with file_type as key
  // POSTs to: /api/bookings/{bookingId}/upload
  // Response includes: success, message, files array
}
```

**Request Format:**

```javascript
formData.append("receipt", fileData.file);
formData.append("moa", fileData.file);
formData.append("billing", fileData.file);
// etc. - each file_type as separate key

POST /api/bookings/{bookingId}/upload
Headers: {
  "X-Requested-With": "XMLHttpRequest"
}
Body: FormData with file_type keys
```

### Smart Routing Function

**Function:** `saveUploadedFiles()` (Line 2030)

```javascript
async function saveUploadedFiles() {
  // Detects booking type by checking visible file upload items
  // Routes to correct save function:
  // - saveFreeBookingUploadedFiles() for student/employee
  // - saveAdminUserUploadedFiles() for user/external
}
```

### Loading Existing Files

#### For Student/Employee

**Function:** `loadExistingStudentFilesForUpload(bookingId)`

- Loads student/employee files from database
- Updates UI with existing file info
- Shows download/cancel buttons for existing files

#### For User/External

**Function:** `loadExistingFiles(bookingId)` (Line 2260+)

- Loads all 7 file types from database
- Updates file input status
- Shows file count progress

### Upload Progress Tracking

**Function:** `updateUploadProgress()` (Line 2010)

```javascript
function updateUploadProgress() {
  // Calculates: uploaded / total files
  // Updates progress bar width
  // Enables/disables save button based on files uploaded
  // Total is 3 for student/employee, 7 for user/external
}
```

### HTML Elements (bookingManagement.php)

- Upload Modal ID: `uploadModal`
- Booking ID Display: `uploadBookingId`
- Client Name Display: `uploadClientName`
- File Count: `uploadedCount` / `totalCount`
- Progress Bar: `uploadProgressFill`
- Save Button: `saveUploadBtn`

---

## 9. COMPARISON TABLE - UPLOAD FLOWS

| Aspect              | Internal Booking Form                                           | Booking Management                                                                             |
| ------------------- | --------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| **Location**        | [internal.php](app/Views/admin/internal.php)                    | [bookingManagement.php](app/Views/admin/bookingManagement.php)                                 |
| **JS File**         | [student.js](public/js/admin/student.js)                        | [bookingManagement.js](public/js/admin/bookingManagement.js)                                   |
| **Step Type**       | Multi-step form (Step 4)                                        | Modal popup                                                                                    |
| **Student Files**   | 3 files                                                         | 3 files                                                                                        |
| **User Files**      | -                                                               | 7 files                                                                                        |
| **FormData Format** | files[] (array)                                                 | files[] (array) for free / file_type keys for user                                             |
| **Upload Endpoint** | `/api/student/bookings/create` then `/api/bookings/{id}/upload` | `/api/bookings/{id}/upload`                                                                    |
| **Controller**      | `StudentBookingApi::uploadStudentDocuments`                     | `BookingApiController::uploadFiles` / `StudentBookingApi::uploadStudentDocuments`              |
| **Existing Files**  | N/A (new booking)                                               | Can load & download existing files                                                             |
| **File Types**      | Permission, Request, Approval                                   | Permission, Request, Approval, Receipt, MOA, Billing, Equipment, Evaluation, Inspection, Order |

---

## 10. SUMMARY

**Internal Booking Form Upload Flow:**

1. User selects files in HTML form (Step 4)
2. Files stored in `uploadedStudentFiles` object
3. User submits form → `submitStudentBooking()` called
4. Step 1: POST booking data to `/api/student/bookings/create`
5. Step 2: POST files (FormData) to `/api/bookings/{bookingId}/upload`
6. Server validates and stores files
7. Response includes uploaded file information
8. Client verifies files stored in database

**Booking Management Upload Flow:**

1. Admin clicks "Upload" button on booking row
2. Opens upload modal with `openStudentUploadModal()` or `openUploadModal()`
3. Admin selects files from file inputs
4. `saveUploadedFiles()` routes to appropriate save function
5. `saveFreeBookingUploadedFiles()` or `saveAdminUserUploadedFiles()` executes
6. FormData sent to POST `/api/bookings/{bookingId}/upload`
7. Server processes files based on file_type or array
8. UI updates with download/cancel buttons for uploaded files

**Key Files:**

- Internal booking: [public/js/admin/student.js](public/js/admin/student.js#L374-L700)
- Booking management: [public/js/admin/bookingManagement.js](public/js/admin/bookingManagement.js#L1785-L2260)
- Routes: [app/Config/Routes.php](app/Config/Routes.php#L155-L322)
- Controllers: `StudentBookingApi` and `BookingApiController`
