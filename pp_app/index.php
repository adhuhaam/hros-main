<?php
// hros.rccmaldives.com/pp_app/index.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include DB connection
require '../db.php'; // Adjust this path if needed

// Parse JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate input
$emp_no = $input['emp_no'] ?? '';
$passport_nic_no = $input['passport_nic_no'] ?? '';
$passport_nic_no_expires = $input['passport_nic_no_expires'] ?? '';

if (empty($emp_no) || empty($passport_nic_no) || empty($passport_nic_no_expires)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields: emp_no, passport_nic_no, or passport_nic_no_expires"
    ]);
    exit;
}

// Sanitize input
$emp_no = trim($emp_no);
$passport_nic_no = trim($passport_nic_no);
$passport_nic_no_expires = trim($passport_nic_no_expires);

// Prepare and execute SQL update
$stmt = $conn->prepare("
    UPDATE employees 
    SET passport_nic_no = ?, 
        passport_nic_no_expires = ? 
    WHERE emp_no = ?
");

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare SQL statement"
    ]);
    exit;
}

$stmt->bind_param("sss", $passport_nic_no, $passport_nic_no_expires, $emp_no);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Passport information updated for employee $emp_no"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No employee found with emp_no: $emp_no or data is unchanged"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Database update failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
