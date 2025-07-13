<?php
include '../db.php';
include '../session.php';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = "";
$search_param = "%$search%";
if (!empty($search)) {
    $where_clause = " AND (e.name LIKE ? OR lb.emp_no LIKE ? OR lt.name LIKE ?)";
}

// Fetch leave balances with pagination and search
$query = "
    SELECT lb.emp_no, e.name as employee_name, lt.name as leave_type, lb.balance, lb.last_updated
    FROM leave_balances lb
    INNER JOIN employees e ON lb.emp_no = e.emp_no
    INNER JOIN leave_types lt ON lb.leave_type_id = lt.id
    WHERE 1=1 $where_clause
    ORDER BY lb.last_updated DESC
    LIMIT ?, ?
";

$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $offset, $records_per_page);
} else {
    $stmt->bind_param("ii", $offset, $records_per_page);
}
$stmt->execute();
$result = $stmt->get_result();

// Count total records for pagination
$count_query = "
    SELECT COUNT(*) as total FROM leave_balances lb
    INNER JOIN employees e ON lb.emp_no = e.emp_no
    INNER JOIN leave_types lt ON lb.leave_type_id = lt.id
    WHERE 1=1 $where_clause
";
$count_stmt = $conn->prepare($count_query);
if (!empty($search)) {
    $count_stmt->bind_param("sss", $search_param, $search_param, $search_param);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Balances</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-between">
                        <h4 class="fw-semibold">Leave Balances</h4>
                        <a href="lb_add.php" class="btn btn-primary">Add or Update Balance</a>
                    </div>
                </nav>
            </header>
            
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Manage Leave Balances</h5>
                        
                        <!-- Search Bar -->
                        <form method="GET" action="balance.php" class="d-flex mb-3">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search Employee No, Name, or Leave Type" value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>

                        <!-- Leave Balance Table -->
                        <div class="table-responsive">
                            <table class="table align-middle table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Employee No</th>
                                        <th>Employee Name</th>
                                        <th>Leave Type</th>
                                        <th>Balance</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['emp_no']; ?></td>
                                                <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                                                <td><?php echo htmlspecialchars($row['balance']); ?></td>
                                                <td><?php echo date("d-M-Y H:i", strtotime($row['last_updated'])); ?></td>
                                                <td>
                                                    <a href="edit_leave_balance.php?emp_no=<?php echo $row['emp_no']; ?>&leave_type=<?php echo urlencode($row['leave_type']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center">No leave balances available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center mt-3">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
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
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
