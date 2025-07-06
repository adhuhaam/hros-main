<?php
include '../db.php';

$term = $_GET['term'] ?? '';

$stmt = $conn->prepare("SELECT emp_no, name FROM employees WHERE emp_no LIKE ? OR name LIKE ? LIMIT 10");
$like = "%$term%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        "label" => $row['emp_no'] . " - " . $row['name'],
        "value" => $row['emp_no']
    ];
}

echo json_encode($data);
