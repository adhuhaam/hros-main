<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require '../db.php';

$emp_no = $_POST['emp_no'] ?? '';
$visa_number = $_POST['visa_number'] ?? null;
$visa_issue_date = $_POST['visa_issue_date'] ?? null;
$visa_expiry_date = $_POST['visa_expiry_date'] ?? null;
$visa_status = $_POST['visa_status'] ?? null;

// Allowed statuses from the app
$allowed_statuses = [
    "Expiring Soon", "Pending", "Pending Approval", 
    "Ready for Submission", "Ready for Collection", 
    "Completed", "Expired"
];

// Validate emp_no
if (empty($emp_no)) {
    echo json_encode(["success" => false, "message" => "Employee number is required."]);
    exit;
}

// Validate visa_status if provided
if (!is_null($visa_status) && $visa_status !== "" && !in_array($visa_status, $allowed_statuses)) {
    echo json_encode(["success" => false, "message" => "Invalid visa status value."]);
    exit;
}

// Convert empty strings to NULL
$visa_number = $visa_number !== "" ? $visa_number : null;
$visa_issue_date = $visa_issue_date !== "" ? $visa_issue_date : null;
$visa_expiry_date = $visa_expiry_date !== "" ? $visa_expiry_date : null;
$visa_status = $visa_status !== "" ? $visa_status : null;

// Check if visa record exists
$stmt = $conn->prepare("SELECT id FROM work_visa WHERE emp_no = ?");
$stmt->bind_param("s", $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $stmt = $conn->prepare("
        UPDATE work_visa 
        SET visa_number = ?, visa_issue_date = ?, visa_expiry_date = ?, visa_status = ? 
        WHERE emp_no = ?
    ");
    $stmt->bind_param("sssss", $visa_number, $visa_issue_date, $visa_expiry_date, $visa_status, $emp_no);
} else {
    // Insert new record
    $stmt = $conn->prepare("
        INSERT INTO work_visa (emp_no, visa_number, visa_issue_date, visa_expiry_date, visa_status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $emp_no, $visa_number, $visa_issue_date, $visa_expiry_date, $visa_status);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Visa details updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}
?>
