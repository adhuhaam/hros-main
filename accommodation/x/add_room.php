<?php
include '../db.php';

// Fetch floors with their building names
$floors = $conn->query("
    SELECT f.id, f.floor_number, b.building_name 
    FROM accommodation_floors f
    JOIN accommodation_buildings b ON f.building_id = b.id
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $floor_id = $_POST['floor_id'];
    $room_number = $_POST['room_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_rooms (floor_id, room_number) VALUES (?, ?)");
    $stmt->bind_param("is", $floor_id, $room_number);
    $stmt->execute();

    echo "✅ Room added successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
</head>
<body>
    <h2>Add New Room</h2>
    <form method="post">
        <label>Floor (Building - Floor):</label>
        <select name="floor_id" required>
            <option value="">-- Select Floor --</option>
            <?php while ($row = $floors->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= $row['building_name'] ?> - Floor <?= $row['floor_number'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Room Number:</label>
        <input type="text" name="room_number" required><br><br>

        <button type="submit">Add Room</button>
    </form>
</body>
</html>
