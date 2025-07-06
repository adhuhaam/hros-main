<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include_once '../db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all projects
    try {
        $query = "SELECT id, name, description, started_date, end_date, status, images, created_at 
                  FROM projects 
                  ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $projects = [];

            while ($row = $result->fetch_assoc()) {
                // Decode the JSON-encoded images
                $row['images'] = $row['images'] ? json_decode($row['images'], true) : [];
                $projects[] = $row;
            }

            echo json_encode(['status' => 'success', 'data' => $projects]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch projects.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'An unexpected error occurred while fetching projects.',
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
