<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['emp_no'];
    $missing_date = $_POST['missing_date'];
    $reported_by = $_POST['reported_by'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("INSERT INTO missing (emp_no, missing_date, reported_by, remarks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $emp_no, $missing_date, $reported_by, $remarks);
    $stmt->execute();

    header("Location: index.php?success=MissingMarked");
    exit;
}

$employees = $conn->query("SELECT emp_no, name FROM employees ORDER BY name");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mark Missing</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />

  <!-- Select2 CSS for searchable dropdown -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .select2-container--default .select2-selection--single {
      height: 38px;
      padding: 6px 12px;
    }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <?php include '../header.php'; ?>
      <div class="container-fluid mt-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Mark Employee as Missing</h5>

            <form method="POST" class="row g-3">
              <div class="col-md-6">
                <label for="emp_no" class="form-label">Employee</label>
                <select name="emp_no" id="emp_no" class="form-select" required>
                  <option value="">-- Select Employee --</option>
                  <?php while ($e = $employees->fetch_assoc()): ?>
                    <option value="<?= $e['emp_no'] ?>"><?= $e['name'] ?> (<?= $e['emp_no'] ?>)</option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="missing_date" class="form-label">Missing Date</label>
                <input type="date" name="missing_date" id="missing_date" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label for="reported_by" class="form-label">Reported By</label>
                <input type="text" name="reported_by" id="reported_by" class="form-control">
              </div>

              <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="form-control"></textarea>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery (required by Select2) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Initialize Select2 -->
  <script>
    $(document).ready(function() {
      $('#emp_no').select2({
        placeholder: "Select an employee",
        allowClear: true
      });
    });
  </script>
</body>
</html>
