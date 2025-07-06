<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Dates
$today = date('Y-m-d');
$thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));

// Count: Expired
$expiredCount = $conn->query("
  SELECT COUNT(*) AS count
  FROM medical_examinations m
  JOIN employees e ON m.employee_id = e.emp_no
  WHERE e.employment_status = 'Active'
    AND e.nationality != 'MALDIVIAN'
    AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) <= '$today'
")->fetch_assoc()['count'];

// Count: Expiring Soon
$expiringSoonCount = $conn->query("
  SELECT COUNT(*) AS count
  FROM medical_examinations m
  JOIN employees e ON m.employee_id = e.emp_no
  WHERE e.employment_status = 'Active'
    AND e.nationality != 'MALDIVIAN'
    AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) > '$today'
    AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) <= '$thirtyDaysFromNow'
")->fetch_assoc()['count'];

// Count: Valid
$validCount = $conn->query("
  SELECT COUNT(*) AS count
  FROM medical_examinations m
  JOIN employees e ON m.employee_id = e.emp_no
  WHERE e.employment_status = 'Active'
    AND e.nationality != 'MALDIVIAN'
    AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) > '$thirtyDaysFromNow'
")->fetch_assoc()['count'];

// Filter
$search = $_GET['search'] ?? '';
$expiryStatus = $_GET['expiry_status'] ?? '';

$whereClause = "WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
                AND (e.emp_no LIKE ? OR e.name LIKE ? OR m.status LIKE ?)";

if ($expiryStatus === 'expired') {
    $whereClause .= " AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) <= '$today'";
} elseif ($expiryStatus === 'expiring') {
    $whereClause .= " AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) > '$today'
                      AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) <= '$thirtyDaysFromNow'";
} elseif ($expiryStatus === 'valid') {
    $whereClause .= " AND DATE_ADD(m.date_of_medical, INTERVAL 1 YEAR) > '$thirtyDaysFromNow'";
}

// Query
$query = "SELECT m.*, e.name AS employee_name, e.emp_no AS employee_id, e.designation 
          FROM medical_examinations m
          JOIN employees e ON m.employee_id = e.emp_no
          $whereClause
          ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<!---head>
  <meta charset="utf-8">
  <title>Medical Records</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head--->

<head>
  <meta charset="UTF-8">
  <title>Medical Records</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .status-card:hover { cursor: pointer; opacity: 0.9; }
    .status-card.border-highlight { border: 3px solid #fff !important; }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
       data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <div class="container-fluid" style="max-width:100%;">

      <!-- Filter Buttons -->
      <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="?expiry_status=valid" class="btn btn-outline-success">Valid (<?= $validCount ?>)</a>
        <a href="?expiry_status=expiring" class="btn btn-outline-warning">Expiring Soon (<?= $expiringSoonCount ?>)</a>
        <a href="?expiry_status=expired" class="btn btn-outline-danger">Expired (<?= $expiredCount ?>)</a>
        <a href="index.php" class="btn btn-outline-secondary">Show All</a>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold">Medical Examination Records</h5>

          <!-- Messages -->
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php endif; ?>
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <!-- Search -->
          <form method="GET" class="mb-3">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn btn-primary">Search</button>
            </div>
          </form>

         

          <!-- Table -->
          <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
              <table id="medicalTable" class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Old status</th>
                    <th>Medical Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Xpat?</th>
                    <th>File</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $medDate = $row['date_of_medical'];
                    $expiryDate = date('Y-m-d', strtotime($medDate . ' +1 year'));
                    $status = '';
                    $badge = '';

                    if ($expiryDate <= $today) {
                        $status = 'Expired';
                        $badge = 'bg-danger';
                    } elseif ($expiryDate <= $thirtyDaysFromNow) {
                        $status = 'Expiring Soon';
                        $badge = 'bg-warning';
                    } else {
                        $status = 'Valid';
                        $badge = 'bg-success';
                    }
                ?>
                  <tr>
                    <td><?= htmlspecialchars($row['employee_id']) ?></td>
                    <td><?= htmlspecialchars($row['employee_name']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= date('d-M-Y', strtotime($medDate)) ?></td>
                    <td><?= date('d-M-Y', strtotime($expiryDate)) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= $status ?></span></td>
                    <td><input type="checkbox" disabled <?= $row['uploaded_xpat'] ? 'checked' : '' ?> /></td>
                    <td>
                      <?php if (!empty($row['medical_document'])): ?>
                      
                        <a href="../assets/medicals/<?= htmlspecialchars($row['medical_document']) ?>" target="_blank" class="fa-solid fa-file-lines  btn  btn-dark"></a>
                      <?php else: ?>
                        <span class="text-muted">No File</span>
                      <?php endif; ?>
                    </td>
                    <td><a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Update</a></td>
                  </tr>
                <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="mt-3">No records found.</p>
          <?php endif; ?>
        </div>
      </div>
       <!-- Actions -->
          <a href="sync.php" class="btn btn-danger btn-sm mt-2">Sync Database</a>
          <a href="error_log.php" class="btn btn-primary btn-sm mt-2">Error Log</a>
    </div>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#medicalTable').DataTable({
      pageLength: 25,
      order: [[3, 'desc']]
    });
  });
</script>
</body>
</html>
