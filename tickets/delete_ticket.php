<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=Invalid Ticket ID");
    exit();
}

$id = intval($_GET['id']); // Ensure it's a valid integer

// Check if the ticket exists
$check_query = $conn->prepare("SELECT * FROM employee_tickets WHERE id = ?");
$check_query->bind_param("i", $id);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=Ticket Not Found");
    exit();
}

// Delete the ticket
$delete_query = $conn->prepare("DELETE FROM employee_tickets WHERE id = ?");
$delete_query->bind_param("i", $id);

if ($delete_query->execute()) {
    header("Location: index.php?success=Ticket Deleted Successfully");
} else {
    header("Location: index.php?error=Error Deleting Ticket");
}

exit();
?>
