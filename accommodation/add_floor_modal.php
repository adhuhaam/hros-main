<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $building_id = $_POST['building_id'];
    $floor_number = $_POST['floor_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_floors (building_id, floor_number) VALUES (?, ?)");
    $stmt->bind_param("ii", $building_id, $floor_number);
    $stmt->execute();
}

header("Location: floors.php?building_id=" . $_POST['building_id']);
exit;
