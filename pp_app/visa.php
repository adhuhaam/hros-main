<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include '../db.php';

$emp_no = $_GET['emp_no'] ?? '';

if (empty($emp_no)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing emp_no"
    ]);
    exit;
}

// 1. Get employee's visa details
$stmt = $conn->prepare("
    SELECT visa_number, visa_issue_date, visa_expiry_date, visa_status 
    FROM work_visa 
    WHERE emp_no = ?
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Employee not found"
    ]);
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// 2. Get ENUM values from visa_status field
$enumResult = $conn->query("SHOW COLUMNS FROM work_visa LIKE 'visa_status'");
$enumRow = $enumResult->fetch_assoc();

$enumValues = [];
if (preg_match("/^enum\((.*)\)$/", $enumRow['Type'], $matches)) {
    $vals = explode(",", $matches[1]);
    foreach ($vals as $val) {
        $enumValues[] = trim($val, "'");
    }
}

// Return both employee visa data and enum options
echo json_encode([
    "success" => true,
    "data" => $data,
    "status_options" => $enumValues
]);
?>
