<?php 
include 'session.php'; // Ensure user is logged in
include 'db.php'; // Database connection

// Fetch KPI data
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'] ?? 0;

$totalVisaStickers = $conn->query("SELECT COUNT(*) AS total FROM visa_sticker")->fetch_assoc()['total'] ?? 0;
$completedVisaStickers = $conn->query("SELECT COUNT(*) AS total FROM visa_sticker WHERE visa_status = 'Completed'")->fetch_assoc()['total'] ?? 0;

$totalCardPrints = $conn->query("SELECT COUNT(*) AS total FROM card_print WHERE status = 'Printed'")->fetch_assoc()['total'] ?? 0;
$pendingCardPrints = $conn->query("SELECT COUNT(*) AS total FROM card_print WHERE status = 'Pending'")->fetch_assoc()['total'] ?? 0;

$totalLeaveApplications = $conn->query("SELECT COUNT(*) AS total FROM leave_records")->fetch_assoc()['total'] ?? 0;

$resignedStaffCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Resigned'")->fetch_assoc()['total'] ?? 0;
$terminatedStaffCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Terminated'")->fetch_assoc()['total'] ?? 0;

$visaProgress = ($totalVisaStickers > 0) ? ($completedVisaStickers / $totalVisaStickers) * 100 : 0;
$cardPrintProgress = ($totalCardPrints + $pendingCardPrints > 0) ? ($totalCardPrints / ($totalCardPrints + $pendingCardPrints)) * 100 : 0;

// Fetch recent leave applications
$recentLeavesQuery = "
  SELECT lr.emp_no, e.name, lt.name AS leave_type, lr.start_date, lr.end_date, lr.status 
  FROM leave_records lr
  JOIN employees e ON lr.emp_no = e.emp_no
  JOIN leave_types lt ON lr.leave_type_id = lt.id
  ORDER BY lr.applied_date DESC LIMIT 5";
$recentLeaves = $conn->query($recentLeavesQuery);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Leave Officer Dashboard</title>
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
        <h5 class="fw-semibold mb-4">Leave Officer Dashboard</h5>

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
                <h5 class="card-title fw-semibold">Total Leave Applications</h5>
                <h4 class="fw-bold text-warning"><?php echo $totalLeaveApplications; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Resigned Staff</h5>
                <h4 class="fw-bold text-danger"><?php echo $resignedStaffCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Terminated Staff</h5>
                <h4 class="fw-bold text-danger"><?php echo $terminatedStaffCount; ?></h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bars Section -->
        <div class="row mb-4">
          <!-- Visa Sticker Progress -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Visa Sticker Progress</h5>
                <div class="progress" role="progressbar" aria-label="Visa Sticker Progress" aria-valuenow="<?php echo $visaProgress; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-success" style="width: <?php echo $visaProgress; ?>%">
                    <?php echo round($visaProgress, 2); ?>% Completed
                  </div>
                </div>
                <p class="text-muted mt-3"><?php echo "$completedVisaStickers out of $totalVisaStickers completed"; ?></p>
              </div>
            </div>
          </div>

          <!-- Card Print Progress -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Card Print Progress</h5>
                <div class="progress" role="progressbar" aria-label="Card Print Progress" aria-valuenow="<?php echo $cardPrintProgress; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: <?php echo $cardPrintProgress; ?>%">
                    <?php echo round($cardPrintProgress, 2); ?>% Completed
                  </div>
                </div>
                <p class="text-muted mt-3"><?php echo "$totalCardPrints Printed, $pendingCardPrints Pending"; ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Leave Applications -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Recent Leave Applications</h5>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Employee No</th>
                    <th>Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($recentLeaves->num_rows > 0): ?>
                    <?php while ($leave = $recentLeaves->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo $leave['emp_no']; ?></td>
                        <td><?php echo htmlspecialchars($leave['name']); ?></td>
                        <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($leave['start_date'])); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($leave['end_date'])); ?></td>
                        <td>
                          <span class="badge <?php echo ($leave['status'] == 'Approved') ? 'bg-success' : (($leave['status'] == 'Rejected') ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                            <?php echo htmlspecialchars($leave['status']); ?>
                          </span>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">No recent leave applications.</td>
                    </tr>
                  <?php endif; ?>
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
