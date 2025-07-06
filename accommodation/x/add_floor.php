<?php
include '../db.php';

// Fetch building list for dropdown
$buildings = $conn->query("SELECT id, building_name FROM accommodation_buildings");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $building_id = $_POST['building_id'];
    $floor_number = $_POST['floor_number'];

    $stmt = $conn->prepare("INSERT INTO accommodation_floors (building_id, floor_number) VALUES (?, ?)");
    $stmt->bind_param("ii", $building_id, $floor_number);
    $stmt->execute();

    echo "✅ Floor added successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Floor</title>
</head>
<body>
    <h2>Add New Floor</h2>
    <form method="post">
        <label>Building:</label>
        <select name="building_id" required>
            <option value="">-- Select Building --</option>
            <?php while ($row = $buildings->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['building_name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Floor Number:</label>
        <input type="number" name="floor_number" required><br><br>

        <button type="submit">Add Floor</button>
    </form>
</body>
</html>
