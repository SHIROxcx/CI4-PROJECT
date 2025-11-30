# Facilities Dynamic System - Complete Exploration

## Current Architecture

### 1. DATABASE LAYER

**Table: `facilities`**

- `id`: Primary key
- `facility_key`: Unique identifier (auditorium, gymnasium, function-hall, pearl-restaurant, staff-house, classrooms)
- `name`: Facility name
- `icon`: Emoji icon (🎭, 🏀, 🏛️, 🍽️, 🏠, 📖)
- `description`: Facility description
- `additional_hours_rate`: Hourly rate for extended hours
- `extended_hour_rate`: Extended hour pricing
- `is_active`: Boolean flag for active status
- `is_maintenance`: Boolean flag for maintenance status
- `created_at`, `updated_at`: Timestamps

### 2. BACKEND LAYER (Controllers)

#### StudentController (`/app/Controllers/StudentController.php`)

- **Route**: `/student/book`
- **Method**: `book()`
- **Flow**:
  1. Checks if user is logged in
  2. Verifies user role (student/admin)
  3. Fetches ALL facilities from database using `FacilityModel::findAll()`
  4. Passes `$facilities` array to view
  5. Returns `student/student_book` view

```php
// StudentController::book()
$facilities = $facilityModel->orderBy('name', 'ASC')->findAll();
// Returns: Array of facility objects with all fields
```

#### Admin Controller (`/app/Controllers/Admin.php`)

- **Routes**:

  - `/admin/student` → `student()` method → renders `admin/student.php` view
  - `/admin/external` → `external()` method → renders `admin/external.php` view

- **Flow**:
  1. Checks admin role
  2. Returns view WITHOUT passing facilities data
  3. Views load facilities via JavaScript API calls

#### FacilitiesController (`/app/Controllers/FacilitiesController.php`)

- **API Endpoint**: `GET /api/facilities/list`
- **Method**: `getFacilitiesList()`
- **Returns**:

```json
{
  "success": true,
  "facilities": [
    {
      "id": 1,
      "facility_key": "auditorium",
      "name": "University Auditorium",
      "icon": "🎭",
      "is_active": 1,
      "is_maintenance": 0,
      "extended_hour_rate": 500
    }
  ]
}
```

### 3. FRONTEND LAYER (Views & JavaScript)

#### Student Booking Page (`/app/Views/student/student_book.php`)

- **Data Source**: Server-rendered (PHP loops through `$facilities` array)
- **Grid Element**: `<div class="facilities-grid">`
- **Rendering**: Loop through facilities and generate cards:

```html
<?php foreach ($facilities as $facility): ?>
<div
  class="facility-card"
  onclick="openStudentBookingModal('<?= $facility['facility_key'] ?>', <?= $facility['id'] ?>)"
>
  <div class="facility-image"><!-- Icon mapping --></div>
  <h3><?= $facility['name'] ?></h3>
  <!-- ... -->
</div>
<?php endforeach; ?>
```

#### Admin Student Booking Page (`/app/Views/admin/student.php`)

- **Data Source**: API-driven (JavaScript fetch)
- **Grid Element**: `<div class="facilities-grid" id="studentFacilitiesGrid">`
- **Script**: `/public/js/admin/student-facilities.js`
- **Rendering**: JavaScript generates cards on page load

#### Admin External Booking Page (`/app/Views/admin/external.php`)

- **Data Source**: API-driven (JavaScript fetch)
- **Grid Element**: `<div class="facilities-grid" id="externalFacilitiesGrid">`
- **Script**: `/public/js/admin/external-facilities.js`
- **Rendering**: JavaScript generates cards on page load

### 4. JAVASCRIPT LAYER

#### student-facilities.js (`/public/js/admin/student-facilities.js`)

**Current Implementation:**

```javascript
async function loadStudentFacilities() {
  // Attempts to fetch from /api/facilities/student
  const response = await fetch("/api/facilities/student", {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  });

  const data = response.json();
  // Renders facility cards dynamically
}
```

**Status**: ⚠️ **API ENDPOINT MISSING**

- Route `/api/facilities/student` is NOT defined in Routes.php
- Will currently fail or return 404

#### external-facilities.js (`/public/js/admin/external-facilities.js`)

**Current Implementation:**

```javascript
async function loadExternalFacilities() {
  // Attempts to fetch from /api/facilities/external
  const response = await fetch("/api/facilities/external", {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  });

  const data = response.json();
  // Renders facility cards dynamically
}
```

**Status**: ⚠️ **API ENDPOINT MISSING**

- Route `/api/facilities/external` is NOT defined in Routes.php
- Will currently fail or return 404

### 5. ROUTES CONFIGURATION (`/app/Config/Routes.php`)

**Currently Defined Facility Routes:**

```php
// Public API (no auth required)
$routes->get('api/facilities/(:any)/data', 'Api\BookingApiController::getFacilityData/$1');
$routes->get('api/facilities/list', 'FacilitiesController::getFacilitiesList');
$routes->get('api/facilities/data', 'FacilitiesController::getFacilityData');
$routes->get('api/facilities/data/(:segment)', 'FacilitiesController::getFacilityData/$1');
$routes->get('api/facilities/gallery/(:segment)', 'FacilitiesController::getFacilityGallery/$1');

// Admin API (requires auth)
$routes->group('api/facilities', ['filter' => 'auth'], function($routes) {
    $routes->get('all', 'FacilitiesController::getAllFacilities');
    $routes->post('create', 'FacilitiesController::createFacility');
    $routes->post('update/(:num)', 'FacilitiesController::updateFacility/$1');
    $routes->delete('delete/(:num)', 'FacilitiesController::deleteFacility/$1');
});
```

**Missing Routes:**

- ❌ `/api/facilities/student` - Expected by student-facilities.js
- ❌ `/api/facilities/external` - Expected by external-facilities.js

---

## Current Flow Diagram

### Student Booking (PHP Rendered - WORKING)

```
User visits /student/book
  ↓
StudentController::book()
  ↓
Fetches from DB: SELECT * FROM facilities
  ↓
Passes $facilities to view
  ↓
student_book.php loops and renders cards (server-side HTML)
  ↓
Cards displayed with icons, names, descriptions
```

### Admin Student Booking (API-driven - BROKEN)

```
User visits /admin/student
  ↓
Admin::student()
  ↓
Returns admin/student.php view WITHOUT facilities data
  ↓
Page loads, DOMContentLoaded fires
  ↓
student-facilities.js calls loadStudentFacilities()
  ↓
JavaScript attempts: fetch('/api/facilities/student')
  ↓
❌ Route not defined - Returns 404 or error
  ↓
Error message displayed: "Error loading facilities"
```

### Admin External Booking (API-driven - BROKEN)

```
User visits /admin/external
  ↓
Admin::external()
  ↓
Returns admin/external.php view WITHOUT facilities data
  ↓
Page loads, DOMContentLoaded fires
  ↓
external-facilities.js calls loadExternalFacilities()
  ↓
JavaScript attempts: fetch('/api/facilities/external')
  ↓
❌ Route not defined - Returns 404 or error
  ↓
Error message displayed: "Error loading facilities"
```

---

## Key Findings

### What's Working ✅

1. **Student Booking Page**: Uses PHP server-side rendering

   - Facilities loaded via FacilityModel
   - Displayed via PHP foreach loop
   - Real-time data from database

2. **FaciliesController Methods**:

   - `getFacilitiesList()` - API endpoint exists at `/api/facilities/list`
   - `getFacilityData()` - Can fetch individual facility details

3. **Database Design**:
   - Properly structured facilities table
   - Has status flags (is_active, is_maintenance)
   - Icon field for emoji display

### What's Broken ❌

1. **Admin Student Booking**: API calls fail

   - `/api/facilities/student` route missing
   - JavaScript can't render cards

2. **Admin External Booking**: API calls fail
   - `/api/facilities/external` route missing
   - JavaScript can't render cards

---

## Solution Options

### Option 1: Add Missing Routes (Quick Fix)

Add to `/app/Config/Routes.php`:

```php
$routes->get('api/facilities/student', 'FacilitiesController::getFacilitiesList');
$routes->get('api/facilities/external', 'FacilitiesController::getFacilitiesList');
```

**Pros**: Quick, reuses existing code
**Cons**: Both return same data (no student/external differentiation)

### Option 2: Create Dedicated Methods (Better)

Add to `FacilitiesController`:

```php
public function getStudentFacilities() {
    // Return facilities for student booking (free only)
}

public function getExternalFacilities() {
    // Return facilities for external booking (with pricing)
}
```

**Pros**: Separate logic, can customize response per booking type
**Cons**: More code duplication

### Option 3: Use Existing getFacilitiesList Endpoint

Modify JavaScript to call existing endpoint:

```javascript
// Change from: /api/facilities/student
// To: /api/facilities/list
const response = await fetch("/api/facilities/list");
```

**Pros**: No backend changes needed
**Cons**: JavaScript modification required

---

## Data Flow Summary

| Component                | Current Status | Data Source    | Rendering        |
| ------------------------ | -------------- | -------------- | ---------------- |
| Student Booking          | ✅ Working     | Database (PHP) | Server-side HTML |
| Admin Student            | ❌ Broken      | Missing API    | Client-side JS   |
| Admin External           | ❌ Broken      | Missing API    | Client-side JS   |
| API /api/facilities/list | ✅ Working     | Database       | JSON response    |
| Database Tables          | ✅ Complete    | N/A            | Data storage     |

---

## Files Involved

### Backend

- `/app/Controllers/StudentController.php` - Student booking page
- `/app/Controllers/Admin.php` - Admin booking pages
- `/app/Controllers/FacilitiesController.php` - API endpoints
- `/app/Models/FacilityModel.php` - Database queries
- `/app/Config/Routes.php` - URL routing

### Frontend Views

- `/app/Views/student/student_book.php` - PHP rendered
- `/app/Views/admin/student.php` - API-driven
- `/app/Views/admin/external.php` - API-driven

### Frontend Scripts

- `/public/js/admin/student-facilities.js` - Loads student facilities
- `/public/js/admin/external-facilities.js` - Loads external facilities
- `/public/js/booking.js` - Booking modal logic
- `/public/js/student-book.js` - Student page logic

### Database

- Table: `facilities` - Stores all facility data
- Table: `plans` - Stores pricing plans per facility
- Table: `equipment` - Stores equipment data
- Table: `bookings` - Stores booking records

---

## Next Steps

To make the admin booking pages fully dynamic:

1. Define the missing routes
2. Optionally add dedicated controller methods
3. Test API endpoints
4. Ensure JavaScript properly handles responses
5. Consider adding filtering by booking type (internal/external)
