<?php
include '../db.php';
include '../session.php';

// Define pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle search query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// Count total resignations for pagination
$countSql = "SELECT COUNT(*) AS total FROM resignations WHERE 
            emp_no LIKE '%$search%' OR 
            status LIKE '%$search%' OR 
            remarks LIKE '%$search%'";
$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch resignations with pagination
$sql = "SELECT * FROM resignations WHERE 
        emp_no LIKE '%$search%' OR 
        status LIKE '%$search%' OR 
        remarks LIKE '%$search%'
        ORDER BY id DESC LIMIT $start, $limit";
$result = $conn->query($sql);

// Fetch status counts
$statusCounts = [
    'Pending' => 0,
    'Approved' => 0,
    'Rejected' => 0
];
$statusSql = "SELECT status, COUNT(*) AS count FROM resignations GROUP BY status";
$statusResult = $conn->query($statusSql);
while ($row = $statusResult->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resignation List</title>
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
          <div class="navbar-collapse justify-content-end">
            <a href="add.php" class="btn btn-primary">Add Resignation</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid">
        <!-- Status Cards -->
        <div class="row">
          <div class="col-md-4">
            <div class="card bg-warning text-dark">
              <div class="card-body">
                <h5 class="card-title">Pending Resignations</h5>
                <h3><?php echo $statusCounts['Pending']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-success text-white">
              <div class="card-body">
                <h5 class="card-title">Approved Resignations</h5>
                <h3><?php echo $statusCounts['Approved']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-danger text-white">
              <div class="card-body">
                <h5 class="card-title">Rejected Resignations</h5>
                <h3><?php echo $statusCounts['Rejected']; ?></h3>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Resignation Requests</h5>

            <!-- Search Bar -->
            <form method="GET" class="mb-3">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Employee No, Status, or Remarks" value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </form>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Employee No</th>
                  <th>Resignation Date</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['emp_no']; ?></td>
                    <td><?php echo date("d-M-Y", strtotime($row['resignation_date'])); ?></td>
                    <td>
                      <?php
                        $status_badge = [
                          'Pending' => 'badge bg-warning text-dark',
                          'Approved' => 'badge bg-success',
                          'Rejected' => 'badge bg-danger'
                        ];
                        echo "<span class='{$status_badge[$row['status']]}'>{$row['status']}</span>";
                      ?>
                    </td>
                    <td><?php echo $row['remarks'] ? $row['remarks'] : 'N/A'; ?></td>
                    <td>
                      <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                      <a href="form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Form</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <!-- Pagination -->
            <nav>
              <ul class="pagination">
                <?php if ($page > 1): ?>
                  <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                  <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>">Next</a></li>
                <?php endif; ?>
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
