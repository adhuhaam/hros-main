<?php
include '../db.php';
session_start();

$query = "INSERT IGNORE INTO medical_examinations (employee_id, medical_center_name, date_of_medical, status)
          SELECT emp_no, 'Not Assigned', CURDATE(), 'Pending'
          FROM employees
          WHERE employment_status = 'Active'
            AND nationality != 'MALDIVIAN'
            AND emp_no NOT IN (SELECT employee_id FROM medical_examinations)";

if ($conn->query($query)) {
    $_SESSION['success'] = "Sync completed. New employees added to DB, please check, if there is any issue contact System Admin.";
} else {
    $_SESSION['error'] = "Sync failed: " . $conn->error;
}

header("Location: index.php");
exit;
?>
