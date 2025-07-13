<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

// Get the role of the user (HOD, HRM, or Director)
$role = $_GET['role'] ?? '';

if (empty($role)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Role is required.']);
    exit;
}

try {
    $status = '';

    // Determine the status to filter warnings based on the role
    switch ($role) {
        case 'HOD':
            $status = 'Pending HOD Review';
            break;
        case 'HRM':
            $status = 'Pending HRM Review';
            break;
        case 'Director':
            $status = 'Pending Director Review';
            break;
        default:
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Invalid role specified. Allowed roles are: HOD, HRM, Director.']);
            exit;
    }

    // Add pagination support
    $limit = $_GET['limit'] ?? 10; // Default limit is 10
    $offset = $_GET['offset'] ?? 0; // Default offset is 0

    // Fetch warnings with the specified status
    $query = "SELECT * FROM warnings WHERE status = ? LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sii', $status, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'success', 'data' => [], 'message' => 'No warnings found for the specified role.']);
        exit;
    }

    $warnings = [];
    while ($row = $result->fetch_assoc()) {
        $warnings[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $warnings]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch warnings due to a server error.']);
}
?>
