<?php
session_start();
include '../db.php';
include '../session.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch payroll summary data
$totalEmployees = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'] ?? 0;
$totalIncome = $conn->query("SELECT SUM(basic_salary + service_allowance + island_allowance + attendance_allowance + salary_arrear_other + safety_allowance + pump_brick_batching + food_and_tea + long_term_service_allowance + living_allowance + ot + ot_arrears + phone_allowance + petrol_allowance + pension) AS total FROM salary_income WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0.00;
$totalDeductions = $conn->query("SELECT SUM(other_deduction + salary_advance + loan + pension + medical_deduction + no_pay + late) AS total FROM salary_deductions WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0.00;
$totalNetPay = $totalIncome - $totalDeductions;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <header class="app-header">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="navbar-collapse justify-content-end">
          <h2 class="fw-bold">Payroll Dashboard</h2>
        </div>
      </nav>
    </header>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="card">
            <div class="card-body text-center">
              <h5 class="card-title">Total Employees</h5>
              <p class="fs-3"><?php echo $totalEmployees; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card">
            <div class="card-body text-center">
              <h5 class="card-title">Total Payroll (MVR)</h5>
              <p class="fs-3"><?php echo number_format($totalIncome, 2); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card">
            <div class="card-body text-center">
              <h5 class="card-title">Total Deductions (MVR)</h5>
              <p class="fs-3"><?php echo number_format($totalDeductions, 2); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card">
            <div class="card-body text-center">
              <h5 class="card-title">Net Payroll (MVR)</h5>
              <p class="fs-3"><?php echo number_format($totalNetPay, 2); ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-4"><a href="salary_setup.php" class="btn btn-primary w-100">Manage Salaries</a></div>
        <div class="col-md-4"><a href="process_payroll.php" class="btn btn-success w-100">Process Payroll</a></div>
        <div class="col-md-4"><a href="salary_slip_generator.php" class="btn btn-warning w-100">Generate Salary Slips</a></div>
      </div>

      <div class="row mt-3">
        <div class="col-md-4"><a href="manage_deductions.php" class="btn btn-danger w-100">Manage Deductions</a></div>
        <div class="col-md-4"><a href="loan_management.php" class="btn btn-info w-100">Manage Loans</a></div>
        <div class="col-md-4"><a href="payroll_reports.php" class="btn btn-secondary w-100">View Payroll Reports</a></div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>
