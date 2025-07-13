<?php
require_once '../db.php';
require_once '../utils/response.php';

$stmt = $pdo->query("SELECT * FROM holidays");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendResponse($data);
?>