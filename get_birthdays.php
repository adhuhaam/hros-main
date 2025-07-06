<?php
include 'db.php';

// Fetch all birthdays from the employees table
$sql = "SELECT name, dob FROM employees";
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'title' => $row['name'],
            'start' => date('Y') . '-' . date('m', strtotime($row['dob'])) . '-' . date('d', strtotime($row['dob'])), // Adjust year to current year
            'color' => '#d1e7dd',
            'textColor' => '#0f5132',
        ];
    }
}

// Set JSON response header
header('Content-Type: application/json');
echo json_encode($events);
