<?php
include '../db.php';

$query = $_GET['query'];
$result = $conn->query("SELECT emp_no, name FROM employees WHERE emp_no LIKE '%$query%' OR name LIKE '%$query%'");

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = [
        'emp_no' => $row['emp_no'],
        'name' => $row['name']
    ];
}

echo json_encode($employees);
?>
