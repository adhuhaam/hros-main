<?php
include('../db.php');

// Get total passports OUT (latest `taken_by_date` per emp_no is OUT)
$out_count_query = "
    SELECT COUNT(*) AS count 
    FROM (
        SELECT emp_no, direction, MAX(taken_by_date) AS latest_date 
        FROM passport_inventory 
        GROUP BY emp_no 
        HAVING MAX(direction) = 'OUT'
    ) AS latest";
$out_count_result = mysqli_query($conn, $out_count_query);
$out_count_row = mysqli_fetch_assoc($out_count_result);
$out_count = $out_count_row['count'];

// Get total passports IN (latest `received_by_date` per emp_no is IN)
$in_count_query = "
    SELECT COUNT(*) AS count 
    FROM (
        SELECT emp_no, direction, MAX(received_by_date) AS latest_date 
        FROM passport_inventory 
        GROUP BY emp_no 
        HAVING MAX(direction) = 'IN'
    ) AS latest";
$in_count_result = mysqli_query($conn, $in_count_query);
$in_count_row = mysqli_fetch_assoc($in_count_result);
$in_count = $in_count_row['count'];

// Get total employees with Active status and non-Maldivian nationality
$employee_count_query = "
    SELECT COUNT(*) AS count 
    FROM employees 
    WHERE employment_status = 'Active' 
    AND nationality <> 'MALDIVIAN'";
$employee_count_result = mysqli_query($conn, $employee_count_query);
$employee_count_row = mysqli_fetch_assoc($employee_count_result);
$total_employees = $employee_count_row['count'];

$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = "";
if (!empty($search)) {
    $search_query = "WHERE emp_no LIKE '%$search%' OR taken_by LIKE '%$search%' OR received_by LIKE '%$search%' OR direction LIKE '%$search%'";
}

// Get total records
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM passport_inventory $search_query");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Fetch records with pagination
$query = "SELECT * FROM passport_inventory $search_query ORDER BY id DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <style>
        .direction-in td { background-color: #d4edda !important; }  /* Green for IN */
        .direction-out td { background-color: #f8d7da !important; } /* Red for OUT */
    </style>
</head>
<body class="container mt-4">
    <h2>Passport Inventory</h2>

    <!-- Summary Count Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Passports OUT</h5>
                    <p class="card-text display-4"><?= $out_count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Passports IN</h5>
                    <p class="card-text display-4"><?= $in_count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="card-text display-4"><?= $total_employees ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by EMP No, Taken By, Received By..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
        <a href="index.php" class="btn btn-secondary mt-2">Reset</a>
    </form>

    <!-- Add New Entry Buttons -->
    <a href="add_passport_request.php" class="btn btn-primary mb-3">Mark as OUT</a>
    <a href="return_passport.php" class="btn btn-success mb-3">Mark as IN</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Emp No</th>
                <th>Direction</th>
                <th>Taken By</th>
                <th>Purpose</th>
                <th>Handed Over By</th>
                <th>Taken Date</th>
                <th>Received By</th>
                <th>Remark</th>
                <th>Received Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = mysqli_fetch_assoc($result)) { 
                $directionClass = ($row['direction'] == 'IN') ? 'direction-in' : 'direction-out';
            ?>
                <tr class="<?= $directionClass ?>">
                    <td><?= $row['emp_no'] ?></td>
                    <td><?= $row['direction'] ?></td>
                    <td><?= $row['taken_by'] ?? 'N/A' ?></td>
                    <td><?= $row['purpose'] ?? 'N/A' ?></td>
                    <td><?= $row['handed_over_by'] ?? 'N/A' ?></td>
                    <td><?= $row['taken_by_date'] ?? 'N/A' ?></td>
                    <td><?= $row['received_by'] ?? 'N/A' ?></td>
                    <td><?= $row['remark'] ?? 'N/A' ?></td>
                    <td><?= $row['received_by_date'] ?? 'N/A' ?></td>
                    <td>
                        <?php if ($row['direction'] == 'IN') { ?>
                            <a href="add_passport_request.php?emp_no=<?= $row['emp_no'] ?>" class="btn btn-warning btn-sm">Mark as OUT</a>
                        <?php } else { ?>
                            <a href="return_passport.php?emp_no=<?= $row['emp_no'] ?>" class="btn btn-success btn-sm">Mark as IN</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= ($page - 1) ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                </li>
            <?php } ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= ($page + 1) ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
            </li>
        </ul>
    </nav>
</body>
</html>
