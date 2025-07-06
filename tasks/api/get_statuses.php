<?php
include '../../db.php';

$result = $conn->query("SELECT * FROM task_statuses ORDER BY id ASC");
$statuses = [];

while ($row = $result->fetch_assoc()) {
    $statuses[] = $row;
}

echo json_encode($statuses);
?>
