<?php
// Quick test to check database records
require_once FCPATH . 'index.php';

$db = \Config\Database::connect();

echo "<h2>Checking All Bookings with File Counts</h2>";
echo "<pre>";

// Get all bookings with file counts
$query = "
SELECT 
    b.id,
    b.client_name,
    b.booking_type,
    b.created_at,
    (SELECT COUNT(*) FROM booking_files bf WHERE bf.booking_id = b.id) as user_files_count,
    (SELECT COUNT(*) FROM student_booking_files sbf WHERE sbf.booking_id = b.id) as student_files_count
FROM bookings b
ORDER BY b.id DESC
LIMIT 20
";

$results = $db->query($query)->getResultArray();

foreach ($results as $row) {
    $total = ($row['booking_type'] === 'student' || $row['booking_type'] === 'faculty')
        ? $row['student_files_count']
        : $row['user_files_count'];
    
    echo "Booking #{$row['id']}: {$row['client_name']} ({$row['booking_type']}) - Student Files: {$row['student_files_count']}, User Files: {$row['user_files_count']}, Total: $total\n";
}

echo "</pre>";

// Check the actual student_booking_files table
echo "<h2>Student Booking Files Table</h2>";
echo "<pre>";
$files = $db->table('student_booking_files')->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
foreach ($files as $file) {
    echo "File ID #{$file['id']}: Booking #{$file['booking_id']}, Type: {$file['file_type']}, Name: {$file['original_filename']}\n";
}
echo "</pre>";
?>
