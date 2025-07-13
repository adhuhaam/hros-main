<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $emp_no = $_POST['emp_no'];
  $tags = $_POST['tags'];
  $status = $_POST['status'];

  $check = $conn->prepare("SELECT * FROM mailing_group WHERE emp_no = ?");
  $check->bind_param("s", $emp_no);
  $check->execute();
  $existing = $check->get_result();

  if ($existing->num_rows > 0) {
    $error = "This employees is already added to the mailing group.";
  } else {
    $insert = $conn->prepare("INSERT INTO mailing_group (emp_no, tags, status) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $emp_no, $tags, $status);

    if ($insert->execute()) {
      header('Location: index.php');
      exit;
    } else {
      $error = "Error: " . $conn->error;
    }
  }
}

// Fetch employeess with company_email set
$employeess = $conn->query("SELECT emp_no, name, designation FROM employees WHERE company_email IS NOT NULL AND company_email != '' ORDER BY emp_no ASC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Mailing Recipient</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-secondary">← Back to List</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Mailing Group Recipient</h5>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="bg-light p-4 rounded shadow-sm">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Select employees</label>
                <select name="emp_no" id="emp_no" class="form-control" required>
                  <option value="">-- Select --</option>
                  <?php while ($emp = $employeess->fetch_assoc()): ?>
                    <option value="<?= $emp['emp_no'] ?>"><?= $emp['name'] ?> -  (<?= $emp['designation'] ?>)</option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" id="tags" class="form-control" required placeholder="e.g., new-join,hr,payroll">
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <button type="submit" class="btn btn-success w-100">Add Recipient</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
