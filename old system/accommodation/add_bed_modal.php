<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $bed_number = $_POST['bed_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_beds (room_id, bed_number) VALUES (?, ?)");
    $stmt->bind_param("is", $room_id, $bed_number);
    $stmt->execute();
}

header("Location: beds.php?room_id=" . $_POST['room_id']);
exit;
