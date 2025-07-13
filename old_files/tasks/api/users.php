<?php
include '../db.php'; // Correct path now

$users = [];
$result = $conn->query("SELECT id, staff_name FROM users ORDER BY staff_name ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['staff_name']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($users);
?>
