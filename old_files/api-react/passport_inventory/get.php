<?php
require_once '../db.php';
require_once '../utils/response.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    sendResponse(['error' => 'ID is required'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM passport_inventory WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

sendResponse($data ?: ['message' => 'Not found'], $data ? 200 : 404);
?>