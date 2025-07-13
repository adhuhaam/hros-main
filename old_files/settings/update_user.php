<?php
include '../db.php';
$stmt = $conn->prepare("UPDATE users SET username=?, staff_name=?, des=?, email=?, role_id=? WHERE id=?");
$stmt->bind_param("ssssii", $_POST['username'], $_POST['staff_name'], $_POST['des'], $_POST['email'], $_POST['role_id'], $_POST['id']);
$stmt->execute();
header("Location: users.php");
