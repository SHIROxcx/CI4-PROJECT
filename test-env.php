<?php
// Quick test to verify environment is loaded
echo "Environment Test\n";
echo "================\n";
echo "CI_ENVIRONMENT: " . getenv('CI_ENVIRONMENT') . "\n";
echo "database.hostname: " . getenv('database.hostname') . "\n";
echo "database.username: " . getenv('database.username') . "\n";
echo "database.database: " . getenv('database.database') . "\n";
echo "\nenv() function test:\n";
echo "env('database.hostname'): " . env('database.hostname', 'NOT SET') . "\n";
echo "env('database.username'): " . env('database.username', 'NOT SET') . "\n";
echo "env('database.database'): " . env('database.database', 'NOT SET') . "\n";
?>
