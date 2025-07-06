<?php
include_once '../db.php';
require 'send_push.php';

// 🔐 Get POST data securely
$empNos = $_POST['emp_no'] ?? [];
$title = trim($_POST['title'] ?? 'Notification');
$message = trim($_POST['message'] ?? 'You have a new message.');

if (empty($empNos)) {
    echo json_encode(['status' => 'error', 'message' => 'No employees selected']);
    exit;
}

// 🛠 Prepare SQL with dynamic placeholders
$placeholders = implode(',', array_fill(0, count($empNos), '?'));
$types = str_repeat('s', count($empNos));
$stmt = $conn->prepare("SELECT emp_no, player_id FROM employees WHERE emp_no IN ($placeholders) AND player_id IS NOT NULL");
$stmt->bind_param($types, ...$empNos);
$stmt->execute();
$result = $stmt->get_result();

$playerIds = [];
$empData = [];

while ($row = $result->fetch_assoc()) {
    $playerIds[] = $row['player_id'];
    $empData[] = $row;
}

// 🚀 Send Push Notification
if (!empty($playerIds)) {
    $rawResponse = sendPushNotification($playerIds, $title, $message);
    $response = json_decode($rawResponse, true);
    $isError = isset($response['errors']) || (isset($response['status']) && $response['status'] === 'error');

    // 📦 Optional: Log push details to DB
    if (!$isError && !empty($empData)) {
        $logStmt = $conn->prepare("INSERT INTO push_logs (emp_no, player_id, title, message, response, status) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($empData as $row) {
            $logStmt->bind_param(
                'ssssss',
                $row['emp_no'],
                $row['player_id'],
                $title,
                $message,
                $rawResponse,
                $isError ? 'failed' : 'sent'
            );
            $logStmt->execute();
        }
    }

    if ($isError) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send some or all notifications.', 'response' => $response]);
        exit;
    }

    // ✅ Redirect with success
    header("Location: index.php?sent=true");
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'No valid player IDs found']);
}
