<?php
// Include database and any required files
include 'db.php';
include 'session.php';

// Fetch counts for specific employment statuses
$activeCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'")->fetch_assoc()['total'];
$deadCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'DEAD'")->fetch_assoc()['total'];
$missingCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'MISSING'")->fetch_assoc()['total'];
$resignedCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'RESIGNED'")->fetch_assoc()['total'];
$retiredCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'RETIRED'")->fetch_assoc()['total'];
$terminatedCount = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'TERMINATED'")->fetch_assoc()['total'];
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

     <aside class="left-sidebar">
        <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main Wrapper -->
    <div class="body-wrapper">
        <!-- Header -->
           <?php include 'header.php'; ?>
 
      <div class="container-fluid">
        

        <!-- Employment Status Cards -->
        <div class="row mb-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Active</h5>
                <h4 class="fw-bold text-info"><?php echo $activeCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">DEAD</h5>
                <h4 class="fw-bold text-danger"><?php echo $deadCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">MISSING</h5>
                <h4 class="fw-bold text-warning"><?php echo $missingCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">RESIGNED</h5>
                <h4 class="fw-bold text-secondary"><?php echo $resignedCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">RETIRED</h5>
                <h4 class="fw-bold text-primary"><?php echo $retiredCount; ?></h4>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">TERMINATED</h5>
                <h4 class="fw-bold text-danger"><?php echo $terminatedCount; ?></h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Sections -->
        <div class="row">
          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Recent Requests</h5>
                <div class="table-responsive">
                  <table class="table table-striped align-middle">
                    <thead>
                      <tr>
                        <th>Employee No</th>
                        <th>Print Type</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $recentRequests = $conn->query("SELECT emp_no, print_type, status, requested_date FROM card_print ORDER BY id DESC LIMIT 5");
                      while ($row = $recentRequests->fetch_assoc()): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                          <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                          <td>
                            <span class="badge 
                              <?php echo $row['status'] == 'Pending' ? 'bg-warning text-dark' : ($row['status'] == 'Printed' ? 'bg-success text-white' : 'bg-danger text-white'); ?>">
                              <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                          </td>
                          <td><?php echo htmlspecialchars($row['requested_date']); ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Upcoming Deadlines</h5>
                <ul class="timeline-widget">
                  <li class="timeline-item">
                    <span class="timeline-badge bg-success"></span>
                    <span>Employee Training - 15 Dec 2024</span>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-warning"></span>
                    <span>Policy Review - 20 Dec 2024</span>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-danger"></span>
                    <span>Quarterly Review - 31 Dec 2024</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
</body>

</html>
