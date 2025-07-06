<?php
include '../db.php';
include '../session.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT t.*, e.name FROM termination t LEFT JOIN employees e ON t.emp_no = e.emp_no WHERE t.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Termination</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <?php include '../header.php'; ?>
    <div class="container-fluid">
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title fw-semibold">Termination Details</h5>
          <p><strong>Employee No:</strong> <?= htmlspecialchars($data['emp_no']) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($data['name']) ?></p>
          <p><strong>Termination Date:</strong> <?= date('d-M-Y', strtotime($data['termination_date'])) ?></p>
          <p><strong>Reason:</strong> <?= nl2br(htmlspecialchars($data['reason'])) ?></p>
          <p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($data['remarks'])) ?></p>
          <p><strong>Status:</strong>
            <span class="badge bg-<?= $data['status'] == 'Approved' ? 'success' : ($data['status'] == 'Rejected' ? 'danger' : 'warning') ?>">
              <?= $data['status'] ?>
            </span>
          </p>
          <a href="index.php" class="btn btn-secondary mt-3">Back</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
