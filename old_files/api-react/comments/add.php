<?php
require_once '../db.php';
require_once '../utils/response.php';

$data = json_decode(file_get_contents("php://input"), true);
$columns = array_keys($data);
$values = array_values($data);
$placeholders = implode(',', array_fill(0, count($columns), '?'));

$sql = "INSERT INTO comments (" . implode(',', $columns) . ") VALUES ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute($values);

sendResponse(['message' => 'Record added successfully']);
?>