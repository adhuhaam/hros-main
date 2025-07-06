<?php
header('Content-Type: application/json');
require '../db.php'; // adjust path as needed

// Define all possible statuses to ensure zeroes are returned even if some are missing
$statuses = ['Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'];

// Initialize response array
$response = [
    'success' => true,
    'data' => array_fill_keys($statuses, 0)
];

try {
    $stmt = $conn->query("SELECT employment_status, COUNT(*) as count FROM employees GROUP BY employment_status");

    while ($row = $stmt->fetch_assoc()) {
        $status = $row['employment_status'];
        if (in_array($status, $statuses)) {
            $response['data'][$status] = (int)$row['count'];
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Query error: ' . $e->getMessage()
    ]);
}
?>
