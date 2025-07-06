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

$emp_no = $data['emp_no'] ?? '';
$problem = $data['problem'] ?? '';
$employee_statement = $data['employee_statement'] ?? '';

if (empty($emp_no) || empty($problem) || empty($employee_statement)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Employee number, problem, and employee statement are required.']);
    exit;
}

try {
    $query = "INSERT INTO warnings (emp_no, problem, employee_statement, status) VALUES (?, ?, ?, 'Pending HOD Review')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $emp_no, $problem, $employee_statement);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Warning added successfully']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to add warning.', 'error' => $e->getMessage()]);
}
?>
