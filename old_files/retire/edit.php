<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
    echo "Retirement ID is required.";
    exit;
}

$id = $_GET['id'];

// Fetch the retirement record
$sql = "SELECT r.*, e.name FROM retirement_records r JOIN employees e ON r.emp_no = e.emp_no WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Retirement record not found.";
    exit;
}

$record = $result->fetch_assoc();
$success = '';
$error = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $retirement_date = $_POST['retirement_date'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $updateSql = "UPDATE retirement_records SET retirement_date = ?, status = ?, remarks = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('sssi', $retirement_date, $status, $remarks, $id);

    if ($updateStmt->execute()) {
        // If approved, update employee record
        if ($status === 'Approved') {
            $empUpdate = $conn->prepare("UPDATE employees SET employment_status = 'Retired', termination_date = ? WHERE emp_no = ?");
            $empUpdate->bind_param('ss', $retirement_date, $record['emp_no']);
            $empUpdate->execute();
        }

        $success = "Record updated successfully.";
        // Refresh record
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Retirement</title>
  <link rel="shortcut icon" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
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
          <h4 class="card-title text-primary mb-4">Edit Retirement Record</h4>

          <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-primary">Employee Number</label>
              <input type="text" class="form-control" value="<?= $record['emp_no'] ?>" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label text-primary">Employee Name</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($record['name']) ?>" disabled>
            </div>

            <div class="col-md-6">
              <label for="retirement_date" class="form-label text-primary">Retirement Date</label>
              <input type="date" name="retirement_date" id="retirement_date" class="form-control" required value="<?= $record['retirement_date'] ?>">
            </div>

            <div class="col-md-6">
              <label for="status" class="form-label text-primary">Status</label>
              <select name="status" id="status" class="form-select" required>
                <?php foreach (['Pending', 'Approved', 'Rejected'] as $option): ?>
                  <option value="<?= $option ?>" <?= $record['status'] === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label for="remarks" class="form-label text-primary">Remarks</label>
              <textarea name="remarks" id="remarks" rows="4" class="form-control"><?= htmlspecialchars($record['remarks']) ?></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-success">Update Record</button>
              <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
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
