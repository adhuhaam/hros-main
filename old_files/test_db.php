<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test environment variables
echo "<h2>Environment Variables</h2>";
$env_vars = [
    'DB_HOST',
    'DB_USERNAME', 
    'DB_PASSWORD',
    'DB_NAME'
];

foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    echo "<p><strong>$var:</strong> " . ($var === 'DB_PASSWORD' ? '***HIDDEN***' : $value) . "</p>";
}

// Test database configuration file
echo "<h2>Database Configuration</h2>";
try {
    include 'config/database.php';
    echo "<p style='color: green;'>✓ Database configuration loaded</p>";
    
    // Test connection
    if (isset($conn) && $conn instanceof mysqli) {
        echo "<p style='color: green;'>✓ Database connection object created</p>";
        
        // Test a simple query
        $result = $conn->query("SELECT 1 as test");
        if ($result) {
            echo "<p style='color: green;'>✓ Database query test successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Database query test failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection object not created</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database configuration error: " . $e->getMessage() . "</p>";
}

// Test employees table
echo "<h2>Employees Table Test</h2>";
try {
    if (isset($conn)) {
        $result = $conn->query("SHOW TABLES LIKE 'employees'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Employees table exists</p>";
            
            // Test count query
            $count_result = $conn->query("SELECT COUNT(*) as total FROM employees");
            if ($count_result) {
                $count = $count_result->fetch_assoc()['total'];
                echo "<p>Total employees: $count</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Employees table does not exist</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Employees table test error: " . $e->getMessage() . "</p>";
}

// Test card_print table
echo "<h2>Card Print Table Test</h2>";
try {
    if (isset($conn)) {
        $result = $conn->query("SHOW TABLES LIKE 'card_print'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Card print table exists</p>";
        } else {
            echo "<p style='color: red;'>✗ Card print table does not exist</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Card print table test error: " . $e->getMessage() . "</p>";
}

echo "<h2>Test Complete</h2>";
?>