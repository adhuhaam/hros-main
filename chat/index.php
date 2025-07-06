<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

$user_id = $_SESSION['user_id'];
$chat_with = $_GET['emp_no'] ?? null;

// Recent chats
$recent = $conn->query("
  SELECT DISTINCT CASE 
    WHEN sender_type='employee' THEN sender_id 
    ELSE receiver_id END AS emp_no
  FROM chat_messages
  WHERE (sender_type='employee' AND receiver_type='hr' AND receiver_id='$user_id')
     OR (sender_type='hr' AND receiver_type='employee' AND sender_id='$user_id')
  ORDER BY id DESC
");

// Unread counts
$unreadMap = [];
$unread = $conn->prepare("
  SELECT sender_id, COUNT(*) AS cnt
  FROM chat_messages
  WHERE receiver_id=? AND receiver_type='hr' AND is_read=0
  GROUP BY sender_id
");
$unread->bind_param("s", $user_id);
$unread->execute();
$res = $unread->get_result();
while ($r = $res->fetch_assoc()) $unreadMap[$r['sender_id']] = $r['cnt'];

// Employees
$employeeMap = [];
$rs = $conn->query("SELECT emp_no, name FROM employees");
while ($e = $rs->fetch_assoc()) $employeeMap[$e['emp_no']] = $e['name'];

// Selected employee
$selected = null;
if ($chat_with && isset($employeeMap[$chat_with])) {
  $selected = ['emp_no'=>$chat_with, 'name'=>$employeeMap[$chat_with]];
  $mark = $conn->prepare("
    UPDATE chat_messages SET is_read=1 
    WHERE sender_id=? AND receiver_id=? AND sender_type='employee' AND receiver_type='hr'
  ");
  $mark->bind_param("ss", $chat_with, $user_id);
  $mark->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    .chat-container { display: flex; height: 80vh; border: 1px solid #ccc; border-radius: 5px; overflow: hidden; }
    .chat-sidebar { width: 30%; background: #f8f9fa; border-right: 1px solid #ddd; padding: 1rem; overflow-y: auto; }
    .chat-window { width: 70%; display: flex; flex-direction: column; background: #e5ddd5; }
    .chat-header { background: #f5f5f5; padding: 10px 16px; font-weight: bold; border-bottom: 1px solid #ccc; }
    .chat-body { flex: 1; padding: 15px; overflow-y: auto; }
    .chat-footer { padding: 12px 16px; border-top: 1px solid #ccc; background: #fff; }
    .message.sent { text-align: right; margin-bottom: 10px; }
    .message.received { text-align: left; margin-bottom: 10px; }
    .bubble {
      display: inline-block;
      padding: 10px 14px;
      border-radius: 20px;
      max-width: 70%;
      word-wrap: break-word;
    }
    .sent .bubble {
      background-color: #006bad;
      color: white;
    }
    .received .bubble {
      background-color: white;
      color: black;
      border: 1px solid #ccc;
    }
    .dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      background: red;
      border-radius: 50%;
      margin-left: 6px;
    }
    .employee-item a {
      text-decoration: none;
      display: block;
      padding: 10px 12px;
      border-radius: 5px;
    }
    .employee-item a:hover, .employee-item.active a {
      background: #e0e0e0;
    }
  </style>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
      <?php include '../header.php'; ?>
    <div class="container-fluid" style="max-width:100%;">
        <br>
      <div class="card">
        <div class="chat-container">

          <!-- Sidebar -->
          <div class="chat-sidebar">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5>Recent Chats</h5>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#new-chat">New</button>
            </div>
            <?php foreach ($recent as $row):
              $eno = $row['emp_no'];
              if (!isset($employeeMap[$eno])) continue;
              $isActive = ($eno == $chat_with);
              $hasUnread = isset($unreadMap[$eno]);
            ?>
              <div class="employee-item<?= $isActive ? ' active' : '' ?>">
                <a href="?emp_no=<?= $eno ?>">
                  <?= htmlspecialchars($employeeMap[$eno]) ?> (<?= $eno ?>)
                  <?php if ($hasUnread): ?><span class="dot"></span><?php endif; ?>
                </a>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Chat Window -->
          <div class="chat-window">
            <?php if ($selected): ?>
              <div class="chat-header">
                Chat with <?= htmlspecialchars($selected['name']) ?> (<?= $selected['emp_no'] ?>)
              </div>
              <div id="chat-body" class="chat-body"></div>
              <div class="chat-footer">
                <form id="chat-form" class="d-flex">
                  <input type="hidden" name="receiver_id" value="<?= $selected['emp_no'] ?>">
                  <input type="hidden" name="receiver_type" value="employee">
                  <input type="text" id="message" name="message" class="form-control me-2 rounded-pill"
                         placeholder="Type a message..." required autocomplete="off">
                  <button type="submit" class="btn btn-success rounded-pill px-4">Send</button>
                </form>
              </div>
            <?php else: ?>
              <div class="chat-header">Select an employee to start chatting</div>
              <div class="chat-body d-flex justify-content-center align-items-center text-muted">
                No conversation selected
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Chat Modal (Bootstrap 5) -->
<div class="modal fade" id="new-chat" tabindex="-1" aria-labelledby="newChatLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newChatLabel">Select Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="search" class="form-control mb-3" placeholder="Search employee...">
        <div id="all-employee-list" style="max-height: 60vh; overflow-y: auto;">
          <?php foreach ($employeeMap as $empNo => $empName): ?>
            <div class="mb-2">
              <a href="?emp_no=<?= $empNo ?>" class="d-block"><?= htmlspecialchars($empName) ?> (<?= $empNo ?>)</a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if ($chat_with): ?>
function fetchMessages() {
  fetch('fetch.php?with_id=<?= $chat_with ?>&with_type=employee')
    .then(res => res.text())
    .then(html => {
      const box = document.getElementById('chat-body');
      box.innerHTML = html;
      box.scrollTop = box.scrollHeight;
    });
}
function sendMessage() {
  const input = document.getElementById('message');
  const msg = input.value.trim();
  if (!msg) return;
  const data = new FormData();
  data.append('receiver_id', '<?= $chat_with ?>');
  data.append('receiver_type', 'employee');
  data.append('message', msg);
  fetch('send.php', { method: 'POST', body: data }).then(() => {
    input.value = '';
    fetchMessages();
  });
}
document.getElementById('chat-form').addEventListener('submit', function(e) {
  e.preventDefault();
  sendMessage();
});
setInterval(fetchMessages, 3000);
fetchMessages();
<?php endif; ?>
document.getElementById('search').addEventListener('input', function () {
  const term = this.value.toLowerCase();
  document.querySelectorAll('#all-employee-list a').forEach(a => {
    a.parentElement.style.display = a.textContent.toLowerCase().includes(term) ? '' : 'none';
  });
});
</script>
</body>
</html>
