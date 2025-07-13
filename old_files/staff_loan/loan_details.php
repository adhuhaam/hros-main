<?php
include '../db.php';

$loan_id = $_GET['id'];

// Fetch loan details
$query = "SELECT * FROM employee_loan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$loan = $stmt->get_result()->fetch_assoc();

// Calculate Amortization Schedule
$remaining_balance = $loan['remaining_balance'];
$monthly_interest_rate = $loan['interest_rate'] / 12 / 100;
$tenure_months = $loan['installment_period'];
$emi = $loan['emi'];
$installments_paid = $loan['installments_paid'];

$schedule = [];
for ($month = 1; $month <= $tenure_months; $month++) {
    $interest = round($remaining_balance * $monthly_interest_rate, 2);
    $principal = round($emi - $interest, 2);
    $remaining_balance = round($remaining_balance - $principal, 2);

    $schedule[] = [
        'month' => $month,
        'emi' => $emi,
        'interest' => $interest,
        'principal' => $principal,
        'remaining_balance' => $remaining_balance > 0 ? $remaining_balance : 0,
        'status' => $month <= $installments_paid ? 'Paid' : 'Unpaid'
    ];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loan Details</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper">
  <!-- Sidebar -->
  <?php include '../sidebar.php'; ?>

  <!-- Main Content -->
  <div class="body-wrapper">
    <div class="container-fluid">
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Loan Amortization Schedule</h5>

          <!-- Loan Details -->
          <div class="row">
            <div class="col-md-6">
              <p><strong>Employee ID:</strong> <?= $loan['employee_id'] ?></p>
              <p><strong>Loan Amount:</strong> <?= number_format($loan['loan_amount'], 2) ?></p>
              <p><strong>Interest Rate:</strong> <?= number_format($loan['interest_rate'], 2) ?>%</p>
            </div>
            <div class="col-md-6">
              <p><strong>EMI:</strong> <?= number_format($loan['emi'], 2) ?></p>
              <p><strong>Total Outstanding:</strong> <?= number_format($loan['remaining_balance'], 2) ?></p>
              <p><strong>Installments Paid:</strong> <?= $loan['installments_paid'] ?> / <?= $loan['installment_period'] ?></p>
            </div>
          </div>

          <!-- Amortization Table -->
          <div class="table-responsive">
            <table class="table table-bordered text-nowrap mt-4">
              <thead class="bg-light-primary">
                <tr>
                  <th>Month</th>
                  <th>EMI</th>
                  <th>Interest</th>
                  <th>Principal</th>
                  <th>Remaining Balance</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($schedule as $row): ?>
                  <tr>
                    <td><?= $row['month'] ?></td>
                    <td><?= number_format($row['emi'], 2) ?></td>
                    <td><?= number_format($row['interest'], 2) ?></td>
                    <td><?= number_format($row['principal'], 2) ?></td>
                    <td><?= number_format($row['remaining_balance'], 2) ?></td>
                    <td>
                      <?php if ($row['status'] == 'Paid'): ?>
                        <span class="badge bg-success text-white">Paid</span>
                      <?php else: ?>
                        <span class="badge bg-danger text-white">Unpaid</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Back Button -->
          <div class="mt-4">
            <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>
