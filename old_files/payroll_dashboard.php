<?php
include 'session.php'; // Ensure user is logged in
include 'db.php'; // Database connection

// Fetch data for KPIs
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'] ?? 0;

// Check attendance data for the current date
$totalAttendanceToday = $conn->query("
    SELECT COUNT(*) AS total 
    FROM attendance_records 
    WHERE present_absent = 'Present' AND upload_date = CURDATE()
")->fetch_assoc()['total'] ?? 0;

$totalAbsenteesToday = $conn->query("
    SELECT COUNT(*) AS total 
    FROM attendance_records 
    WHERE present_absent = 'Absent' AND upload_date = CURDATE()
")->fetch_assoc()['total'] ?? 0;

$totalNetSalaryPaid = $conn->query("
    SELECT SUM(net_salary) AS total 
    FROM salary_slips 
    WHERE status = 'issued'
")->fetch_assoc()['total'] ?? 0;

$totalPendingPayrolls = $conn->query("
    SELECT COUNT(*) AS total 
    FROM salary_slips 
    WHERE status = 'pending'
")->fetch_assoc()['total'] ?? 0;

// Calculate percentages
$totalRecorded = $totalAttendanceToday + $totalAbsenteesToday;
$presentPercentage = ($totalRecorded > 0) ? ($totalAttendanceToday / $totalRecorded) * 100 : 0;
$absentPercentage = ($totalRecorded > 0) ? ($totalAbsenteesToday / $totalRecorded) * 100 : 0;
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payroll Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="body-wrapper">
      <!-- Header -->
      <?php include 'header.php'; ?>

      <div class="container-fluid">
        <h5 class="fw-semibold mb-4">Payroll Dashboard</h5>

        <!-- KPI Section -->
        <div class="row mb-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Total Employees</h5>
                <h4 class="fw-bold text-primary"><?php echo $totalEmployees; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Net Salary Paid (MVR)</h5>
                <h4 class="fw-bold text-success">MVR <?php echo number_format($totalNetSalaryPaid, 2); ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Pending Payrolls</h5>
                <h4 class="fw-bold text-danger"><?php echo $totalPendingPayrolls; ?></h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Attendance Summary -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Overall Attendance Summary</h5>
            <div class="row">
              <div class="col-lg-6">
                <div class="progress" role="progressbar" aria-label="Present Percentage" aria-valuenow="<?php echo $presentPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: <?php echo $presentPercentage; ?>%">
                    <?php echo round($presentPercentage, 2); ?>% Present
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="progress" role="progressbar" aria-label="Absent Percentage" aria-valuenow="<?php echo $absentPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" style="width: <?php echo $absentPercentage; ?>%">
                    <?php echo round($absentPercentage, 2); ?>% Absent
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Payroll History -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Recent Payroll History</h5>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Employee No</th>
                    <th>Net Salary (MVR)</th>
                    <th>Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $payrollQuery = "
                    SELECT emp_no, net_salary, date, status 
                    FROM salary_slips 
                    ORDER BY date DESC LIMIT 10";
                  $payrollResult = $conn->query($payrollQuery);

                  if ($payrollResult->num_rows > 0) {
                    while ($row = $payrollResult->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['emp_no']}</td>
                              <td>MVR " . number_format($row['net_salary'], 2) . "</td>
                              <td>" . date('d-M-Y', strtotime($row['date'])) . "</td>
                              <td>{$row['status']}</td>
                            </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='4' class='text-center'>No payroll history found.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
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
