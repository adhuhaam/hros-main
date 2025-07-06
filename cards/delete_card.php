<?php
include '../db.php';
include '../session.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql = "DELETE FROM card_print WHERE id = $id";
  $conn->query($sql);
  header('Location: card_print.php');
}
?>
