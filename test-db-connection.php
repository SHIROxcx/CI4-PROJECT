<?php

// Load CodeIgniter bootstrap
require_once __DIR__ . '/public/index.php' === 'index.php' ? 'public/index.php' : 'public/index.php';

// This will test if the database connection works
try {
    $db = \Config\Database::connect();
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful!',
        'database' => $db->getDatabase(),
        'driver' => $db->DBDriver,
    ]);
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed!',
        'error' => $e->getMessage(),
    ]);
}
?>
