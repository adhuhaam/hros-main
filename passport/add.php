<?php
include '../db.php';
include '../session.php';

$alert = '';
$employeeInfo = null;
$showInfoForm = false; // Flag to control information form display

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);

    // Step 1: Check Button Logic
    if (isset($_POST['check_status'])) {
        // Check for approved salary advance
        $loanQuery = "SELECT status FROM salary_loans WHERE emp_no = ? AND status = 'Management Approved'";
        $stmtLoan = $conn->prepare($loanQuery);
        $stmtLoan->bind_param("s", $emp_no);
        $stmtLoan->execute();
        $loanResult = $stmtLoan->get_result();

        // Fetch passport expiry date
        $employeeQuery = "SELECT passport_nic_no_expires FROM employees WHERE emp_no = ?";
        $stmtEmp = $conn->prepare($employeeQuery);
        $stmtEmp->bind_param("s", $emp_no);
        $stmtEmp->execute();
        $employeeResult = $stmtEmp->get_result();
        $employeeInfo = $employeeResult->fetch_assoc();

        if ($loanResult->num_rows > 0 && $employeeInfo) {
            $alert = "<div class='alert alert-success'>Cash ready. Passport expiry date: " . date("d-M-Y", strtotime($employeeInfo['passport_nic_no_expires'])) . "</div>";
            $showInfoForm = true; // Show information collection form
        } else {
            $alert = "<div class='alert alert-danger'>No salary advance approved or no employee found.</div>";
        }
    }

    // Step 2: Save Employee Information
    if (isset($_POST['save_info'])) {
        $emp_no = $conn->real_escape_string($_POST['emp_no']);
        $renewal_date = $conn->real_escape_string($_POST['renewal_date']);
        $remarks = $conn->real_escape_string($_POST['remarks']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $address = $conn->real_escape_string($_POST['address']);

        $insertSql = "INSERT INTO passport_renewals (emp_no, renewal_date, remarks, phone, email, address) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertSql);
        $stmtInsert->bind_param("ssssss", $emp_no, $renewal_date, $remarks, $phone, $email, $address);
        $stmtInsert->execute();

        header('Location: index.php');
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Passport Renewal</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css">
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
            <h5 class="card-title fw-semibold mb-4">Add Passport Renewal Request</h5>
            <?php if (!empty($alert)): ?>
              <?php echo $alert; ?>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Employee No:</label>
                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No">
              </div>
              <button type="submit" name="check_status" class="btn btn-info mb-3">Check</button>
            </form>
            <?php if ($showInfoForm): ?>
            <form method="POST" class="shadow p-4 bg-light rounded mt-4">
              <input type="hidden" name="emp_no" value="<?php echo htmlspecialchars($emp_no); ?>">
              <div class="mb-3">
                <label for="renewal_date" class="form-label">Renewal Date:</label>
                <input type="date" name="renewal_date" id="renewal_date" required class="form-control">
              </div>
              <div class="mb-3">
                <label for="remarks" class="form-label">Remarks:</label>
                <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter any remarks"></textarea>
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone:</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address">
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea name="address" id="address" class="form-control" placeholder="Enter address"></textarea>
              </div>
              <button type="submit" name="save_info" class="btn btn-success w-100">Submit</button>
            </form>
            <?php endif; ?>
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
