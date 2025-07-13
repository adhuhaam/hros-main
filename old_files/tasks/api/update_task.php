



<?php
include '../db.php';

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$status_id = $_POST['status_id'];
$assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
$due_date = $_POST['due_date'];
$priority = $_POST['priority'];
$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;



// Validate
if (empty($id) || empty($title) || empty($status_id) || empty($priority)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}


$stmt = $conn->prepare("UPDATE tasks 
                        SET title = ?, description = ?, status_id = ?, assigned_to = ?, due_date = ?, priority = ?, category_id = ?
                        WHERE id = ?");
$stmt->bind_param("ssiissii", $title, $description, $status_id, $assigned_to, $due_date, $priority, $category_id, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
?>
