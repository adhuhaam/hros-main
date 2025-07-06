<?php

include 'db.php';
include 'session.php';

// Fetch relevant KPIs for the Xpat Officer Dashboard
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'")->fetch_assoc()['total'] ?? 0;

// Visa sticker progress bar calculations
$validVisaStickers = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visa_sticker 
    WHERE visa_expiry_date > DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$expiringSoonVisaStickers = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visa_sticker 
    WHERE visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$totalVisaStickers = $validVisaStickers + $expiringSoonVisaStickers;

// Calculate percentages for progress bars
$visaExpiringPercentage = $totalVisaStickers > 0 ? ($expiringSoonVisaStickers / $totalVisaStickers) * 100 : 0;
$visaValidPercentage = $totalVisaStickers > 0 ? ($validVisaStickers / $totalVisaStickers) * 100 : 0;

// Medical progress bar calculations (using medical_examinations table)
$totalMedicalRecords = $conn->query("SELECT COUNT(*) AS total FROM medical_examinations")->fetch_assoc()['total'] ?? 0;
$pendingMedicalRecords = $conn->query("SELECT COUNT(*) AS total FROM medical_examinations WHERE status = 'Pending'")->fetch_assoc()['total'] ?? 0;
$completedMedicalRecords = $totalMedicalRecords - $pendingMedicalRecords;

// Calculate percentages for medical progress bars
$medicalPendingPercentage = $totalMedicalRecords > 0 ? ($pendingMedicalRecords / $totalMedicalRecords) * 100 : 0;
$medicalCompletedPercentage = $totalMedicalRecords > 0 ? ($completedMedicalRecords / $totalMedicalRecords) * 100 : 0;

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Xpat Officer Dashboard</title>
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

      <!-- Main Content -->
      <div class="container-fluid">
        <div class="row">
          <!-- KPI Cards -->
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Total Active Employees</h5>
                <h3 class="fw-bold text-primary"><?php echo $totalEmployees; ?></h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bars Section -->
        <div class="row mt-4">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Visa Sticker Status</h5>
                <p class="mb-1">Expiring in ≤ 3 Months: <?php echo $expiringSoonVisaStickers; ?></p>
                <div class="progress mb-3">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: <?php echo $visaExpiringPercentage; ?>%;" 
                  aria-valuenow="<?php echo $visaExpiringPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo round($visaExpiringPercentage, 2); ?>%
                  </div>
                </div>
                <p class="mb-1">Validity > 3 Months: <?php echo $validVisaStickers; ?></p>
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: <?php echo $visaValidPercentage; ?>%;" 
                  aria-valuenow="<?php echo $visaValidPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo round($visaValidPercentage, 2); ?>%
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Medical Status</h5>
                <p class="mb-1">Pending: <?php echo $pendingMedicalRecords; ?></p>
                <div class="progress mb-3">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: <?php echo $medicalPendingPercentage; ?>%;" 
                  aria-valuenow="<?php echo $medicalPendingPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo round($medicalPendingPercentage, 2); ?>%
                  </div>
                </div>
                <p class="mb-1">Completed: <?php echo $completedMedicalRecords; ?></p>
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: <?php echo $medicalCompletedPercentage; ?>%;" 
                  aria-valuenow="<?php echo $medicalCompletedPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo round($medicalCompletedPercentage, 2); ?>%
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Visa Sticker Activity -->
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Recent Visa Sticker Updates</h5>
            <div class="table-responsive">
              <table class="table table-striped align-middle">
                <thead>
                  <tr>
                    <th>Employee No</th>
                    <th>Visa Expiry Date</th>
                    <th>Visa Status</th>
                    <th>Updated At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $recentVisaUpdates = $conn->query("
                    SELECT emp_no, visa_expiry_date, visa_status, updated_at 
                    FROM visa_sticker 
                    ORDER BY updated_at DESC 
                    LIMIT 5
                  ");
                  while ($row = $recentVisaUpdates->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                      <td><?php echo date('d-M-Y', strtotime($row['visa_expiry_date'])); ?></td>
                      <td><?php echo htmlspecialchars($row['visa_status']); ?></td>
                      <td><?php echo date('d-M-Y', strtotime($row['updated_at'])); ?></td>
                    </tr>
                  <?php endwhile; ?>
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
