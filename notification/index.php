<?php
include '../db.php';
include '../session.php';


// Fetch active employees for dropdown
$employees = $conn->query("SELECT emp_no, name FROM employees WHERE employment_status = 'Active' AND player_id IS NOT NULL ORDER BY name ASC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Send Notification</title>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <?php include '../header.php'; ?>

      <div class="container-fluid" style="max-width:100%;">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Send Push Notification</h5>

            <?php if (isset($_GET['sent']) && $_GET['sent'] === 'true'): ?>
              <div class="alert alert-success">Notification sent successfully!</div>
            <?php endif; ?>

            <form action="notify_employees.php" method="post">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Select Employees</label>
                <select id="emp_no" multiple class="form-select" name="emp_no[]" required>
                  <?php while ($row = $employees->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['emp_no']) ?>">
                      <?= htmlspecialchars($row['emp_no']) ?> - <?= htmlspecialchars($row['name']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="title" class="form-label">Notification Title</label>
                <input type="text" class="form-control" name="title" required placeholder="Enter title">
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">Notification Message</label>
                <textarea class="form-control" name="message" rows="3" required placeholder="Enter message"></textarea>
              </div>

              <button type="submit" class="btn btn-primary">Send Notification</button>
            </form>
          </div>
        </div>
     
    <hr class="my-5">

<h5 class="fw-semibold mb-3">Notification Log History</h5>
<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Emp No</th>
        <th>Title</th>
        <th>Message</th>
        <th>Status</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $logResult = $conn->query("SELECT id, emp_no, title, message, status, sent_at FROM push_logs ORDER BY sent_at DESC LIMIT 50");
      if ($logResult && $logResult->num_rows > 0):
        while ($log = $logResult->fetch_assoc()):
      ?>
          <tr>
            <td><?= $log['id'] ?></td>
            <td><?= htmlspecialchars($log['emp_no']) ?></td>
            <td><?= htmlspecialchars($log['title']) ?></td>
            <td><?= htmlspecialchars($log['message']) ?></td>
            <td>
              <span class="badge <?= $log['status'] === 'sent' ? 'bg-success' : 'bg-danger' ?>">
                <?= ucfirst($log['status']) ?>
              </span>
            </td>
            <td><?= date('d-M-Y H:i', strtotime($log['sent_at'])) ?></td>
          </tr>
      <?php
        endwhile;
      else:
        echo '<tr><td colspan="6" class="text-center">No push logs found.</td></tr>';
      endif;
      ?>
    </tbody>
  </table>
</div>

 </div>
    </div>
  </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('#emp_no').select2({
      placeholder: "Select employee(s)",
      width: '100%'
    });
  });
</script>

</body>
</html>
