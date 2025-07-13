<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Search condition
$searchCondition = '';
$params = [];
$bindTypes = '';

if (!empty($search)) {
    $searchCondition = "WHERE r.emp_no LIKE ? OR e.name LIKE ?";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $bindTypes .= 'ss';
}

// Count total
$countSql = "SELECT COUNT(*) as total FROM retirement_records r JOIN employees e ON r.emp_no = e.emp_no";
if ($searchCondition) $countSql .= " $searchCondition";

$countStmt = $conn->prepare($countSql);
if (!empty($searchCondition)) {
    $countStmt->bind_param($bindTypes, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalRows = $countResult['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated results
$sql = "SELECT r.*, e.name, e.designation, e.nationality, e.passport_nic_no, e.date_of_join 
        FROM retirement_records r 
        JOIN employees e ON r.emp_no = e.emp_no";

if ($searchCondition) $sql .= " $searchCondition";

$sql .= " ORDER BY r.retirement_date DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$bindTypes .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($bindTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Retirement Records</title>
  <link rel="shortcut icon" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <!-- Sidebar -->
  <?php include '../sidebar.php'; ?>

  <!-- Main Content -->
  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid">
      <div class="card mt-4">
        <div class="card-body">
          <h4 class="card-title mb-4 text-primary">Retirement Records</h4>
          <a href="add.php" class="btn btn-primary  mb-3">+ Add Retirement</a>

          <!-- Search -->
          <form class="mb-3" method="GET" action="">
            <div class="input-group" style="max-width: 100%;">
              <input type="text" class="form-control" name="search" placeholder="Search by Emp No or Name" value="<?= htmlspecialchars($search) ?>">
              <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Emp No</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Nationality</th>
                  <th>Date of Join</th>
                  <th>Request Date</th>
                  <th>Reason</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php if ($result->num_rows > 0): $i = $offset + 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['emp_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['designation']) ?></td>
                    <td><?= htmlspecialchars($row['nationality']) ?></td>
                    <td class="text-center"><?= date('d-M-Y', strtotime($row['date_of_join'])) ?></td>
                    <td class="text-center"><?= date('d-M-Y', strtotime($row['retirement_date'])) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><span class="badge bg-<?php
                        switch ($row['status']) {
                          case 'Approved': echo 'success'; break;
                          case 'Rejected': echo 'danger'; break;
                          default: echo 'warning'; break;
                        }
                      ?>"><?= $row['status'] ?></span></td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                    <td class="text-nowrap">
                      <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                      <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary">Form</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="11" class="text-center">No retirement records found.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
            <nav>
              <ul class="pagination justify-content-center">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                  <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>
</body>
</html>
