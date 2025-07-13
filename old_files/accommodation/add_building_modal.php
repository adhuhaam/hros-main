<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['building_name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO accommodation_buildings (building_name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $location);

    if ($stmt->execute()) {
        header('Location: index.php?added=1');
    } else {
        header('Location: index.php?error=add_failed');
    }
    exit;
}

header('Location: index.php');
exit;
