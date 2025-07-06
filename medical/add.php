<?php
include '../db.php';
include '../session.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Medical Record</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
        <?php include '../sidebar.php'; ?>
    </aside>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-primary">Back to Records</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Medical Record</h5>
            <form action="process.php" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label text-primary">Employee ID</label>
                <input type="number" name="employee_id" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Medical Center</label>
                <select name="medical_center_name" class="form-control" required>
                  <option value="MDC">MDC</option>
                  <option value="Pearl Medical Center">Pearl Medical Center</option>
                  <option value="AMDC">AMDC</option>
                </select>
              </div>
              
              <div class="mb-3">
                <label class="form-label text-primary">Date of Medical</label>
                <input type="date" name="date_of_medical" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Status</label>
                <select name="status" class="form-control" required>
                  <option value="Pending">Pending</option>
                  <option value="Medical Center Visited">Medical Center Visited</option>
                  <option value="Uploaded">Uploaded</option>
                  <option value="Incomplete">Incomplete</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Medical Document</label>
                <input type="file" name="medical_document" class="form-control" required>
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" name="uploaded_xpat" class="form-check-input" id="uploaded_xpat" value="1">
                <label class="form-check-label text-primary" for="uploaded_xpat">Uploaded Xpat</label>
              </div>
              <button type="submit" class="btn btn-primary">Add Record</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
</body>
</html>
