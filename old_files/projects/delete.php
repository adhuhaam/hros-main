<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);

// Delete project from the database
$query = "DELETE FROM projects WHERE id = $project_id";

if ($conn->query($query)) {
    header('Location: index.php?success=Project deleted successfully');
    exit();
} else {
    header('Location: index.php?error=Error deleting project');
    exit();
}
?>
