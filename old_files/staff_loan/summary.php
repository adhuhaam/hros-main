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
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loan Summary</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <div class="container-fluid">
      <h1 class="my-4">Loan Summary</h1>
      <div class="mb-3">
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Summary of All Loans</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Emp No</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Loan Amount</th>
                <th>Annual Interest (%)</th>
                <th>EMI</th>
                <th>Total Outstanding</th>
                <th>Installments Paid</th>
                <th>Installment Period</th>
                <th>Status</th>
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
                    <td><?= number_format($row['total_outstanding'], 2) ?></td>
                    <td><?= $row['installments_paid'] ?></td>
                    <td><?= $row['installment_period'] ?> months</td>
                    <td><?= $row['active_status'] ? 'Active' : 'Completed' ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="11" class="text-center">No loans found</td>
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
