<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=Invalid request");
    exit();
}

$id = $conn->real_escape_string($_GET['id']);

// Fetch resignation details
$sql = "SELECT r.*, e.name, e.passport_nic_no, e.designation, e.wp_no, e.employment_status 
        FROM resignations r 
        LEFT JOIN employees e ON r.emp_no = e.emp_no 
        WHERE r.id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php?error=Resignation not found");
    exit();
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $status = $conn->real_escape_string($_POST['status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $resign_requested_date = $conn->real_escape_string($_POST['resign_requested_date']);

    // Ensure resign requested date is before resignation date
    if ($resign_requested_date > $row['resignation_date']) {
        $error = "Resign Requested Date cannot be after Resignation Date.";
    } else {
        // Update resignation record
        $updateSql = "UPDATE resignations 
                      SET status='$status', resign_requested_date='$resign_requested_date', remarks='$remarks' 
                      WHERE id='$id'";

        if ($conn->query($updateSql) === TRUE) {
            // If status is "Approved", update employee termination details
            if ($status === "Approved") {
                $resignation_date = $row['resignation_date'];
                $emp_no = $row['emp_no'];

                $updateEmployeeSql = "UPDATE employees 
                                      SET termination_date='$resignation_date', employment_status='Resigned' 
                                      WHERE emp_no='$emp_no'";
                $conn->query($updateEmployeeSql);
            }

            header("Location: index.php?success=Resignation updated successfully");
            exit();
        } else {
            $error = "Error updating record: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Resignation</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-primary">Back to List</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Resignation</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label class="form-label">Employee No:</label>
                <input type="text" value="<?php echo $row['emp_no']; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" value="<?php echo $row['name']; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Passport/NIC No:</label>
                <input type="text" value="<?php echo $row['passport_nic_no']; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Designation:</label>
                <input type="text" value="<?php echo $row['designation']; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">WP No:</label>
                <input type="text" value="<?php echo $row['wp_no']; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Resignation Date:</label>
                <input type="text" value="<?php echo date("d-M-Y", strtotime($row['resignation_date'])); ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Resign Requested Date:</label>
                <input type="date" name="resign_requested_date" value="<?php echo $row['resign_requested_date']; ?>" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Employee Statement:</label>
                <textarea class="form-control" readonly><?php echo $row['statement']; ?></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Status:</label>
                <select name="status" class="form-control" required>
                  <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                  <option value="Rejected" <?php echo ($row['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="remarks" class="form-label">Remarks:</label>
                <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks (optional)"><?php echo $row['remarks']; ?></textarea>
              </div>
              <button type="submit" class="btn btn-success w-100">Update Resignation</button>
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
