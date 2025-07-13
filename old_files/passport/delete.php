<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete record query
    $deleteQuery = "DELETE FROM passport_renewals WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to index.php after successful deletion
        header('Location: index.php?message=Record deleted successfully');
        exit();
    } else {
        $error = "Error deleting record: " . $conn->error;
    }
} else {
    // Redirect back if no valid ID is provided
    header('Location: index.php?error=Invalid request');
    exit();
}
?>
