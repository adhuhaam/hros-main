<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    $loan_amount = floatval($_POST['loan_amount']);
    $annual_interest_rate = floatval($_POST['interest_rate']); // 12% annually
    $monthly_interest_rate = $annual_interest_rate / 12 / 100; // 1% per month
    $tenure_months = intval($_POST['tenure_months']);

    // Initialize values
    $remaining_balance = $loan_amount;
    $total_interest = 0;

    // Calculate the first month's EMI
    $emi = ($loan_amount * $monthly_interest_rate * pow(1 + $monthly_interest_rate, $tenure_months)) /
           (pow(1 + $monthly_interest_rate, $tenure_months) - 1);
    $emi = round($emi, 2);

    // Save loan details to the database
    $query = "INSERT INTO employee_loan (employee_id, loan_amount, interest_rate, emi, installment_period, remaining_balance, active_status)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $active_status = 1; // Active loan
    $stmt->bind_param("sddidii", $employee_id, $loan_amount, $annual_interest_rate, $emi, $tenure_months, $remaining_balance, $active_status);

    if ($stmt->execute()) {
        header('Location: index.php'); // Redirect to the dashboard
        exit();
    } else {
        $error = "Error adding loan: " . $conn->error;
    }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Loan</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <div class="container-fluid">
      <h1 class="my-4">Add New Loan</h1>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      <div class="card">
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label for="employee_id" class="form-label">Employee ID</label>
              <input type="text" id="employee_id" name="employee_id" required class="form-control">
            </div>
            <div class="mb-3">
              <label for="loan_amount" class="form-label">Loan Amount</label>
              <input type="number" id="loan_amount" name="loan_amount" required class="form-control">
            </div>
            <div class="mb-3">
              <label for="interest_rate" class="form-label">Annual Interest Rate (%)</label>
              <input type="number" id="interest_rate" name="interest_rate" required class="form-control" step="0.01">
            </div>
            <div class="mb-3">
              <label for="tenure_months" class="form-label">Loan Tenure (Months)</label>
              <input type="number" id="tenure_months" name="tenure_months" required class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Add Loan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/app.min.js"></script>
</body>
</html>
