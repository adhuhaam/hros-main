<?php
include '../db.php';

$building_id = $_GET['id'];
$result = $conn->query("SELECT * FROM accommodation_floors WHERE building_id = $building_id");

echo "<div class='card mt-4'>";
echo "<div class='card-body'>";
echo "<div class='d-flex justify-content-between align-items-center mb-3'>";
echo "<h5 class='card-title mb-0'>🏬 Floors</h5>";
echo "<a href='add_floor.php?building_id=$building_id' class='btn btn-sm btn-primary'>➕ Add Floor</a>";
echo "</div>";

if ($result->num_rows > 0) {
    echo "<div class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "
            <button class='list-group-item list-group-item-action floor' data-id='{$row['id']}'>
                Floor {$row['floor_number']}
            </button>";
    }
    echo "</div>";
} else {
    echo "<p class='text-muted'>No floors found for this building.</p>";
}

echo "</div></div>";
