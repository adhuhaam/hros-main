<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$success = '';
$error = '';

// Fetch all employees for the dropdown
$employeeSql = "SELECT emp_no, name FROM employees ORDER BY name ASC";
$employeeResult = $conn->query($employeeSql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['emp_no'];
    $retirement_date = $_POST['retirement_date'];
    $reason = $_POST['reason'];
    $remarks = $_POST['remarks'];

    if ($emp_no && $retirement_date) {
        $stmt = $conn->prepare("INSERT INTO retirement_records (emp_no, retirement_date, reason, remarks) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $emp_no, $retirement_date, $reason, $remarks);

        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $error = "Error adding record: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Retirement</title>
  <link rel="shortcut icon" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .select2-container .select2-selection--single {
      height: 38px !important;
      padding: 4px 12px;
      border: 1px solid #ced4da;
    }
  </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid">
      <div class="card mt-4">
        <div class="card-body">
          <h4 class="card-title text-primary mb-4">Add Retirement Record</h4>

          <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" class="row g-3">
            <div class="col-md-6">
              <label for="emp_no" class="form-label text-primary">Employee</label>
              <select name="emp_no" id="emp_no" class="form-select" required>
                <option value="">-- Select Employee --</option>
                <?php while ($row = $employeeResult->fetch_assoc()): ?>
                  <option value="<?= $row['emp_no'] ?>">
                    <?= $row['emp_no'] ?> - <?= htmlspecialchars($row['name']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="retirement_date" class="form-label text-primary">Retirement Date</label>
              <input type="date" name="retirement_date" id="retirement_date" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label for="reason" class="form-label text-primary">Reason for Retirement</label>
              <input type="text" name="reason" id="reason" class="form-control" placeholder="e.g., Reached retirement age" required>
            </div>

            <div class="col-12">
              <label for="remarks" class="form-label text-primary">Remarks</label>
              <textarea name="remarks" id="remarks" rows="4" class="form-control" placeholder="Enter any remarks..."></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-success">Save Record</button>
              <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>


<!-- JS Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    $('#emp_no').select2({
      placeholder: "Search by Emp No or Name",
      allowClear: true,
      width: '100%'
    });
  });
</script>

</body>
</html>
