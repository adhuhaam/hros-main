<?php
include '../db.php';

$bed_id = $_POST['bed_id'];

$conn->query("UPDATE accommodation_beds SET occupied_by = NULL, assigned_at = NULL WHERE id = $bed_id");

echo "Unassigned successfully";
