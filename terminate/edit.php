<?php
include '../db.php';
include '../session.php';

$id = $_GET['id'];

// Fetch termination record
$stmt = $conn->prepare("SELECT * FROM termination WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Termination record not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_no = $_POST['emp_no'];
    $termination_date = $_POST['termination_date'];
    $reason = $_POST['reason'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];

    // Update termination record
    $stmt = $conn->prepare("UPDATE termination SET emp_no=?, termination_date=?, reason=?, remarks=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $emp_no, $termination_date, $reason, $remarks, $status, $id);
    $stmt->execute();

    // Update employee status and termination date if approved
    if ($status === 'Approved') {
        $update = $conn->prepare("UPDATE employees SET employment_status = 'Terminated', termination_date = ? WHERE emp_no = ?");
        $update->bind_param("ss", $termination_date, $emp_no);
        $update->execute();
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Termination</title>
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
          <h5 class="card-title fw-semibold">Edit Termination</h5>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Employee Number</label>
              <input type="text" name="emp_no" value="<?= htmlspecialchars($data['emp_no']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Termination Date</label>
              <input type="date" name="termination_date" value="<?= htmlspecialchars($data['termination_date']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Reason</label>
              <textarea name="reason" class="form-control" rows="3"><?= htmlspecialchars($data['reason']) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="2"><?= htmlspecialchars($data['remarks']) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-control">
                <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Approved" <?= $data['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Rejected" <?= $data['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Termination</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
