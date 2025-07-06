<?php
include '../session.php';
$chat_with_id = $_GET['emp_no'] ?? '';
$chat_with_type = 'employee';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat with Employee</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<?php include '../sidebar.php'; ?>
<div class="body-wrapper">
  <?php include '../header.php'; ?>

  <div class="container-fluid pt-4">
    <div class="card">
      <div class="card-header"><h4>Chat with Employee #<?= htmlspecialchars($chat_with_id) ?></h4></div>
      <div class="card-body">
        <div id="chat-box" class="border rounded p-3 mb-3 bg-light" style="height: 400px; overflow-y: scroll;"></div>

        <form id="chat-form" class="d-flex">
          <input type="hidden" name="receiver_id" value="<?= $chat_with_id ?>">
          <input type="hidden" name="receiver_type" value="employee">
          <input type="text" name="message" id="message" class="form-control me-2" placeholder="Type a message..." required>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function fetchMessages() {
  fetch('fetch.php?with_id=<?= $chat_with_id ?>&with_type=employee')
    .then(res => res.text())
    .then(html => {
      const box = document.getElementById('chat-box');
      box.innerHTML = html;
      box.scrollTop = box.scrollHeight;
    });
}

document.getElementById('chat-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('send.php', {
    method: 'POST',
    body: formData
  }).then(() => {
    document.getElementById('message').value = '';
    fetchMessages();
  });
});

fetchMessages();
setInterval(fetchMessages, 3000);
</script>
</body>
</html>
