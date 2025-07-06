<?php
include '../db.php';

$sql = "SELECT t.*, 
               s.name AS status_name, 
               u.staff_name AS assignee_name, 
               c.name AS category_name
        FROM tasks t
        LEFT JOIN task_statuses s ON t.status_id = s.id
        LEFT JOIN users u ON t.assigned_to = u.id
        LEFT JOIN task_categories c ON t.category_id = c.id
        ORDER BY t.status_id, t.id DESC";

$result = $conn->query($sql);
$tasks = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($tasks);
?>
