<?php
require_once __DIR__ . '/app/Config/boot.php';

use Config\Database;

$db = Database::connect();

$sql = "CREATE TABLE IF NOT EXISTS booking_survey_responses (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    booking_id INT(11) UNSIGNED NOT NULL,
    survey_token VARCHAR(255) NOT NULL UNIQUE,
    response_data LONGTEXT,
    is_submitted TINYINT(1) DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    CONSTRAINT pk_booking_survey_responses PRIMARY KEY(id),
    KEY booking_id (booking_id),
    KEY survey_token (survey_token),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    $db->query($sql);
    echo "✓ booking_survey_responses table created successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
