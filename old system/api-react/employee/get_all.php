<?php
require_once '../db.php';
require_once '../utils/response.php';

$stmt = $pdo->query("SELECT * FROM employee ORDER BY emp_no DESC");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendResponse($employees);
?>
