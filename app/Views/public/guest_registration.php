<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Guest Registration | CSPC Digital Booking System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .registration-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .card-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .card-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .card-body {
            padding: 40px;
        }
        .event-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .event-info h5 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .event-detail {
            margin-bottom: 10px;
        }
        .event-detail strong {
            color: #333;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .required {
            color: #dc3545;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 8px;
        }
        .footer-text {
            text-align: center;
            color: white;
            margin-top: 20px;
            font-size: 14px;
        }
        .success-message {
            display: none;
            text-align: center;
            padding: 40px;
        }
        .success-message i {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .spinner-border {
            display: none;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-card">
            <div class="card-header">
                <h1><i class="fas fa-calendar-check"></i> Event Registration</h1>
                <p>Complete your registration to receive your QR code</p>
            </div>
            <div class="card-body">
                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                    </div>
                <?php elseif (isset($booking)): ?>

                    <!-- Event Information -->
                    <div class="event-info">
                        <h5><i class="fas fa-info-circle"></i> Event Details</h5>
                        <div class="event-detail">
                            <strong>Event:</strong> <?= esc($booking['event_title']) ?>
                        </div>
                        <div class="event-detail">
                            <strong>Facility:</strong> <?= esc($booking['facility_name']) ?>
                        </div>
                        <div class="event-detail">
                            <strong>Date:</strong> <?= date('F d, Y', strtotime($booking['event_date'])) ?>
                        </div>
                        <div class="event-detail">
                            <strong>Time:</strong> <?= esc($booking['event_time']) ?>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form id="registrationForm" onsubmit="submitRegistration(event)">
                        <input type="hidden" id="bookingId" value="<?= $booking['id'] ?>">

                        <div class="mb-3">
                            <label for="guestName" class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="guestName" required placeholder="Enter your full name">
                        </div>

                        <div class="mb-3">
                            <label for="guestEmail" class="form-label">Email Address <span class="required">*</span></label>
                            <input type="email" class="form-control" id="guestEmail" required placeholder="Enter your email address">
                            <small class="text-muted">Your QR code will be sent to this email</small>
                        </div>

                        <div class="mb-3">
                            <label for="guestPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="guestPhone" placeholder="Enter your phone number (optional)">
                        </div>

                        <button type="submit" class="btn btn-primary btn-register" id="submitBtn">
                            <span id="btnText">
                                <i class="fas fa-check-circle"></i> Register for Event
                            </span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                    <!-- Success Message (hidden initially) -->
                    <div id="successMessage" class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <h3>Registration Successful!</h3>
                        <p>Your registration has been confirmed.</p>
                        <p><strong>Check your email</strong> for your unique QR code.</p>
                        <p class="text-muted">Please bring your QR code (digital or printed) to the event for check-in.</p>
                    </div>

                <?php endif; ?>
            </div>
        </div>
        <p class="footer-text">
            <small>CSPC Digital Booking System &copy; <?= date('Y') ?></small>
        </p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        async function submitRegistration(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const form = document.getElementById('registrationForm');

            // Disable button and show spinner
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-block';

            const guestData = {
                booking_id: document.getElementById('bookingId').value,
                guest_name: document.getElementById('guestName').value.trim(),
                guest_email: document.getElementById('guestEmail').value.trim(),
                guest_phone: document.getElementById('guestPhone').value.trim()
            };

            try {
                const response = await fetch('/api/guest-registration/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(guestData)
                });

                const data = await response.json();

                if (data.success) {
                    // Hide form and show success message
                    form.style.display = 'none';
                    document.querySelector('.event-info').style.display = 'none';
                    document.getElementById('successMessage').style.display = 'block';
                } else {
                    showAlert('danger', data.message || 'Registration failed. Please try again.');
                    // Re-enable button
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
                // Re-enable button
                submitBtn.disabled = false;
                btnText.style.display = 'inline-block';
                btnSpinner.style.display = 'none';
            }
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
</body>
</html>
