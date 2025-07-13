<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Default values
$search = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$limit = 10; // Records per page
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Query with filtering, search, and pagination
$query = "SELECT it.id, it.emp_no, e.name, e.designation, p1.name AS destination_from, p2.name AS destination_to, it.transfer_date
          FROM island_transfers it
          JOIN projects p1 ON it.destination_from = p1.id
          JOIN projects p2 ON it.destination_to = p2.id
          JOIN employees e ON it.emp_no = e.emp_no
          WHERE (e.name LIKE ? OR e.designation LIKE ? OR it.emp_no LIKE ?)
          AND (it.transfer_date BETWEEN ? AND ?)
          ORDER BY it.transfer_date DESC
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$startDateParam = $start_date ? $start_date : '1900-01-01';
$endDateParam = $end_date ? $end_date : '2100-12-31';
$stmt->bind_param("ssssssi", $searchParam, $searchParam, $searchParam, $startDateParam, $endDateParam, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM island_transfers it
               JOIN employees e ON it.emp_no = e.emp_no
               WHERE (e.name LIKE ? OR e.designation LIKE ? OR it.emp_no LIKE ?)
               AND (it.transfer_date BETWEEN ? AND ?)";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $startDateParam, $endDateParam);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Island Transfers</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <?php include '../header.php'; ?>
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <a href="add.php" class="btn btn-primary">Add New Transfer</a>
                    <a href="export_csv.php" class="btn btn-success ms-3">Export to CSV</a>
                        <h5 class="card-title">Island Transfers</h5>

                        <!-- Search and Filter -->
                        <form method="GET" class="row mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by Employee Name, No, or Designation" value="<?= $search ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info w-100">Filter</button>
                            </div>
                        </form>

                        <!-- Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee No</th>
                                    <th>Employee Name</th>
                                    <th>Designation</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Transfer Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['emp_no'] ?></td>
                                        <td><?= $row['name'] ?></td>
                                        <td><?= $row['designation'] ?></td>
                                        <td><?= $row['destination_from'] ?></td>
                                        <td><?= $row['destination_to'] ?></td>
                                        <td><?= date("d-M-Y", strtotime($row['transfer_date'])) ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>"><?= $i ?></a>
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
