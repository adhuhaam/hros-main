<?php
include '../db.php';
include '../session.php';

// Handle search and pagination
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total records and filtered records
$totalSql = "SELECT COUNT(*) AS total FROM card_print WHERE emp_no LIKE ? OR print_type LIKE ?";
$stmt = $conn->prepare($totalSql);
$searchTerm = '%' . $search . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$totalRecords = $result->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch card print data
$sql = "SELECT * FROM card_print 
        WHERE emp_no LIKE ? OR print_type LIKE ?
        ORDER BY created_at DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $limit);
$stmt->execute();
$data = $stmt->get_result();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Card Print</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="add_card.php" class="btn btn-success me-3">Add New Request</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Card Print Requests</h5>
            
            <!-- Search -->
            <form method="GET" class="mb-3">
              <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search by Employee No or Print Type">
            </form>
            
            <!-- Card Requests Table -->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Employee No</th>
                    <th>Print Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Requested Date</th>
                    <th>Handover Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $data->fetch_assoc()): ?>
                    <?php
                    $rowClass = '';
                    if ($row['payment_status'] === 'Received' && $row['status'] === 'Handed Over') {
                      $rowClass = 'table-success';
                    } elseif ($row['payment_status'] === 'Pending' && $row['status'] === 'Received') {
                      $rowClass = 'table-warning';
                    } elseif ($row['payment_status'] === 'Printed' && $row['status'] === 'Received') {
                      $rowClass = 'table-info';
                    }
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                      <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                      <td><?php echo number_format($row['price'], 2); ?></td>
                      <td><?php echo htmlspecialchars($row['status']); ?></td>
                      <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                      <td><?php echo date('d-M-Y', strtotime($row['requested_date'])); ?></td>
                      <td><?php echo ($row['handover_date'] !== '0000-00-00') ? date('d-M-Y', strtotime($row['handover_date'])) : '-'; ?></td>
                      <td>
                        <a href="view_card.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">View</a>
                        <a href="update_card.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                        <a href="delete_card.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav class="d-flex justify-content-center mt-4">
              <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
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
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
