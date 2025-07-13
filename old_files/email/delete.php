<?php
include '../db.php';

if (!isset($_GET['id'])) {
  die("❌ Invalid Request");
}

$id = $_GET['id'];

// Optional: confirm the record exists before deleting
$check = $conn->prepare("SELECT * FROM mailing_group WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
  die("❌ Record not found.");
}

// Delete the record
$delete = $conn->prepare("DELETE FROM mailing_group WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
  header("Location: index.php?msg=deleted");
  exit;
} else {
  echo "❌ Failed to delete.";
}
?>
