<?php
// Check current booking types
require_once 'vendor/autoload.php';

$db = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check current values
$sql = "SELECT DISTINCT booking_type, COUNT(*) as count FROM bookings GROUP BY booking_type";
$result = mysqli_query($db, $sql);

echo "Current booking_type values in database:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "  - '" . $row['booking_type'] . "': " . $row['count'] . " records\n";
}

// Check the column definition
$sql2 = "SHOW COLUMNS FROM bookings WHERE Field = 'booking_type'";
$result2 = mysqli_query($db, $sql2);
echo "\nCurrent column definition:\n";
while ($row = mysqli_fetch_assoc($result2)) {
    echo "  Type: " . $row['Type'] . "\n";
    echo "  Null: " . $row['Null'] . "\n";
    echo "  Default: " . $row['Default'] . "\n";
}

mysqli_close($db);
?>
