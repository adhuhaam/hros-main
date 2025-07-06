<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Main HRMS database connection
$conn = new mysqli("localhost", "rccmgvfd_hros_user", "Ompl@65482*", "rccmgvfd_hros");

// Recruit database connection
$connRecruit = new mysqli("localhost", "rccmgvfd_recruit_user", "Ompl@65482*", "rccmgvfd_recruit");

// Check connection
if ($conn->connect_error) {
    die("HRMS DB Connection failed: " . $conn->connect_error);
}
if ($connRecruit->connect_error) {
    die("Recruit DB Connection failed: " . $connRecruit->connect_error);
}
?>
