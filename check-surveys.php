<?php
$connection = mysqli_connect('localhost', 'Shiro', '', 'capsdb');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check surveys
$sql = "SELECT id, booking_id, survey_token, is_submitted FROM booking_survey_responses ORDER BY id DESC LIMIT 5";
$result = mysqli_query($connection, $sql);

echo "Recent surveys:\n";
echo "==============\n";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['id']}, Booking: {$row['booking_id']}, Token: {$row['survey_token']}, Submitted: {$row['is_submitted']}\n";
    }
} else {
    echo "No surveys found\n";
}

mysqli_close($connection);
?>
