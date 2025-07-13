<?php
include '../db.php';

$bed_id = $_POST['bed_id'];
$emp_no = $_POST['emp_no'];

// Optional: check if emp_no exists in employees table
$check = $conn->query("SELECT emp_no FROM employees WHERE emp_no = '$emp_no'");
if ($check->num_rows === 0) {
    echo "Invalid employee number.";
    exit;
}

$conn->query("UPDATE accommodation_beds SET occupied_by = '$emp_no', assigned_at = NOW() WHERE id = $bed_id");

echo "Assigned successfully";
