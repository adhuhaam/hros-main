<?php
include '../db.php';

$project_id = $_GET['project_id'];
$employee_id = $_GET['employee_id'];

$conn->query("DELETE FROM employee_project_allocations WHERE project_id = $project_id AND employee_id = '$employee_id'");

header("Location: index.php");
exit();
?>
