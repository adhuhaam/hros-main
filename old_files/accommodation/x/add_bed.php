<?php
include '../db.php';

// Fetch all rooms with their floor and building info
$rooms = $conn->query("
    SELECT r.id, r.room_number, f.floor_number, b.building_name 
    FROM accommodation_rooms r
    JOIN accommodation_floors f ON r.floor_id = f.id
    JOIN accommodation_buildings b ON f.building_id = b.id
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $bed_number = $_POST['bed_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_beds (room_id, bed_number) VALUES (?, ?)");
    $stmt->bind_param("is", $room_id, $bed_number);
    $stmt->execute();

    echo "✅ Bed added successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Bed</title>
</head>
<body>
    <h2>Add New Bed</h2>
    <form method="post">
        <label>Room (Building - Floor - Room):</label>
        <select name="room_id" required>
            <option value="">-- Select Room --</option>
            <?php while ($row = $rooms->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= $row['building_name'] ?> - Floor <?= $row['floor_number'] ?> - Room <?= $row['room_number'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Bed Number:</label>
        <input type="text" name="bed_number" required><br><br>

        <button type="submit">Add Bed</button>
    </form>
</body>
</html>
