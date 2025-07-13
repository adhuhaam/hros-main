<?php
require_once '../db.php';
require_once '../utils/response.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    sendResponse(['error' => 'ID is required'], 400);
}

$stmt = $pdo->prepare("DELETE FROM visa_sticker WHERE id = ?");
$stmt->execute([$id]);

sendResponse(['message' => 'Record deleted successfully']);
?>