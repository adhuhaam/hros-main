<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? '';
$statement = $data['statement'] ?? '';
$role = $data['role'] ?? '';

if (empty($id) || empty($statement) || empty($role)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID, statement, and role are required.']);
    exit;
}

try {
    $nextStatus = '';
    $column = '';

    // Determine the column to update and the next status
    switch ($role) {
        case 'HOD':
            $nextStatus = 'Pending HRM Review';
            $column = 'hod_statement';
            break;
        case 'HRM':
            $nextStatus = 'Pending Director Review';
            $column = 'hrm_statement';
            break;
        case 'Director':
            $nextStatus = 'Resolved';
            $column = 'management_comment';
            break;
        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid role.']);
            exit;
    }

    // Update the warning in the database
    $query = "UPDATE warnings SET $column = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $statement, $nextStatus, $id);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Warning updated successfully']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to update warning.', 'error' => $e->getMessage()]);
}
?>
