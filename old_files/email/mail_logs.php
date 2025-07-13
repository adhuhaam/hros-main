<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$logs = $conn->query("SELECT * FROM mail_logs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mail Logs</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">
  <?php include 'sidebar.php'; ?>
  <div class="ml-64 p-6">
    <div class="max-w-7xl mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Mail Logs</h1>
        <a href="index.php" class="text-sm text-blue-600 hover:underline">← Back</a>
      </div>

      <?php if ($logs->num_rows === 0): ?>
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded">No emails sent yet.</div>
      <?php else: ?>
        <div class="overflow-auto bg-white shadow-md rounded">
          <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
              <tr>
                <th class="p-3">Subject</th>
                <th class="p-3">Recipients</th>
                <th class="p-3">Type</th>
                <th class="p-3">Sent By</th>
                <th class="p-3">Date</th>
                <th class="p-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($log = $logs->fetch_assoc()): ?>
                <tr class="border-t hover:bg-gray-50 transition">
                  <td class="p-3 font-medium text-gray-900"><?= htmlspecialchars($log['subject']) ?></td>
                  <td class="p-3 text-gray-700"><?= count(explode(',', $log['recipients'])) ?> recipient(s)</td>
                  <td class="p-3">
                    <span class="px-2 py-1 text-xs rounded-full font-semibold 
                      <?= $log['send_type'] === 'manual' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' ?>">
                      <?= ucfirst($log['send_type']) ?>
                    </span>
                  </td>
                  <td class="p-3"><?= htmlspecialchars($log['sent_by']) ?></td>
                  <td class="p-3"><?= date('d-M-Y h:i A', strtotime($log['created_at'])) ?></td>
                  <td class="p-3">
                    <button type="button" onclick="toggleMessage(<?= $log['id'] ?>)"
                            class="text-indigo-600 hover:underline rounded">View</button>
                  </td>
                </tr>
                <tr id="msg-<?= $log['id'] ?>" class="hidden border-t bg-gray-50">
                  <td colspan="6" class="p-4 text-sm">
                    <div class="font-medium text-gray-600 mb-2">Message Preview:</div>
                    <div class="border border-gray-200 rounded p-4 bg-white text-gray-800 leading-relaxed overflow-x-auto">
                      <?= $log['body'] ?>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleMessage(id) {
      const el = document.getElementById('msg-' + id);
      el.classList.toggle('hidden');
    }
  </script>
</body>
</html>
