<?php
// Check the latest bookings and their files

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Models/BookingModel.php';

$db = \Config\Database::connect();

// Get last 5 bookings
$recentBookings = $db->table('bookings')
    ->orderBy('id', 'DESC')
    ->limit(5)
    ->get()
    ->getResultArray();

echo "=== RECENT BOOKINGS ===\n";
foreach ($recentBookings as $booking) {
    echo "\nBooking ID: {$booking['id']}, Type: {$booking['booking_type']}, Status: {$booking['booking_status']}\n";
    
    // Check files for this booking
    $files = $db->table('student_booking_files')
        ->where('booking_id', $booking['id'])
        ->get()
        ->getResultArray();
    
    echo "  Files: " . count($files) . "\n";
    
    foreach ($files as $file) {
        echo "    - {$file['file_type']}: {$file['original_filename']} (ID: {$file['id']})\n";
        echo "      Path: {$file['file_path']}\n";
        echo "      Exists: " . (file_exists($file['file_path']) ? "YES" : "NO") . "\n";
    }
}
?>
