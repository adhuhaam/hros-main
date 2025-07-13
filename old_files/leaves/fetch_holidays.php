<?php
include '../db.php';

$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y'); // Get requested year or default to current year

$holidays = [];
$query = $conn->prepare("SELECT holiday_date FROM holidays WHERE YEAR(holiday_date) = ?");
$query->bind_param("i", $year);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $holidays[] = $row['holiday_date']; // Fetch holiday dates
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['holidays' => $holidays]);
?>
