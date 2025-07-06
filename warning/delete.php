<?php
session_start();
include '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Delete warning and related data
if (isset($_GET['id'])) {
    $warning_id = intval($_GET['id']);

    // Delete related comments
    $delete_comments_sql = "DELETE FROM comments WHERE warning_id = ?";
    $stmt = $conn->prepare($delete_comments_sql);
    $stmt->bind_param("i", $warning_id);
    $stmt->execute();

    // Delete the warning
    $delete_warning_sql = "DELETE FROM warnings WHERE id = ?";
    $stmt = $conn->prepare($delete_warning_sql);
    $stmt->bind_param("i", $warning_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Error: Could not delete the warning.";
    }
} else {
    header('Location: index.php');
    exit();
}
?>
