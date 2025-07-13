<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM salary_loans WHERE id = $id");
    $loan = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $received = isset($_POST['received']) ? 1 : 0;
    $deduct = isset($_POST['deduct']) ? 1 : 0;

    // Validate `status` field
    $valid_statuses = ['Pending', 'HRM Approved', 'Management Approved', 'Rejected'];
    if (!in_array($status, $valid_statuses)) {
        $error = "Invalid status value.";
    } else {
        // Determine approved_date and received_date based on conditions
        $approved_date = ($status === 'HRM Approved' || $status === 'Management Approved') ? date('Y-m-d') : null;
        $received_date = $received ? date('Y-m-d') : null;

        // Update query for the database
        $updateSql = "UPDATE salary_loans SET status = ?, received = ?, approved_date = ?, received_date = ?, deduct = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("sissii", $status, $received, $approved_date, $received_date, $deduct, $id);
            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            } else {
                $error = "Error executing the query: " . $stmt->error;
            }
        } else {
            $error = "Error preparing the statement: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Loan</title>
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
            <a href="index.php" class="btn btn-secondary">Back to List</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Loan Status</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <input type="hidden" name="id" value="<?php echo isset($loan['id']) ? $loan['id'] : ''; ?>">
              <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" required class="form-control">
                  <option value="" disabled <?php echo !isset($loan['status']) ? 'selected' : ''; ?>>-- Select Status --</option>
                  <option value="Pending" <?php echo (isset($loan['status']) && $loan['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="HRM Approved" <?php echo (isset($loan['status']) && $loan['status'] == 'HRM Approved') ? 'selected' : ''; ?>>HRM Approved</option>
                  <option value="Management Approved" <?php echo (isset($loan['status']) && $loan['status'] == 'Management Approved') ? 'selected' : ''; ?>>Management Approved</option>
                  <option value="Rejected" <?php echo (isset($loan['status']) && $loan['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="received" class="form-label">Received:</label>
                <input type="checkbox" name="received" id="received" <?php echo isset($loan['received']) && $loan['received'] ? 'checked' : ''; ?>>
              </div>
              <div class="mb-3">
                <label for="deduct" class="form-label">Deduct from Salary:</label>
                <input type="checkbox" name="deduct" id="deduct" <?php echo isset($loan['deduct']) && $loan['deduct'] ? 'checked' : ''; ?>>
              </div>
              <button type="submit" class="btn btn-success w-100">Update Status</button>
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
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
