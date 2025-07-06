<?php
require_once '../db.php';
require_once '../utils/response.php';

$emp_no = $_GET['emp_no'] ?? '';
if (!$emp_no) {
    sendResponse(['error' => 'emp_no is required'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM leave_balance WHERE emp_no = ?");
$stmt->execute([$emp_no]);
$balance = $stmt->fetch(PDO::FETCH_ASSOC);

sendResponse($balance ?: ['message' => 'No data found']);
?>
