<?php
$connection = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the table (matching bookings table structure)
$sql = "CREATE TABLE IF NOT EXISTS booking_survey_responses (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    booking_id int NOT NULL,
    survey_token VARCHAR(64) NOT NULL UNIQUE KEY,
    staff_punctuality VARCHAR(50),
    staff_courtesy_property VARCHAR(50),
    staff_courtesy_audio VARCHAR(50),
    staff_courtesy_janitor VARCHAR(50),
    facility_level_expectations VARCHAR(50),
    facility_cleanliness VARCHAR(50),
    facility_maintenance VARCHAR(50),
    venue_accuracy_setup VARCHAR(50),
    venue_accuracy_space VARCHAR(50),
    catering_quality VARCHAR(50),
    catering_presentation VARCHAR(50),
    catering_service VARCHAR(50),
    overall_satisfaction VARCHAR(50),
    most_enjoyed LONGTEXT,
    improvements_needed LONGTEXT,
    recommendation VARCHAR(50),
    is_submitted TINYINT(1) DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY booking_id (booking_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

if (mysqli_query($connection, $sql)) {
    echo "✓ booking_survey_responses table created successfully!\n";
    
    // Also record the migration
    $migration_sql = "INSERT IGNORE INTO migrations (version, class, `group`, namespace, time, batch) VALUES ('2025-11-27-000001', 'CreateBookingSurveyResponsesTable', 'default', 'App\\\\Database\\\\Migrations', " . time() . ", 1)";
    if (mysqli_query($connection, $migration_sql)) {
        echo "✓ Migration record created!\n";
    }
} else {
    echo "Error: " . mysqli_error($connection) . "\n";
}

mysqli_close($connection);
?>
