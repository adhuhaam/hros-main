<?php
include '../db.php';
include '../session.php';

// Fetch loans for the report
$query = "SELECT id, employee_id, loan_amount, total_outstanding, installments_paid, installment_period, active_status FROM employee_loan";
$result = $conn->query($query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loan Reports</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <div class="container-fluid">
      <h1 class="my-4">Loan Reports</h1>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Overall Loan Summary</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Employee ID</th>
                <th>Loan Amount</th>
                <th>Total Outstanding</th>
                <th>Installments Paid</th>
                <th>Total Installments</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['employee_id'] ?></td>
                  <td><?= number_format($row['loan_amount'], 2) ?></td>
                  <td><?= number_format($row['total_outstanding'], 2) ?></td>
                  <td><?= $row['installments_paid'] ?></td>
                  <td><?= $row['installment_period'] ?></td>
                  <td><?= $row['active_status'] ? 'Active' : 'Completed' ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/app.min.js"></script>
</body>
</html>
