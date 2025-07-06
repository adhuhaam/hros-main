<?php
include '../db.php';

$id = $_POST['id'];

if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'Task ID missing']);
  exit;
}

$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
?>
