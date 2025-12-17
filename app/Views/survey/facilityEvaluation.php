<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC Rental Facility Evaluation Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #003366;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #003366;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
            font-style: italic;
        }

        .booking-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #003366;
        }

        .booking-info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .booking-info strong {
            color: #003366;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            background-color: #003366;
            color: white;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 3px solid #ddd;
        }

        .form-group.focused {
            border-left-color: #003366;
            background-color: #f0f4f8;
        }

        .question-label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            color: #333;
            font-size: 14px;
        }

        .rating-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .rating-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-option input[type="radio"],
        .rating-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #003366;
        }

        .rating-option label {
            cursor: pointer;
            margin: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rating-option input[type="radio"]:checked ~ label,
        .rating-option input[type="checkbox"]:checked ~ label {
            color: #003366;
            font-weight: 600;
        }

        .subquestion {
            margin-left: 30px;
            margin-top: 15px;
            padding: 12px;
            background-color: white;
            border-radius: 4px;
            border-left: 2px solid #999;
        }

        .subquestion .question-label {
            font-size: 13px;
            color: #555;
        }

        textarea {
            width: 100%;
            padding: 12px;
            font-family: inherit;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
        }

        textarea:focus {
            outline: none;
            border-color: #003366;
            box-shadow: 0 0 5px rgba(0, 51, 102, 0.1);
        }

        select {
            width: 100%;
            padding: 10px;
            font-family: inherit;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        select:focus {
            outline: none;
            border-color: #003366;
            box-shadow: 0 0 5px rgba(0, 51, 102, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        button {
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-submit {
            background-color: #003366;
            color: white;
        }

        .btn-submit:hover {
            background-color: #002244;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }

        .btn-submit:disabled {
            background-color: #999;
            cursor: not-allowed;
            transform: none;
        }

        .btn-reset {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-reset:hover {
            background-color: #e0e0e0;
        }

        .required-note {
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #003366;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            display: none;
        }

        .success-message {
            background-color: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #3c3;
            display: none;
        }

        .rating-scale {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            button {
                width: 100%;
            }

            .rating-options {
                gap: 10px;
            }

            .subquestion {
                margin-left: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 CSPC RENTAL FACILITY EVALUATION FORM</h1>
            <p>Thank you for choosing CSPC as the venue of your occasion/event. In CSPC, we are committed to provide excellent services and open to suggestions for the continual improvement of our system. To help us serve you better, may we ask you to take a few minutes to answer this survey.</p>
        </div>

        <div class="booking-info">
            <p><strong>Booking ID:</strong> #BK<?= str_pad($booking['id'], 4, '0', STR_PAD_LEFT) ?></p>
            <p><strong>Facility Rented:</strong> <?= htmlspecialchars($booking['event_title']) ?></p>
            <p><strong>Event Date:</strong> <?= date('F d, Y', strtotime($booking['event_date'])) ?></p>
            <p><strong>Your Name:</strong> <?= htmlspecialchars($booking['client_name']) ?></p>
        </div>

        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>

        <form id="surveyForm">
            <input type="hidden" name="survey_token" value="<?= htmlspecialchars($token) ?>">

            <p class="required-note">* All fields are required to complete the survey</p>

            <!-- STAFF SECTION -->
            <div class="form-section">
                <div class="section-title">👥 STAFF EVALUATION</div>

                <div class="form-group">
                    <label class="question-label">1. Punctuality of the staff (Property Staff and Audio Operator) *</label>
                    <div class="rating-options">
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_excellent" name="staff_punctuality" value="Excellent" required>
                            <label for="staff_punctuality_excellent">Excellent</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_very_good" name="staff_punctuality" value="Very Good">
                            <label for="staff_punctuality_very_good">Very Good</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_good" name="staff_punctuality" value="Good">
                            <label for="staff_punctuality_good">Good</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_fair" name="staff_punctuality" value="Fair">
                            <label for="staff_punctuality_fair">Fair</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_poor" name="staff_punctuality" value="Poor">
                            <label for="staff_punctuality_poor">Poor</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="staff_punctuality_na" name="staff_punctuality" value="N/A">
                            <label for="staff_punctuality_na">N/A</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="question-label">2. Level of courtesy, respect, and helpfulness of the following</label>

                    <div class="subquestion">
                        <label class="question-label">a. Property Staff *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_excellent" name="staff_courtesy_property" value="Excellent" required>
                                <label for="staff_courtesy_property_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_very_good" name="staff_courtesy_property" value="Very Good">
                                <label for="staff_courtesy_property_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_good" name="staff_courtesy_property" value="Good">
                                <label for="staff_courtesy_property_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_fair" name="staff_courtesy_property" value="Fair">
                                <label for="staff_courtesy_property_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_poor" name="staff_courtesy_property" value="Poor">
                                <label for="staff_courtesy_property_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_property_na" name="staff_courtesy_property" value="N/A">
                                <label for="staff_courtesy_property_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">b. Audio Operator *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_excellent" name="staff_courtesy_audio" value="Excellent" required>
                                <label for="staff_courtesy_audio_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_very_good" name="staff_courtesy_audio" value="Very Good">
                                <label for="staff_courtesy_audio_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_good" name="staff_courtesy_audio" value="Good">
                                <label for="staff_courtesy_audio_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_fair" name="staff_courtesy_audio" value="Fair">
                                <label for="staff_courtesy_audio_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_poor" name="staff_courtesy_audio" value="Poor">
                                <label for="staff_courtesy_audio_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_audio_na" name="staff_courtesy_audio" value="N/A">
                                <label for="staff_courtesy_audio_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">c. Janitor *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_excellent" name="staff_courtesy_janitor" value="Excellent" required>
                                <label for="staff_courtesy_janitor_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_very_good" name="staff_courtesy_janitor" value="Very Good">
                                <label for="staff_courtesy_janitor_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_good" name="staff_courtesy_janitor" value="Good">
                                <label for="staff_courtesy_janitor_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_fair" name="staff_courtesy_janitor" value="Fair">
                                <label for="staff_courtesy_janitor_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_poor" name="staff_courtesy_janitor" value="Poor">
                                <label for="staff_courtesy_janitor_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="staff_courtesy_janitor_na" name="staff_courtesy_janitor" value="N/A">
                                <label for="staff_courtesy_janitor_na">N/A</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FACILITY SECTION -->
            <div class="form-section">
                <div class="section-title">🏢 FACILITY EVALUATION</div>

                <div class="form-group">
                    <label class="question-label">1. Level at which the facility met your expectations *</label>
                    <div class="rating-options">
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_excellent" name="facility_level_expectations" value="Excellent" required>
                            <label for="facility_level_expectations_excellent">Excellent</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_very_good" name="facility_level_expectations" value="Very Good">
                            <label for="facility_level_expectations_very_good">Very Good</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_good" name="facility_level_expectations" value="Good">
                            <label for="facility_level_expectations_good">Good</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_fair" name="facility_level_expectations" value="Fair">
                            <label for="facility_level_expectations_fair">Fair</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_poor" name="facility_level_expectations" value="Poor">
                            <label for="facility_level_expectations_poor">Poor</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="facility_level_expectations_na" name="facility_level_expectations" value="N/A">
                            <label for="facility_level_expectations_na">N/A</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="question-label">2. The cleanliness of the following</label>

                    <div class="subquestion">
                        <label class="question-label">a. Function Hall / Gym / Auditorium / Seminar Hall / ATS *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_excellent" name="facility_cleanliness_function_hall" value="Excellent" required>
                                <label for="facility_cleanliness_function_hall_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_very_good" name="facility_cleanliness_function_hall" value="Very Good">
                                <label for="facility_cleanliness_function_hall_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_good" name="facility_cleanliness_function_hall" value="Good">
                                <label for="facility_cleanliness_function_hall_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_fair" name="facility_cleanliness_function_hall" value="Fair">
                                <label for="facility_cleanliness_function_hall_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_poor" name="facility_cleanliness_function_hall" value="Poor">
                                <label for="facility_cleanliness_function_hall_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_function_hall_na" name="facility_cleanliness_function_hall" value="N/A">
                                <label for="facility_cleanliness_function_hall_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">b. Classrooms & Rooms *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_excellent" name="facility_cleanliness_classrooms" value="Excellent" required>
                                <label for="facility_cleanliness_classrooms_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_very_good" name="facility_cleanliness_classrooms" value="Very Good">
                                <label for="facility_cleanliness_classrooms_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_good" name="facility_cleanliness_classrooms" value="Good">
                                <label for="facility_cleanliness_classrooms_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_fair" name="facility_cleanliness_classrooms" value="Fair">
                                <label for="facility_cleanliness_classrooms_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_poor" name="facility_cleanliness_classrooms" value="Poor">
                                <label for="facility_cleanliness_classrooms_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_classrooms_na" name="facility_cleanliness_classrooms" value="N/A">
                                <label for="facility_cleanliness_classrooms_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">c. Restrooms *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_excellent" name="facility_cleanliness_restrooms" value="Excellent" required>
                                <label for="facility_cleanliness_restrooms_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_very_good" name="facility_cleanliness_restrooms" value="Very Good">
                                <label for="facility_cleanliness_restrooms_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_good" name="facility_cleanliness_restrooms" value="Good">
                                <label for="facility_cleanliness_restrooms_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_fair" name="facility_cleanliness_restrooms" value="Fair">
                                <label for="facility_cleanliness_restrooms_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_poor" name="facility_cleanliness_restrooms" value="Poor">
                                <label for="facility_cleanliness_restrooms_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_restrooms_na" name="facility_cleanliness_restrooms" value="N/A">
                                <label for="facility_cleanliness_restrooms_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">d. Reception Area *</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_excellent" name="facility_cleanliness_reception" value="Excellent" required>
                                <label for="facility_cleanliness_reception_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_very_good" name="facility_cleanliness_reception" value="Very Good">
                                <label for="facility_cleanliness_reception_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_good" name="facility_cleanliness_reception" value="Good">
                                <label for="facility_cleanliness_reception_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_fair" name="facility_cleanliness_reception" value="Fair">
                                <label for="facility_cleanliness_reception_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_poor" name="facility_cleanliness_reception" value="Poor">
                                <label for="facility_cleanliness_reception_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="facility_cleanliness_reception_na" name="facility_cleanliness_reception" value="N/A">
                                <label for="facility_cleanliness_reception_na">N/A</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="question-label">3. Please rate the function of the following equipment *</label>

                    <div class="subquestion">
                        <label class="question-label">a. Aircondition unit</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_excellent" name="equipment_airconditioning" value="Excellent">
                                <label for="equipment_airconditioning_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_very_good" name="equipment_airconditioning" value="Very Good">
                                <label for="equipment_airconditioning_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_good" name="equipment_airconditioning" value="Good">
                                <label for="equipment_airconditioning_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_fair" name="equipment_airconditioning" value="Fair">
                                <label for="equipment_airconditioning_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_poor" name="equipment_airconditioning" value="Poor">
                                <label for="equipment_airconditioning_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_airconditioning_na" name="equipment_airconditioning" value="N/A">
                                <label for="equipment_airconditioning_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">b. Lightings</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_excellent" name="equipment_lighting" value="Excellent">
                                <label for="equipment_lighting_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_very_good" name="equipment_lighting" value="Very Good">
                                <label for="equipment_lighting_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_good" name="equipment_lighting" value="Good">
                                <label for="equipment_lighting_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_fair" name="equipment_lighting" value="Fair">
                                <label for="equipment_lighting_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_poor" name="equipment_lighting" value="Poor">
                                <label for="equipment_lighting_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_lighting_na" name="equipment_lighting" value="N/A">
                                <label for="equipment_lighting_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">c. Electric Fans</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_excellent" name="equipment_electric_fans" value="Excellent">
                                <label for="equipment_electric_fans_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_very_good" name="equipment_electric_fans" value="Very Good">
                                <label for="equipment_electric_fans_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_good" name="equipment_electric_fans" value="Good">
                                <label for="equipment_electric_fans_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_fair" name="equipment_electric_fans" value="Fair">
                                <label for="equipment_electric_fans_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_poor" name="equipment_electric_fans" value="Poor">
                                <label for="equipment_electric_fans_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_electric_fans_na" name="equipment_electric_fans" value="N/A">
                                <label for="equipment_electric_fans_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">d. Tables</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_excellent" name="equipment_tables" value="Excellent">
                                <label for="equipment_tables_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_very_good" name="equipment_tables" value="Very Good">
                                <label for="equipment_tables_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_good" name="equipment_tables" value="Good">
                                <label for="equipment_tables_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_fair" name="equipment_tables" value="Fair">
                                <label for="equipment_tables_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_poor" name="equipment_tables" value="Poor">
                                <label for="equipment_tables_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_tables_na" name="equipment_tables" value="N/A">
                                <label for="equipment_tables_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">e. Monobloc Chairs</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_excellent" name="equipment_monobloc_chairs" value="Excellent">
                                <label for="equipment_monobloc_chairs_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_very_good" name="equipment_monobloc_chairs" value="Very Good">
                                <label for="equipment_monobloc_chairs_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_good" name="equipment_monobloc_chairs" value="Good">
                                <label for="equipment_monobloc_chairs_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_fair" name="equipment_monobloc_chairs" value="Fair">
                                <label for="equipment_monobloc_chairs_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_poor" name="equipment_monobloc_chairs" value="Poor">
                                <label for="equipment_monobloc_chairs_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_monobloc_chairs_na" name="equipment_monobloc_chairs" value="N/A">
                                <label for="equipment_monobloc_chairs_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">f. Chair Cover</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_excellent" name="equipment_chair_cover" value="Excellent">
                                <label for="equipment_chair_cover_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_very_good" name="equipment_chair_cover" value="Very Good">
                                <label for="equipment_chair_cover_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_good" name="equipment_chair_cover" value="Good">
                                <label for="equipment_chair_cover_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_fair" name="equipment_chair_cover" value="Fair">
                                <label for="equipment_chair_cover_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_poor" name="equipment_chair_cover" value="Poor">
                                <label for="equipment_chair_cover_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_chair_cover_na" name="equipment_chair_cover" value="N/A">
                                <label for="equipment_chair_cover_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">g. Podium</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_excellent" name="equipment_podium" value="Excellent">
                                <label for="equipment_podium_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_very_good" name="equipment_podium" value="Very Good">
                                <label for="equipment_podium_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_good" name="equipment_podium" value="Good">
                                <label for="equipment_podium_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_fair" name="equipment_podium" value="Fair">
                                <label for="equipment_podium_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_poor" name="equipment_podium" value="Poor">
                                <label for="equipment_podium_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_podium_na" name="equipment_podium" value="N/A">
                                <label for="equipment_podium_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">h. Multimedia Projector</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_excellent" name="equipment_multimedia_projector" value="Excellent">
                                <label for="equipment_multimedia_projector_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_very_good" name="equipment_multimedia_projector" value="Very Good">
                                <label for="equipment_multimedia_projector_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_good" name="equipment_multimedia_projector" value="Good">
                                <label for="equipment_multimedia_projector_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_fair" name="equipment_multimedia_projector" value="Fair">
                                <label for="equipment_multimedia_projector_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_poor" name="equipment_multimedia_projector" value="Poor">
                                <label for="equipment_multimedia_projector_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_multimedia_projector_na" name="equipment_multimedia_projector" value="N/A">
                                <label for="equipment_multimedia_projector_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">i. Sound System</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_excellent" name="equipment_sound_system" value="Excellent">
                                <label for="equipment_sound_system_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_very_good" name="equipment_sound_system" value="Very Good">
                                <label for="equipment_sound_system_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_good" name="equipment_sound_system" value="Good">
                                <label for="equipment_sound_system_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_fair" name="equipment_sound_system" value="Fair">
                                <label for="equipment_sound_system_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_poor" name="equipment_sound_system" value="Poor">
                                <label for="equipment_sound_system_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_sound_system_na" name="equipment_sound_system" value="N/A">
                                <label for="equipment_sound_system_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">j. Microphone</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_excellent" name="equipment_microphone" value="Excellent">
                                <label for="equipment_microphone_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_very_good" name="equipment_microphone" value="Very Good">
                                <label for="equipment_microphone_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_good" name="equipment_microphone" value="Good">
                                <label for="equipment_microphone_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_fair" name="equipment_microphone" value="Fair">
                                <label for="equipment_microphone_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_poor" name="equipment_microphone" value="Poor">
                                <label for="equipment_microphone_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_microphone_na" name="equipment_microphone" value="N/A">
                                <label for="equipment_microphone_na">N/A</label>
                            </div>
                        </div>
                    </div>

                    <div class="subquestion">
                        <label class="question-label">k. Others</label>
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_excellent" name="equipment_others" value="Excellent">
                                <label for="equipment_others_excellent">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_very_good" name="equipment_others" value="Very Good">
                                <label for="equipment_others_very_good">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_good" name="equipment_others" value="Good">
                                <label for="equipment_others_good">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_fair" name="equipment_others" value="Fair">
                                <label for="equipment_others_fair">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_poor" name="equipment_others" value="Poor">
                                <label for="equipment_others_poor">Poor</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="equipment_others_na" name="equipment_others" value="N/A">
                                <label for="equipment_others_na">N/A</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OVERALL EXPERIENCE SECTION -->
            <div class="form-section">
                <div class="section-title">⭐ OVERALL EXPERIENCE</div>

                <div class="form-group">
                    <label class="question-label">1. Would you rent this facility again? *</label>
                    <div class="rating-options">
                        <div class="rating-option">
                            <input type="radio" id="overall_would_rent_again_yes" name="overall_would_rent_again" value="Yes" required>
                            <label for="overall_would_rent_again_yes">Yes</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="overall_would_rent_again_no" name="overall_would_rent_again" value="No">
                            <label for="overall_would_rent_again_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="question-label">2. Would you recommend this facility to others? *</label>
                    <div class="rating-options">
                        <div class="rating-option">
                            <input type="radio" id="overall_would_recommend_yes" name="overall_would_recommend" value="Yes" required>
                            <label for="overall_would_recommend_yes">Yes</label>
                        </div>
                        <div class="rating-option">
                            <input type="radio" id="overall_would_recommend_no" name="overall_would_recommend" value="No">
                            <label for="overall_would_recommend_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="question-label">How did you find out about this facility? *</label>
                    <select name="overall_how_found_facility" required>
                        <option value="">-- Select an option --</option>
                        <option value="Website">Website</option>
                        <option value="Brochure">Brochure</option>
                        <option value="Friend">Friend</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            </div>

            <!-- COMMENTS SECTION -->
            <div class="form-section">
                <div class="section-title">💬 COMMENTS/SUGGESTIONS</div>

                <div class="form-group">
                    <label class="question-label">Please share any comments or suggestions for improvement:</label>
                    <textarea name="comments_suggestions" placeholder="Your feedback is valuable to us..."></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn btn-reset">Clear Form</button>
                <button type="submit" class="btn btn-submit">Submit Survey</button>
            </div>

            <div class="loading" id="loadingIndicator">
                <div class="spinner"></div>
                <p>Submitting your survey...</p>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('surveyForm');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        // Add focus listeners to form groups
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('focus', function() {
                const group = this.closest('.form-group');
                if (group) group.classList.add('focused');
            });
            field.addEventListener('blur', function() {
                const group = this.closest('.form-group');
                if (group) group.classList.remove('focused');
            });
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate all required fields
            const requiredFields = form.querySelectorAll('[required]');
            let allFilled = true;

            requiredFields.forEach(field => {
                if (field.type === 'radio') {
                    const radioGroup = form.querySelector(`input[name="${field.name}"]:checked`);
                    if (!radioGroup) {
                        allFilled = false;
                    }
                } else if (!field.value) {
                    allFilled = false;
                }
            });

            if (!allFilled) {
                showError('Please fill in all required fields before submitting.');
                return;
            }

            const formData = new FormData(form);

            loadingIndicator.style.display = 'block';
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            try {
                const response = await fetch('<?= base_url("survey/submit") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred while submitting the survey. Please try again.');
            } finally {
                loadingIndicator.style.display = 'none';
            }
        });

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function showSuccess(message) {
            successMessage.textContent = message;
            successMessage.style.display = 'block';
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>
</html>
