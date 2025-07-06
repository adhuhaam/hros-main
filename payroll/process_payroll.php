<?php
session_start();
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payroll_month = $_POST['payroll_month'] ?? null;

    // Ensure valid date format (YYYY-MM-DD)
    if (!$payroll_month || !preg_match("/^\d{4}-\d{2}$/", $payroll_month)) {
        $_SESSION['error'] = "Invalid payroll month format. Please select a valid month.";
        header("Location: process_payroll.php");
        exit();
    }

    // Convert to full date format for storage (e.g., 2025-02-01 for February)
    $payroll_month = $payroll_month . "-01";

    // Check if payroll already exists for this month
    $payrollCheck = $conn->prepare("SELECT COUNT(*) as count FROM payroll_summary WHERE payroll_month = ?");
    $payrollCheck->bind_param("s", $payroll_month);
    $payrollCheck->execute();
    $result = $payrollCheck->get_result()->fetch_assoc();

    if ($result['count'] > 0) {
        $_SESSION['error'] = "Payroll for this month has already been processed!";
        header("Location: process_payroll.php");
        exit();
    }

    // Fetch employee salaries and deductions
    $employees = $conn->query("SELECT e.emp_no, e.name, 
                                      s.basic_salary, s.service_allowance, s.island_allowance, s.attendance_allowance, s.pension,
                                      d.other_deduction, d.salary_advance, d.loan, d.pension AS pension_deduction, d.medical_deduction, d.no_pay, d.late 
                               FROM employees e
                               LEFT JOIN salary_income s ON e.emp_no = s.emp_no
                               LEFT JOIN salary_deductions d ON e.emp_no = d.emp_no
                               ORDER BY e.emp_no");

    while ($row = $employees->fetch_assoc()) {
        $emp_no = $row['emp_no'];
        $total_earnings = $row['basic_salary'] + $row['service_allowance'] + $row['island_allowance'] + $row['attendance_allowance'];
        $total_deductions = $row['other_deduction'] + $row['salary_advance'] + $row['loan'] + $row['pension_deduction'] + $row['medical_deduction'] + $row['no_pay'] + $row['late'];
        $net_pay = $total_earnings - $total_deductions;

        // Insert into payroll_summary
        $insertPayroll = $conn->prepare("INSERT INTO payroll_summary (emp_no, payroll_month, total_earnings, total_deductions, net_pay) VALUES (?, ?, ?, ?, ?)");
        $insertPayroll->bind_param("ssddd", $emp_no, $payroll_month, $total_earnings, $total_deductions, $net_pay);
        $insertPayroll->execute();
    }

    $_SESSION['message'] = "Payroll processed successfully for " . date("F Y", strtotime($payroll_month)) . "!";
    header("Location: process_payroll.php");
    exit();
}

// Fetch existing payroll records
$payrollRecords = $conn->query("SELECT p.*, e.name FROM payroll_summary p JOIN employees e ON p.emp_no = e.emp_no ORDER BY p.payroll_month DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Processing</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
    
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <div class="container-fluid">
      <h2>Payroll Processing</h2>

      <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
      <?php elseif (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <!-- Process Payroll Form -->
      <div class="card p-3 mb-4">
        <h5>Generate Payroll</h5>
        <form action="process_payroll.php" method="POST">
          <label class="form-label">Select Payroll Month</label>
          <input type="month" name="payroll_month" class="form-control" required>
          <button type="submit" class="btn btn-success mt-3">Process Payroll</button>
        </form>
      </div>

      <!-- Payroll Records -->
      <h5>Payroll History</h5>
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>Payroll Month</th>
            <th>Employee No</th>
            <th>Name</th>
            <th>Total Earnings</th>
            <th>Total Deductions</th>
            <th>Net Pay</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $payrollRecords->fetch_assoc()): ?>
            <tr>
              <td><?php echo date('F Y', strtotime($row['payroll_month'])); ?></td>
              <td><?php echo $row['emp_no']; ?></td>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo number_format($row['total_earnings'], 2); ?></td>
              <td><?php echo number_format($row['total_deductions'], 2); ?></td>
              <td><?php echo number_format($row['net_pay'], 2); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>