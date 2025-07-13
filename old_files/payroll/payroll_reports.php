<?php
session_start();
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Default to current month
$currentMonth = date('m');
$currentYear = date('Y');

// Get filter values
$month = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
$year = isset($_GET['year']) ? $_GET['year'] : $currentYear;
$emp_no = isset($_GET['emp_no']) ? $_GET['emp_no'] : "";

// Prepare SQL Query with Filters
$query = "SELECT ps.payroll_month, e.name, e.emp_no, ps.total_earnings, ps.total_deductions, ps.net_pay 
          FROM payroll_summary ps 
          JOIN employees e ON ps.emp_no = e.emp_no 
          WHERE MONTH(ps.payroll_month) = ? AND YEAR(ps.payroll_month) = ?";

$params = [$month, $year];
$types = "ii";

if (!empty($emp_no)) {
    $query .= " AND ps.emp_no = ?";
    $params[] = $emp_no;
    $types .= "s";
}

$query .= " ORDER BY ps.payroll_month DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$payroll_summary = $stmt->get_result();

// Fetch Employees for Filtering
$employees = $conn->query("SELECT emp_no, name FROM employees ORDER BY name ASC");

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payroll_report_' . $month . '-' . $year . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Payroll Month', 'Employee ID', 'Employee Name', 'Total Earnings', 'Total Deductions', 'Net Pay']);

    while ($row = $payroll_summary->fetch_assoc()) {
        fputcsv($output, [$row['payroll_month'], $row['emp_no'], $row['name'], $row['total_earnings'], $row['total_deductions'], $row['net_pay']]);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Reports</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper">
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <div class="container-fluid">
      <h2>Payroll Reports</h2>

      <!-- Filters -->
      <form method="GET" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Month</label>
          <select name="month" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Year</label>
          <select name="year" class="form-control">
            <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
              <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Employee</label>
          <select name="emp_no" class="form-control">
            <option value="">All Employees</option>
            <?php while ($emp = $employees->fetch_assoc()): ?>
              <option value="<?php echo $emp['emp_no']; ?>" <?php echo ($emp['emp_no'] == $emp_no) ? 'selected' : ''; ?>>
                <?php echo $emp['name']; ?> (<?php echo $emp['emp_no']; ?>)
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3 align-self-end">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="?month=<?php echo $month; ?>&year=<?php echo $year; ?>&emp_no=<?php echo $emp_no; ?>&export=csv" class="btn btn-success">Export CSV</a>
        </div>
      </form>

      <!-- Payroll Summary Table -->
      <h5 class="mt-4">Payroll Summary</h5>
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>Payroll Month</th>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Total Earnings</th>
            <th>Total Deductions</th>
            <th>Net Pay</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($payroll_summary->num_rows > 0): ?>
            <?php while ($row = $payroll_summary->fetch_assoc()): ?>
              <tr>
                <td><?php echo date('F Y', strtotime($row['payroll_month'])); ?></td>
                <td><?php echo $row['emp_no']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo number_format($row['total_earnings'], 2); ?></td>
                <td><?php echo number_format($row['total_deductions'], 2); ?></td>
                <td><b><?php echo number_format($row['net_pay'], 2); ?></b></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">No payroll records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>