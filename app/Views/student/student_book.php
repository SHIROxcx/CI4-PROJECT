<?php
// Check if user is logged in
$session = session();
$isLoggedIn = $session->get('user_id') !== null;
$userRole = $session->get('role');
$userName = $session->get('full_name');
$userEmail = $session->get('email');
$userPhone = $session->get('contact_number');

// Redirect if not logged in
if (!$isLoggedIn) {
    redirect('/login');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Facility | CSPC Digital Booking System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/dashboard/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin/student.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin/student-modern.css'); ?>">

</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="">
                <div class="cspc-logo-nav">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                CSPC Sphere
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= $userName ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('/student/dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                                                  <li><a class="dropdown-item" href="<?= site_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h5>Dashboard</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/student/dashboard') ?>">
                            <i class="fas fa-tachometer-alt"></i> Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= site_url('/student/book') ?>">
                            <i class="fas fa-calendar-plus"></i> Book Facility
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/student/bookings') ?>">
                            <i class="fas fa-calendar-check"></i> My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/student/profile') ?>">
                            <i class="fas fa-user-edit"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/student/history') ?>">
                            <i class="fas fa-history"></i> Booking History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/student/attendance') ?>">
                            <i class="fas fa-qrcode"></i> Attendance
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="booking-page">
                    <div class="page-title">
                        <h2>Book a Facility</h2>
                        <p>Select a facility below to start your booking</p>
                    </div>

                    <!-- Facilities Grid -->
                  <div class="facilities-grid">
    <?php if (!empty($facilities)): ?>
        <?php foreach ($facilities as $facility): ?>
            <div class="facility-card" onclick="openStudentBookingModal('<?= esc($facility['facility_key']) ?>', <?= $facility['id'] ?>)">
                <div class="facility-image">
                    <?php
                    // Map facility icons
                    $icons = [
                        'auditorium' => '🎭',
                        'gymnasium' => '🏀',
                        'function-hall' => '🏛️',
                        'pearl-restaurant' => '🍽️',
                        'staff-house' => '🏠',
                        'classrooms' => '📖'
                    ];
                    echo $icons[$facility['facility_key']] ?? '🏢';
                    ?>
                </div>
                <div class="facility-info">
                    <h3 class="facility-title"><?= esc($facility['name']) ?></h3>
                    <p class="facility-description"><?= esc($facility['description'] ?? 'No description available') ?></p>
                    <div class="facility-features">
                        <span class="feature-tag">Air Conditioned</span>
                        <span class="feature-tag">Sound System</span>
                        <span class="feature-tag">Projector</span>
                    </div>
                    <div class="facility-price">
                        <span class="price-range">Free for Students</span>
                        <button class="book-btn">Book Now</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No facilities available for booking at this time.</p>
    <?php endif; ?>
</div>
  <!-- Student Booking Modal -->
<div id="studentBookingModal" class="modal student-booking-modal">
    <div class="modal-content student-modal-content">
        <div class="modal-header student-modal-header">
            <div class="header-badge">🎓</div>
            <div>
                <h2 class="modal-title" id="modalTitle">Facility Booking</h2>
                <p class="modal-subtitle">Complete your booking request</p>
            </div>
            <span class="close" onclick="closeStudentModal()">&times;</span>
        </div>
        <div class="modal-body student-modal-body">
            <!-- Progress Indicator -->
            <div class="progress-steps">
                <div class="progress-step active" data-step="1">
                    <div class="step-number">1</div>
                    <span>Basic Info</span>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="step-number">2</div>
                    <span>Event Details</span>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="step-number">3</div>
                    <span>Equipment</span>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="step-number">4</div>
                    <span>Documents</span>
                </div>
            </div>

            <!-- Step 1: Basic Information -->
            <div class="form-step active" data-step="1">
                <div class="section-card">
                    <h3 class="card-title">👤 Personal Information</h3>
                    <form id="studentBookingForm">
                        <!-- Hidden fields - auto-filled from session -->
                        <input type="hidden" id="clientName" value="<?= esc($userName ?? '') ?>">
                        <input type="hidden" id="clientEmail" value="<?= esc($userEmail ?? '') ?>">

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control form-control-modern" id="contactNumber" 
                                       value="<?= esc($userPhone ?? '') ?>" 
                                       placeholder="+63 (xxx) xxx-xxxx" 
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label">Organization/Group Name *</label>
                                <input type="text" class="form-control form-control-modern" id="organization" placeholder="Enter organization or group name" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label">Address</label>
                                <textarea class="form-control form-control-modern textarea" id="address" rows="2" placeholder="San Nicolas, Iriga City, Camarines Sur"></textarea>
                                <small class="form-hint">Optional. If provided, must be at least 10 characters (street, city, province)</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 2: Event Details -->
            <div class="form-step" data-step="2">
                <div class="section-card">
                    <h3 class="card-title">📅 Event Information</h3>
                    <form>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Event Date *</label>
                                <input type="date" class="form-control form-control-modern" id="eventDate" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Event Time *</label>
                                <input type="time" class="form-control form-control-modern" id="eventTime" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Duration (hours) *</label>
                                <div class="input-with-unit">
                                    <input type="number" class="form-control form-control-modern" id="duration" min="1" max="12" value="4" required>
                                    <span class="unit-label">hrs</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Expected Attendees</label>
                                <input type="number" class="form-control form-control-modern" id="attendees" min="1" placeholder="Enter estimated count">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label">Event Title/Purpose *</label>
                                <input type="text" class="form-control form-control-modern" id="eventTitle" placeholder="What is your event about?" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label">Special Requirements</label>
                                <textarea class="form-control form-control-modern textarea" id="specialRequirements" placeholder="Any special setup or requirements?"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 3: Equipment -->
            <div class="form-step" data-step="3">
                <div class="section-card">
                    <h3 class="card-title">🔧 Equipment & Resources</h3>
                    <div class="equipment-grid" id="studentEquipmentGrid">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>
            </div>

            <!-- Step 4: Document Upload -->
            <div class="form-step" data-step="4">
                <div class="section-card">
                    <h3 class="card-title">📎 Required Documents</h3>
                    <p class="card-description">Upload the following documents (PDF, JPG, PNG - Max 10MB each)</p>

                    <div class="documents-container">
                        <!-- Permission Document -->
                        <div class="document-upload-card" id="upload-permission">
                            <div class="document-icon">📄</div>
                            <h4 class="document-title">Approved Permission to Conduct</h4>
                            <p class="document-desc">Official permission letter from your organization's adviser</p>
                            <div class="upload-area">
                                <input type="file" id="file-permission" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'permission')">
                                <div class="file-name-display" id="filename-permission"></div>
                            </div>
                            <span class="upload-status">Not uploaded</span>
                        </div>

                        <!-- Request Letter -->
                        <div class="document-upload-card" id="upload-request">
                            <div class="document-icon">📝</div>
                            <h4 class="document-title">Letter Request for Venue</h4>
                            <p class="document-desc">Formal letter requesting the use of the facility</p>
                            <div class="upload-area">
                                <input type="file" id="file-request" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'request')">
                                <div class="file-name-display" id="filename-request"></div>
                            </div>
                            <span class="upload-status">Not uploaded</span>
                        </div>

                        <!-- Approval Letter -->
                        <div class="document-upload-card" id="upload-approval">
                            <div class="document-icon">✅</div>
                            <h4 class="document-title">Approval Letter of the Venue</h4>
                            <p class="document-desc">Pre-approval or recommendation letter from authorized personnel</p>
                            <div class="upload-area">
                                <input type="file" id="file-approval" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'approval')">
                                <div class="file-name-display" id="filename-approval"></div>
                            </div>
                            <span class="upload-status">Not uploaded</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer student-modal-footer">
            <div class="footer-nav">
                <button type="button" class="btn btn-tertiary" id="prevBtn" onclick="prevStep()" style="display: none;">← Previous</button>
            </div>
            <div class="footer-actions">
                <button type="button" class="btn btn-outline" onclick="closeStudentModal()">Cancel</button>
                <button type="button" class="btn btn-next" id="nextBtn" onclick="nextStep()">Next →</button>
                <button type="button" class="btn btn-success" id="submitStudentBtn" onclick="submitStudentBooking()" style="display: none;" disabled>
                    ✓ Submit Booking
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('js/student-book.js') ?>"></script>
<script src="<?= base_url('js/student-book-steps.js') ?>"></script>
</body>
</html>