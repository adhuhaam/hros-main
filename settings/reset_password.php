<?php
include '../db.php';
$newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $newPassword, $_POST['id']);
$stmt->execute();
header("Location: users.php");
