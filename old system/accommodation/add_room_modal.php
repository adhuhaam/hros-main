<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $floor_id = $_POST['floor_id'];
    $room_number = $_POST['room_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_rooms (floor_id, room_number) VALUES (?, ?)");
    $stmt->bind_param("is", $floor_id, $room_number);
    $stmt->execute();
}

header("Location: rooms.php?floor_id=" . $_POST['floor_id']);
exit;
