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
    <title>Staff House Rooms - CSPC Digital Booking System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= base_url('css/facilities/gymnasium.css'); ?>">
  </head>
  <body>
    <!-- Navigation -->
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

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <div class="breadcrumb-nav">
            <a href="/">Home</a>
            <span class="separator">•</span>
            <a href="/facilities">Facilities</a>
            <span class="separator">•</span>
            <span>Staff House Rooms</span>
          </div>
          <h1>Staff House Rooms</h1>
          <p>
            Comfortable and affordable accommodation for CSPC staff and visitors. 
            Equipped with modern amenities including private shower, TV, internet, and air conditioning.
          </p>
        </div>
      </div>
    </section>

    <!-- Facility Details Section -->
    <section class="facility-details-section">
      <div class="container">
        <!-- Facility Overview -->
        <div class="facility-hero">
          <div class="facility-icon-large">
            <i class="fas fa-bed"></i>
          </div>
          <h2>Premium Staff Accommodation</h2>
          <p>
            Convenient and comfortable rooms perfect for staff residency and guest accommodation. 
            Our facility offers flexible booking options with daily and monthly rates to suit your needs.
          </p>
        </div>

        <!-- Packages Section -->
        <div class="packages-section">
          <h3>Room Rates & Booking Options</h3>
          <div class="row g-4">
            <!-- Daily Room -->
            <div class="col-lg-6 col-md-6">
              <div class="package-card">
                <div class="package-header">
                  <div class="package-name">Standard Room</div>
                  <div class="package-price">₱400</div>
                  <div class="package-duration">per person / night</div>
                </div>
                <div class="package-features">
                  <h5>Amenities:</h5>
                  <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Private Shower Room</li>
                    <li><i class="fas fa-check"></i> Telephone</li>
                    <li><i class="fas fa-check"></i> Cable TV</li>
                    <li><i class="fas fa-check"></i> Internet Access</li>
                    <li><i class="fas fa-check"></i> Air Conditioner</li>
                  </ul>
                </div>
                <button class="book-package-btn" onclick="openBookingModal('Standard Room - Daily')">
                  <i class="fas fa-calendar-check"></i> Book Now
                </button>
              </div>
            </div>

            <!-- Monthly Room (Staff Only) -->
            <div class="col-lg-6 col-md-6">
              <div class="package-card featured-package">
                <div class="featured-badge">Staff Only</div>
                <div class="package-header">
                  <div class="package-name">Monthly Accommodation</div>
                  <div class="package-price">₱1,500</div>
                  <div class="package-duration">per person / month</div>
                </div>
                <div class="package-features">
                  <h5>All Amenities Plus:</h5>
                  <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Private Shower Room</li>
                    <li><i class="fas fa-check"></i> Telephone</li>
                    <li><i class="fas fa-check"></i> Cable TV</li>
                    <li><i class="fas fa-check"></i> Internet Access</li>
                    <li><i class="fas fa-check"></i> Air Conditioner</li>
                    <li><i class="fas fa-check"></i> Flexible Check-in/Check-out</li>
                  </ul>
                </div>
                <button class="book-package-btn" onclick="openBookingModal('Monthly Accommodation - Staff Only')">
                  <i class="fas fa-calendar-check"></i> Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Charges Section -->
        <div class="charges-section">
          <div class="container">
            <h3 class="text-center mb-5" style="color: #1e293b;">Additional Services & Policies</h3>
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <div class="charges-card">
                  <div class="charge-item">
                    <span class="charge-description">Extra Bed (if applicable)</span>
                    <span class="charge-price">₱150</span>
                  </div>
                  <div class="charge-item">
                    <span class="charge-description">Early Check-in (before 2:00 PM)</span>
                    <span class="charge-price">₱100</span>
                  </div>
                  <div class="charge-item">
                    <span class="charge-description">Late Check-out (after 12:00 Noon)</span>
                    <span class="charge-price">₱100</span>
                  </div>
                  <div class="charge-item">
                    <span class="charge-description">Refundable Security Deposit</span>
                    <span class="charge-price">₱1,000</span>
                  </div>
                  <div class="charge-item">
                    <span class="charge-description">Additional Guest (per night)</span>
                    <span class="charge-price">₱200</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Section -->
        <div class="gallery-section">
          <h3 class="text-center mb-4" style="color: #1e293b;">Facility Gallery</h3>
          <div class="gallery-grid">
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-bed"></i>
              </div>
              <span>Room Interior</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-shower"></i>
              </div>
              <span>Private Shower</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-wifi"></i>
              </div>
              <span>Internet Access</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-tv"></i>
              </div>
              <span>Cable TV</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-snowflake"></i>
              </div>
              <span>Air Conditioning</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-phone"></i>
              </div>
              <span>Telephone</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
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
                Your premier destination for world-class facilities and exceptional service. 
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
              <a href="/function-hall">Function Hall</a>
              <a href="/staff-house">Staff House Rooms</a>
              <a href="/classrooms">Classrooms</a>
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
              <h6 style="color: #1e3c72; font-weight: 700;">Selected Room Type:</h6>
              <p id="loginRequiredRoom" style="font-size: 1.1rem; color: #1e293b; font-weight: 600;"></p>
            </div>
          </div>
          <div class="modal-footer" style="padding: 20px 40px; border: none;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="redirectToLogin()" style="background: linear-gradient(45deg, #1e3c72, #2a5298); border: none;">
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
              Book Staff House Room
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="selected-package mb-4 p-3" style="background: rgba(30, 60, 114, 0.05); border-radius: 12px;">
              <h6 style="color: #1e3c72; font-weight: 700;">Selected Room Type:</h6>
              <p id="selectedRoom" style="font-size: 1.1rem; color: #1e293b; font-weight: 600; margin: 0;"></p>
            </div>

            <form id="bookingForm">
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

              <!-- Equipment -->
              <div class="mb-4">
                <label class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-tools me-1"></i>
                  Equipment
                </label>
                <div class="row" id="equipmentContainer">
                  <!-- Dynamically populated -->
                </div>
              </div>

              <hr class="my-4">

              <!-- Booking Information -->
              <h6 class="mb-3" style="color: #1e3c72; font-weight: 700;">
                <i class="fas fa-info-circle me-2"></i>
                Booking Details
              </h6>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="eventDate" class="form-label" style="color: #1e293b; font-weight: 600;">
                    <i class="fas fa-calendar me-1"></i>
                    Event Date *
                  </label>
                  <input type="date" class="form-control" id="eventDate" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="eventTime" class="form-label" style="color: #1e293b; font-weight: 600;">
                    <i class="fas fa-clock me-1"></i>
                    Start Time *
                  </label>
                  <input type="time" class="form-control" id="eventTime" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="attendees" class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-users me-1"></i>
                  Number of Attendees
                </label>
                <input type="number" class="form-control" id="attendees" min="1">
              </div>

              <div class="mb-3">
                <label for="organization" class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-building me-1"></i>
                  Organization/Department
                </label>
                <input type="text" class="form-control" id="organization" placeholder="CSPC or external organization">
              </div>

              <div class="mb-3">
                <label for="address" class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-map-marker-alt me-1"></i>
                  Complete Address *
                </label>
                <textarea class="form-control" id="address" rows="2" placeholder="Street, Barangay, City, Province" required></textarea>
              </div>

              <div class="mb-3">
                <label for="eventTitle" class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-heading me-1"></i>
                  Purpose of Stay *
                </label>
                <input type="text" class="form-control" id="eventTitle" placeholder="e.g., Conference attendance, Training, etc." required>
              </div>

              <div class="mb-3">
                <label for="specialRequirements" class="form-label" style="color: #1e293b; font-weight: 600;">
                  <i class="fas fa-comment me-1"></i>
                  Special Requests/Notes
                </label>
                <textarea class="form-control" id="specialRequirements" rows="3" placeholder="Any special requests or requirements..."></textarea>
              </div>

              <!-- Cost Summary -->
              <div class="booking-summary" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                <h6 class="mb-3" style="color: #1e3c72; font-weight: 700;">
                  <i class="fas fa-receipt me-2"></i>
                  Booking Summary
                </h6>
                <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 500;">
                  <span>Room Type:</span>
                  <span id="summaryRoom" style="color: #1e293b; font-weight: 600;">-</span>
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
                  <span>Security Deposit:</span>
                  <span id="summaryDeposit" style="color: #1e293b; font-weight: 600;">₱1,000</span>
                </div>
                <hr style="border-color: #cbd5e1; margin: 12px 0;">
                <div class="summary-item" style="display: flex; justify-content: space-between; padding: 8px 0; color: #1e293b; font-weight: 700; font-size: 1.2rem;">
                  <span>Total Amount:</span>
                  <span id="summaryTotal" style="color: #1e3c72; font-weight: 700;">₱1,000</span>
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
      // Global variables
      let facilityData = {};
      let addonsData = [];
      let equipmentData = [];
      let selectedPlan = null;
      let selectedAddons = [];
      let selectedEquipment = {};
      let currentFacility = 'staff-house';
      const SECURITY_DEPOSIT = 1000;
      
      const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
      const userRole = '<?php echo $userRole ?? ''; ?>';
      const canBook = isLoggedIn && (userRole === 'user' || userRole === 'faculty');

      document.addEventListener("DOMContentLoaded", function () {
        loadFacilityData();
        loadAddonsData();
        loadEquipmentData();

        const today = new Date().toISOString().split("T")[0];
        const eventDateInput = document.getElementById("eventDate");
        
        if (eventDateInput) {
          eventDateInput.min = today;
        }
      });

      async function loadFacilityData() {
        try {
          const response = await fetch("<?= base_url('api/facilities/data/staff-house') ?>");
          const result = await response.json();
          
          if (result.success && result.facility) {
            facilityData = result.facility;
            console.log("Facility data loaded:", facilityData);
          } else {
            console.error("Failed to load facility data");
          }
        } catch (error) {
          console.error("Error loading facility data:", error);
        }
      }

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
        } catch (error) {
          console.error("Error loading addons data:", error);
        }
      }

      async function loadEquipmentData() {
        try {
          const response = await fetch("<?= base_url('api/bookings/equipment') ?>");
          const result = await response.json();

          if (!result.success) {
            throw new Error(result.message || "Failed to load equipment");
          }

          equipmentData = result.equipment
            .filter((equipment) => {
              const isRentable =
                equipment.is_rentable == 1 ||
                equipment.is_rentable === true ||
                equipment.is_rentable === "1";
              const hasRate = parseFloat(equipment.rate || 0) > 0;
              const isFurnitureOrLogistics =
                equipment.category === "furniture" ||
                equipment.category === "logistics";

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

          console.log("Rentable equipment loaded:", equipmentData);
        } catch (error) {
          console.error("Error loading equipment data:", error);
          equipmentData = [];
        }
      }

      function openBookingModal(roomType) {
        console.log('Opening booking modal for:', roomType);
        
        if (!isLoggedIn) {
          showLoginRequiredModal(roomType);
          return;
        }
        
        if (!canBook) {
          alert('Only regular users can make bookings. Admin accounts cannot create bookings.');
          return;
        }

        let planDuration = '';
        if (roomType.includes('Daily')) {
          planDuration = 'daily';
        } else if (roomType.includes('Monthly')) {
          planDuration = 'monthly';
        }

        selectedPlan = facilityData.plans ? facilityData.plans.find(p => p.duration === planDuration) : null;

        if (!selectedPlan) {
          alert('Unable to find plan details. Please try again.');
          return;
        }

        document.getElementById('selectedRoom').textContent = roomType;
        document.getElementById('summaryRoom').textContent = roomType;
        
        populateAddons();
        populateEquipment();
        updateCostSummary();
        
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
      }

      function showLoginRequiredModal(roomType) {
        document.getElementById('loginRequiredRoom').textContent = roomType;
        const modal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
        modal.show();
      }

      function redirectToLogin() {
        window.location.href = '<?= site_url('/login') ?>';
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
              <input class="form-check-input" type="checkbox" id="addon-${addon.id}" 
                     onchange="toggleAddon('${addon.id}')">
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
          const stockInfo = isAvailable
            ? `Available: ${equipment.available}`
            : "Out of Stock";

          equipmentCard.innerHTML = `
            <div class="equipment-item mb-3">
              <label class="form-label" style="color: #000;">
                ${equipment.name}
                <span class="text-primary" style="font-weight: 600;">(₱${equipment.rate.toLocaleString()} / ${equipment.unit})</span>
              </label>
              ${!isAvailable
                ? `<input type="number" class="form-control" value="0" disabled>
                   <small class="text-danger">${stockInfo}</small>`
                : `<input type="number" class="form-control quantity-input" id="qty-${equipment.id}" 
                         min="0" max="${equipment.available}" value="0" 
                         onchange="updateEquipment('${equipment.id}')"
                         style="border: 2px solid #e2e8f0;">
                   <small class="text-muted" style="color: #64748b !important;">${stockInfo}</small>`
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
        let basePrice = 0;
        let packageName = '-';
        let addonsPrice = 0;
        let equipmentPrice = 0;

        if (selectedPlan) {
          basePrice = selectedPlan.price;
          packageName = selectedPlan.name;
        }

        selectedAddons.forEach((addonId) => {
          const addon = addonsData.find((a) => a.id === addonId);
          if (addon) {
            addonsPrice += addon.price;
          }
        });

        Object.keys(selectedEquipment).forEach((equipmentId) => {
          const equipment = equipmentData.find((e) => e.id === equipmentId);
          const quantity = selectedEquipment[equipmentId];
          if (equipment && quantity > 0 && equipment.rate > 0) {
            const itemCost = equipment.rate * quantity;
            equipmentPrice += itemCost;
          }
        });

        document.getElementById('summaryRoom').textContent = packageName;
        document.getElementById('summaryBasePrice').textContent = `₱${basePrice.toLocaleString()}`;
        document.getElementById('summaryAddons').textContent = `₱${addonsPrice.toLocaleString()}`;
        document.getElementById('summaryEquipment').textContent = `₱${equipmentPrice.toLocaleString()}`;

        const total = basePrice + addonsPrice + equipmentPrice + SECURITY_DEPOSIT;
        document.getElementById('summaryTotal').textContent = `₱${total.toLocaleString()}`;
      }

      async function submitBooking() {
        const form = document.getElementById('bookingForm');
        
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        if (!selectedPlan) {
          alert('Please select a room type before proceeding.');
          return;
        }

        const addonsPrice = selectedAddons.reduce((sum, addonId) => {
          const addon = addonsData.find((a) => a.id === addonId);
          return sum + (addon ? addon.price : 0);
        }, 0);

        const equipmentPrice = Object.keys(selectedEquipment).reduce(
          (sum, equipmentId) => {
            const equipment = equipmentData.find((e) => e.id === equipmentId);
            const quantity = selectedEquipment[equipmentId];
            if (equipment && quantity > 0 && equipment.rate > 0) {
              return sum + equipment.rate * quantity;
            }
            return sum;
          },
          0
        );

        const basePrice = selectedPlan.price;
        const totalCost = basePrice + addonsPrice + equipmentPrice + SECURITY_DEPOSIT;

        // Extract duration hours from plan
        const durationMatch = selectedPlan.duration.match(/\d+/);
        const duration = durationMatch ? parseInt(durationMatch[0]) : 8;

        const formData = {
          facility_key: currentFacility,
          plan_id: selectedPlan.id,
          organization: document.getElementById("organization").value,
          address: document.getElementById("address").value,
          event_date: document.getElementById("eventDate").value,
          event_time: document.getElementById("eventTime").value,
          duration: duration,
          attendees: document.getElementById("attendees").value || null,
          event_title: document.getElementById("eventTitle").value,
          special_requirements: document.getElementById("specialRequirements").value,
          selected_addons: selectedAddons,
          selected_equipment: selectedEquipment,
          additional_hours: 0,
          maintenance_fee: 0,
          total_cost: totalCost,
        };

        console.log("Booking data to submit:", formData);

        try {
          const response = await fetch("<?= base_url('api/bookings') ?>", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(formData),
          });

          console.log("Response status:", response.status);

          if (!response.ok) {
            const errorData = await response.json();
            console.error("Server error:", errorData);
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
          }

          const result = await response.json();
          console.log("Server response:", result);

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
          console.error("Error submitting booking:", error);
          alert("Error: " + error.message);
        }
      }

      function closeSuccessModal() {
        bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
        window.location.href = '<?= site_url('/dashboard') ?>';
      }
    </script>
  </body>
</html>