<?php
include '../db.php';
include '../session.php';

// Fetch all loans with employee details
$query = "
    SELECT 
        el.id, 
        el.employee_id, 
        el.loan_amount, 
        el.interest_rate, 
        el.emi, 
        el.total_outstanding, 
        el.installments_paid, 
        el.installment_period, 
        el.active_status, 
        el.created_at, 
        e.emp_no, 
        e.name, 
        e.designation
    FROM 
        employee_loan el
    JOIN 
        employees e 
    ON 
        el.employee_id = e.emp_no";

$result = $conn->query($query);

// Calculate Total Outstanding
$total_outstanding_query = "SELECT SUM(total_outstanding) AS total_outstanding FROM employee_loan WHERE active_status = 1";
$total_outstanding_result = $conn->query($total_outstanding_query);
$total_outstanding = $total_outstanding_result->fetch_assoc()['total_outstanding'];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Loan Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    .installment-breakdown {
      max-height: 200px;
      overflow-y: auto;
    }
    .installment-breakdown div {
      display: flex;
      justify-content: space-between;
      padding: 4px;
      border-bottom: 1px solid #e0e0e0;
    }
    .installment-breakdown div span {
      flex: 1;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <div class="container-fluid" style="max-width:100%;">
      <h1 class="my-4">Employee Loan Dashboard</h1>

      

      <div class="mb-3">
        <a href="add_loan.php" class="btn btn-primary">Add New Loan</a>
        <a href="summary.php" class="btn btn-primary">View Summary</a>
        <a href="deduct_payment.php" class="btn btn-primary">Deduct Payments</a>
        <a href="report.php" class="btn btn-primary">View Reports</a>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">All Loans</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Emp No</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Loan Amount</th>
                <th>Interest Rate (%)</th>
                <th>EMI</th>
                <th>Total Outstanding</th>
                <th>Installments Paid</th>
                <th>Installment Period</th>
                <th>Status</th>
                <th>Installment Breakdown</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php $serial = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $serial++ ?></td>
                    <td><?= $row['emp_no'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['designation'] ?></td>
                    <td><?= number_format($row['loan_amount'], 2) ?></td>
                    <td><?= number_format($row['interest_rate'], 2) ?>%</td>
                    <td><?= number_format($row['emi'], 2) ?></td>
                    <td>
                      <?php
                      $remaining_balance = $row['loan_amount'];
                      $monthly_interest_rate = $row['interest_rate'] / 12 / 100;
                    
                      for ($month = 1; $month <= $row['installments_paid']; $month++) {
                          $interest = round($remaining_balance * $monthly_interest_rate, 2);
                          $principal = round($row['emi'] - $interest, 2);
                          $remaining_balance = round($remaining_balance - $principal, 2);
                      }
                    
                      echo number_format($remaining_balance, 2); // Dynamically calculated remaining balance
                      ?>
                    </td>

                    <td><?= $row['installments_paid'] ?></td>
                    <td><?= $row['installment_period'] ?> months</td>
                    <td><?= $row['active_status'] ? 'Active' : 'Completed' ?></td>
                    <td>
                      <!-- Installment Breakdown -->
                      <div class="installment-breakdown">
                        <?php
                        $remaining_balance = $row['loan_amount'];
                        $monthly_interest_rate = $row['interest_rate'] / 12 / 100;

                        for ($month = 1; $month <= $row['installment_period']; $month++) {
                            // Calculate interest and principal for the month
                            $interest = round($remaining_balance * $monthly_interest_rate, 2);
                            $principal = round($row['emi'] - $interest, 2);
                            $remaining_balance = round($remaining_balance - $principal, 2);

                            // Determine the payment status
                            $status = $month <= $row['installments_paid'] ? 'Paid' : 'Unpaid';
                            $badge_class = $status === 'Paid' ? 'bg-success text-white' : 'bg-danger text-white';

                            // Display breakdown with balance
                            echo "<div>
                                    <span>Month $month</span>
                                    <span>" . number_format($remaining_balance, 2) . "</span>
                                    <span><span class='badge $badge_class'>$status</span></span>
                                  </div>";
                        }
                        ?>
                      </div>
                    </td>
                    <td>
                      <a href="loan_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Details</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="13" class="text-center">No loans found</td>
                </tr>
              <?php endif; ?>
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
