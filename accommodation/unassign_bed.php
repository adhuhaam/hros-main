<?php
include '../db.php';

$bed_id = $_POST['bed_id'] ?? 0;

if (!$bed_id) {
    echo "Missing bed ID.";
    exit;
}

$stmt = $conn->prepare("UPDATE accommodation_beds SET occupied_by = NULL, assigned_at = NULL WHERE id = ?");
$stmt->bind_param("i", $bed_id);

if ($stmt->execute()) {
    echo "Bed successfully unassigned.";
} else {
    echo "Error unassigning bed.";
}
