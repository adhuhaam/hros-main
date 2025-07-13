<?php 
include '../db.php';
include '../session.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Count statuses for buttons
$statusCounts = [];
$statuses = ['Pending', 'Collection Created', 'Paid', 'Completed'];
foreach ($statuses as $status) {
    $countSql = "
        SELECT COUNT(*) AS count
        FROM work_permit_fees wpf
        INNER JOIN employees e ON wpf.emp_no = e.emp_no
        WHERE wpf.status = '" . $conn->real_escape_string($status) . "'
        AND e.employment_status = 'Active'
        AND e.nationality != 'MALDIVIAN'";
    $result = $conn->query($countSql);
    $statusCounts[$status] = $result->fetch_assoc()['count'];
}

// Build SQL query with filters
$sql = "
    SELECT wpf.id, e.emp_no, e.name AS employee_name, wpf.expiry_date, wpf.status, wpf.remarks
    FROM work_permit_fees wpf
    INNER JOIN employees e ON wpf.emp_no = e.emp_no
    WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
";
if ($status_filter) {
    $sql .= " AND wpf.status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($search_query) {
    $sql .= " AND (e.emp_no LIKE '%" . $conn->real_escape_string($search_query) . "%' OR e.name LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}
$sql .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get total rows for pagination
$total_rows_sql = "
    SELECT COUNT(*) AS total
    FROM work_permit_fees wpf
    INNER JOIN employees e ON wpf.emp_no = e.emp_no
    WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
";
if ($status_filter) {
    $total_rows_sql .= " AND wpf.status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($search_query) {
    $total_rows_sql .= " AND (e.emp_no LIKE '%" . $conn->real_escape_string($search_query) . "%' OR e.name LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}
$total_rows_result = $conn->query($total_rows_sql);
$total_rows = $total_rows_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Work Permit Management</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <?php include '../header.php'; ?>
        <div class="container-fluid" style="max-width:100%;">
            <div class="mb-4">
                <!-- Status Buttons -->
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-dark">All Records</a>
                    <a href="?status=Pending" class="btn btn-info">Pending (<?= $statusCounts['Pending'] ?>)</a>
                    <a href="?status=Collection Created" class="btn btn-warning">Collection Created(<?= $statusCounts['Collection Created'] ?>)</a>
                    <a href="?status=Paid" class="btn btn-info">Paid (<?= $statusCounts['Paid'] ?>)</a>
                    <a href="?status=Completed" class="btn btn-success">Completed (<?= $statusCounts['Completed'] ?>)</a> |||| <a class=" btn btn-danger" href="bulk_import.php">import Records</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    
                    <h5 class="card-title fw-semibold"> Work Permit Fee Management </h5>

                    <!-- Search Bar -->
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Employee No or Name" value="<?= htmlspecialchars($search_query) ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Employee Name</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['emp_no'] ?></td>
                                    <td><?= $row['employee_name'] ?></td>
                                    <td><?= $row['expiry_date'] ? date('d-M-Y', strtotime($row['expiry_date'])) : 'N/A' ?></td>
                                    <td><?= $row['status'] ?></td>
                                    <td><?= $row['remarks'] ?></td>
                                    <td>
                                        <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary">Update</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item <?= ($page > 1) ? '' : 'disabled' ?>">
                                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= htmlspecialchars($search_query) ?>">Previous</a>
                            </li>
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= htmlspecialchars($search_query) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page < $total_pages) ? '' : 'disabled' ?>">
                                <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= htmlspecialchars($search_query) ?>">Next</a>
                            </li>
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
