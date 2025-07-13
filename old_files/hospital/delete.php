<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate if ID is provided
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        header("Location: index.php?error=Invalid request: ID is missing.");
        exit();
    }

    // Sanitize the input
    $id = $conn->real_escape_string($_POST['id']);

    // Check if the record exists
    $check_sql = "SELECT * FROM opd_records WHERE id = '$id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows === 0) {
        header("Location: index.php?error=Record not found.");
        exit();
    }

    // Perform the deletion
    $delete_sql = "DELETE FROM opd_records WHERE id = '$id'";
    if ($conn->query($delete_sql) === TRUE) {
        header("Location: index.php?message=Record deleted successfully.");
        exit();
    } else {
        header("Location: index.php?error=Error deleting record: " . $conn->error);
        exit();
    }
} else {
    // Deny direct access
    header("Location: index.php?error=Invalid request method.");
    exit();
}
?>
