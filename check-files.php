<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Database.php';

$db = \Config\Database::connect();

// Check files for booking 165
$result = $db->table('student_booking_files')
    ->where('booking_id', 165)
    ->get()
    ->getResultArray();

echo "Files for booking 165:\n";
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "\n";
?>
