<?php
// Database configuration - use environment variables for security
// Set these in your .env file or server environment

// Main HRMS database configuration
$db_config = [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'username' => $_ENV['DB_USERNAME'] ?? 'rccmgvfd_hros_user',
    'password' => $_ENV['DB_PASSWORD'] ?? 'Ompl@65482*',
    'database' => $_ENV['DB_NAME'] ?? 'rccmgvfd_hros',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

// Recruit database configuration
$recruit_db_config = [
    'host' => $_ENV['RECRUIT_DB_HOST'] ?? 'localhost',
    'username' => $_ENV['RECRUIT_DB_USERNAME'] ?? 'rccmgvfd_recruit_user',
    'password' => $_ENV['RECRUIT_DB_PASSWORD'] ?? 'Ompl@65482*',
    'database' => $_ENV['RECRUIT_DB_NAME'] ?? 'rccmgvfd_recruit',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

// Function to create database connection with error handling
function createDatabaseConnection($config) {
    try {
        $conn = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            throw new Exception("Database connection failed");
        }
        
        // Set charset
        $conn->set_charset($config['charset']);
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw $e;
    }
}

// Create main database connection
try {
    $conn = createDatabaseConnection($db_config);
} catch (Exception $e) {
    // Log error and redirect to error page
    header("Location: error.php?message=" . urlencode("Database connection failed"));
    exit();
}
?>