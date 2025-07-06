<?php
include '../db.php';
include '../session.php';

// Initialize search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $query = "
        SELECT DISTINCT p.* FROM projects p
        LEFT JOIN employee_project_allocations ep ON p.id = ep.project_id
        LEFT JOIN employees e ON ep.employee_id = e.emp_no
        WHERE p.name LIKE '%$search_query%' 
        OR e.emp_no LIKE '%$search_query%' 
        OR e.name LIKE '%$search_query%' 
        OR e.passport_nic_no LIKE '%$search_query%'
    ";
} else {
    $query = "SELECT * FROM projects";
}

// Fetch projects from the database
$projects = $conn->query($query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Allocations</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    .project-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      margin-bottom: 20px;
      padding: 15px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .project-title {
      font-weight: bold;
      font-size: 18px;
      color: #007bff;
      margin-bottom: 10px;
    }

    .employee-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .employee-table th,
    .employee-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    .employee-table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    .no-employees {
      text-align: center;
      color: #666;
      font-style: italic;
    }
  </style>
</head>

<body>
  <!-- Page Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php include '../header.php'; ?>
      <!-- End Header -->

      <div class="container-fluid">
        <h1 class="text-center my-4">Project Allocations</h1>

        <!-- Search Bar -->
        <form method="GET" action="" class="mb-4">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Project Name, Employee No, Employee Name, or Passport No" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
          </div>
        </form>

        <!-- Projects -->
        <div class="row">
          <?php if ($projects->num_rows > 0): ?>
            <?php while ($project = $projects->fetch_assoc()): ?>
              <div class="col-12 mb-4">
                <div class="project-card">
                  <div class="project-title"><?php echo htmlspecialchars($project['name']); ?></div>

                  <!-- Employee Table -->
                  <table class="employee-table">
                    <thead>
                      <tr>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Passport Number</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $project_id = $project['id'];
                      $employees = $conn->query("SELECT e.emp_no, e.name, e.passport_nic_no FROM employee_project_allocations AS ep
                                                 JOIN employees AS e ON ep.employee_id = e.emp_no
                                                 WHERE ep.project_id = $project_id");

                      if ($employees->num_rows > 0) {
                          while ($employee = $employees->fetch_assoc()) {
                              echo "<tr>
                                      <td>{$employee['emp_no']}</td>
                                      <td>{$employee['name']}</td>
                                      <td>{$employee['passport_nic_no']}</td>
                                      <td>
                                        <a href='remove_employee.php?project_id=$project_id&employee_id={$employee['emp_no']}' class='btn btn-sm btn-danger'>Remove</a>
                                      </td>
                                    </tr>";
                          }
                      } else {
                          echo "<tr>
                                  <td colspan='4' class='no-employees'>No employees allocated</td>
                                </tr>";
                      }
                      ?>
                    </tbody>
                  </table>

                  <a href="add_employees_to_project.php?project_id=<?php echo $project['id']; ?>" class="btn btn-sm btn-primary mt-3">Add Employees</a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-12">
              <p class="text-center">No projects found.</p>
            </div>
          <?php endif; ?>
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
