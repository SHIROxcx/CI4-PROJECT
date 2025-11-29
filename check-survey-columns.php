<?php
$connection = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get table columns
$sql = "DESCRIBE booking_survey_responses";
$result = mysqli_query($connection, $sql);

echo "booking_survey_responses columns:\n";
echo "==================================\n";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "No columns found\n";
}

mysqli_close($connection);
?>
