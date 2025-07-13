<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require '../db.php';

try {
    $sql = "SELECT DISTINCT visa_status FROM work_visa WHERE visa_status IS NOT NULL AND visa_status != '' ORDER BY visa_status";
    $result = $conn->query($sql);

    $statuses = [];
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row['visa_status'];
    }

    echo json_encode([
        "success" => true,
        "statuses" => $statuses
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
