<?php
include '../db.php';
include '../session.php';

// Fetch total active staff count from the employees table where employment_status = 'Active'
$totalActiveStaffQuery = "SELECT COUNT(*) AS count FROM employees WHERE employment_status = 'Active'";
$totalActiveStaff = $conn->query($totalActiveStaffQuery)->fetch_assoc()['count'];

// Fetch total bank account records (all records, not distinct emp_no)
$totalBankRecordsQuery = "SELECT COUNT(*) AS count FROM bank_account_records";
$totalBankRecords = $conn->query($totalBankRecordsQuery)->fetch_assoc()['count'];

// Calculate "to apply"
$toApplyCount = $totalActiveStaff - $totalBankRecords;

// Fetch counts for status cards (all records)
$pendingCount = $conn->query("SELECT COUNT(*) AS count FROM bank_account_records WHERE status = 'Pending'")->fetch_assoc()['count'];
$scheduledCount = $conn->query("SELECT COUNT(*) AS count FROM bank_account_records WHERE status = 'Scheduled'")->fetch_assoc()['count'];
$completedCount = $conn->query("SELECT COUNT(*) AS count FROM bank_account_records WHERE status = 'Completed'")->fetch_assoc()['count'];

// Filter logic for status cards
$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
$filterCondition = '';
if ($filter === 'pending') {
    $filterCondition = "b.status = 'Pending'";
} elseif ($filter === 'scheduled') {
    $filterCondition = "b.status = 'Scheduled'";
} elseif ($filter === 'completed') {
    $filterCondition = "b.status = 'Completed'";
}

// Pagination setup
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$limit = 30;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build WHERE conditions for search and filter
$whereConditions = [];
if (!empty($search)) {
    $whereConditions[] = "(b.emp_no LIKE '%$search%' OR b.bank_name LIKE '%$search%' OR e.name LIKE '%$search%')";
}
if (!empty($filterCondition)) {
    $whereConditions[] = $filterCondition;
}
$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
}

// Fetch total records count for pagination (all records)
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM bank_account_records b 
                      LEFT JOIN employees e ON b.emp_no = e.emp_no 
                      $whereClause";
$totalRecords = $conn->query($totalRecordsQuery)->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch all records for the table (no GROUP BY to ensure all records appear)
$query = "SELECT b.*, e.name AS employee_name FROM bank_account_records b 
          LEFT JOIN employees e ON b.emp_no = e.emp_no 
          $whereClause
          ORDER BY b.created_at DESC 
          LIMIT $limit OFFSET $offset";
$records = $conn->query($query);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank Records Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>

        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end">
                        <a href="add.php" class="btn btn-success mb-3">Request New</a> 
                        <a href="export_csv.php" class="btn btn-warning mb-3">Export Records</a>
                    </div>
                </nav>
            </header>

            <div class="container-fluid" style="max-width: 100%;">
                <div class="row">
                    <!-- Status Cards -->
                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Staff</h5>
                                <p class="card-text fs-4"><?= $totalActiveStaff ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="?filter=to_apply">
                            <div class="card text-bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">To Apply</h5>
                                    <p class="card-text fs-4"><?= $toApplyCount ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?filter=pending">
                            <div class="card text-bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Pending</h5>
                                    <p class="card-text fs-4"><?= $pendingCount ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?filter=scheduled">
                            <div class="card text-bg-secondary">
                                <div class="card-body">
                                    <h5 class="card-title">Scheduled</h5>
                                    <p class="card-text fs-4"><?= $scheduledCount ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?filter=completed">
                            <div class="card text-bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Completed</h5>
                                    <p class="card-text fs-4"><?= $completedCount ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Employee, Bank, or Name" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Bank Account Records</h5>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>EmpNo</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Entry Date</th>
                                    <th>Form</th>
                                    <th>Acc No.</th>
                                    <th>Bank</th>
                                    <th>Scheduled</th>
                                    <th>#</th>
                                    <th>Letter</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $records->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['emp_no']) ?></td>
                                        <td class="text-break"><?= htmlspecialchars($row['employee_name']) ?></td>
                                        <td>
                                            <span class="badge 
                                            <?php 
                                                switch ($row['status']) {
                                                    case 'Pending': echo 'bg-warning'; break;
                                                    case 'Scheduled': echo 'bg-secondary'; break;
                                                    case 'Completed': echo 'bg-success'; break;
                                                    default: echo 'bg-light text-dark';
                                                }
                                            ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-nowrap"><?= $row['entry_date'] ? date('d-M-Y', strtotime($row['entry_date'])) : '-' ?></td>
                                        <td>
                                            <input type="checkbox" readonly <?= $row['form_filled'] ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-nowrap">
                                            <input type="checkbox" readonly <?= (!empty($row['bank_acc_no']) && $row['bank_acc_no'] !== null) ? 'checked' : '' ?>>
                                            - <mark><?= htmlspecialchars($row['bank_acc_no']) ?></mark>
                                        </td>
                                        <td><?= htmlspecialchars($row['bank_name']) ?></td>
                                        <td class="text-nowrap"><?= $row['scheduled_date'] ? date('d-M-Y', strtotime($row['scheduled_date'])) : '-' ?></td>
                                        <td class="text-nowrap"> 
                                            <a href="sbi.php?record_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">SBI</a> 
                                            <a href="mib.php?record_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">MIB</a>  
                                            <a href="mib_from.php?record_id=<?= $row['id'] ?>" class="btn btn-dark btn-sm">MIB Form</a>  
                                            <!--a href="#" class="btn btn-danger btn-sm">BML</a> 
                                            <a href="#" class="btn btn-warning btn-sm">BOC</a---> 
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-pen text-success"></i></a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fa-regular fa-trash-can text-danger"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if ($records->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
</body>
</html>
