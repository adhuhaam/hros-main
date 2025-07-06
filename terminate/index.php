<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$searchTerm = "%$search%";

// Count total records
$countSql = "SELECT COUNT(*) as total FROM termination t 
             LEFT JOIN employees e ON t.emp_no = e.emp_no 
             WHERE t.emp_no LIKE ? OR e.name LIKE ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("ss", $searchTerm, $searchTerm);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch termination records
$sql = "SELECT t.*, e.name FROM termination t 
        LEFT JOIN employees e ON t.emp_no = e.emp_no 
        WHERE t.emp_no LIKE ? OR e.name LIKE ? 
        ORDER BY t.created_at DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Termination Records</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
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

          <h5 class="card-title fw-semibold">Termination Management</h5>
          <div class="d-flex justify-content-between mb-3">
            <form class="d-flex" method="GET">
              <input type="text" name="search" class="form-control me-2" placeholder="Search by Emp No or Name"
                     value="<?= htmlspecialchars($search) ?>">
              <button class="btn btn-outline-success">Search</button>
            </form>
            <a href="add.php" class="btn btn-danger">+ Add Termination</a>
          </div>

          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Emp No</th>
                <th>Name</th>
                <th>Termination Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php $i = $offset + 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['emp_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= date('d-M-Y', strtotime($row['termination_date'])) ?></td>
                    <td>
                      <?php
                      $statusClass = match ($row['status']) {
                          'Approved' => 'success',
                          'Rejected' => 'danger',
                          default => 'warning'
                      };
                      ?>
                      <span class="badge bg-<?= $statusClass ?>"><?= $row['status'] ?></span>
                    </td>
                    <td>
                      <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                      <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center">No termination records found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>

          <?php if ($totalPages > 1): ?>
            <nav>
              <ul class="pagination justify-content-center mt-3">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
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

<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
