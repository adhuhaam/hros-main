<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Dashboard Test</h1>";

// Test database connection
echo "<h2>Testing Database Connection</h2>";
try {
    include 'db.php';
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test session
echo "<h2>Testing Session</h2>";
try {
    session_start();
    echo "<p style='color: green;'>✓ Session started successfully</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Session error: " . $e->getMessage() . "</p>";
}

// Test sidebar include
echo "<h2>Testing Sidebar Include</h2>";
try {
    ob_start();
    include 'sidebar.php';
    $sidebar_content = ob_get_clean();
    echo "<p style='color: green;'>✓ Sidebar include successful</p>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h3>Sidebar Content Preview:</h3>";
    echo htmlspecialchars(substr($sidebar_content, 0, 500)) . "...";
    echo "</div>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Sidebar error: " . $e->getMessage() . "</p>";
}

// Test header include
echo "<h2>Testing Header Include</h2>";
try {
    ob_start();
    include 'header.php';
    $header_content = ob_get_clean();
    echo "<p style='color: green;'>✓ Header include successful</p>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h3>Header Content Preview:</h3>";
    echo htmlspecialchars(substr($header_content, 0, 500)) . "...";
    echo "</div>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Header error: " . $e->getMessage() . "</p>";
}

// Test CSS and JS files
echo "<h2>Testing Asset Files</h2>";
$assets_to_test = [
    'assets/css/styles.min.css',
    'assets/js/app.min.js',
    'assets/js/sidebarmenu.js',
    'assets/libs/jquery/dist/jquery.min.js',
    'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'
];

foreach ($assets_to_test as $asset) {
    if (file_exists($asset)) {
        echo "<p style='color: green;'>✓ " . $asset . " exists</p>";
    } else {
        echo "<p style='color: red;'>✗ " . $asset . " missing</p>";
    }
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='admin_dashboard.php'>Try the main admin dashboard</a></p>";
echo "<p><a href='admin_dashboard_fixed.php'>Try the fixed admin dashboard</a></p>";
?>