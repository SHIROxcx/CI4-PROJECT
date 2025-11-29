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
    <title>Our Facilities - CSPC Digital Booking System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
   <link rel="stylesheet" href="<?= base_url(relativePath: 'css/facilities.css'); ?>">
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
                        <a class="nav-link active" href="<?= site_url('/facilities') ?>">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/event') ?>">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/contact') ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <?php if ($isLoggedIn): ?>
                            <!-- Show Dashboard button for logged-in users -->
                            <button class="nav-link dashboard-btn btn btn-success px-3 py-2" onclick="window.location.href='<?= site_url('/user/dashboard') ?>'">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </button>
                        <?php else: ?>
                            <!-- Show Login button for guests -->
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
            <a href="#home">Home</a>
            <span class="separator">•</span>
            <span>Facilities</span>
          </div>
          <h1>Premium Facilities</h1>
          <p>
            Discover our state-of-the-art facilities equipped with cutting-edge
            technology and premium amenities designed to enhance your academic
            and professional experience.
          </p>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
      <div class="container">
        <div class="filter-tabs">
          <button class="filter-btn active" onclick="filterFacilities('all')">
            <i class="fas fa-th-large"></i> All Facilities
          </button>
          <button class="filter-btn" onclick="filterFacilities('academic')">
            <i class="fas fa-graduation-cap"></i> Academic
          </button>
          <button class="filter-btn" onclick="filterFacilities('technology')">
            <i class="fas fa-laptop"></i> Technology
          </button>
          <button class="filter-btn" onclick="filterFacilities('events')">
            <i class="fas fa-calendar"></i> Events
          </button>
          <button class="filter-btn" onclick="filterFacilities('sports')">
            <i class="fas fa-dumbbell"></i> Sports
          </button>
          <button class="filter-btn" onclick="filterFacilities('hospitality')">
            <i class="fas fa-concierge-bell"></i> Hospitality
          </button>
        </div>
      </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities-section" id="facilities">
      <div class="container">
        <div class="section-header">
          <h2>Our Facilities</h2>
          <p>
            Choose from our comprehensive range of world-class facilities, each
            designed to meet your specific needs and requirements.
          </p>
        </div>

  <div class="row g-4" id="facilitiesGrid">
          <!-- University Gymnasium -->
          <div class="col-lg-4 col-md-6" data-category="sports">
            <div class="facility-card">
              <div class="facility-image">
                <i class="fas fa-basketball-ball facility-icon"></i>
                <div class="availability-indicator">Available</div>
              </div>
              <div class="facility-content">
                <h3>University Gymnasium</h3>
                <p>
                  Multi-purpose sports facility with professional lighting systems,
                  sound equipment, and spacious parking. Perfect for athletic events,
                  performances, and large gatherings.
                </p>
                <div class="facility-features">
                  <span class="feature-tag"
                    ><i class="fas fa-lightbulb"></i> Stage Lighting</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-video"></i> Projector</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-parking"></i> Parking Area</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-users"></i> Large Capacity</span
                  >
                </div>
                <div class="pricing-info">
                  <span class="price-tag">₱12,000 (8 hrs) | ₱7,000 (4 hrs)</span>
                </div>
                <a href="<?= base_url('facilities/gymnasium'); ?>" class="book-now-btn">
                  <i class="fas fa-calendar-check"></i> Book Now
                </a>
              </div>
            </div>
          </div>

          <!-- Auditorium -->
<div class="col-lg-4 col-md-6" data-category="events">
  <div class="facility-card">
    <div class="facility-image">
      <i class="fas fa-theater-masks facility-icon"></i>
      <div class="availability-indicator">Available</div>
    </div>
    <div class="facility-content">
      <h3>Auditorium</h3>
      <p>
        State-of-the-art auditorium designed for performances, lectures, and 
        large-scale events. Features professional acoustics, stage lighting, 
        and comfortable seating for audiences of various sizes.
      </p>
      <div class="facility-features">
        <span class="feature-tag"
          ><i class="fas fa-microphone"></i> Professional Sound</span
        >
        <span class="feature-tag"
          ><i class="fas fa-lightbulb"></i> Stage Lighting</span
        >
        <span class="feature-tag"
          ><i class="fas fa-video"></i> Projector</span
        >
        <span class="feature-tag"
          ><i class="fas fa-chair"></i> Comfortable Seating</span
        >
      </div>
      <div class="pricing-info">
        <span class="price-tag">₱15,000 (8 hrs) | ₱9,000 (4 hrs)</span>
      </div>
      <a href="<?= base_url('facilities/Auditorium'); ?>" class="book-now-btn">
        <i class="fas fa-calendar-check"></i> Book Now
      </a>
    </div>
  </div>
</div>

                
          <!-- Function Hall (ACAD Building 2) -->
          <div class="col-lg-4 col-md-6" data-category="events">
            <div class="facility-card">
              <div class="facility-image">
                <i class="fas fa-utensils facility-icon"></i>
                <div class="availability-indicator">Available</div>
              </div>
              <div class="facility-content">
                <h3>Function Hall (ACAD Building 2)</h3>
                <p>
                  Spacious function hall perfect for formal events, conferences,
                  and celebrations with complete audio-visual equipment and
                  comfortable seating.
                </p>
                <div class="facility-features">
                  <span class="feature-tag"
                    ><i class="fas fa-video"></i> Projector</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-volume-up"></i> Sound System</span
                  >                  <span class="feature-tag"
                    ><i class="fas fa-snowflake"></i> Air Conditioned</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-chair"></i> Monobloc Chairs</span
                  >
                </div>
                <div class="pricing-info">
                  <span class="price-tag">₱3,000 (8 hrs) | ₱1,500 (4 hrs)</span>
                </div>
                 <a href="<?= base_url('facilities/FunctionHall'); ?>" class="book-now-btn">
                  <i class="fas fa-calendar-check"></i> Book Now
                </a>
              </div>
            </div>
          </div>


          <!-- Pearl Mini Restaurant -->
          <div class="col-lg-4 col-md-6" data-category="hospitality">
            <div class="facility-card">
              <div class="facility-image">
                <i class="fas fa-utensils facility-icon"></i>
                <div class="availability-indicator">Available</div>
              </div>
              <div class="facility-content">
                <h3>Pearl Mini Restaurant</h3>
                <p>
                  Intimate dining facility perfect for small gatherings, business meetings,
                  and special celebrations. Features quality cuisine service and 
                  comfortable ambiance for memorable dining experiences.
                </p>
                <div class="facility-features">
                  <span class="feature-tag"
                    ><i class="fas fa-utensils"></i> Full Service</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-snowflake"></i> Air Conditioned</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-users"></i> Private Dining</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-wine-glass"></i> Catering Service</span
                  >
                </div>
                <div class="pricing-info">
                  <span class="price-tag">₱5,000 (8 hrs) | ₱3,000 (4 hrs)</span>
                </div>
                <a href="<?= base_url('facilities/pearlmini'); ?>" class="book-now-btn">
                  <i class="fas fa-calendar-check"></i> Book Now
                </a>
              </div>
            </div>
          </div>

      
           <!-- Classrooms -->
          <div class="col-lg-4 col-md-6" data-category="academic">
            <div class="facility-card">
              <div class="facility-image">
                <i class="fas fa-chalkboard-teacher facility-icon"></i>
                <div class="availability-indicator">Available</div>
              </div>
              <div class="facility-content">
                <h3>Classrooms</h3>
                <p>
                  Modern learning spaces with air conditioning, multimedia equipment,
                  and comfortable seating. Perfect for training sessions, workshops,
                  seminars, and educational activities.
                </p>
                <div class="facility-features">
                  <span class="feature-tag"
                    ><i class="fas fa-snowflake"></i> Air Conditioned</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-video"></i> Projector</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-volume-up"></i> Sound System</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-chalkboard"></i> Whiteboard</span
                  >
                </div>
                <div class="pricing-info">
                  <span class="price-tag">₱800 (8 hrs) | ₱500 (4 hrs) | ₱300 (2 hrs)</span>
                </div>
                <a href="<?= base_url('facilities/classroom'); ?>" class="book-now-btn">
                  <i class="fas fa-calendar-check"></i> Book Now
                </a>
              </div>
            </div>
          </div>

<!-- Student Dormitory -->
          <div class="col-lg-4 col-md-6" data-category="academic">
            <div class="facility-card">
              <div class="facility-image">
                <i class="fas fa-bed facility-icon"></i>
                <div class="availability-indicator">Available</div>
              </div>
              <div class="facility-content">
                <h3>Student Dormitory</h3>
                <p>
                  Comfortable accommodation facility providing a safe and
                  conducive living environment for students, with modern
                  amenities and security.
                </p>
                <div class="facility-features">
                  <span class="feature-tag"
                    ><i class="fas fa-bed"></i> Room Rate</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-shield-alt"></i> Security</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-wifi"></i> Internet Access</span
                  >
                  <span class="feature-tag"
                    ><i class="fas fa-snowflake"></i> Air Conditioned</span
                  >
                </div>
                <div class="pricing-info">
                  <span class="price-tag">₱1,000 per month</span>
                </div>
                <a href="<?= base_url('facilities/Dormitory'); ?>" class="book-now-btn">
                  <i class="fas fa-calendar-check"></i> Book Now
                </a>
              </div>
            </div>
          </div>


          
        </div>
      </div>


    </section>
  

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="container">
        <div class="section-header">
          <h2>Facility Metrics</h2>
          <p>
            Our campus facilities are actively used by students, faculty, and
            staff for various academic and extracurricular activities.
          </p>
        </div>
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">2,500+</div>
            <div class="stat-label">Bookings per Month</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">17</div>
            <div class="stat-label">Premium Facilities</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">98%</div>
            <div class="stat-label">Satisfaction Rate</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Online Booking</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 footer-section">
            <div class="footer-logo">
              <div class="cspc-logo-nav">
                <i class="fas fa-graduation-cap"></i>
              </div>
              CSPC Sphere
            </div>
            <p class="mb-4">
              The ultimate platform for booking and managing campus facilities
              at Camarines Sur Polytechnic College, enhancing the academic
              experience.
            </p>
            <div class="footer-social">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          <div class="col-lg-2 col-md-6 footer-section">
            <h5>Quick Links</h5>
            <a href="#">Home</a>
            <a href="#">Facilities</a>
            <a href="#">About Us</a>
            <a href="#">Contact</a>
            <a href="#">FAQs</a>
          </div>
          <div class="col-lg-3 col-md-6 footer-section">
            <h5>Facilities</h5>
            <a href="#">Auditorium</a>
            <a href="#">Conference Rooms</a>
            <a href="#">Computer Labs</a>
            <a href="#">Sports Facilities</a>
            <a href="#">Science Labs</a>
          </div>
          <div class="col-lg-3 col-md-6 footer-section">
            <h5>Contact Us</h5>
            <a href="#"
              ><i class="fas fa-map-marker-alt me-2"></i> Nabua, Camarines Sur, Philippines</a
            >
            <a href="#"><i class="fas fa-phone me-2"></i> (054) 361-2101</a>
            <a href="#"><i class="fas fa-envelope me-2"></i> cspc@edu.ph</a>
            <a href="#"
              ><i class="fas fa-clock me-2"></i> Mon-Fri: 8:00 AM - 5:00 PM</a
            >
          </div>
        </div>
        <div class="footer-bottom">
          <p>
            © 2023 CSPC Sphere. All rights reserved. Camarines Sur Polytechnic
            College.
          </p>
        </div>
      </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
      // Navbar scroll effect
      window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

// Filter facilities
function filterFacilities(category) {
  const facilities = document.querySelectorAll("#facilitiesGrid > div");
  const filterButtons = document.querySelectorAll(".filter-btn");

  // Reset active state
  filterButtons.forEach((btn) => btn.classList.remove("active"));
  
  // Find and activate the clicked button
  filterButtons.forEach((btn) => {
    if (btn.getAttribute("data-category") === category) {
      btn.classList.add("active");
    }
  });

  facilities.forEach((facility) => {
    if (category === "all" || facility.dataset.category === category) {
      facility.style.display = "block";
      setTimeout(() => {
        facility.style.opacity = "1";
        facility.style.transform = "translateY(0)";
      }, 100);
    } else {
      facility.style.opacity = "0";
      facility.style.transform = "translateY(20px)";
      setTimeout(() => {
        facility.style.display = "none";
      }, 300);
    }
  });
}

// Animation for facilities on page load
document.addEventListener("DOMContentLoaded", function () {
  const facilities = document.querySelectorAll(".facility-card");
  facilities.forEach((facility, index) => {
    setTimeout(() => {
      facility.style.opacity = "1";
      facility.style.transform = "translateY(0)";
    }, 100 * index);
  });
});

      // Login alert
      function showLoginAlert() {
        alert(
          "Login functionality will be available soon. Please check back later!"
        );
      }

      // Booking modal
      function openBookingModal(facilityName) {
        alert(
          `Booking system for ${facilityName} will be implemented soon. Thank you for your interest!`
        );
      }

      // Animation for facilities on page load
      document.addEventListener("DOMContentLoaded", function () {
        const facilities = document.querySelectorAll(".facility-card");
        facilities.forEach((facility, index) => {
          setTimeout(() => {
            facility.style.opacity = "1";
            facility.style.transform = "translateY(0)";
          }, 100 * index);
        });
      });
    </script>
  </body>
</html>

