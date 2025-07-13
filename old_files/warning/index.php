<?php
session_start();
include '../db.php';
include '../session.php';

// Handle search and filtering
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Pagination settings
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch total warnings count for pagination
$count_sql = "SELECT COUNT(*) AS total FROM warnings 
              WHERE (emp_no LIKE '%$search%' OR problem LIKE '%$search%' ) 
              " . ($filter_status ? "AND status = '$filter_status'" : "");
$count_result = $conn->query($count_sql);
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Fetch warnings
$sql = "SELECT w.*, e.name, e.designation FROM warnings w
        LEFT JOIN employees e ON w.emp_no = e.emp_no
        WHERE (w.id LIKE '%$search%' 
               OR w.emp_no LIKE '%$search%' 
               OR e.name LIKE '%$search%' 
               OR w.problem LIKE '%$search%')
        " . ($filter_status ? "AND w.status = '$filter_status'" : "") . "
        ORDER BY w.created_at DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Fetch status counts for cards
$status_counts = [];
$status_sql = "SELECT status, COUNT(*) AS count FROM warnings GROUP BY status";
$status_result = $conn->query($status_sql);
while ($row = $status_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warnings</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" 
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        
        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="body-wrapper">
            <!-- Header -->
                <header class="app-header">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <div class="navbar-collapse justify-content-between">
                            <!-- Added a left margin -->
                            <h3 class="fw-semibold mb-3 ms-6">disciplinary actions</h3>
                            <a href="add_warning.php" class="btn btn-primary">Add New Warning</a>
                        </div>
                    </nav>
                </header>


            <!-- Container -->
            <div class="container-fluid ">
                <!-- Status Cards -->
                <div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center" onclick="filterByStatus('Pending HOD Review')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="card-title">Pending HOD Review</h5>
                <p class="card-text"><?php echo $status_counts['Pending HOD Review'] ?? 0; ?> Warnings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" onclick="filterByStatus('Pending HRM Review')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="card-title">Pending HRM Review</h5>
                <p class="card-text"><?php echo $status_counts['Pending HRM Review'] ?? 0; ?> Warnings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" onclick="filterByStatus('Pending Director Review')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="card-title">Pending Director Review</h5>
                <p class="card-text"><?php echo $status_counts['Pending Director Review'] ?? 0; ?> Warnings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" onclick="filterByStatus('Resolved')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="card-title">Resolved</h5>
                <p class="card-text"><?php echo $status_counts['Resolved'] ?? 0; ?> Warnings</p>
            </div>
        </div>
    </div>
</div>


                <!-- Search Bar -->
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by EmpNo, Name or Ref.No" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <!-- Warnings Table -->
                <div class="card">
                    <div class="card-body">
                        <!-- Flex container for inline elements -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold mb-0">Warnings List</h5>
                            <a href="add_warning.php" class="btn btn-primary">Add New Warning</a>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref.No</th>
                                        <th>EmpNo</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Problem</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                                <td>
                                                    <small>
                                                        <?php 
                                                            echo htmlspecialchars(strlen($row['problem']) > 30 
                                                                ? substr($row['problem'], 0, 30) . '...' 
                                                                : $row['problem']); 
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        <?php echo htmlspecialchars($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($row['created_at']))); ?></td>
                                                <td>
                                                    <a href="view_warning.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                    <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this warning?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No warnings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                
                        <!-- Pagination -->
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>">
                                            <?php echo $i; ?>
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

    <!-- Scripts -->
    <script>
        function filterByStatus(status) {
            window.location.href = '?status=' + encodeURIComponent(status);
        }
    </script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
