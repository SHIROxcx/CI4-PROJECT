<?php
$connection = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check bookings table structure
$sql = "SHOW CREATE TABLE bookings";
$result = mysqli_query($connection, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Bookings table structure:\n";
    echo $row['Create Table'];
    echo "\n\n";
}

mysqli_close($connection);
?>
