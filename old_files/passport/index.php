<?php
include '../db.php';
include '../session.php';

// Pagination and search logic
$limit = 30; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$start = ($page - 1) * $limit;

// Query for total records
$totalQuery = "SELECT COUNT(*) as total FROM passport_renewals pr
               LEFT JOIN employees e ON pr.emp_no = e.emp_no
               WHERE e.name LIKE '%$search%' OR e.passport_nic_no LIKE '%$search%' OR e.emp_no LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Query for paginated results
$query = "SELECT pr.id, e.emp_no, e.name, e.designation, e.passport_nic_no, e.passport_nic_no_expires, pr.renewal_date, pr.status
          FROM passport_renewals pr
          LEFT JOIN employees e ON pr.emp_no = e.emp_no
          WHERE e.name LIKE '%$search%' OR e.passport_nic_no LIKE '%$search%' OR e.emp_no LIKE '%$search%'
          ORDER BY pr.renewal_date DESC LIMIT $start, $limit";
$results = $conn->query($query);

// Query for passports expiring within 6 months from employees table
$sixMonthsFromNow = date('Y-m-d', strtotime('+6 months'));
$today = date('Y-m-d');

$expiryQuery = "SELECT COUNT(*) as expiring_soon FROM employees 
                 WHERE passport_nic_no_expires IS NOT NULL AND passport_nic_no_expires BETWEEN '$today' AND '$sixMonthsFromNow'";
$expiryResult = $conn->query($expiryQuery);
$expiringSoonCount = $expiryResult->fetch_assoc()['expiring_soon'];
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Passport Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="add.php" class="btn btn-primary">Add New Request</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid" style="max-width:100%;">

       
            

        <div class="card mt-2">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4"> <i class="fa-solid fa-passport fs-5"> &nbsp;</i>Passport Renewals</h5>

            <!-- Search Form -->
            <form method="GET" class="mb-4">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by employee number, name, or passport number" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
              </div>
            </form>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Emp no</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Passport Number</th>
                  <th>Passport Exp. Date</th>
                  <th>Renewal Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['designation']); ?></td>
                    <td><?php echo htmlspecialchars($row['passport_nic_no']); ?></td>
                    <td><?php echo date("d-M-Y", strtotime($row['passport_nic_no_expires'])); ?></td>
                    <td><?php echo date("d-M-Y", strtotime($row['renewal_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                      <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                      <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <!-- Pagination -->
            <nav>
              <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
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
    <script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  
            <script
                type="module"
                src="https://agent.d-id.com/v1/index.js"
                data-name="did-agent"
                data-mode="fabio"
                data-client-key="Z29vZ2xlLW9hdXRoMnwxMDU5ODEzMTI2NDgzOTE3NjQ0NzU6dC1YZ3FsNWxXdm02Ulhmdno4WXN3"
                data-agent-id="agt_8f-OYVOK"
                data-monitor="true">
            </script>
                
</body>

</html>
