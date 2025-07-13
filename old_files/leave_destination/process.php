<?php
include '../db.php';
include '../session.php';

if (isset($_POST['add'])) {
    $destination_name = $conn->real_escape_string($_POST['destination_name']);
    $sql = "INSERT INTO employee_tickets_destination (destination_name) VALUES ('$destination_name')";
    $conn->query($sql);
    header("Location: index.php");
}

if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $destination_name = $conn->real_escape_string($_POST['destination_name']);
    $sql = "UPDATE employee_tickets_destination SET destination_name='$destination_name' WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
}

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM employee_tickets_destination WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
}
?>
