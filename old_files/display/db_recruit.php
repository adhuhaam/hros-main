
<?php
// db_recruit.php - Secondary database connection
$host = 'localhost';
$user = 'rccmgvfd_recruit_user';
$password = 'Ompl@65482*';
$dbname = 'rccmgvfd_recruit';

// Create connection
$conn_recruit = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn_recruit->connect_error) {
    die("Recruit DB Connection failed: " . $conn_recruit->connect_error);
}
?>
