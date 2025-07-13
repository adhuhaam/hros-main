<?php
include '../db.php';

$floor_id = $_GET['id'];
$result = $conn->query("SELECT * FROM accommodation_rooms WHERE floor_id = $floor_id");

echo "<div class='card mt-4'>";
echo "<div class='card-body'>";
echo "<div class='d-flex justify-content-between align-items-center mb-3'>";
echo "<h5 class='card-title mb-0'>🚪 Rooms</h5>";
echo "<a href='add_room.php?floor_id=$floor_id' class='btn btn-sm btn-primary'>➕ Add Room</a>";
echo "</div>";

if ($result->num_rows > 0) {
    echo "<div class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "
            <button class='list-group-item list-group-item-action room' data-id='{$row['id']}'>
                Room {$row['room_number']}
            </button>";
    }
    echo "</div>";
} else {
    echo "<p class='text-muted'>No rooms found for this floor.</p>";
}

echo "</div></div>";
