<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>HR Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <!--link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"-->
  <style>
    .nav-tabs .nav-link {
      border: none;
      border-bottom: 2px solid transparent;
      color: #555;
      font-weight: 500;
    }

    .nav-tabs .nav-link.active {
      color: #0d6efd;
      border-color: #0d6efd;
      background-color: #f8f9fa;
    }

    .tab-pane {
      padding: 1rem;
    }

    .tab-content {
      border: 1px solid #dee2e6;
      border-top: none;
      background-color: #fff;
      border-radius: 0 0 8px 8px;
    }
  </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid" style="max-width:100%;">
      <div class="card">
        <div class="card-body">
          <h5 class="fs-6 card-title fw-bold text-center mb-4">HR Report</h5>

          <!-- Filter & Export (Separate Forms) -->
          <div class="row g-3 align-items-end mb-4">
            <!-- Date Filter -->
            <div class="col-md-7">
              <form method="GET" class="row g-3">
                <div class="col-md-12">
                  <label for="from_date" class="form-label">From Date</label>
                  <input type="date" name="from_date" id="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
                
                  <label for="to_date" class="form-label">To Date</label>
                  <input type="date" name="to_date" id="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
                </div>
                <div class="col-12 d-flex">
                  <button type="submit" class="btn btn-primary me-2">Apply Filter</button>
                  <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                </div>
              </form>
            </div>

            <!-- Export Button -->
            <div class="col-md-5 text-end">
              <form method="GET" action="export_hr_report_excel.php" target="_blank">
                <input type="hidden" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                <input type="hidden" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                <button type="submit" class="btn btn-success w-100 mt-md-4 mt-3">
                  <i class="fa fa-file-excel"></i> Export Excel
                </button>
              </form>
            </div>
          </div>

          <!-- Nav Tabs -->
          <ul class="nav nav-tabs" id="hrReportTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">Summary</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="on-leave-tab" data-bs-toggle="tab" data-bs-target="#on-leave" type="button" role="tab">On Leave</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">Returned</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="requested-tab" data-bs-toggle="tab" data-bs-target="#requested" type="button" role="tab">Requested</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="terminated-tab" data-bs-toggle="tab" data-bs-target="#terminated" type="button" role="tab">Terminated / Resigned</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending Arrival</button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="hrReportTabsContent">
            <div class="tab-pane fade show active" id="summary" role="tabpanel"><?php include 'summary.php'; ?></div>
            <div class="tab-pane fade" id="on-leave" role="tabpanel"><?php include 'on_leave.php'; ?></div>
            <div class="tab-pane fade" id="returned" role="tabpanel"><?php include 'returned.php'; ?></div>
            <div class="tab-pane fade" id="requested" role="tabpanel"><?php include 'requested.php'; ?></div>
            <div class="tab-pane fade" id="terminated" role="tabpanel"><?php include 'terminated.php'; ?></div>
            <div class="tab-pane fade" id="pending" role="tabpanel"><?php include 'pending_arrival.php'; ?></div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Store active tab in localStorage
  const tabs = document.querySelectorAll('#hrReportTabs .nav-link');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      localStorage.setItem('activeHRTab', tab.id);
    });
  });

  // Re-activate tab on load
  document.addEventListener('DOMContentLoaded', () => {
    const activeTabId = localStorage.getItem('activeHRTab');
    if (activeTabId) {
      const triggerEl = document.getElementById(activeTabId);
      if (triggerEl) {
        new bootstrap.Tab(triggerEl).show();
      }
    }
  });
</script>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>
</body>
</html>
