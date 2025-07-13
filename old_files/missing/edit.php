<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$id = $_GET['id'] ?? 0;

// Fetch missing record
$stmt = $conn->prepare("SELECT m.*, e.name FROM missing m JOIN employees e ON m.emp_no = e.emp_no WHERE m.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Missing record not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Update missing table
    $update = $conn->prepare("UPDATE missing SET status = ?, remarks = ? WHERE id = ?");
    $update->bind_param("ssi", $status, $remarks, $id);
    $update->execute();

    // If status is Approved or Resolved, update employee status and termination date
    if ($status === 'Approved' || $status === 'Resolved') {
        $updateEmp = $conn->prepare("UPDATE employees SET employment_status = 'Missing', termination_date = ? WHERE emp_no = ?");
        $updateEmp->bind_param("ss", $data['missing_date'], $data['emp_no']);
        $updateEmp->execute();
    }

    header("Location: index.php?success=Updated");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Missing Record</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <?php include '../header.php'; ?>
      <div class="container-fluid mt-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Edit Missing Record</h5>

            <form method="POST" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control" value="<?= $data['name'] ?> (<?= $data['emp_no'] ?>)" disabled>
              </div>

              <div class="col-md-6">
                <label class="form-label">Missing Date</label>
                <input type="text" class="form-control" value="<?= $data['missing_date'] ?>" disabled>
              </div>

              <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class=" text-dark form-select" required>
                  <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="Approved" <?= $data['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                  <option value="Resolved" <?= $data['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
              </div>

              <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="form-control"><?= htmlspecialchars($data['remarks']) ?></textarea>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-warning">Update</button>
                <a href="index.php" class="btn btn-secondary">Back</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
