<?php
include '../db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// Fetch the record to get file names
$query = "SELECT * FROM employee_documents WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found");
}

$document = $result->fetch_assoc();
$target_dir = "../assets/document/";

// Delete the files if they exist
if ($document['front_file_name'] && file_exists($target_dir . $document['front_file_name'])) {
    unlink($target_dir . $document['front_file_name']);
}
if ($document['back_file_name'] && file_exists($target_dir . $document['back_file_name'])) {
    unlink($target_dir . $document['back_file_name']);
}

// Delete the record from the database
$deleteQuery = "DELETE FROM employee_documents WHERE id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    die("Error: " . $conn->error);
}
?>
