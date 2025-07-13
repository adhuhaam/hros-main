<?php
include '../db.php';
include '../session.php';

$id = $_GET['id'] ?? null;

// Validate the ID
if (!$id) {
    $_SESSION['error'] = "Invalid record ID.";
    header("Location: index.php");
    exit;
}

// Fetch the record
$sql = "SELECT * FROM medical_examinations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();

// Redirect if no record is found
if (!$record) {
    $_SESSION['error'] = "Medical record not found.";
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Medical Record</title>
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
            <h5 class="card-title fw-semibold mb-4">Edit Medical Record</h5>
            <form action="process.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $record['id'] ?>">
              <div class="mb-3">
                <label class="form-label text-primary">Employee ID</label>
                <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($record['employee_id']) ?>" disabled readonly>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Medical Center Name</label>
                <select name="medical_center_name" class="form-control" required>
                  <option value="MDC" <?= $record['medical_center_name'] === 'MDC' ? 'selected' : '' ?>>MDC</option>
                  <option value="PEARL MEDICAL CENTER" <?= $record['medical_center_name'] === 'PEARL MEDICAL CENTER' ? 'selected' : '' ?>>PEARL MEDICAL CENTER</option>
                  <option value="AMDC" <?= $record['medical_center_name'] === 'AMDC' ? 'selected' : '' ?>>AMDC</option>
                  <option value="CARE TRUST" <?= $record['medical_center_name'] === 'CARE TRUST' ? 'selected' : '' ?>>CARE TRUST</option>
                  <option value="Other" <?= $record['medical_center_name'] === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Expiry date</label>
                <input type="date" name="date_of_medical" class="form-control" value="<?= htmlspecialchars($record['date_of_medical']) ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Status</label>
                <select name="status" class="form-control" required>
                  <option value="Pending" <?= $record['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="Medical Center Visited" <?= $record['status'] === 'Medical Center Visited' ? 'selected' : '' ?>>Medical Center Visited</option>
                  <option value="Uploaded" <?= $record['status'] === 'Uploaded' ? 'selected' : '' ?>>Uploaded</option>
                  <option value="Incomplete" <?= $record['status'] === 'Incomplete' ? 'selected' : '' ?>>Incomplete</option>
                  <option value="Completed" <?= $record['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label text-primary">Medical Document</label>
                <input type="file" name="medical_document" class="form-control"><br>
                <?php if (!empty($record['medical_document'])): ?>
                  <a class="btn btn-sm btn-success" href="../assets/medicals/<?= htmlspecialchars($record['medical_document']) ?>" target="_blank"> 
                    <i class="fa-solid fa-file-contract"></i> View Medical File
                  </a>
                  <input type="hidden" name="existing_medical_document" value="<?= htmlspecialchars($record['medical_document']) ?>">
                <?php endif; ?>
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" name="uploaded_xpat" class="form-check-input" id="uploaded_xpat" value="1" <?= $record['uploaded_xpat'] ? 'checked' : '' ?>>
                <label class="form-check-label text-primary" for="uploaded_xpat">Uploaded Xpat</label>
              </div>
              <button type="submit" class="btn btn-primary">Update Record</button>
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
