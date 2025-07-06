<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['building_name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO accommodation_buildings (building_name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $location);
    $stmt->execute();

    echo "✅ Building added successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Building</title>
</head>
<body>
    <h2>Add New Building</h2>
    <form method="post">
        <label>Building Name:</label>
        <input type="text" name="building_name" required><br><br>

        <label>Location:</label>
        <input type="text" name="location" required><br><br>

        <button type="submit">Add Building</button>
    </form>
</body>
</html>
