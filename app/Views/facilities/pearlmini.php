<?php
// Check if user is logged in
$session = session();
$isLoggedIn = $session->get('user_id') !== null;
$userRole = $session->get('role');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pearl Mini Restaurant - CSPC Digital Booking System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= base_url('css/facilities/pearlmini.css'); ?>">
    <style>
      /* Toast Notification Styles */
      .toast-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease-out;
        font-size: 14px;
        font-weight: 500;
        min-width: 300px;
      }

      .toast-content {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
      }

      .toast-icon {
        font-size: 18px;
        flex-shrink: 0;
      }

      .toast-message {
        color: inherit;
      }

      .toast-close {
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        margin-left: 12px;
        opacity: 0.7;
        transition: opacity 0.2s;
      }

      .toast-close:hover {
        opacity: 1;
      }

      .toast-info {
        background: #e0f2fe;
        border-left: 4px solid #0284c7;
        color: #0c4a6e;
      }

      .toast-success {
        background: #dcfce7;
        border-left: 4px solid #16a34a;
        color: #15803d;
      }

      .toast-warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        color: #92400e;
      }

      .toast-error {
        background: #fee2e2;
        border-left: 4px solid #dc2626;
        color: #7f1d1d;
      }

      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }

      @keyframes slideOut {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(400px);
          opacity: 0;
        }
      }

      /* Charges section styling */
      .charges-section-group {
        background: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        border-left: 4px solid #1e3c72;
      }

      .charge-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(30, 60, 114, 0.1);
      }

      .charge-item:last-child {
        border-bottom: none;
      }

      .charge-description {
        color: #1e293b;
        font-weight: 500;
      }

      .charge-price {
        color: #1e3c72;
        font-weight: 700;
        font-size: 1.1rem;
      }

      .equipment-section {
        background: #f1f5f9;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
      }

      .equipment-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-weight: 700;
        color: #1e3c72;
      }

      .equipment-list {
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .equipment-item-li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        color: #1e293b;
      }

      .equipment-quantity {
        background: rgba(30, 60, 114, 0.1);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #1e3c72;
        font-weight: 600;
      }
    </style>
  </head>
  <body>
<nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
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
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/facilities') ?>">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= site_url('/event') ?>">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/contact') ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <?php if ($isLoggedIn): ?>
                            <button class="nav-link dashboard-btn btn btn-success px-3 py-2" onclick="window.location.href='<?= site_url('/dashboard') ?>'">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </button>
                        <?php else: ?>
                            <button class="nav-link login-btn btn btn-primary px-3 py-2" onclick="window.location.href='<?= site_url('/login') ?>'">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <div class="breadcrumb-nav">
            <a href="/">Home</a>
            <span class="separator">•</span>
            <a href="/facilities">Facilities</a>
            <span class="separator">•</span>
            <span>Pearl Mini Restaurant</span>
          </div>
          <h1>Pearl Mini Restaurant</h1>
          <p>
            An elegant dining venue perfect for intimate gatherings, corporate meetings, and special occasions. 
            Experience refined dining with complete comfort in our air-conditioned space with professional audio-visual support.
          </p>
        </div>
      </div>
    </section>

    <section class="facility-details-section">
      <div class="container">
        <div class="facility-hero">
          <div class="facility-icon-large">
            <i class="fas fa-utensils"></i>
          </div>
          <h2>Premium Dining & Event Space</h2>
          <p>
            Pearl Mini Restaurant offers a sophisticated atmosphere for your dining and event needs. 
            With modern amenities and professional service, create memorable experiences for your guests 
            in our beautifully appointed venue.
          </p>
        </div>

        <!-- Packages Section -->
        <div class="packages-section">
          <h3>Venue Rental Packages</h3>
          <div class="row g-4 justify-content-center" id="packagesContainer">
            <!-- Packages will be loaded dynamically -->
          </div>
        </div>

        <!-- Additional Charges Section -->
        <div class="charges-section">
          <div class="container">
            <h3 class="text-center mb-5" style="color: #1e293b;">Additional Services, Equipment & Amenities</h3>
            <div class="row justify-content-center">
              <div class="col-lg-10">
                <div id="chargesContainer">
                  <!-- Charges, equipment, and amenities will be loaded dynamically -->
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Section -->
        <div class="gallery-section">
          <h3 class="text-center mb-4" style="color: #1e293b;">Restaurant Gallery</h3>
          <div class="gallery-grid">
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-utensils"></i>
              </div>
              <span>Main Dining Area</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-table"></i>
              </div>
              <span>Table Settings</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-snowflake"></i>
              </div>
              <span>Air Conditioning</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-volume-up"></i>
              </div>
              <span>Sound System</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-car"></i>
              </div>
              <span>Parking Area</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-camera"></i>
              </div>
              <span>Photo Areas</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="footer-section">
              <div class="footer-logo">
                <div class="cspc-logo-nav">
                  <i class="fas fa-graduation-cap"></i>
                </div>
                CSPC Sphere
              </div>
              <p style="color: #94a3b8; line-height: 1.6;">
                Your premier destination for world-class facilities and exceptional event experiences. 
                Book with confidence at Camarines Sur Polytechnic Colleges.
              </p>
              <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-6 mb-4">
            <div class="footer-section">
              <h5>Quick Links</h5>
              <a href="/">Home</a>
              <a href="/facilities">Facilities</a>
              <a href="/event">Events</a>
              <a href="/about">About Us</a>
              <a href="/contact">Contact</a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="footer-section">
              <h5>Facilities</h5>
              <a href="/gymnasium">University Gymnasium</a>
              <a href="/auditorium">Main Auditorium</a>
              <a href="/pearl-restaurant">Pearl Mini Restaurant</a>
              <a href="/conference">Conference Rooms</a>
              <a href="/outdoor">Outdoor Venues</a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="footer-section">
              <h5>Contact Info</h5>
              <a href="tel:+63123456789">
                <i class="fas fa-phone" style="margin-right: 8px;"></i>
                +63 123 456 7890
              </a>
              <a href="mailto:info@cspc.edu.ph">
                <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                info@cspc.edu.ph
              </a>
              <a href="#">
                <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>
                Nabua, Camarines Sur, Philippines
              </a>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2024 CSPC Sphere - Camarines Sur Polytechnic Colleges. All rights reserved.</p>
        </div>
      </div>
    </footer>

<!-- Login Required Modal -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; border: none;">
      <div class="modal-header" style="background: linear-gradient(45deg, #1e3c72, #2a5298); color: white; border-radius: 20px 20px 0 0;">
        <h5 class="modal-title">
          <i class="fas fa-sign-in-alt me-2"></i>
          Login Required
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 40px;">
        <div class="alert alert-info" style="border-radius: 12px; border: none; background: rgba(30, 60, 114, 0.1);">
          <i class="fas fa-info-circle me-2"></i>
          Please log in to complete your booking.
        </div>
        <div class="selected-package mb-4">
          <h6 style="color: #1e3c72; font-weight: 700;">Selected Package:</h6>
          <p id="loginRequiredPackage" style="font-size: 1.1rem; color: #1e293b; font-weight: 600;"></p>
        </div>
        <div class="booking-info">
          <h6 style="color: #1e3c72; font-weight: 700; margin-bottom: 20px;">What happens next?</h6>
          <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 15px; display: flex; align-items: center;">
              <i class="fas fa-check-circle me-3" style="color: #22c55e;"></i>
              <span style="color: black;">Log in to your account or create a new one</span>
            </li>
            <li style="margin-bottom: 15px; display: flex; align-items: center;">
              <i class="fas fa-user me-3" style="color: #22c55e;"></i>
              <span style="color: black;">Your details will be automatically filled</span>
            </li>
            <li style="margin-bottom: 15px; display: flex; align-items: center;">
              <i class="fas fa-calendar-alt me-3" style="color: #22c55e;"></i>
              <span style="color: black;">Select your preferred date and time</span>
            </li>
            <li style="margin-bottom: 15px; display: flex; align-items: center;">
              <i class="fas fa-check me-3" style="color: #22c55e;"></i>
              <span style="color: black;">Submit your booking request</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="modal-footer" style="padding: 20px 40px; border: none;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 12px; padding: 12px 24px;">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" onclick="redirectToLogin()" style="background: linear-gradient(45deg, #1e3c72, #2a5298); border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600;">
          <i class="fas fa-sign-in-alt me-2"></i>
          Proceed to Login
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalLabel">
          <i class="fas fa-calendar-check me-2"></i>
          Book Pearl Mini Restaurant
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="selected-package mb-4 p-3" style="background: rgba(30, 60, 114, 0.05); border-radius: 12px;">
          <h6 style="color: #1e3c72; font-weight: 700;">Selected Package:</h6>
          <p id="selectedPackage" style="font-size: 1.1rem; color: #1e293b; font-weight: 600; margin: 0;"></p>
        </div>

        <form id="bookingForm">

          <hr class="my-4">

          <!-- Booking Information -->
          <h6 class="mb-3" style="color: #1e3c72; font-weight: 700;">
            <i class="fas fa-info-circle me-2"></i>
            Booking Details
          </h6>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="organization" class="form-label" style="color: #1e293b; font-weight: 600;">
                <i class="fas fa-building me-1"></i>
                Organization/Company
              </label>
              <input type="text" class="form-control" id="organization">
            </div>
            <div class="col-md-6 mb-3">
              <label for="attendees" class="form-label" style="color: #1e293b; font-weight: 600;">
                <i class="fas fa-users me-1"></i>
                Expected Attendees
              </label>
              <input type="number" class="form-control" id="attendees" min="1">
            </div>
          </div>

          <div class="mb-3">
            <label for="address" class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-map-marker-alt me-1"></i>
              Complete Address *
            </label>
            <textarea class="form-control" id="address" rows="2" placeholder="Street, Barangay, City, Province" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="eventDate" class="form-label" style="color: #1e293b; font-weight: 600;">
                <i class="fas fa-calendar me-1"></i>
                Event Date * <span class="text-danger">Required first</span>
              </label>
              <input type="date" class="form-control" id="eventDate" required onchange="handleDateSelection()">
            </div>
            <div class="col-md-6 mb-3">
              <label for="eventTime" class="form-label" style="color: #1e293b; font-weight: 600;">
                <i class="fas fa-clock me-1"></i>
                Start Time *
              </label>
              <input type="time" class="form-control" id="eventTime" required>
            </div>
          </div>

          <hr class="my-4">

          <!-- Additional Services -->
          <div class="mb-4">
            <label class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-plus-circle me-1"></i>
              Additional Services
            </label>
            <div class="row" id="addonsContainer">
              <!-- Dynamically populated -->
            </div>
          </div>

          <!-- Equipment (Hidden until date is selected) -->
          <div class="mb-4" id="equipmentSection" style="display: none;">
            <label class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-tools me-1"></i>
              Equipment
            </label>
            <div class="row" id="equipmentContainer">
              <!-- Dynamically populated -->
            </div>
          </div>

          <!-- Equipment Date Placeholder (shown until date is selected) -->
          <div class="mb-4" id="equipmentPlaceholder">
            <div class="alert alert-info" style="border-radius: 12px; border: none; background: rgba(59, 130, 246, 0.1); padding: 15px;">
              <i class="fas fa-calendar-check me-2" style="color: #3b82f6;"></i>
              <span style="color: #1e40af;"><strong>Please select an event date first</strong> to view available equipment and quantities for that date.</span>
            </div>
          </div>

          <!-- Additional Hours -->
          <div class="mb-4">
            <label for="additionalHours" class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-clock me-1"></i>
              Additional Hours (<span id="additionalHoursRateLabel">₱0</span>/hour)
            </label>
            <input type="number" class="form-control" id="additionalHours" min="0" max="12" value="0" onchange="updateCostSummary()">
            <small class="text-muted" style="color: #64748b !important;">Add extra hours beyond your selected plan duration</small>
          </div>

          <div class="mb-3">
            <label for="eventTitle" class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-heading me-1"></i>
              Event Title/Purpose *
            </label>
            <input type="text" class="form-control" id="eventTitle" required>
          </div>

          <div class="mb-3">
            <label for="specialRequirements" class="form-label" style="color: #1e293b; font-weight: 600;">
              <i class="fas fa-comment me-1"></i>
              Special Requirements/Notes
            </label>
            <textarea class="form-control" id="specialRequirements" rows="3" placeholder="Please specify any special requirements..."></textarea>
          </div>

          <!-- Booking Summary -->
          <div class="booking-summary" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <h6 class="mb-3" style="color: #1e3c72; font-weight: 700;">
              <i class="fas fa-receipt me-2"></i>
              Booking Summary
            </h6>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Selected Package:</span>
              <span id="summaryPackage" style="color: #1e293b; font-weight: 600;">-</span>
            </div>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Base Price:</span>
              <span id="summaryBasePrice" style="color: #1e293b; font-weight: 600;">₱0</span>
            </div>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Additional Services:</span>
              <span id="summaryAddons" style="color: #1e293b; font-weight: 600;">₱0</span>
            </div>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Equipment:</span>
              <span id="summaryEquipment" style="color: #1e293b; font-weight: 600;">₱0</span>
            </div>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Additional Hours:</span>
              <span id="summaryAdditionalHours" style="color: #1e293b; font-weight: 600;">₱0</span>
            </div>
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
              <span>Maintenance Fee:</span>
              <span id="summaryMaintenance" style="color: #1e293b; font-weight: 600;">₱2,000</span>
            </div>
            <hr style="border-color: #cbd5e1; margin: 12px 0;">
            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 700; font-size: 1.2rem;">
              <span>Total Amount:</span>
              <span id="summaryTotal" style="color: #1e3c72; font-weight: 700;">₱2,000</span>
            </div>
          </div>

          <div class="alert alert-warning mt-3" role="alert" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b; border-left: 4px solid #f59e0b; color: #78350f; border-radius: 8px;">
            <h6 style="color: #92400e; font-weight: 700; margin-bottom: 10px;">
              <i class="fas fa-exclamation-triangle me-2" style="color: #f59e0b;"></i>
              Important Notice
            </h6>
            <p style="margin-bottom: 10px; font-size: 14px;">
              <i class="fas fa-building me-2" style="color: #f59e0b;"></i>
              After submitting this booking, you must visit the office within <strong style="color: #92400e;">7 days</strong> to:
            </p>
            <ul style="margin-left: 25px; margin-bottom: 10px; font-size: 14px;">
              <li>Sign the booking agreement</li>
              <li>Pay the required amount</li>
            </ul>
            <p style="margin-bottom: 0; font-weight: 600; color: #dc2626; font-size: 14px;">
              <i class="fas fa-times-circle me-2"></i>
              Failure to comply will result in automatic cancellation of your booking.
            </p>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>
          Cancel
        </button>
        <button type="button" class="btn btn-primary" onclick="submitBooking()">
          <i class="fas fa-check me-1"></i>
          Submit Booking Request
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(45deg, #22c55e, #16a34a); color: white;">
        <h5 class="modal-title" id="successModalLabel">
          <i class="fas fa-check-circle me-2"></i>
          Booking Request Submitted Successfully!
        </h5>
      </div>
      <div class="modal-body text-center">
        <div style="font-size: 4rem; color: #22c55e; margin-bottom: 20px;">
          <i class="fas fa-check-circle"></i>
        </div>
        <h4 style="color: #1e293b; margin-bottom: 15px;">Thank You!</h4>
        <p style="color: #64748b; margin-bottom: 20px;">
          Your booking request has been submitted successfully. Our team will review your request and contact you within 24 hours to confirm availability and payment details.
        </p>
        <div style="background: rgba(34, 197, 94, 0.1); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
          <p style="margin: 0; color: #1e293b; font-weight: 600;">
            <i class="fas fa-envelope me-2" style="color: #22c55e;"></i>
            A confirmation email has been sent to your registered email address.
          </p>
        </div>
        <p style="color: #64748b; font-size: 0.9rem;">
          Reference Number: <strong id="referenceNumber" style="color: #1e3c72;"></strong>
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-primary" onclick="closeSuccessModal()">
          <i class="fas fa-home me-1"></i>
          Continue Browsing
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
// Global variables
let facilityData = {};
let addonsData = [];
let equipmentData = [];
let selectedPlan = null;
let selectedAddons = [];
let selectedEquipment = {};
let currentFacility = 'pearl-restaurant';
const MAINTENANCE_FEE = 2000;

// Check if user is logged in
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const userRole = '<?php echo $userRole ?? ''; ?>';
const canBook = isLoggedIn && (userRole === 'user' || userRole === 'faculty');
const userEmail = '<?php echo $userEmail ?? ''; ?>';
const userContact = '<?php echo $userContact ?? ''; ?>';

// Toast notification system
function showToast(message, type = 'info') {
  const toastContainer = document.getElementById('toastContainer') || createToastContainer();
  
  const toastId = 'toast-' + Date.now();
  const toastHTML = `
    <div id="${toastId}" class="toast-notification toast-${type}">
      <div class="toast-content">
        <span class="toast-icon">
          ${type === 'error' ? '❌' : type === 'success' ? '✅' : type === 'warning' ? '⚠️' : 'ℹ️'}
        </span>
        <span class="toast-message">${message}</span>
      </div>
      <div class="toast-close" onclick="closeToast('${toastId}')">×</div>
    </div>
  `;
  
  toastContainer.insertAdjacentHTML('beforeend', toastHTML);
  
  // Auto remove after 5 seconds
  setTimeout(() => closeToast(toastId), 5000);
}

function createToastContainer() {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 400px;
    `;
    document.body.appendChild(container);
  }
  return container;
}

function closeToast(toastId) {
  const toast = document.getElementById(toastId);
  if (toast) {
    toast.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  }
}

// Load data on page load
document.addEventListener("DOMContentLoaded", function () {
  loadFacilityData();
  loadAddonsData();
  loadEquipmentData();

  // Set minimum date to today
  const today = new Date().toISOString().split("T")[0];
  const eventDateInput = document.getElementById("eventDate");
  if (eventDateInput) {
    eventDateInput.min = today;
  }
});

// Load facility data from database
async function loadFacilityData() {
  try {
    const response = await fetch("<?= base_url('api/facilities/data/pearl-restaurant') ?>");
    const data = await response.json();
    facilityData = data.facility || data;
    console.log("Facility data loaded:", facilityData);
    renderPackages();
  } catch (error) {
    console.error("Error loading facility data:", error);
  }
}

// Render packages dynamically
function renderPackages() {
  const container = document.getElementById('packagesContainer');
  if (!container) return;
  
  if (!facilityData.plans || facilityData.plans.length === 0) {
    container.innerHTML = '<div class="col-12"><p class="text-center">No packages available</p></div>';
    return;
  }

  let html = '';
  facilityData.plans.forEach((plan, index) => {
    const features = plan.features || [];
    const includedEquipment = plan.included_equipment || [];
    const isMostPopular = index === 0;
    
    let featuresList = features.map(feature => `<li><i class="fas fa-check"></i> ${feature}</li>`).join('');
    
    let equipmentHtml = '';
    if (includedEquipment && includedEquipment.length > 0) {
      let equipmentItems = includedEquipment.map(eq => 
        `<li class="equipment-item-li">
          <i class="fas fa-check"></i>
          <span>${eq.name}</span>
          <span class="equipment-quantity">${eq.quantity_included} ${eq.unit}</span>
        </li>`
      ).join('');
      
      equipmentHtml = `
        <div class="equipment-section">
          <div class="equipment-header">
            <i class="fas fa-tools" style="color: #1e3c72;"></i>
            <span>Equipment Included</span>
          </div>
          <ul class="equipment-list">
            ${equipmentItems}
          </ul>
        </div>
      `;
    }
    
    const packageCol = `
      <div class="col-lg-6 col-md-6">
        <div class="package-card ${isMostPopular ? 'featured-package' : ''}">
          ${isMostPopular ? '<div class="featured-badge">✨ Most Popular</div>' : ''}
          <div class="package-header">
            <div style="margin-bottom: 15px;">
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <i class="fas fa-utensils" style="font-size: 1.2rem; color: #1e3c72;"></i>
                <div class="package-name">${plan.name}</div>
              </div>
            </div>
            <div class="package-price">₱${parseFloat(plan.price).toLocaleString()}</div>
            <div class="package-duration">
              <i class="fas fa-clock me-1" style="color: #64748b;"></i>
              ${plan.duration}
            </div>
          </div>
          <div class="package-features">
            <h5><i class="fas fa-list-check me-2" style="color: #1e3c72;"></i>Includes:</h5>
            <ul class="feature-list">
              ${featuresList}
            </ul>
          </div>
          ${equipmentHtml}
          <button class="book-package-btn" onclick="openBookingModal('${plan.name}')">
            <i class="fas fa-calendar-check"></i> Book Now
          </button>
        </div>
      </div>
    `;
    
    html += packageCol;
  });
  
  container.innerHTML = html;
}

// Load addons data
async function loadAddonsData() {
  try {
    const response = await fetch("<?= base_url('api/addons') ?>");
    const data = await response.json();
    addonsData = data
      .filter((addon) => addon.addon_key !== "additional-hours")
      .map((addon) => ({
        id: addon.addon_key,
        name: addon.name,
        description: addon.description,
        price: parseFloat(addon.price),
      }));
    console.log("Addons data loaded:", addonsData);
    renderCharges();
  } catch (error) {
    console.error("Error loading addons data:", error);
  }
}

// Render charges - FIXED VERSION
function renderCharges() {
  const container = document.getElementById('chargesContainer');
  if (!container) return;
  
  let html = '';
  
  // Section 1: Additional Services
  if (addonsData && addonsData.length > 0) {
    html += '<div class="charges-section-group">';
    html += '<h5 style="color: #1e3c72; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid rgba(30, 60, 114, 0.1);"><i class="fas fa-plus-circle me-2"></i>Additional Services</h5>';
    
    addonsData.forEach((addon) => {
      html += `
        <div class="charge-item">
          <span class="charge-description">${addon.name}</span>
          <span class="charge-price">₱${addon.price.toLocaleString()}</span>
        </div>
      `;
    });
    
    html += '</div>';
  }
  
  // Section 2: Equipment Plans
  if (equipmentData && equipmentData.length > 0) {
    html += '<div class="charges-section-group" style="margin-top: 40px;">';
    html += '<h5 style="color: #1e3c72; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid rgba(30, 60, 114, 0.1);"><i class="fas fa-tools me-2"></i>Equipment Rental Packages</h5>';
    
    const categories = {};
    equipmentData.forEach(item => {
      if (!categories[item.category]) {
        categories[item.category] = [];
      }
      categories[item.category].push(item);
    });
    
    Object.entries(categories).forEach(([category, items]) => {
      html += `<div style="margin-bottom: 20px;">`;
      html += `<h6 style="color: #2a5298; font-weight: 600; text-transform: capitalize; margin-bottom: 12px;"><i class="fas fa-box me-2" style="color: #1e3c72;"></i>${category}</h6>`;
      
      items.forEach(equipment => {
        const availabilityText = equipment.available > 0 ? `<span style="color: #059669; font-size: 0.85rem;"><i class="fas fa-check-circle me-1"></i>Available</span>` : `<span style="color: #dc2626; font-size: 0.85rem;"><i class="fas fa-times-circle me-1"></i>Limited</span>`;
        
        html += `
          <div class="charge-item" style="margin-bottom: 12px;">
            <div style="flex: 1;">
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <span style="color: #1e293b; font-weight: 600;">${equipment.name}</span>
                ${availabilityText}
              </div>
              <span style="color: #94a3b8; font-size: 0.85rem;">Per ${equipment.unit}</span>
            </div>
            <span class="charge-price">₱${equipment.rate.toLocaleString()}</span>
          </div>
        `;
      });
      
      html += `</div>`;
    });
    
    html += '</div>';
  }
  
  // Section 3: Amenities
  if (facilityData && facilityData.amenities && facilityData.amenities.length > 0) {
    html += '<div class="charges-section-group" style="margin-top: 40px;">';
    html += '<h5 style="color: #1e3c72; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid rgba(30, 60, 114, 0.1);"><i class="fas fa-star me-2"></i>Amenities & Features</h5>';
    
    facilityData.amenities.forEach((amenity) => {
      html += `
        <div class="charge-item" style="margin-bottom: 12px;">
          <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
            <i class="fas fa-check-circle" style="color: #22c55e; font-size: 1rem;"></i>
            <span style="color: #1e293b; font-weight: 500;">${amenity}</span>
          </div>
        </div>
      `;
    });
    
    html += '</div>';
  }
  
  if (html === '') {
    container.innerHTML = '<p class="text-center">No additional services available</p>';
  } else {
    container.innerHTML = html;
  }
}

// Load equipment data
async function loadEquipmentData() {
  try {
    const response = await fetch("<?= base_url('api/bookings/equipment') ?>");
    const result = await response.json();

    console.log("Equipment API Debug:", result.success);

    if (!result.success) {
      throw new Error(result.message || "Failed to load equipment");
    }

    equipmentData = result.equipment
      .filter((equipment) => {
        const isRentable = equipment.is_rentable == 1 || equipment.is_rentable === true || equipment.is_rentable === "1";
        const hasRate = parseFloat(equipment.rate || 0) > 0;
        const isFurnitureOrLogistics = equipment.category === "furniture" || equipment.category === "logistics";
        return isFurnitureOrLogistics && isRentable && hasRate;
      })
      .map((equipment) => ({
        id: equipment.id.toString(),
        name: equipment.name,
        rate: parseFloat(equipment.rate || 0),
        unit: equipment.unit || "piece",
        available: parseInt(equipment.available || 0),
        category: equipment.category,
      }));

    console.log("Equipment loaded:", equipmentData.length);
  } catch (error) {
    console.error("Error loading equipment:", error);
    equipmentData = [];
  }
}

// Check equipment availability by date
async function checkEquipmentAvailabilityOnDate(eventDate) {
  try {
    const response = await fetch("<?= base_url('api/bookings/equipment-availability') ?>", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        event_date: eventDate,
        facility_id: facilityData.facility_id
      })
    });

    const result = await response.json();
    
    if (result.success && result.equipment) {
      equipmentData = result.equipment
        .filter((equipment) => {
          const isRentable = equipment.is_rentable == 1 || equipment.is_rentable === true || equipment.is_rentable === "1";
          const hasRate = parseFloat(equipment.rate || 0) > 0;
          const isFurnitureOrLogistics = equipment.category === "furniture" || equipment.category === "logistics";
          return isFurnitureOrLogistics && isRentable && hasRate;
        })
        .map((equipment) => ({
          id: equipment.id.toString(),
          name: equipment.name,
          rate: parseFloat(equipment.rate || 0),
          unit: equipment.unit || "piece",
          available: parseInt(equipment.available_on_date || 0),
          category: equipment.category,
        }));
      
      populateEquipment();
      updateCostSummary();
    }
  } catch (error) {
    console.error("Error checking equipment availability:", error);
  }
}

function openBookingModal(packageName) {
  if (!isLoggedIn) {
    showLoginRequiredModal(packageName);
    return;
  }
  
  if (!canBook) {
    showAccessDeniedModal(packageName);
    return;
  }

  selectedPlan = facilityData.plans ? facilityData.plans.find(p => p.name === packageName) : null;
  
  if (!selectedPlan) {
    alert('Unable to find plan details. Please try again.');
    return;
  }

  document.getElementById('selectedPackage').textContent = packageName;
  document.getElementById('additionalHours').value = 0;
  document.getElementById('bookingForm').reset();
  
  const additionalHoursRate = facilityData.additional_hours_rate || 500;
  document.getElementById('additionalHoursRateLabel').textContent = `₱${additionalHoursRate.toLocaleString()}`;
  
  selectedAddons = [];
  selectedEquipment = {};
  
  document.getElementById('equipmentSection').style.display = 'none';
  document.getElementById('equipmentPlaceholder').style.display = 'block';
  
  populateAddons();
  updateCostSummary();
  
  const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
  modal.show();
}

async function handleDateSelection() {
  const eventDate = document.getElementById('eventDate').value;
  const equipmentSection = document.getElementById('equipmentSection');
  const equipmentPlaceholder = document.getElementById('equipmentPlaceholder');
  
  if (eventDate) {
    try {
      const conflictCheck = await fetch("<?= base_url('api/bookings/checkDateConflict') ?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          facility_id: facilityData.facility_id,
          event_date: eventDate,
          event_time: document.getElementById('eventTime').value || '08:00',
          duration: selectedPlan?.duration?.match(/\d+/)?.[0] || 4
        })
      });

      const conflictResult = await conflictCheck.json();
      
      if (conflictResult.hasConflict) {
        showToast('⚠️ There is a pending or accepted booking on this date. Please select another date.', 'warning');
        equipmentSection.style.display = 'none';
        equipmentPlaceholder.style.display = 'block';
        return;
      }
    } catch (error) {
      console.error('Error checking date conflict:', error);
    }

    equipmentSection.style.display = 'block';
    equipmentPlaceholder.style.display = 'none';
    selectedEquipment = {};
    await checkEquipmentAvailabilityOnDate(eventDate);
  } else {
    equipmentSection.style.display = 'none';
    equipmentPlaceholder.style.display = 'block';
  }
}

function showLoginRequiredModal(packageName) {
  document.getElementById('loginRequiredPackage').textContent = packageName;
  const modal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
  modal.show();
}

function redirectToLogin() {
  window.location.href = '<?= site_url('/login') ?>';
}

function showAccessDeniedModal() {
  alert('Only regular users can make bookings. Admin accounts cannot create bookings.');
}

function populateAddons() {
  const addonsGrid = document.getElementById("addonsContainer");
  if (!addonsGrid) return;
  
  addonsGrid.innerHTML = "";

  if (!addonsData || addonsData.length === 0) {
    addonsGrid.innerHTML = '<p class="no-data">No add-ons available.</p>';
    return;
  }

  addonsData.forEach((addon) => {
    const addonCard = document.createElement("div");
    addonCard.className = "col-md-6";
    addonCard.innerHTML = `
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="addon-${addon.id}" onchange="toggleAddon('${addon.id}')">
        <label class="form-check-label" for="addon-${addon.id}" style="color: #000;">
          ${addon.name} - ₱${addon.price.toLocaleString()}
        </label>
      </div>
    `;
    addonsGrid.appendChild(addonCard);
  });
}

function populateEquipment() {
  const equipmentGrid = document.getElementById("equipmentContainer");
  if (!equipmentGrid) return;
  
  equipmentGrid.innerHTML = "";

  if (!equipmentData || equipmentData.length === 0) {
    equipmentGrid.innerHTML = `
      <div class="col-12">
        <div class="alert alert-info" style="border-radius: 12px; border: none; background: rgba(59, 130, 246, 0.1);">
          <i class="fas fa-info-circle me-2" style="color: #3b82f6;"></i>
          <span style="color: #1e40af;">No additional rental equipment available at this time.</span>
        </div>
      </div>
    `;
    return;
  }

  equipmentData.forEach((equipment) => {
    const equipmentCard = document.createElement("div");
    equipmentCard.className = "col-md-6";
    const isAvailable = equipment.available > 0;
    const stockInfo = isAvailable ? `Available: ${equipment.available}` : "Out of Stock";

    equipmentCard.innerHTML = `
      <div class="equipment-item mb-3">
        <label class="form-label" style="color: #000;">
          ${equipment.name}
          <span class="text-primary" style="font-weight: 600;">(₱${equipment.rate.toLocaleString()} / ${equipment.unit})</span>
        </label>
        ${!isAvailable
          ? `<input type="number" class="form-control" value="0" disabled style="border-radius: 8px; border: 2px solid #e2e8f0;"><small class="text-danger">${stockInfo}</small>`
          : `<input type="number" class="form-control quantity-input" id="qty-${equipment.id}" min="0" max="${equipment.available}" value="0" onchange="updateEquipment('${equipment.id}')" style="border-radius: 8px; border: 2px solid #e2e8f0;"><small class="text-muted">${stockInfo}</small>`
        }
      </div>
    `;
    equipmentGrid.appendChild(equipmentCard);
  });
}

function updateEquipment(equipmentId) {
  const quantityInput = document.getElementById(`qty-${equipmentId}`);
  const quantity = parseInt(quantityInput.value) || 0;
  const equipment = equipmentData.find((e) => e.id === equipmentId);

  if (!equipment) return;

  if (quantity > equipment.available) {
    alert(`Only ${equipment.available} units available for ${equipment.name}`);
    quantityInput.value = equipment.available;
    selectedEquipment[equipmentId] = equipment.available;
  } else if (quantity > 0) {
    selectedEquipment[equipmentId] = quantity;
  } else {
    delete selectedEquipment[equipmentId];
  }

  updateCostSummary();
}

function toggleAddon(addonId) {
  const checkbox = document.getElementById(`addon-${addonId}`);
  if (checkbox.checked) {
    selectedAddons.push(addonId);
  } else {
    selectedAddons = selectedAddons.filter((id) => id !== addonId);
  }
  updateCostSummary();
}

function updateCostSummary() {
  const summaryPackage = document.getElementById("summaryPackage");
  const summaryBasePrice = document.getElementById("summaryBasePrice");
  const summaryAddons = document.getElementById("summaryAddons");
  const summaryEquipment = document.getElementById("summaryEquipment");
  const summaryAdditionalHours = document.getElementById("summaryAdditionalHours");
  const summaryMaintenance = document.getElementById("summaryMaintenance");
  const summaryTotal = document.getElementById("summaryTotal");

  let basePrice = 0;
  let packageName = '-';
  let addonsPrice = 0;
  let equipmentPrice = 0;
  let additionalHoursPrice = 0;

  if (selectedPlan) {
    basePrice = selectedPlan.price;
    packageName = selectedPlan.name;
    summaryBasePrice.textContent = `₱${basePrice.toLocaleString()}`;
    summaryPackage.textContent = packageName;
  } else {
    summaryBasePrice.textContent = "₱0";
    summaryPackage.textContent = "-";
  }

  const additionalHoursRate = facilityData.additional_hours_rate || 500;
  const additionalHours = parseInt(document.getElementById("additionalHours")?.value) || 0;
  if (additionalHours > 0) {
    additionalHoursPrice = additionalHours * additionalHoursRate;
  }
  summaryAdditionalHours.textContent = `₱${additionalHoursPrice.toLocaleString()}`;

  selectedAddons.forEach((addonId) => {
    const addon = addonsData.find((a) => a.id === addonId);
    if (addon) {
      addonsPrice += addon.price;
    }
  });
  summaryAddons.textContent = `₱${addonsPrice.toLocaleString()}`;

  Object.keys(selectedEquipment).forEach((equipmentId) => {
    const equipment = equipmentData.find((e) => e.id === equipmentId);
    const quantity = selectedEquipment[equipmentId];
    if (equipment && quantity > 0 && equipment.rate > 0) {
      equipmentPrice += equipment.rate * quantity;
    }
  });
  summaryEquipment.textContent = `₱${equipmentPrice.toLocaleString()}`;
  summaryMaintenance.textContent = `₱${MAINTENANCE_FEE.toLocaleString()}`;

  const total = basePrice + addonsPrice + equipmentPrice + additionalHoursPrice + MAINTENANCE_FEE;
  summaryTotal.textContent = `₱${total.toLocaleString()}`;
}

function calculateTotalDuration() {
  if (!selectedPlan) return 0;
  const durationMatch = selectedPlan.duration.match(/\d+/);
  const planDuration = durationMatch ? parseInt(durationMatch[0]) : 0;
  const additionalHours = parseInt(document.getElementById("additionalHours")?.value) || 0;
  return planDuration + additionalHours;
}

async function submitBooking() {
  const form = document.getElementById("bookingForm");
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  if (!selectedPlan) {
    alert("Please select a plan before proceeding.");
    return;
  }

  const formData = {
    facility_key: currentFacility,
    plan_id: selectedPlan.id,
    organization: document.getElementById("organization").value,
    address: document.getElementById("address").value,
    event_date: document.getElementById("eventDate").value,
    event_time: document.getElementById("eventTime").value,
    duration: calculateTotalDuration(),
    attendees: document.getElementById("attendees").value || null,
    event_title: document.getElementById("eventTitle").value,
    special_requirements: document.getElementById("specialRequirements").value,
    selected_addons: selectedAddons,
    selected_equipment: selectedEquipment,
    additional_hours: parseInt(document.getElementById("additionalHours")?.value) || 0,
    maintenance_fee: MAINTENANCE_FEE,
    total_cost: calculateTotalCost(),
  };

  try {
    const response = await fetch("<?= base_url('api/bookings') ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(formData),
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      document.getElementById('referenceNumber').textContent = 'BK' + String(result.booking_id).padStart(3, '0');
      bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
      new bootstrap.Modal(document.getElementById('successModal')).show();
      
      form.reset();
      selectedPlan = null;
      selectedAddons = [];
      selectedEquipment = {};
      updateCostSummary();
    } else {
      alert(result.message || "Failed to create booking");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error: " + error.message);
  }
}

function calculateTotalCost() {
  let basePrice = selectedPlan ? selectedPlan.price : 0;
  const additionalHoursRate = facilityData.additional_hours_rate || 500;
  const additionalHours = parseInt(document.getElementById("additionalHours")?.value) || 0;
  const additionalHoursPrice = additionalHours * additionalHoursRate;

  const addonsPrice = selectedAddons.reduce((sum, addonId) => {
    const addon = addonsData.find((a) => a.id === addonId);
    return sum + (addon ? addon.price : 0);
  }, 0);

  const equipmentPrice = Object.keys(selectedEquipment).reduce((sum, equipmentId) => {
    const equipment = equipmentData.find((e) => e.id === equipmentId);
    const quantity = selectedEquipment[equipmentId];
    if (equipment && quantity > 0) {
      return sum + equipment.rate * quantity;
    }
    return sum;
  }, 0);

  return basePrice + addonsPrice + equipmentPrice + additionalHoursPrice + MAINTENANCE_FEE;
}

function closeSuccessModal() {
  bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
  window.location.href = '<?= site_url('/dashboard') ?>';
}

const additionalHoursInput = document.getElementById('additionalHours');
if (additionalHoursInput) {
  additionalHoursInput.addEventListener('input', updateCostSummary);
}
</script>
  </body>
</html>