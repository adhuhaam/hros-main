<?php
$host = 'localhost';
$db   = 'rccmgvfd_hros';
$user = 'rccmgvfd_hros_user';
$pass = 'Ompl@65482*';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
?>
