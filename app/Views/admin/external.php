<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC External Booking - Booking Management</title>
    <link rel="stylesheet" href="<?= base_url('css/booking.css') ?>">
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>
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
                <li><a href="<?= base_url('/admin/facilities-management') ?>" class="menu-item"><i>🏗️</i> Facilities</a></li>
            

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
        <!-- Header -->
 
        
        <!-- Booking Page Content -->
        <div class="booking-page">
            <div class="page-title">
                <h2>Facility Booking Management</h2>
                <p>Manage and create new facility bookings for CSPC facilities</p>
            </div>


            <!-- Facilities Grid -->
            <div class="facilities-grid" id="externalFacilitiesGrid">
                <!-- Will be populated dynamically by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Book Facility</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <!-- Plan Selection -->
                <div class="plan-section">
                    <h3 class="section-title">
                        <span>📋</span> Select Your Plan
                    </h3>
                    <div class="plans-grid" id="plansGrid">
                        <!-- Plans will be populated dynamically -->
                    </div>
                </div> 

                <div class="plan-section">
    <h3 class="section-title">
        <span>📝</span> Booking Information
    </h3>
    <form class="booking-form" id="bookingForm">
        <div class="form-group">
            <label class="form-label">Client Name *</label>
            <input type="text" class="form-control" id="clientName" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Contact Number *</label>
            <input type="tel" class="form-control" id="contactNumber" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" class="form-control" id="emailAddress" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Organization/Company</label>
            <input type="text" class="form-control" id="organization">
        </div>

        <div class="form-group full-width">
    <label class="form-label">Complete Address *</label>
    <textarea class="form-control textarea" id="address" rows="3" 
              placeholder="Street, Barangay, City, Province" required></textarea>
    <small style="color: #6c757d; font-size: 0.875rem;">Please provide your complete mailing address</small>
       </div>
        
        <div class="form-group">
            <label class="form-label">Event Date *</label>
            <input type="date" class="form-control" id="eventDate" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Event Time *</label>
            <input type="time" class="form-control" id="eventTime" required>
        </div>
        
        <!-- Duration field removed - now calculated automatically from plan + extended hours -->
        
        <div class="form-group">
            <label class="form-label">Expected Attendees</label>
            <input type="number" class="form-control" id="attendees" min="1">
        </div>
        
        <div class="form-group full-width">
            <label class="form-label">Event Title/Purpose *</label>
            <input type="text" class="form-control" id="eventTitle" required>
        </div>
        
        <div class="form-group full-width">
            <label class="form-label">Special Requirements/Notes</label>
            <textarea class="form-control textarea" id="specialRequirements" placeholder="Please specify any special requirements, setup instructions, or additional notes..."></textarea>
        </div>
    </form>
</div>

                <!-- Add-ons Section -->
                <div class="plan-section">
                    <h3 class="section-title">
                        <span>✨</span> Additional Services
                    </h3>
                    <div class="addons-grid" id="addonsGrid">
                        <!-- Add-ons will be populated dynamically -->
                    </div>
                </div>

                <!-- Equipment Section -->
                <div class="plan-section">
                    <h3 class="section-title">
                        <span>🔧</span> Equipment & Logistics
                    </h3>
                    <div class="equipment-grid" id="equipmentGrid">
                        <!-- Equipment cards will be populated dynamically -->
                        <div class="equipment-card">
                            <div class="equipment-info">
                                <h4 class="equipment-name">Sound System</h4>
                                <p class="equipment-description">Professional audio equipment for events</p>
                                <span class="equipment-price">₱500/day</span>
                            </div>
                            <div class="equipment-actions-card">
                                <input type="number" class="form-control qty-input" id="qty-sound" min="0" max="5" value="0" onchange="updateEquipment('sound')">
                                <label class="equipment-label">Quantity</label>
                            </div>
                        </div>
                        
                        <div class="equipment-card">
                            <div class="equipment-info">
                                <h4 class="equipment-name">Projector</h4>
                                <p class="equipment-description">HD projector for presentations</p>
                                <span class="equipment-price">₱300/day</span>
                            </div>
                            <div class="equipment-actions-card">
                                <input type="number" class="form-control qty-input" id="qty-projector" min="0" max="3" value="0" onchange="updateEquipment('projector')">
                                <label class="equipment-label">Quantity</label>
                            </div>
                        </div>
                        
                        <div class="equipment-card">
                            <div class="equipment-info">
                                <h4 class="equipment-name">Microphone</h4>
                                <p class="equipment-description">Wireless microphone system</p>
                                <span class="equipment-price">₱200/day</span>
                            </div>
                            <div class="equipment-actions-card">
                                <input type="number" class="form-control qty-input" id="qty-microphone" min="0" max="10" value="0" onchange="updateEquipment('microphone')">
                                <label class="equipment-label">Quantity</label>
                            </div>
                        </div>
                        
                        <div class="equipment-card">
                            <div class="equipment-info">
                                <h4 class="equipment-name">Tables & Chairs</h4>
                                <p class="equipment-description">Additional seating arrangements</p>
                                <span class="equipment-price">₱50/set</span>
                            </div>
                            <div class="equipment-actions-card">
                                <input type="number" class="form-control qty-input" id="qty-furniture" min="0" max="20" value="0" onchange="updateEquipment('furniture')">
                                <label class="equipment-label">Sets</label>
                            </div>
                        </div>
                        
                        <div class="equipment-card">
                            <div class="equipment-info">
                                <h4 class="equipment-name">Lighting Equipment</h4>
                                <p class="equipment-description">Professional stage lighting</p>
                                <span class="equipment-price">₱800/day</span>
                            </div>
                            <div class="equipment-actions-card">
                                <input type="number" class="form-control qty-input" id="qty-lighting" min="0" max="2" value="0" onchange="updateEquipment('lighting')">
                                <label class="equipment-label">Quantity</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extended Hours Section -->
                <div class="plan-section">
                    <h3 class="section-title">
                        <span>⏰</span> Extended Hours
                    </h3>
                    <div class="form-group">
                        <label class="form-label">Additional Hours (Rate: <span id="hourlyRateLabel">₱500</span>/hour)</label>
                        <input type="number" class="form-control" id="additionalHours" min="0" max="12" value="0" onchange="updateCostSummary()">
                        <small>Add extra hours beyond your selected plan duration</small>
                    </div>
                </div>

                <!-- Cost Summary -->
                <div class="cost-summary">
                    <h3 class="section-title">
                        <span>💰</span> Cost Summary
                    </h3>
                    <div id="costBreakdown">
                        <div class="cost-row">
                            <span>Base Plan:</span>
                            <span id="baseCost">₱0</span>
                        </div>
                        <div class="cost-row mandatory">
                            <span>Maintenance Fee (Required):</span>
                            <span id="maintenanceCost">₱0</span>
                        </div>
                        <div id="addonCosts"></div>
                        <div class="cost-row total">
                            <span>Total Amount:</span>
                            <span id="totalCost">₱0</span>
                        </div>
                    </div>
                </div>

  

            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBooking()">Create Booking</button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('js/admin/external-facilities.js') ?>"></script>
    <script src="<?= base_url('js/booking.js') ?>"></script>
</body>
</html>