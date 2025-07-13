<?php
require_once '../db.php';
require_once '../utils/response.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    sendResponse(['error' => 'ID is required'], 400);
}

$data = json_decode(file_get_contents("php://input"), true);
$fields = implode(',', array_map(fn($key) => "$key = ?", array_keys($data)));
$values = array_values($data);
$values[] = $id;

$sql = "UPDATE warnings SET $fields WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($values);

sendResponse(['message' => 'Record updated successfully']);
?>