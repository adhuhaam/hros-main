<?php
include '../db.php';

$title = $_POST['title'];
$description = $_POST['description'];
$status_id = $_POST['status_id'];
$assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
$due_date = $_POST['due_date'];
$priority = $_POST['priority'];
$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

$stmt = $conn->prepare("INSERT INTO tasks (title, description, status_id, assigned_to, due_date, priority, category_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiissi", $title, $description, $status_id, $assigned_to, $due_date, $priority, $category_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
?>
