<?php
include '../db.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Convert display format
function formatDate($date) {
  return !empty($date) ? date('d-M-Y', strtotime($date)) : '-';
}

// Initialize counts
$count_on_leave = $count_returned = $count_requested = $count_terminated = $count_pending = 0;

// Run counts only if both dates are present
if (!empty($fromDate) && !empty($toDate)) {
  // 1. On Leave
  $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_records WHERE status = 'Departed' AND start_date <= ? AND (actual_arrival_date IS NULL OR actual_arrival_date = '') AND end_date >= ?");
  $stmt->bind_param("ss", $toDate, $fromDate);
  $stmt->execute(); $stmt->bind_result($count_on_leave); $stmt->fetch(); $stmt->close();

  // 2. Returned
  $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_records WHERE status = 'Arrived' AND (actual_arrival_date BETWEEN ? AND ? OR end_date BETWEEN ? AND ?)");
  $stmt->bind_param("ssss", $fromDate, $toDate, $fromDate, $toDate);
  $stmt->execute(); $stmt->bind_result($count_returned); $stmt->fetch(); $stmt->close();

  // 3. Requested
  $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_records WHERE status IN ('Pending', 'Approved') AND applied_date BETWEEN ? AND ?");
  $stmt->bind_param("ss", $fromDate, $toDate);
  $stmt->execute(); $stmt->bind_result($count_requested); $stmt->fetch(); $stmt->close();

  // 4. Terminated/Resigned/Missing/Retired
  $stmt = $conn->prepare("SELECT COUNT(*) FROM employees WHERE employment_status IN ('Terminated', 'Resigned', 'Missing', 'Retired') AND termination_date BETWEEN ? AND ?");
  $stmt->bind_param("ss", $fromDate, $toDate);
  $stmt->execute(); $stmt->bind_result($count_terminated); $stmt->fetch(); $stmt->close();

  // 5. Pending Arrival
  $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_records WHERE status = 'Departed' AND (actual_arrival_date IS NULL OR actual_arrival_date = '') AND end_date BETWEEN ? AND ?");
  $stmt->bind_param("ss", $fromDate, $toDate);
  $stmt->execute(); $stmt->bind_result($count_pending); $stmt->fetch(); $stmt->close();
}

$fromFormatted = formatDate($fromDate);
$toFormatted = formatDate($toDate);
?>

<div class="container py-3">
  <h4 class="text-center mb-4 text-primary">📋 HR Report Summary (<?= $fromFormatted ?> - <?= $toFormatted ?>)</h4>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow border-0 bg-info bg-opacity-10">
        <div class="card-body d-flex justify-content-between align-items-center" role="button" onclick="switchTab('on-leave-tab')">
          <div>
            <h6 class="mb-0 fw-bold text-info">1. Staffs on Leave</h6>
            <small class="text-muted">As at <?= $fromFormatted ?> to <?= $toFormatted ?></small>
          </div>
          <span class="badge rounded-pill bg-info fs-6"><?= $count_on_leave ?></span>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow border-0 bg-success bg-opacity-10">
        <div class="card-body d-flex justify-content-between align-items-center" role="button" onclick="switchTab('returned-tab')">
          <div>
            <h6 class="mb-0 fw-bold text-success">2. Staffs Returned from Leave</h6>
            <small class="text-muted">Within selected range</small>
          </div>
          <span class="badge rounded-pill bg-success fs-6"><?= $count_returned ?></span>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow border-0 bg-warning bg-opacity-10">
        <div class="card-body d-flex justify-content-between align-items-center" role="button" onclick="switchTab('requested-tab')">
          <div>
            <h6 class="mb-0 fw-bold text-warning">3. Leave Requests</h6>
            <small class="text-muted">Applied in selected range</small>
          </div>
          <span class="badge rounded-pill bg-warning text-dark fs-6"><?= $count_requested ?></span>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow border-0 bg-danger bg-opacity-10">
        <div class="card-body d-flex justify-content-between align-items-center" role="button" onclick="switchTab('terminated-tab')">
          <div>
            <h6 class="mb-0 fw-bold text-danger">4. Staffs Terminated / Resigned</h6>
            <small class="text-muted">Includes Missing / Retired</small>
          </div>
          <span class="badge rounded-pill bg-danger fs-6"><?= $count_terminated ?></span>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow border-0 bg-secondary bg-opacity-10">
        <div class="card-body d-flex justify-content-between align-items-center" role="button" onclick="switchTab('pending-tab')">
          <div>
            <h6 class="mb-0 fw-bold text-secondary">5. Pending Leave Arrivals</h6>
            <small class="text-muted">End date passed but not arrived</small>
          </div>
          <span class="badge rounded-pill bg-secondary fs-6"><?= $count_pending ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function switchTab(tabId) {
  const triggerEl = document.getElementById(tabId);
  if (triggerEl) {
    const tab = new bootstrap.Tab(triggerEl);
    tab.show();
  }
}
</script>
