<?php
include '../db.php';

$id = $_POST['id'] ?? null;
$emp_no = $_POST['emp_no'] ?? null;
$visa_expiry_date = $_POST['visa_expiry_date'] ?? null;
$visa_status = $_POST['visa_status'] ?? null;
$passport_nic_no = $_POST['passport_nic_no'] ?? null;
$passport_nic_no_expires = $_POST['passport_nic_no_expires'] ?? null;

$allowed = ['Pending','Pending Approval','Ready for Submission','Ready for Collection','Completed'];

if (!$id || !$emp_no || !$visa_status || !in_array($visa_status, $allowed)) {
    die('Missing or invalid data');
}

// Update visa_sticker
$vs = $conn->prepare("UPDATE visa_sticker SET visa_expiry_date = ?, visa_status = ? WHERE id = ?");
$vs->bind_param('ssi', $visa_expiry_date, $visa_status, $id);
$vs->execute();

// Update employees
$emp = $conn->prepare("UPDATE employees SET passport_nic_no = ?, passport_nic_no_expires = ? WHERE emp_no = ?");
$emp->bind_param('sss', $passport_nic_no, $passport_nic_no_expires, $emp_no);
$emp->execute();

header("Location: index.php?success=1");
exit;
