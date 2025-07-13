<?php
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
        <h5 class="fw-semibold mb-4">Information Dashboard</h5>

       


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
