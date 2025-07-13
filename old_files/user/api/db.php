<?php
// Database Configuration
$servername = "localhost"; // Database host
$username = "rccmgvfd_hros_user"; // Database username
$password = "Ompl@65482*"; // Database password
$database = "rccmgvfd_hros"; // Database name

// Establish Database Connection
$conn = new mysqli($servername, $username, $password, $database);

// Check for Connection Errors
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Set Character Set for the Connection
$conn->set_charset("utf8mb4");
?>
