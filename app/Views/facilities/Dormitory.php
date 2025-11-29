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
    <title>Student Dormitory - CSPC Digital Booking System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= base_url('css/facilities/gymnasium.css'); ?>">
    <style>
      .info-section {
        background: white;
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 60px;
        border: 1px solid rgba(255, 255, 255, 0.8);
      }

      .info-section h3 {
        color: #1e3c72;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 30px;
      }

      .info-section p {
        color: #64748b;
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 20px;
      }

      .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 40px;
      }

      .feature-item {
        background: rgba(30, 60, 114, 0.05);
        padding: 25px;
        border-radius: 16px;
        border: 2px solid rgba(30, 60, 114, 0.1);
        text-align: center;
        transition: all 0.3s ease;
      }

      .feature-item:hover {
        transform: translateY(-8px);
        border-color: rgba(30, 60, 114, 0.3);
        box-shadow: 0 10px 25px rgba(30, 60, 114, 0.1);
      }

      .feature-item i {
        font-size: 2.5rem;
        color: #1e3c72;
        margin-bottom: 15px;
        display: block;
      }

      .feature-item h5 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 10px;
      }

      .feature-item p {
        color: #64748b;
        font-size: 0.95rem;
        margin: 0;
      }

      .cta-section {
        background: linear-gradient(135deg, rgba(30, 60, 114, 0.95) 0%, rgba(42, 82, 152, 0.95) 100%);
        color: white;
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        margin: 60px 0;
      }

      .cta-section h2 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 20px;
        color: white;
      }

      .cta-section p {
        font-size: 1.1rem;
        margin-bottom: 40px;
        color: rgba(255, 255, 255, 0.95);
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
      }

      .cta-button {
        background: white;
        color: #1e3c72;
        border: none;
        padding: 16px 40px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 12px;
      }

      .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.95);
      }

      .pricing-info-box {
        background: rgba(248, 250, 252, 0.95);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 30px;
        margin-top: 30px;
        text-align: center;
      }

      .pricing-info-box h4 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 15px;
      }

      .pricing-info-box p {
        color: #64748b;
        margin-bottom: 10px;
        font-size: 1rem;
      }

      .price-highlight {
        font-size: 1.8rem;
        color: #1e3c72;
        font-weight: 800;
        margin: 15px 0;
      }

      .amenities-list {
        list-style: none;
        padding: 0;
        margin-top: 20px;
      }

      .amenities-list li {
        color: #64748b;
        padding: 12px 0;
        border-bottom: 1px solid rgba(30, 60, 114, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .amenities-list li:last-child {
        border-bottom: none;
      }

      .amenities-list i {
        color: #22c55e;
        font-weight: 700;
      }
    </style>
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
            <span>Student Dormitory</span>
          </div>
          <h1>Student Dormitory</h1>
          <p>
            Comfortable and secure residential accommodation designed to provide a safe, 
            conducive living environment for students with modern amenities and professional support.
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
          <h2>Student Housing Excellence</h2>
          <p>
            Our Student Dormitory offers a welcoming home away from home for students at 
            Camarines Sur Polytechnic College. With modern facilities, 24/7 security, and 
            a supportive community environment, we provide the ideal setting for academic success 
            and personal growth.
          </p>
        </div>

        <!-- About Section -->
        <div class="info-section">
          <h3>About Our Dormitory</h3>
          <p>
            The Student Dormitory at CSPC is a premier residential facility designed with student 
            comfort and safety in mind. Our well-maintained rooms and common areas provide an ideal 
            living environment that supports both academic excellence and personal development.
          </p>
          <p>
            Each room is equipped with essential furniture and amenities, and our professional 
            staff ensures that residents enjoy a safe, clean, and welcoming atmosphere. We foster 
            a strong community where students can build lasting friendships and focus on their studies.
          </p>
        </div>

        <!-- Amenities Section -->
        <div class="info-section">
          <h3>Facility Amenities</h3>
          <div class="features-grid">
            <div class="feature-item">
              <i class="fas fa-snowflake"></i>
              <h5>Air Conditioning</h5>
              <p>Climate-controlled rooms for comfort</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-shield-alt"></i>
              <h5>24/7 Security</h5>
              <p>Professional security personnel on duty</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-wifi"></i>
              <h5>High-Speed WiFi</h5>
              <p>Reliable internet access in all areas</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-bed"></i>
              <h5>Furnished Rooms</h5>
              <p>Complete bed and desk setup</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-water"></i>
              <h5>Hot Water System</h5>
              <p>Available 24 hours daily</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-users"></i>
              <h5>Common Areas</h5>
              <p>Study lounge and recreation space</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-book"></i>
              <h5>Study Facilities</h5>
              <p>Quiet zones for academic work</p>
            </div>
            <div class="feature-item">
              <i class="fas fa-trash"></i>
              <h5>Housekeeping Service</h5>
              <p>Regular cleaning and maintenance</p>
            </div>
          </div>
        </div>

        <!-- Room Features -->
        <div class="info-section">
          <h3>Standard Room Features</h3>
          <div class="row">
            <div class="col-md-6">
              <ul class="amenities-list">
                <li><i class="fas fa-check-circle"></i> Single or Double Occupancy Options</li>
                <li><i class="fas fa-check-circle"></i> Air Conditioned Bedroom</li>
                <li><i class="fas fa-check-circle"></i> Comfortable Bed with Mattress</li>
                <li><i class="fas fa-check-circle"></i> Study Desk and Chair</li>
                <li><i class="fas fa-check-circle"></i> Storage Cabinet</li>
              </ul>
            </div>
            <div class="col-md-6">
              <ul class="amenities-list">
                <li><i class="fas fa-check-circle"></i> Private Bathroom</li>
                <li><i class="fas fa-check-circle"></i> Hot/Cold Water Shower</li>
                <li><i class="fas fa-check-circle"></i> WiFi Connection</li>
                <li><i class="fas fa-check-circle"></i> Electric Fan Backup</li>
                <li><i class="fas fa-check-circle"></i> Adequate Lighting</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Rules and Information -->
        <div class="info-section">
          <h3>Important Information</h3>
          <p>
            <strong>Occupancy Requirements:</strong> Students must be currently enrolled at 
            Camarines Sur Polytechnic College and in good academic standing. All residents must 
            comply with dormitory rules and CSPC policies.
          </p>
          <p>
            <strong>Duration Options:</strong> Rooms are available on a monthly basis, with 
            flexible check-in and check-out arrangements subject to availability and advance notice.
          </p>
          <p>
            <strong>Deposits and Fees:</strong> A security deposit is required upon booking, which 
            will be refunded upon checkout provided no damages or policy violations have occurred.
          </p>
          <p>
            <strong>House Rules:</strong> To maintain a safe and peaceful environment, all residents 
            must adhere to dormitory guidelines including quiet hours, visitor policies, and 
            maintenance of common areas.
          </p>
        </div>

        <!-- Pricing Information -->
        <div class="pricing-info-box">
          <h4>Room Rates</h4>
          <p>Monthly rate: <span class="price-highlight">₱1,000/month</span></p>
          <p style="font-size: 0.95rem; color: #94a3b8;">*Rates may vary based on room type and current promotions</p>
          <p style="margin-top: 20px; color: #1e3c72; font-weight: 600;">
            For detailed pricing, room availability, and booking inquiries, please contact the Student Services Center.
          </p>
        </div>

        <!-- CTA Section -->
        <div class="cta-section">
          <h2>Ready to Book Your Room?</h2>
          <p>
            For detailed information about room rates, availability, booking procedures, 
            and special rates, please visit the Student Services Center or contact our housing office.
          </p>
          <button class="cta-button" onclick="window.location.href='<?= site_url('/contact') ?>'">
            <i class="fas fa-phone"></i>
            Contact Student Services
          </button>
        </div>

        <!-- Gallery Section -->
        <div class="gallery-section">
          <h3 class="text-center mb-4" style="color: #1e293b;">Facility Gallery</h3>
          <div class="gallery-grid">
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-bed"></i>
              </div>
              <span>Sample Room</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-door-open"></i>
              </div>
              <span>Main Entrance</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-users"></i>
              </div>
              <span>Common Area</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-book"></i>
              </div>
              <span>Study Lounge</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-utensils"></i>
              </div>
              <span>Dining Area</span>
            </div>
            <div class="gallery-item">
              <div class="gallery-placeholder">
                <i class="fas fa-shower"></i>
              </div>
              <span>Bathroom Facilities</span>
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
                Your premier destination for world-class facilities and exceptional student services. 
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
              <a href="/auditorium">University Auditorium</a>
              <a href="/gymnasium">University Gymnasium</a>
              <a href="/function-hall">Function Hall</a>
              <a href="/classrooms">Classrooms</a>
              <a href="/dormitory">Student Dormitory</a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="footer-section">
              <h5>Contact Info</h5>
              <a href="tel:+63541234567">
                <i class="fas fa-phone" style="margin-right: 8px;"></i>
                +63 54 123 4567
              </a>
              <a href="mailto:housing@cspc.edu.ph">
                <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                housing@cspc.edu.ph
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  </body>
</html>