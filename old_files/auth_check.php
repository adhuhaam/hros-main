<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Optional: Restrict based on roles
$required_roles = isset($required_roles) ? $required_roles : [];
if (!empty($required_roles) && !in_array($_SESSION['role'], $required_roles)) {
    die("Access denied. You do not have permission to view this page.");
}
?>
