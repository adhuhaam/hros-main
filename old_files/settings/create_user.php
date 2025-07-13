<?php
include '../db.php';

$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (emp_no, username, staff_name, des, email, password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $_POST['emp_no'], $_POST['username'], $_POST['staff_name'], $_POST['des'], $_POST['email'], $password, $_POST['role_id']);
$stmt->execute();

header("Location: users.php");
