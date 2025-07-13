<?php
include '../db.php';
$result = $conn->query("SELECT id, name FROM task_categories ORDER BY name ASC");
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
echo json_encode($categories);
?>
