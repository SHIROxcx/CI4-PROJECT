<?php
// Test script to check booking files

// Load CodeIgniter
require_once FCPATH . 'index.php';

$db = \Config\Database::connect();

// Get recent bookings with file counts
$query = $db->table('bookings b')
    ->select('b.id, 
             b.client_name, 
             b.booking_type, 
             b.created_at,
             (SELECT COUNT(*) FROM booking_files bf WHERE bf.booking_id = b.id) as user_files_count,
             (SELECT COUNT(*) FROM student_booking_files sbf WHERE sbf.booking_id = b.id) as student_files_count')
    ->orderBy('b.id', 'DESC')
    ->limit(15)
    ->get()
    ->getResultArray();

echo "<!DOCTYPE html><html><body style='font-family:monospace'>";
echo "<h2>Recent Bookings with File Counts</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Client Name</th><th>Type</th><th>User Files</th><th>Student Files</th><th>Total</th><th>Created</th></tr>";

foreach ($query as $row) {
    $total = ($row['booking_type'] === 'student' || $row['booking_type'] === 'faculty') 
        ? $row['student_files_count'] 
        : $row['user_files_count'];
    
    echo "<tr>";
    echo "<td>#{$row['id']}</td>";
    echo "<td>{$row['client_name']}</td>";
    echo "<td>{$row['booking_type']}</td>";
    echo "<td>{$row['user_files_count']}</td>";
    echo "<td>{$row['student_files_count']}</td>";
    echo "<td><strong>$total</strong></td>";
    echo "<td>{$row['created_at']}</td>";
    echo "</tr>";
}

echo "</table>";

// Also check specific booking 120
echo "<h3>Booking #120 Details</h3>";
$booking120 = $db->table('bookings')->where('id', 120)->get()->getRowArray();
if ($booking120) {
    echo "<pre>";
    print_r($booking120);
    echo "</pre>";
    
    // Check for files
    $files = $db->table('student_booking_files')->where('booking_id', 120)->get()->getResultArray();
    echo "<h4>Student Booking Files for #120:</h4>";
    echo "<pre>";
    print_r($files);
    echo "</pre>";
} else {
    echo "Booking #120 not found";
}

echo "</body></html>";
