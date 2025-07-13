<?php
include '../db.php';
include '../session.php';

$emp_no = $_GET['emp_no'] ?? '';
if (!$emp_no) {
  echo "<script>alert('Invalid employee number.'); window.location.href='index.php';</script>";
  exit;
}

// Fetch employee + visa data
$stmt = $conn->prepare("
  SELECT e.emp_no, e.name, w.*
  FROM employees e
  LEFT JOIN work_visa w ON e.emp_no = w.emp_no
  WHERE e.emp_no = ? AND e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
");
$stmt->bind_param("s", $emp_no);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
  echo "<script>alert('Employee not eligible for visa management.'); window.location.href='index.php';</script>";
  exit;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $visa_number = $_POST['visa_number'];
  $visa_issue_date = $_POST['visa_issue_date'];
  $visa_expiry_date = $_POST['visa_expiry_date'];
  $visa_status = $_POST['visa_status'];
  $visa_type = $_POST['visa_type'];
  $place_of_issue = $_POST['place_of_issue'];
  $remarks = $_POST['remarks'];

  $check = $conn->prepare("SELECT id FROM work_visa WHERE emp_no = ?");
  $check->bind_param("s", $emp_no);
  $check->execute();
  $exists = $check->get_result()->num_rows > 0;

  if ($exists) {
    $stmt = $conn->prepare("
      UPDATE work_visa 
      SET visa_number=?, visa_issue_date=?, visa_expiry_date=?, visa_status=?, visa_type=?, place_of_issue=?, remarks=?, last_updated=NOW()
      WHERE emp_no=?
    ");
  } else {
    $stmt = $conn->prepare("
      INSERT INTO work_visa (visa_number, visa_issue_date, visa_expiry_date, visa_status, visa_type, place_of_issue, remarks, emp_no)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
  }

  $stmt->bind_param("ssssssss", $visa_number, $visa_issue_date, $visa_expiry_date, $visa_status, $visa_type, $place_of_issue, $remarks, $emp_no);
  $stmt->execute();

  echo "<script>alert('Visa record saved successfully.'); window.location.href='index.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Work Visa</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<?php include '../sidebar.php'; ?>
<div class="body-wrapper">
  <div class="container-fluid">
    <div class="card mt-8">
      <div class="card-header">
        <h5 class="text-danger"><?= htmlspecialchars($data['name']) ?>  -- <?= htmlspecialchars($emp_no) ?></h5>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Visa Number</label>
              <input type="text" class="form-control" name="visa_number" value="<?= htmlspecialchars($data['visa_number'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Issue Date</label>
              <input type="date" class="form-control" name="visa_issue_date" value="<?= $data['visa_issue_date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Expiry Date</label>
              <input type="date" class="form-control" name="visa_expiry_date" value="<?= $data['visa_expiry_date'] ?? '' ?>">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="visa_status" class="form-control">
                <?php
                $statuses = ['Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Expiring Soon', 'Completed', 'Expired'];
                foreach ($statuses as $s) {
                  $selected = ($data['visa_status'] ?? '') === $s ? 'selected' : '';
                  echo "<option value=\"$s\" $selected>$s</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Visa Type</label>
              <select name="visa_type" class="form-control">
                <?php
                $visaTypes = ['Work Visa', 'Business Visa'];
                foreach ($visaTypes as $type) {
                  $selected = ($data['visa_type'] ?? '') === $type ? 'selected' : '';
                  echo "<option value=\"$type\" $selected>$type</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Place of Issue</label>
              <input type="text" class="form-control" name="place_of_issue" value="<?= htmlspecialchars($data['place_of_issue'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="remarks" rows="3"><?= htmlspecialchars($data['remarks'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn btn-success">Save</button>
          <a href="index.php" class="btn btn-secondary">Back</a>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
