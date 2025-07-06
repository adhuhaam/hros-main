<?php
// Include database connection
include 'db.php';

header('Content-Type: application/json');

try {
    // Query to fetch company holidays
    $query = "SELECT holiday_date FROM holidays";
    $result = $conn->query($query);

    $holidays = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $holidays[] = $row['holiday_date'];
        }
    }

    echo json_encode($holidays);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch company holidays.']);
}
?>
