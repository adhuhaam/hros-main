<?php
$servername = "localhost";
$username = "rccmgvfd_hros_user";
$password = "Ompl@65482*";
$database = "rccmgvfd_hros";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
