<?php
include '../db.php';
include '../session.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch record details
$query = "SELECT pr.id, e.name, e.designation, e.passport_nic_no, e.passport_nic_no_expires, pr.renewal_date, pr.remarks, pr.status
          FROM passport_renewals pr
          LEFT JOIN employees e ON pr.emp_no = e.emp_no
          WHERE pr.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $renewal_date = $conn->real_escape_string($_POST['renewal_date']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $status = $conn->real_escape_string($_POST['status']);
    $passport_nic_no = $conn->real_escape_string($_POST['passport_nic_no']);
    $passport_nic_no_expires = $conn->real_escape_string($_POST['passport_nic_no_expires']);

    // Update passport_renewals table
    $updateQuery = "UPDATE passport_renewals SET renewal_date = ?, remarks = ?, status = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param("sssi", $renewal_date, $remarks, $status, $id);
    $stmtUpdate->execute();

    // Update employees table
    $updateEmployeeQuery = "UPDATE employees SET passport_nic_no = ?, passport_nic_no_expires = ? WHERE emp_no = (SELECT emp_no FROM passport_renewals WHERE id = ?)";
    $stmtEmployeeUpdate = $conn->prepare($updateEmployeeQuery);
    $stmtEmployeeUpdate->bind_param("ssi", $passport_nic_no, $passport_nic_no_expires, $id);
    $stmtEmployeeUpdate->execute();

    header('Location: index.php');
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Passport Renewal</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Passport Renewal</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"> <?php echo $error; ?> </div>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label class="form-label">Employee Name:</label>
                <input type="text" value="<?php echo htmlspecialchars($record['name']); ?>" class="form-control" disabled>
              </div>
              <div class="mb-3">
                <label class="form-label">Designation:</label>
                <input type="text" value="<?php echo htmlspecialchars($record['designation']); ?>" class="form-control" disabled>
              </div>
              <div class="mb-3">
                <label class="form-label">Passport Number:</label>
                <input type="text" name="passport_nic_no" value="<?php echo htmlspecialchars($record['passport_nic_no']); ?>" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Passport Expiration Date:</label>
                <input type="date" name="passport_nic_no_expires" value="<?php echo htmlspecialchars($record['passport_nic_no_expires']); ?>" class="form-control">
              </div>
              <div class="mb-3">
                <label for="renewal_date" class="form-label">Renewal Date:</label>
                <input type="date" name="renewal_date" id="renewal_date" value="<?php echo htmlspecialchars($record['renewal_date']); ?>" required class="form-control">
              </div>
              <div class="mb-3">
                <label for="remarks" class="form-label">Remarks:</label>
                <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks"><?php echo htmlspecialchars($record['remarks']); ?></textarea>
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" class="form-control" required>
                  <option value="Pending" <?php echo $record['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Scheduled" <?php echo $record['status'] == 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                  <option value="Went to embassy" <?php echo $record['status'] == 'Went to embassy' ? 'selected' : ''; ?>>Went to embassy</option>
                  <option value="Applied" <?php echo $record['status'] == 'Applied' ? 'selected' : ''; ?>>Applied</option>
                  <option value="Rejected" <?php echo $record['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                  <option value="Incomplete" <?php echo $record['status'] == 'Incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                  <option value="Approved" <?php echo $record['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                  <option value="Received new passport" <?php echo $record['status'] == 'Received new passport' ? 'selected' : ''; ?>>Received new passport</option>
                </select>
              </div>
              <button type="submit" class="btn btn-success w-100">Update</button>
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