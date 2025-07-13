<?php
header('Content-Type: application/json');
include '../db.php';
include '../session.php';

// Ensure HR user session is active
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$current_user_type = 'hr';

// Get POST data
$receiver_id = $_POST['receiver_id'] ?? '';
$receiver_type = $_POST['receiver_type'] ?? '';
$message = trim($_POST['message'] ?? '');

// Validate input
if (empty($receiver_id) || empty($receiver_type) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Prepare SQL to insert message
$stmt = $conn->prepare("
    INSERT INTO chat_messages (sender_id, sender_type, receiver_id, receiver_type, message)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: statement failed']);
    exit;
}

$stmt->bind_param("sssss", $current_user_id, $current_user_type, $receiver_id, $receiver_type, $message);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}
