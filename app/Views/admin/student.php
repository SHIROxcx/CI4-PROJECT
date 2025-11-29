<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC Internal Booking - Facility Booking</title>
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin/student.css') ?>">

</head>
<body>
     <div class="toast-container" id="toastContainer"></div>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>CSPC Admin</h3>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="<?= base_url('/admin') ?>" class="menu-item"><i>📊</i> Dashboard</a></li>
                <li><a href="<?= base_url('/admin/users') ?>" class="menu-item"><i>👥</i> Users</a></li>

                <!-- Dropdown for Booking -->
                <li class="dropdown">
                    <a href="#" class="menu-item dropdown-toggle" onclick="toggleDropdown(event)">
                        <i>🏢</i> Booking <span class="arrow">▾</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= base_url('/admin/external') ?>" class="submenu-item">🌐 External</a></li>
                        <li><a href="<?= base_url('/admin/student') ?>" class="submenu-item active">🏛️ Internal</a></li>
                    </ul>
                </li>

                <li><a href="<?= base_url('/admin/events') ?>" class="menu-item"><i>📅</i> Events</a></li>
                <li><a href="<?= base_url('/admin/equipment') ?>" class="menu-item"><i>🔧</i> Equipment</a></li>
                <li><a href="<?= base_url('/admin/plans') ?>" class="menu-item"><i>📋</i> Plans</a></li>

                <div class="sidebar-divider"></div>

                <li><a href="<?= base_url('admin/booking-management') ?>" class="menu-item"><i>📝</i> Bookings</a></li>
                <li><a href="<?= base_url('/admin/attendance') ?>" class="menu-item"><i>📋</i> Attendance</a></li>
                <li><a href="<?= base_url('/admin/file-templates') ?>" class="menu-item"><i>📄</i> File Templates</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr(session('full_name'), 0, 2)) ?>
                </div>
                <div class="user-details">
                    <?= session('full_name'); ?>
                    <div class="role">Administrator</div>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>" class="logout-btn" title="Logout">🚪</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        
        <div class="booking-page">
            <div class="page-title">
                <h2>Facility Booking Management</h2>
                <p>Manage and create new facility bookings for CSPC facilities</p>
            </div>

            <!-- Facilities Grid -->
            <div class="facilities-grid">
                <!-- University Auditorium -->
                <div class="facility-card" onclick="openStudentBookingModal('auditorium', 1)">
                    <div class="facility-image">🎭</div>
                    <div class="facility-info">
                        <h3 class="facility-title">University Auditorium</h3>
                        <p class="facility-description">Large capacity venue perfect for concerts, graduations, and major events with professional sound and lighting systems.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Air Conditioned</span>
                            <span class="feature-tag">Sound System</span>
                            <span class="feature-tag">Projector</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Gymnasium -->
                <div class="facility-card" onclick="openStudentBookingModal('gymnasium', 2)">
                    <div class="facility-image">🏀</div>
                    <div class="facility-info">
                        <h3 class="facility-title">University Gymnasium</h3>
                        <p class="facility-description">Spacious indoor sports facility suitable for sports events, large gatherings, and exhibitions.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Heavy Duty Fans</span>
                            <span class="feature-tag">Sound System</span>
                            <span class="feature-tag">Sports Equipment</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Function Hall -->
                <div class="facility-card" onclick="openStudentBookingModal('function-hall', 4)">
                    <div class="facility-image">🏛️</div>
                    <div class="facility-info">
                        <h3 class="facility-title">Function Hall (ACAD Bldg.)</h3>
                        <p class="facility-description">Versatile event space for meetings, conferences, and medium-sized gatherings.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Air Conditioned</span>
                            <span class="feature-tag">Tables & Chairs</span>
                            <span class="feature-tag">Sound System</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Pearl Mini Restaurant -->
                <div class="facility-card" onclick="openStudentBookingModal('pearl-restaurant', 5)">
                    <div class="facility-image">🍽️</div>
                    <div class="facility-info">
                        <h3 class="facility-title">Pearl Mini Restaurant</h3>
                        <p class="facility-description">Cozy dining venue perfect for small gatherings, parties, and intimate events.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Air Conditioned</span>
                            <span class="feature-tag">Tables & Chairs</span>
                            <span class="feature-tag">Kitchen Access</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Staff House Rooms -->
                <div class="facility-card" onclick="openStudentBookingModal('staff-house', 6)">
                    <div class="facility-image">🏠</div>
                    <div class="facility-info">
                        <h3 class="facility-title">Staff House Rooms</h3>
                        <p class="facility-description">Comfortable accommodation rooms for visiting faculty, guests, and special events.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Air Conditioned</span>
                            <span class="feature-tag">Private Bathroom</span>
                            <span class="feature-tag">Wi-Fi</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Classrooms -->
                <div class="facility-card" onclick="openStudentBookingModal('classrooms', 7)">
                    <div class="facility-image">📖</div>
                    <div class="facility-info">
                        <h3 class="facility-title">Classrooms</h3>
                        <p class="facility-description">Well-equipped classrooms suitable for training sessions, workshops, and academic events.</p>
                        <div class="facility-features">
                            <span class="feature-tag">Air Conditioned</span>
                            <span class="feature-tag">Projector</span>
                            <span class="feature-tag">Whiteboard</span>
                        </div>
                        <div class="facility-price">
                            <span class="price-range">Free (Internal)</span>
                            <button class="book-btn">Book Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Booking Modal -->
    <div id="studentBookingModal" class="modal student-booking-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Book Facility</h2>
                <span class="close" onclick="closeStudentModal()">&times;</span>
            </div>
            <div class="modal-body">
                <!-- Basic Information Section -->
                <div class="plan-section">
                    <h3 class="section-title">📝 Event Information</h3>
                    <form id="studentBookingForm">
    <div class="form-group">
        <label class="form-label">Your Full Name *</label>
        <input type="text" class="form-control" id="clientName" required>
    </div>
    
    <div class="form-group">
        <label class="form-label">Your Email Address *</label>
        <input type="email" class="form-control" id="clientEmail" required>
    </div>
    
    <div class="form-group">
        <label class="form-label">Organization/Group Name *</label>
        <input type="text" class="form-control" id="organization" required>
    </div>
                        
                        <div class="form-group">
                            <label class="form-label">Contact Number *</label>
                            <input type="tel" class="form-control" id="contactNumber" required>
                        </div>
                        
<div class="form-group">
    <label class="form-label">Address</label>
    <textarea class="form-control textarea" id="address" rows="2" 
              placeholder="Example: San Nicolas, Iriga City, Camarines Sur"></textarea>
    <small style="color: var(--gray); font-size: 12px; margin-top: 5px; display: block;">
        Optional, but if provided, must be at least 10 characters (include street, city, province)
    </small>
</div>
                        
                        <div class="form-group">
                            <label class="form-label">Event Date *</label>
                            <input type="date" class="form-control" id="eventDate" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Event Time *</label>
                            <input type="time" class="form-control" id="eventTime" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Duration (hours) *</label>
                            <input type="number" class="form-control" id="duration" min="1" max="12" value="4" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Expected Attendees</label>
                            <input type="number" class="form-control" id="attendees" min="1">
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Event Title/Purpose *</label>
                            <input type="text" class="form-control" id="eventTitle" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Special Requirements</label>
                            <textarea class="form-control textarea" id="specialRequirements"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Equipment Section -->
                <div class="plan-section">
                    <h3 class="section-title">🔧 Equipment Needed</h3>
                    <div class="equipment-grid" id="studentEquipmentGrid">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="plan-section upload-section">
                    <h3 class="section-title">📎 Required Documents</h3>
                    <p style="color: var(--gray); font-size: 14px; margin-bottom: 20px;">
                        Please upload the following documents (PDF, JPG, PNG - Max 10MB each)
                    </p>

                    <!-- Permission Document -->
                    <div class="upload-item" id="upload-permission">
                        <div class="upload-header">
                            <div class="upload-title">📄 Approved Permission to Conduct</div>
                            <span class="upload-status">Not uploaded</span>
                        </div>
                        <p style="font-size: 13px; color: var(--gray); margin-bottom: 10px;">
                            Official permission letter from your organization's adviser
                        </p>
                        <input type="file" id="file-permission" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'permission')" style="margin-bottom: 5px;">
                        <div class="file-name-display" id="filename-permission" style="font-size: 12px; color: var(--gray); font-style: italic;"></div>
                    </div>

                    <!-- Request Letter -->
                    <div class="upload-item" id="upload-request">
                        <div class="upload-header">
                            <div class="upload-title">📝 Letter Request for Venue</div>
                            <span class="upload-status">Not uploaded</span>
                        </div>
                        <p style="font-size: 13px; color: var(--gray); margin-bottom: 10px;">
                            Formal letter requesting the use of the facility
                        </p>
                        <input type="file" id="file-request" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'request')" style="margin-bottom: 5px;">
                        <div class="file-name-display" id="filename-request" style="font-size: 12px; color: var(--gray); font-style: italic;"></div>
                    </div>

                    <!-- Approval Letter -->
                    <div class="upload-item" id="upload-approval">
                        <div class="upload-header">
                            <div class="upload-title">✅ Approval Letter of the Venue</div>
                            <span class="upload-status">Not uploaded</span>
                        </div>
                        <p style="font-size: 13px; color: var(--gray); margin-bottom: 10px;">
                            Pre-approval or recommendation letter from authorized personnel
                        </p>
                        <input type="file" id="file-approval" class="form-control" accept=".pdf,.jpg,.jpeg,.png" onchange="handleStudentFileSelect(this, 'approval')" style="margin-bottom: 5px;">
                        <div class="file-name-display" id="filename-approval" style="font-size: 12px; color: var(--gray); font-style: italic;"></div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeStudentModal()">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitStudentBooking()" id="submitStudentBtn" disabled>
                    Submit Booking
                </button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('js/admin/student.js') ?>"></script>
</body>
</html>