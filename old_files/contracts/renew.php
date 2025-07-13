<?php
include '../db.php';
include '../session.php';

// Fetch employees with Active status
$employees = $conn->query("SELECT emp_no, name FROM employees WHERE employment_status = 'Active' ORDER BY name ASC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Generate Renewal Contract</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
       data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <?php include '../header.php'; ?>

      <div class="container-fluid mt-4" style="max-width: 900px;">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Renewal Contract Generator</h5>
            <form action="preview.php" method="get" class="mt-4">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Select Employee</label>
                <select name="emp_no" id="emp_no" class="form-select" required>
                  <option value="">-- Choose Employee --</option>
                  <?php while ($row = $employees->fetch_assoc()): ?>
                    <option value="<?= $row['emp_no'] ?>">
                      <?= htmlspecialchars($row['name']) ?> (<?= $row['emp_no'] ?>)
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="d-flex justify-content-between">
                <a href="../employees/index.php" class="btn btn-outline-secondary">Back to Employees</a>
                <button type="submit" class="btn btn-primary">Preview Contract</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- jQuery (required for Select2) -->
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Activate Select2 -->
  <script>
    $(document).ready(function() {
      $('#emp_no').select2({
        width: '100%',
        placeholder: "-- Choose Employee --",
        allowClear: true
      });
    });
  </script>

  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
</body>
</html>
