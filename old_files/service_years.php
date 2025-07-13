<?php
include 'db.php'; // Assuming db.php contains your database connection details.

$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], ['asc', 'desc']) ? $_GET['order_by'] : 'asc';
$filter_year = isset($_GET['filter_year']) ? intval($_GET['filter_year']) : null;

// Query to group service years and count employees
$group_query = "SELECT TIMESTAMPDIFF(YEAR, date_of_join, CURDATE()) AS service_years, COUNT(*) AS employee_count
                FROM employees
                WHERE employment_status = 'Active'
                GROUP BY service_years
                ORDER BY service_years ASC";
$group_result = $conn->query($group_query);

// Query to fetch employees with filtering and sorting
$query = "SELECT emp_no, name, date_of_join, 
                 TIMESTAMPDIFF(YEAR, date_of_join, CURDATE()) AS service_years 
          FROM employees
          WHERE employment_status = 'Active'";
if ($filter_year !== null) {
    $query .= " AND TIMESTAMPDIFF(YEAR, date_of_join, CURDATE()) = $filter_year";
}
$query .= " ORDER BY service_years $order_by";
$result = $conn->query($query);

// Calculate total service years
$total_service_years_query = "SELECT SUM(TIMESTAMPDIFF(YEAR, date_of_join, CURDATE())) AS total_service_years 
                               FROM employees 
                               WHERE employment_status = 'Active'";
$total_service_years_result = $conn->query($total_service_years_query);
$total_service_years = $total_service_years_result->fetch_assoc()['total_service_years'] ?? 0;
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Active Employees - Service Years</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <h2 class="fw-semibold mb-0">Active Employees - Service Years</h2>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <!-- Buttons for Service Years -->
        <div class="row mt-4">
          <?php if ($group_result->num_rows > 0): ?>
            <?php while ($row = $group_result->fetch_assoc()): ?>
              <div class="col-auto mb-2">
                <a href="?filter_year=<?php echo $row['service_years']; ?>&order_by=<?php echo $order_by; ?>" 
                   class="btn btn-sm btn-primary text-center">
                  <?php echo $row['service_years']; ?> Years<br>
                  Employees: <?php echo $row['employee_count']; ?>
                </a>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>

        <!-- Employee Table -->
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">
              Total Service Years (Active Employees): <?php echo $total_service_years; ?>
            </h5>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Employee No</th>
                    <th>Name</th>
                    <th>Date of Joining</th>
                    <th>
                      <a href="?order_by=<?php echo $order_by === 'asc' ? 'desc' : 'asc'; ?>" class="text-decoration-none">
                        Years of Service 
                        <?php echo $order_by === 'asc' ? '↑' : '↓'; ?>
                      </a>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo date("d-M-Y", strtotime($row['date_of_join'])); ?></td>
                        <td><?php echo $row['service_years']; ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center">No Employees Found for Selected Years</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
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
