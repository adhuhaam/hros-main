<?php
include '../db.php';
include '../session.php';

$with_id = $_GET['with_id'] ?? '';
$with_type = $_GET['with_type'] ?? '';
$current_user_id = $_SESSION['user_id'];
$current_user_type = 'hr';

$stmt = $conn->prepare("
    SELECT * FROM chat_messages
    WHERE 
      (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?)
   OR (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?)
    ORDER BY created_at ASC
");
$stmt->bind_param(
  "ssssssss",
  $current_user_id, $current_user_type, $with_id, $with_type,
  $with_id, $with_type, $current_user_id, $current_user_type
);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $isSender = ($row['sender_id'] === $current_user_id && $row['sender_type'] === $current_user_type);
    $side = $isSender ? 'sent' : 'received';
    $time = date("h:i A", strtotime($row['created_at']));

    echo "<div class='message {$side}'>
            <div class='bubble'>" . nl2br(htmlspecialchars($row['message'])) . "
              <div class='text-muted small mt-1'>$time</div>
            </div>
          </div>";
}
