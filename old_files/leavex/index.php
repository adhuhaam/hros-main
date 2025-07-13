<?php
include '../db.php';
include '../session.php';

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search and date range filters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : "";
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : "";

// Fetch leave statistics for status cards
$total_applied = $conn->query("SELECT COUNT(*) AS total FROM leave_records")->fetch_assoc()['total'];
$total_pending = $conn->query("SELECT COUNT(*) AS total FROM leave_records WHERE status = 'Pending'")->fetch_assoc()['total'];
$total_approved = $conn->query("SELECT COUNT(*) AS total FROM leave_records WHERE status = 'Approved'")->fetch_assoc()['total'];

// Build the base query
$leave_query = "SELECT lr.*, emp.name, emp.emp_no, lt.name AS leave_type, vs.visa_expiry_date 
                FROM leave_records lr 
                JOIN employees emp ON lr.emp_no = emp.emp_no 
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                LEFT JOIN visa_sticker vs ON lr.emp_no = vs.emp_no 
                WHERE 1"; // Placeholder for dynamic conditions

// Apply search filter
if (!empty($search_query)) {
    $leave_query .= " AND (emp.emp_no LIKE '%$search_query%' OR emp.name LIKE '%$search_query%' OR lt.name LIKE '%$search_query%' OR lr.status LIKE '%$search_query%')";
}

// Apply date range filter (based on `start_date`)
if (!empty($from_date) && !empty($to_date)) {
    $leave_query .= " AND (lr.start_date BETWEEN '$from_date' AND '$to_date')";
}

// Get total filtered results for pagination
$total_results = $conn->query(str_replace("SELECT lr.*, emp.name, emp.emp_no, lt.name AS leave_type, vs.visa_expiry_date", "SELECT COUNT(*) AS count", $leave_query))->fetch_assoc()['count'];
$total_pages = ceil($total_results / $limit);

// Apply sorting and pagination
$leave_query .= " ORDER BY lr.start_date DESC LIMIT $limit OFFSET $offset";

$leave_result = $conn->query($leave_query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar_position="fixed" data-header_position="fixed">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
        <?php include '../header.php'; ?>

        <div class="container-fluid" style="max-width:100%;">

            
            

            


            <!-- Leave Management -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">View Leave Records</h5>

                    <!-- Search Form -->
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Employee Name, Leave Type, or Status" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    <!-- Leave Records Table -->
                  <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Emp No</th>
                            <th>Employee</th>
                            <th>Visa Expiry </th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Files</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $leave_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                                <td class="text-wrap"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <?php 
                                    echo !empty($row['visa_expiry_date']) 
                                        ? date('d-M-Y', strtotime($row['visa_expiry_date'])) 
                                        : '<span class="badge bg-danger">Not Available</span>'; 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                                <td  class="text-nowrap"><?php echo date('d-M-Y', strtotime($row['start_date'])); ?></td>
                                <td class="text-nowrap"><?php echo date('d-M-Y', strtotime($row['end_date'])); ?></td>
                                <td><?php echo $row['num_days']; ?></td>
                                <td class="fw-bold text-bg-secondary p-3"><?php echo ucfirst($row['status']); ?></td>
                                <td> <a href="upload_files.php?leave_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Files</a></td>
                                    
                                    
                                </td>
                          
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>


                  <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_query); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">Previous</a>
                                </li>
                            <?php } ?>
                    
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                    
                            <?php if ($page < $total_pages) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_query); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">Next</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>



                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
