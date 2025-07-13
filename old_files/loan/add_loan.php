<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $amount = floatval($_POST['amount']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $currency = $conn->real_escape_string($_POST['currency']);
    $status = $conn->real_escape_string($_POST['status']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $updateSql = "UPDATE salary_loans SET emp_no = ?, amount = ?, purpose = ?, currency = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sdsssi", $emp_no, $amount, $purpose, $currency, $status, $id);
    } else {
        $insertSql = "INSERT INTO salary_loans (emp_no, amount, purpose, currency, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sdsss", $emp_no, $amount, $purpose, $currency, $status);
    }

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM salary_loans WHERE id = $id");
    $loan = $result->fetch_assoc();
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($loan) ? 'Edit Loan' : 'Add Loan'; ?></title>
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
            <h5 class="card-title fw-semibold mb-4"><?php echo isset($loan) ? 'Edit Loan Request' : 'Add Loan Request'; ?></h5>
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
              <button type="submit" class="btn btn-success w-100"><?php echo isset($loan) ? 'Update Request' : 'Add Request'; ?></button>
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
