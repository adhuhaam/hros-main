<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $csvResult = $conn->query("SELECT * FROM missing");
    if ($csvResult->num_rows > 0) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=missing_records.csv');
        $output = fopen('php://output', 'w');
        $headers = array_keys($csvResult->fetch_assoc());
        fputcsv($output, $headers);
        $csvResult->data_seek(0);
        while ($row = $csvResult->fetch_assoc()) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    } else {
        header('Location: index.php?error=NoRecordsFound');
        exit();
    }
}

// Pagination and Search
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
$orderBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'missing_date';
$orderDirection = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

$statusFilterQuery = $filterStatus ? "AND m.status = ?" : "";

$sql = "SELECT m.*, e.name FROM missing m
        JOIN employees e ON m.emp_no = e.emp_no
        WHERE (m.emp_no LIKE ? OR e.name LIKE ? OR m.reported_by LIKE ?)
        $statusFilterQuery
        ORDER BY $orderBy $orderDirection
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";

if ($filterStatus) {
    $stmt->bind_param("sssssii", $searchParam, $searchParam, $searchParam, $filterStatus, $offset, $limit);
} else {
    $stmt->bind_param("ssssi", $searchParam, $searchParam, $searchParam, $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

// Total count
$countSql = "SELECT COUNT(*) AS total FROM missing m
             JOIN employees e ON m.emp_no = e.emp_no
             WHERE (m.emp_no LIKE ? OR e.name LIKE ? OR m.reported_by LIKE ?)
             $statusFilterQuery";
$countStmt = $conn->prepare($countSql);

if ($filterStatus) {
    $countStmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $filterStatus);
} else {
    $countStmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
}
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Status badges
$statusCounts = ['Pending' => 0, 'Approved' => 0, 'Resolved' => 0];
$badgeSql = "SELECT status, COUNT(*) AS count FROM missing GROUP BY status";
$badgeResult = $conn->query($badgeSql);
while ($row = $badgeResult->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}

// Helper for sort links
function sortLink($column, $label, $currentSort, $currentOrder) {
    $newOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'desc' : 'asc';
    $icon = $currentSort === $column ? ($currentOrder === 'ASC' ? '↑' : '↓') : '';
    return "<a href='?sort_by=$column&order=$newOrder' class='text-decoration-none'>$label $icon</a>";
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Missing Employees</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .badge.missing { background-color: #343a40; color: white; padding: 6px 10px; border-radius: 6px; }
    .badge.pending { background-color: #ffc107; }
    .badge.approved { background-color: #007bff; }
    .badge.resolved { background-color: #28a745; }
    .active-sort { font-weight: bold; text-decoration: underline; color: #007bff; }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <?php include '../header.php'; ?>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Missing Employees</h5>
            <a href="add.php" class="btn btn-primary btn-sm mb-2">+ Mark Missing</a>
            <a href="?export=csv" class="btn btn-success btn-sm mb-2">Export CSV</a>

            <!-- Filter badges -->
            <div class="mb-3">
              <?php foreach ($statusCounts as $status => $count): ?>
                <a href="?status=<?= $status ?>" class="badge <?= strtolower($status) ?>"><?= $status ?>: <?= $count ?></a>
              <?php endforeach; ?>
            </div>

            <!-- Search bar -->
            <form class="d-flex mb-3" method="GET">
              <input class="form-control me-2" type="search" name="search" placeholder="Search by Emp No, Name, Reporter"
                value="<?= htmlspecialchars($search) ?>">
              <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

            <!-- Table -->
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><?= sortLink('emp_no', 'Emp No', $orderBy, $orderDirection) ?></th>
                    <th><?= sortLink('name', 'Name', $orderBy, $orderDirection) ?></th>
                    <th><?= sortLink('missing_date', 'Missing Date', $orderBy, $orderDirection) ?></th>
                    <th><?= sortLink('reported_by', 'Reported By', $orderBy, $orderDirection) ?></th>
                    <th><?= sortLink('status', 'Status', $orderBy, $orderDirection) ?></th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['emp_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= date('d-M-Y', strtotime($row['missing_date'])) ?></td>
                    <td><?= htmlspecialchars($row['reported_by']) ?></td>
                    <td><span class="badge <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                    <td>
                      <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                      <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                      <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Mark as resolved?')">Resolve</a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav>
              <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filterStatus) ?>&sort_by=<?= $orderBy ?>&order=<?= strtolower($orderDirection) ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>

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
