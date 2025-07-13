<?php
require_once '../db.php';
require_once '../utils/response.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    sendResponse(['error' => 'Username and password are required'], 400);
}

$stmt = $pdo->prepare("
    SELECT emp_no, username, password, staff_name
              FROM users 
              WHERE username = ?
");

$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $token = base64_encode($user['emp_no'] . '|' . time());

    // Return limited user info
    sendResponse([
        'message' => 'Login successful',
        'token' => $token,
        'user' => [
            'emp_no' => $user['emp_no'],
            'username' => $user['username']
        ]
    ]);
} else {
    sendResponse(['error' => 'Invalid credentials'], 401);
}
?>
