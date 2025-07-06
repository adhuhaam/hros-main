<?php
// index.php - Enhanced List of Generated E-Contracts
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT e.*, emp.name, emp.designation FROM e_contracts e JOIN employees emp ON e.emp_no = emp.emp_no WHERE 1";
$params = [];
$typestr = "";

if ($search !== '') {
    $query .= " AND (emp.emp_no LIKE ? OR emp.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $typestr .= "ss";
}
if ($type !== '') {
    $query .= " AND e.contract_type = ?";
    $params[] = $type;
    $typestr .= "s";
}
if ($status !== '') {
    $query .= " AND e.status = ?";
    $params[] = $status;
    $typestr .= "s";
}

$totalSql = "SELECT COUNT(*) as total FROM ($query) as temp";
$totalStmt = $conn->prepare($totalSql);
if (!empty($params)) $totalStmt->bind_param($typestr, ...$params);
$totalStmt->execute();
$totalRows = $totalStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query .= " ORDER BY e.created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$typestr .= "ii";

$stmt = $conn->prepare($query);
if (!empty($params)) $stmt->bind_param($typestr, ...$params);
$stmt->execute();
$contracts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Contracts</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid mt-4">
      <div class="card">
        <div class="card-body">
          <div class="flex justify-between items-center mb-4">
            <h5 class="text-xl font-semibold">E-Contract Records</h5>
            <div>
              <a href="renew.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate Renewal</a>
              <a href="new_contract.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Generate New</a>
            </div>
          </div>

          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Contract saved successfully!</div>
          <?php endif; ?>

          <!-- Filters -->
          <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search name or emp_no" value="<?= htmlspecialchars($search) ?>">
            <select name="type" class="form-select">
              <option value="">All Types</option>
              <option value="Renewal" <?= $type == 'Renewal' ? 'selected' : '' ?>>Renewal</option>
              <option value="Offer Letter" <?= $type == 'Offer Letter' ? 'selected' : '' ?>>Offer Letter</option>
              <option value="Employment Contract" <?= $type == 'Employment Contract' ? 'selected' : '' ?>>Employment Contract</option>
              <option value="Termination" <?= $type == 'Termination' ? 'selected' : '' ?>>Termination</option>
              <option value="Other" <?= $type == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
            <select name="status" class="form-select">
              <option value="">All Statuses</option>
              <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Expired" <?= $status == 'Expired' ? 'selected' : '' ?>>Expired</option>
              <option value="Revoked" <?= $status == 'Revoked' ? 'selected' : '' ?>>Revoked</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
          </form>

          <div class="overflow-x-auto">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Employee No</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Contract Title</th>
                  <th>Type</th>
                  <th>Issued Date</th>
                  <th>Status</th>
                  <th>File</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $contracts->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['emp_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['designation']) ?></td>
                    <td><?= htmlspecialchars($row['contract_title']) ?></td>
                    <td><?= $row['contract_type'] ?></td>
                    <td><?= date('d M Y', strtotime($row['issued_date'])) ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><a href="<?= $row['contract_file'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View PDF</a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>&status=<?= urlencode($status) ?>">
                    <?= $i ?>
                  </a>
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
