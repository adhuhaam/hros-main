<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test 1: Check if database config file exists
echo "<h2>Test 1: Database Config File</h2>";
if (file_exists('config/database.php')) {
    echo "✅ config/database.php exists<br>";
} else {
    echo "❌ config/database.php not found<br>";
}

// Test 2: Try to include database config
echo "<h2>Test 2: Include Database Config</h2>";
try {
    include 'config/database.php';
    echo "✅ Database config included successfully<br>";
} catch (Exception $e) {
    echo "❌ Error including database config: " . $e->getMessage() . "<br>";
}

// Test 3: Check if $conn variable exists
echo "<h2>Test 3: Database Connection Variable</h2>";
if (isset($conn)) {
    echo "✅ \$conn variable exists<br>";
    
    // Test 4: Test database connection
    echo "<h2>Test 4: Database Connection</h2>";
    if ($conn->ping()) {
        echo "✅ Database connection is working<br>";
        
        // Test 5: Test a simple query
        echo "<h2>Test 5: Simple Query Test</h2>";
        try {
            $result = $conn->query("SELECT 1 as test");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "✅ Simple query successful: " . $row['test'] . "<br>";
            } else {
                echo "❌ Simple query failed<br>";
            }
        } catch (Exception $e) {
            echo "❌ Query error: " . $e->getMessage() . "<br>";
        }
        
        // Test 6: Check if employees table exists
        echo "<h2>Test 6: Employees Table</h2>";
        try {
            $result = $conn->query("SHOW TABLES LIKE 'employees'");
            if ($result && $result->num_rows > 0) {
                echo "✅ employees table exists<br>";
                
                // Test 7: Count employees
                $countResult = $conn->query("SELECT COUNT(*) as total FROM employees");
                if ($countResult) {
                    $count = $countResult->fetch_assoc()['total'];
                    echo "✅ Total employees: " . $count . "<br>";
                } else {
                    echo "❌ Could not count employees<br>";
                }
            } else {
                echo "❌ employees table does not exist<br>";
            }
        } catch (Exception $e) {
            echo "❌ Table check error: " . $e->getMessage() . "<br>";
        }
        
    } else {
        echo "❌ Database connection failed<br>";
    }
} else {
    echo "❌ \$conn variable not found<br>";
}

// Test 8: Check environment variables
echo "<h2>Test 8: Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_USERNAME', 'DB_PASSWORD', 'DB_NAME'];
foreach ($env_vars as $var) {
    if (isset($_ENV[$var])) {
        echo "✅ $var is set<br>";
    } else {
        echo "❌ $var is not set<br>";
    }
}

// Test 9: Check session
echo "<h2>Test 9: Session Test</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ Session user_id: " . $_SESSION['user_id'] . "<br>";
} else {
    echo "❌ No user_id in session<br>";
}

if (isset($_SESSION['role'])) {
    echo "✅ Session role: " . $_SESSION['role'] . "<br>";
} else {
    echo "❌ No role in session<br>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='admin_dashboard.php'>Try Admin Dashboard</a></p>";
echo "<p><a href='test_admin_dashboard.php'>Try Test Admin Dashboard</a></p>";
?>