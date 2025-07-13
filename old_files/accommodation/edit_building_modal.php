<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['building_id'];
    $name = $_POST['building_name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("UPDATE accommodation_buildings SET building_name = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $location, $id);

    if ($stmt->execute()) {
        header("Location: index.php?edited=1");
    } else {
        header("Location: index.php?error=update_failed");
    }
    exit();
}

header("Location: index.php");
exit();
