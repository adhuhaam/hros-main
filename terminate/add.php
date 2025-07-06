<?php
include '../db.php';
include '../session.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_no = $_POST['emp_no'];
    $termination_date = $_POST['termination_date'];
    $reason = $_POST['reason'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("INSERT INTO termination (emp_no, termination_date, reason, remarks) VALUES (?, ?, ?, ?)");
    $stmt->execute([$emp_no, $termination_date, $reason, $remarks]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Termination</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
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
          <h5 class="card-title fw-semibold">Add Termination</h5>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Employee Number</label>
              <input type="text" name="emp_no" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Termination Date</label>
              <input type="date" name="termination_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Reason</label>
              <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-danger">Submit Termination</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
