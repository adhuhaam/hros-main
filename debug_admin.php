<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...<br>";

// Test database connection
echo "Testing database connection...<br>";
try {
    include 'db.php';
    echo "Database connection successful<br>";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}

// Test session
echo "Testing session...<br>";
try {
    include 'session.php';
    echo "Session check passed<br>";
} catch (Exception $e) {
    echo "Session error: " . $e->getMessage() . "<br>";
}

// Test sidebar include
echo "Testing sidebar include...<br>";
try {
    ob_start();
    include 'sidebar.php';
    $sidebar_content = ob_get_clean();
    echo "Sidebar include successful<br>";
} catch (Exception $e) {
    echo "Sidebar error: " . $e->getMessage() . "<br>";
}

// Test header include
echo "Testing header include...<br>";
try {
    ob_start();
    include 'header.php';
    $header_content = ob_get_clean();
    echo "Header include successful<br>";
} catch (Exception $e) {
    echo "Header error: " . $e->getMessage() . "<br>";
}

echo "Debug complete!";
?>