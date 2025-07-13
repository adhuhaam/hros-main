<?php
include '../../db.php';

$task_id = $_POST['task_id'];
$new_status_id = $_POST['status_id'];

$stmt = $conn->prepare("UPDATE tasks SET status_id = ? WHERE id = ?");
$stmt->bind_param("ii", $new_status_id, $task_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
?>
