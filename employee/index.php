<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

// Check if Export Button is Clicked
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $csvSql = "SELECT * FROM employees";
    $csvResult = $conn->query($csvSql);

    if ($csvResult->num_rows > 0) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=employees.csv');
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

$limit = 15;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
$orderBy = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'emp_no';
$orderDirection = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
$statusFilterQuery = $filterStatus ? "AND employment_status = ?" : "";

$sql = "SELECT * FROM employees 
        WHERE (name LIKE ? OR emp_no LIKE ? OR passport_nic_no LIKE ? OR wp_no LIKE ? OR contact_number LIKE ?)";
if ($filterStatus) $sql .= " AND employment_status = ?";
$sql .= " ORDER BY $orderBy $orderDirection LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
if ($filterStatus) {
    $stmt->bind_param('ssssssi', $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $filterStatus, $offset, $limit);
} else {
    $stmt->bind_param('sssssii', $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $offset, $limit);
}
$stmt->execute();
$result = $stmt->get_result();

$countSql = "SELECT COUNT(*) as total FROM employees 
             WHERE (name LIKE ? OR emp_no LIKE ? OR passport_nic_no LIKE ? OR wp_no LIKE ? OR contact_number LIKE ?) 
             $statusFilterQuery";
$countStmt = $conn->prepare($countSql);
if ($filterStatus) {
    $countStmt->bind_param('ssssss', $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $filterStatus);
} else {
    $countStmt->bind_param('sssss', $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$statusCountsSql = "SELECT employment_status, COUNT(*) as count FROM employees GROUP BY employment_status";
$statusCountsResult = $conn->query($statusCountsSql);
$statusCounts = [];
while ($row = $statusCountsResult->fetch_assoc()) {
    $statusCounts[$row['employment_status']] = $row['count'];
}

function sortLink($column, $label, $currentSort, $currentOrder) {
    $newOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'desc' : 'asc';
    return "<a href='?sort_by=$column&order=$newOrder' class='text-decoration-none'>$label</a>";
}

$range = 2;
?>





<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Management</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .badge {
      padding: 10px;
      font-size: 15px;
      border-radius: 8px;
      color: white;
      cursor: pointer;
    }
    .badge.active { background-color: #28a745; }
    .badge.terminated { background-color: #dc3545; }
    .badge.resigned { background-color: #ffc107; }
    .badge.rejoined { background-color: #007bff; }
    .badge.dead { background-color: #6c757d; }
    .badge.retired { background-color: #17a2b8; }
    .badge.missing { background-color: #343a40; }
    .active-sort { font-weight: bold; text-decoration: underline; color: #007bff; }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <?php include '../header.php'; ?>

      <div class="container-fluid" style="max-width:100%;">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Employee Management</h5>
            <a href="add.php" class="btn btn-primary btn-sm mb-4">Add Employee</a>
            <a href="?export=csv" class="btn btn-success btn-sm mb-4">Export CSV</a>
            

            <!-- Status Counts -->
            <div class="mb-4">
              <?php foreach ($statusCounts as $status => $count): ?>
                <a href="?status=<?php echo $status; ?>" class="badge <?php echo strtolower($status); ?>">
                  <?php echo ucfirst($status); ?>: <?php echo $count; ?>
                </a>
              <?php endforeach; ?>
            </div>

            <!-- Search Bar -->
            <form class="d-flex mb-4" method="GET">
              <input class="form-control me-2" type="search" name="search" placeholder="Search by Name, Emp No, Passport No, WorkPermit No, or Phone Number"
                value="<?php echo htmlspecialchars($search); ?>">
              <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

            <!-- Employee Table -->
            <table class="table table-bordered table-hover table-responsive">
              <thead>
                <tr>
                  <th><?php echo sortLink('emp_no', 'EmpID', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('name', 'Name', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('employment_status', 'Status', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('nationality', 'Nationality', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('passport_nic_no', 'Passport/NIC No', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('wp_no', 'WP No', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('designation', 'Designation', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('department', 'Department', $orderBy, $orderDirection); ?></th>
                  <th><?php echo sortLink('date_of_join', 'Date of Join', $orderBy, $orderDirection); ?></th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="text-nowrap"><?php echo htmlspecialchars($row['emp_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="text-nowrap"><?php echo htmlspecialchars($row['employment_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['nationality']); ?></td>
                    <td class="text-nowrap"><?php echo htmlspecialchars($row['passport_nic_no']); ?></td>
                    <td class="text-nowrap"><?php echo !empty($row['wp_no']) ? htmlspecialchars($row['wp_no']) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars($row['designation']); ?></td>
                    <td><?php echo !empty($row['department']) ? htmlspecialchars($row['department']) : 'N/A'; ?></td>
                    <td class="text-nowrap"><?php echo !empty($row['date_of_join']) ? date('d-M-Y', strtotime($row['date_of_join'])) : 'N/A'; ?></td>
                    

                    <td class="text-nowrap">
                      <a href="view_employee.php?emp_no=<?php echo $row['emp_no']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                      <a href="edit.php?emp_no=<?php echo $row['emp_no']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                      <a href="delete.php?emp_no=<?php echo $row['emp_no']; ?>" class="btn btn-sm btn-outline-danger">Delete</a>-
                      <a href="qr_pdf.php?emp_no=<?php echo urlencode($row['emp_no']); ?>" target="_blank" class="btn btn-sm btn-outline-dark">only id</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <!-- Pagination -->
            <nav>
              <ul class="pagination justify-content-center">
                <?php for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++): ?>
                  <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort_by=<?php echo $orderBy; ?>&order=<?php echo strtolower($orderDirection); ?>&status=<?php echo urlencode($filterStatus); ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
            <a href="qr_make.php" class="btn btn-secondary btn-sm mb-4">v1</a>
            <a href="qr_makev2.php" class="btn btn-secondary btn-sm mb-4">v2</a>
            <a href="qr_makev3.php" class="btn btn-secondary btn-sm mb-4">v3</a>
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






                    