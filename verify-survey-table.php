<?php
$connection = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if table exists
$sql = "SHOW TABLES LIKE 'booking_survey_responses'";
$result = mysqli_query($connection, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "✓ booking_survey_responses table exists!\n";
    echo "✓ Survey system is ready to use!\n";
} else {
    echo "✗ Table does not exist\n";
}

mysqli_close($connection);
?>
