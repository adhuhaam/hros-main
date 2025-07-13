<?php
include '../db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subject = $_POST['subject'];
  $body = $_POST['body'];
  $tag = $_POST['tag'] ?? '';

  // Get recipient emails
  $tagSql = $conn->prepare("SELECT e.company_email FROM mailing_group mg JOIN employees e ON mg.emp_no = e.emp_no WHERE mg.status = 'Active' AND mg.tags LIKE ?");
  $likeTag = "%$tag%";
  $tagSql->bind_param("s", $likeTag);
  $tagSql->execute();
  $res = $tagSql->get_result();

  $emails = [];
  while ($row = $res->fetch_assoc()) {
    if (!empty($row['company_email'])) {
      $emails[] = $row['company_email'];
    }
  }

  if (count($emails) > 0) {
    $to = implode(",", $emails);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: HR System <no-reply@rccmaldives.com>\r\n";

    if (mail($to, $subject, $body, $headers)) {
      // Log the email
      $log = $conn->prepare("INSERT INTO mail_logs (subject, body, recipients, send_type, sent_by) VALUES (?, ?, ?, 'manual', 'admin')");
      $log->bind_param("sss", $subject, $body, $to);
      $log->execute();
      $success = "Email sent successfully to " . count($emails) . " recipient(s).";
    } else {
      $error = "Email failed to send.";
    }
  } else {
    $error = "No active recipients found for this tag.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Compose Mail</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <?php include 'sidebar.php'; ?>
  <div class="ml-64 p-6"> <!-- adjust this based on sidebar width -->
  
  

 
<div class="max-w-8xl mx-auto bg-white p-6 shadow-md rounded">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">📧 Compose Mail</h2>
    <a href="index.php" class="text-sm text-blue-600 hover:underline">← Back</a>
  </div>

  <?php if ($success): ?>
    <div class="p-3 bg-green-100 text-green-800 rounded mb-4"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="p-3 bg-red-100 text-red-800 rounded mb-4"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">Tag (to filter recipients)</label>
      <input type="text" name="tag" class="w-full border border-gray-300 p-2 rounded" placeholder="e.g. new-join, payroll" required>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">Subject</label>
      <input type="text" name="subject" class="w-full border border-gray-300 p-2 rounded" required>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">Message (HTML supported)</label>
      <textarea name="body" rows="8" class="w-full border border-gray-300 p-2 rounded" required></textarea>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Send Mail</button>
  </form>
</div>

</div>
</body>
</html>
