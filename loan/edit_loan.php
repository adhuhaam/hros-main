<?php
include '../db.php';
include '../session.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM salary_loans WHERE id = $id");
    $loan = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $amount = floatval($_POST['amount']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $currency = $conn->real_escape_string($_POST['currency']);
    $status = $conn->real_escape_string($_POST['status']);
    $received = isset($_POST['received']) ? 1 : 0;
    $received_date = $received ? date('Y-m-d') : null;

    // Set approved_date if status changes to Approved
    if ($status === 'Approved') {
        $approved_date = date('Y-m-d');
        $updateSql = "UPDATE salary_loans SET emp_no = ?, amount = ?, purpose = ?, currency = ?, status = ?, received = ?, approved_date = ?, received_date = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sdssisssi", $emp_no, $amount, $purpose, $currency, $status, $received, $approved_date, $received_date, $id);
    } else {
        $updateSql = "UPDATE salary_loans SET emp_no = ?, amount = ?, purpose = ?, currency = ?, status = ?, received = ?, received_date = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sdsssisi", $emp_no, $amount, $purpose, $currency, $status, $received, $received_date, $id);
    }

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Error: " . $conn->error;
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
            <h5 class="card-title fw-semibold mb-4">Edit Loan Request</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <input type="hidden" name="id" value="<?php echo isset($loan['id']) ? $loan['id'] : ''; ?>">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Employee No:</label>
                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No" value="<?php echo isset($loan['emp_no']) ? $loan['emp_no'] : ''; ?>">
              </div>
              <div class="mb-3">
                <label for="amount" class="form-label">Amount:</label>
                <input type="number" name="amount" id="amount" required class="form-control" placeholder="Enter Amount" value="<?php echo isset($loan['amount']) ? $loan['amount'] : ''; ?>">
              </div>
              <div class="mb-3">
                <label for="purpose" class="form-label">Purpose:</label>
                <textarea name="purpose" id="purpose" required class="form-control" placeholder="Enter Purpose"><?php echo isset($loan['purpose']) ? $loan['purpose'] : ''; ?></textarea>
              </div>
              <div class="mb-3">
                <label for="currency" class="form-label">Currency:</label>
                <select name="currency" id="currency" required class="form-control">
                  <option value="MVR" <?php echo (isset($loan['currency']) && $loan['currency'] == 'MVR') ? 'selected' : ''; ?>>MVR</option>
                  <option value="USD" <?php echo (isset($loan['currency']) && $loan['currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" required class="form-control">
                  <option value="" disabled <?php echo !isset($loan['status']) ? 'selected' : ''; ?>>-- Select Status --</option>
                  <option value="Pending" <?php echo (isset($loan['status']) && $loan['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Approved" <?php echo (isset($loan['status']) && $loan['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                  <option value="Rejected" <?php echo (isset($loan['status']) && $loan['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="received" class="form-label">Received:</label>
                <input type="checkbox" name="received" id="received" <?php echo (isset($loan['received']) && $loan['received'] == 1) ? 'checked' : ''; ?>>
              </div>
              <button type="submit" class="btn btn-success w-100">Update Request</button>
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
