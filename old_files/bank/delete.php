<?php
include '../db.php';

// Get record ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Delete record from database
    $deleteSql = "DELETE FROM bank_account_records WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to dashboard on success
        header('Location: index.php?message=Record deleted successfully');
        exit();
    } else {
        // Display an error message
        $error = "Error: " . $conn->error;
        header('Location: index.php?error=' . urlencode($error));
        exit();
    }
} else {
    // Redirect if no valid ID provided
    header('Location: index.php?message=Invalid record ID');
    exit();
}
?>
