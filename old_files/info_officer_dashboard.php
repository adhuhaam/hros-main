THIS SHOULD BE A LINTER ERROR<?php
include 'db.php';
include 'session.php';

// Fetch KPIs
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'];
$totalNotices = $conn->query("SELECT COUNT(*) AS total FROM notices")->fetch_assoc()['total'];
$totalCardsPending = $conn->query("SELECT COUNT(*) AS total FROM card_print WHERE status = 'Pending'")->fetch_assoc()['total'];
$totalCardsPrinted = $conn->query("SELECT COUNT(*) AS total FROM card_print WHERE status = 'Printed'")->fetch_assoc()['total'];
$totalVisaExpiringSoon = $conn->query("SELECT COUNT(*) AS total FROM visa_sticker WHERE visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)")->fetch_assoc()['total'];
$totalActiveEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'")->fetch_assoc()['total'];

// Calculate Progress Bars
$totalCardsProcessed = $totalCardsPending + $totalCardsPrinted;
$cardsProgressPending = ($totalCardsProcessed > 0) ? ($totalCardsPending / $totalCardsProcessed) * 100 : 0;
$cardsProgressPrinted = ($totalCardsProcessed > 0) ? ($totalCardsPrinted / $totalCardsProcessed) * 100 : 0;

$totalVisaStickers = $conn->query("SELECT COUNT(*) AS total FROM visa_sticker")->fetch_assoc()['total'];
$visaStickersExpiring = ($totalVisaStickers > 0) ? ($totalVisaExpiringSoon / $totalVisaStickers) * 100 : 0;
$visaStickersValid = ($totalVisaStickers > 0) ? 100 - $visaStickersExpiring : 0;
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Information Officer Dashboard</title>
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
        <h5 class="fw-semibold mb-4">Information Officer Dashboard</h5>

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
                <h5 class="card-title fw-semibold">Total Notices</h5>
                <h4 class="fw-bold text-info"><?php echo $totalNotices; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Pending Cards</h5>
                <h4 class="fw-bold text-warning"><?php echo $totalCardsPending; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Printed Cards</h5>
                <h4 class="fw-bold text-success"><?php echo $totalCardsPrinted; ?></h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bars -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Progress Overview</h5>
            <div class="row">
              <div class="col-lg-6 mb-4">
                <h6>Card Print Progress</h6>
                <div class="progress" role="progressbar" aria-label="Card Print Progress" aria-valuenow="<?php echo $cardsProgressPending; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-warning" style="width: <?php echo $cardsProgressPending; ?>%">
                    <?php echo round($cardsProgressPending, 2); ?>% Pending
                  </div>
                  <div class="progress-bar bg-success" style="width: <?php echo $cardsProgressPrinted; ?>%">
                    <?php echo round($cardsProgressPrinted, 2); ?>% Printed
                  </div>
                </div>
              </div>
              <div class="col-lg-6 mb-4">
                <h6>Visa Stickers Progress</h6>
                <div class="progress" role="progressbar" aria-label="Visa Sticker Progress" aria-valuenow="<?php echo $visaStickersExpiring; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-danger" style="width: <?php echo $visaStickersExpiring; ?>%">
                    <?php echo round($visaStickersExpiring, 2); ?>% Expiring Soon
                  </div>
                  <div class="progress-bar bg-primary" style="width: <?php echo $visaStickersValid; ?>%">
                    <?php echo round($visaStickersValid, 2); ?>% Valid
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notices Table -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Recent Notices</h5>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 10");
                  $count = 1;
                  while ($notice = $notices->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $count++; ?></td>
                      <td><?php echo htmlspecialchars($notice['title']); ?></td>
                      <td><?php echo date('d-M-Y', strtotime($notice['created_at'])); ?></td>
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
