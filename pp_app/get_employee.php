<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require '../db.php';

$emp_no = $_GET['emp_no'] ?? '';

$stmt = $conn->prepare("SELECT emp_no, name, passport_nic_no, passport_nic_no_expires FROM employees WHERE emp_no = ?");
$stmt->bind_param("s", $emp_no);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Employee not found"
    ]);
}
