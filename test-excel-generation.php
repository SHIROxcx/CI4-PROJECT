<?php
// Test script to generate Excel survey file
require 'vendor/autoload.php';

// Manually include the helper since autoloader isn't picking it up
require 'app/Helpers/ExcelSurveyGenerator.php';

use App\Helpers\ExcelSurveyGenerator;

// Test sample data
$booking = [
    'id' => 1,
    'event_name' => 'Corporate Event',
    'created_at' => date('Y-m-d')
];

$surveyData = [
    'staff_punctuality' => 'Excellent',
    'staff_courtesy_property' => 'Very Good',
    'staff_courtesy_audio' => 'Good',
    'staff_courtesy_janitor' => 'Very Good',
    'facility_level_expectations' => 'Excellent',
    'facility_cleanliness' => 'Good',
    'facility_maintenance' => 'Very Good',
    'venue_accuracy_setup' => 'Website',
    'overall_satisfaction' => 'Yes',
    'most_enjoyed' => 'The staff was very helpful and courteous.',
    'improvements_needed' => 'Could improve the lighting.',
    'recommendation' => 'Yes'
];

$uploadDir = __DIR__ . '/writable/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = $uploadDir . 'test_evaluation_' . time() . '.xlsx';

try {
    ExcelSurveyGenerator::generateEvaluationForm($booking, $surveyData, $filename);
    echo "SUCCESS: Excel file generated\n";
    echo "File: " . $filename . "\n";
    echo "Size: " . filesize($filename) . " bytes\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
