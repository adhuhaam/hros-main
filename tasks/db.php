<?php

date_default_timezone_set('Indian/Maldives'); // Set Maldivian Timezone


$servername = "localhost"; // Replace with your server name
$username = "rccmgvfd_hros_user";
$password = "Ompl@65482*";
$database = "rccmgvfd_hros";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // Redirect to an error page if the connection fails
    header("Location: error.php?message=" . urlencode("Database connection failed: " . $conn->connect_error));
    exit();
}
?>
