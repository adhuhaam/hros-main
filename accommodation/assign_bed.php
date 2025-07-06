<?php
include '../db.php';

$bed_id = $_POST['bed_id'] ?? 0;
$emp_no = $_POST['emp_no'] ?? '';

if (!$bed_id || !$emp_no) {
    echo "Missing bed or employee";
    exit;
}

// Check if employee already has a bed
$check = $conn->prepare("SELECT id FROM accommodation_beds WHERE occupied_by = ?");
$check->bind_param("s", $emp_no);
$check->execute();
$checkResult = $check->get_result();
if ($checkResult->num_rows > 0) {
    echo "This employee is already assigned to another bed.";
    exit;
}

$stmt = $conn->prepare("UPDATE accommodation_beds SET occupied_by = ?, assigned_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $emp_no, $bed_id);

if ($stmt->execute()) {
    echo "Assigned successfully!";
} else {
    echo "Error assigning bed.";
}
