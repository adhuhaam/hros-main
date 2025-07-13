<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

// Default pagination values
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search filter by emp_no
$search_emp_no = isset($_GET['search_emp_no']) ? $conn->real_escape_string($_GET['search_emp_no']) : '';

// Base query with search filter
$sql = "
    SELECT 
        opd_records.*, 
        users.staff_name,
        employees.name
    FROM 
        opd_records
    LEFT JOIN 
        users 
    ON 
        opd_records.entered_by = users.username
    LEFT JOIN
        employees
    ON
        opd_records.emp_no = employees.emp_no
    WHERE 
        opd_records.emp_no LIKE '%$search_emp_no%'
    ORDER BY 
        opd_records.created_at DESC
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

// Total record count for pagination
$count_sql = "
    SELECT COUNT(*) AS total 
    FROM opd_records
    WHERE 
        emp_no LIKE '%$search_emp_no%'
";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Calculate statistics for the period from last month's 21st to this month's 20th
$start_date = date('Y-m-d', strtotime('last month 21'));
$end_date = date('Y-m-d', strtotime('this month 20'));

$stats_sql = "
    SELECT 
        COUNT(*) AS total_records, 
        SUM(medication_amount) AS total_amount 
    FROM 
        opd_records 
    WHERE 
        created_at BETWEEN '$start_date' AND '$end_date'
";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
$total_period_records = $stats['total_records'] ?? 0;
$total_period_amount = $stats['total_amount'] ?? 0.00;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View OPD Records</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>

        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg">
                    <a href="add.php" class="btn btn-primary">Add Record</a>
                </nav>
            </header>
            <div class="container-fluid" style="max-width: 100%;">
                <!-- Statistics Section -->
                <div class="row mt-4">
                    <!-- Total Records Card -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body text-bg-primary">
                                <h5 class="card-title fw-semibold">Total Records (Last Month 21 to This Month 20)</h5>
                                <p class="mb-0"><strong>Total Records:</strong> <?php echo $total_period_records; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Medication Amount Card -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body text-bg-success">
                                <h5 class="card-title fw-semibold">Total Medication Amount (Last Month 21 to This Month 20)</h5>
                                <p class="mb-0"><strong>Total Amount:</strong> MVR <?php echo number_format($total_period_amount, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                             <!-- exprt -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Export Records to Excel</h5>
                            <form method="POST" action="export_csv.php" class="row g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" name="export" class="btn btn-success w-100">Export to Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                <!-- Search Bar Section -->
                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Search by Employee No</h5>
                        <form method="GET">
                            <div class="input-group">
                                <input type="text" name="search_emp_no" class="form-control" placeholder="Enter Employee No" value="<?php echo $search_emp_no; ?>">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Records Table -->
                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">OPD General Consultation Records</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Employee No</th>
                                        <th>Employee Name</th>
                                        <th>Project Name</th>
                                        <th>Invoice No</th>
                                        <th>Medication Detail</th>
                                        <th>Medication Amount (MVR)</th>
                                        <th>Entered By</th>
                                        <th>Date </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['emp_no']; ?></td>
                                            <td><?php echo $row['name'] ? $row['name'] : 'Unknown'; ?></td>
                                            <td><?php echo $row['project_name']; ?></td>
                                            <td><?php echo $row['invoice_no']; ?></td>
                                            <td><?php echo $row['medication_detail']; ?></td>
                                            <td>MVR <?php echo number_format($row['medication_amount'], 2); ?></td>
                                            <td><?php echo $row['staff_name'] ? $row['staff_name'] : 'Unknown User'; ?></td>
                                            <td class="text-nowrap"><?php echo date('d-M-Y', strtotime($row['consultation_date'])); ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="delete.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search_emp_no=<?php echo $search_emp_no; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
