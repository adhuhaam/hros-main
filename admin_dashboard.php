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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  
  <!-- Additional responsive CSS -->
  <style>
    /* Mobile-first responsive design */
    @media (max-width: 768px) {
      .card {
        margin-bottom: 1rem;
      }
      
      .table-responsive {
        font-size: 0.875rem;
      }
      
      .table-responsive th,
      .table-responsive td {
        padding: 0.5rem 0.25rem;
      }
      
      .navbar-nav {
        flex-direction: row;
        align-items: center;
      }
      
      .navbar-nav .nav-item {
        margin-right: 0.5rem;
      }
      
      .dropdown-menu {
        position: fixed !important;
        top: 60px !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        border-radius: 0 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      
      .left-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 280px;
        height: 100vh;
        z-index: 1050;
        transition: left 0.3s ease;
        background: white;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      }
      
      .left-sidebar.show {
        left: 0;
      }
      
      .body-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
      }
      
      .app-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1040;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      
      .container-fluid {
        padding-top: 80px;
        padding-left: 1rem;
        padding-right: 1rem;
      }
      
      .col-sm-6 {
        flex: 0 0 50%;
        max-width: 50%;
      }
      
      .col-lg-3 {
        flex: 0 0 100%;
        max-width: 100%;
      }
      
      .col-lg-8 {
        flex: 0 0 100%;
        max-width: 100%;
      }
      
      .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }
    
    @media (min-width: 769px) {
      .left-sidebar {
        position: fixed;
        left: 0;
        width: 280px;
        height: 100vh;
        z-index: 1050;
      }
      
      .body-wrapper {
        margin-left: 280px;
        width: calc(100% - 280px);
      }
      
      .app-header {
        margin-left: 280px;
        width: calc(100% - 280px);
      }
    }
    
    /* Overlay for mobile sidebar */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1045;
    }
    
    .sidebar-overlay.show {
      display: block;
    }
    
    /* Card improvements */
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.2s ease;
    }
    
    .card:hover {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card-body {
      padding: 1.5rem;
    }
    
    /* Status badge improvements */
    .badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
    }
    
    /* Timeline improvements */
    .timeline-widget {
      list-style: none;
      padding: 0;
    }
    
    .timeline-item {
      position: relative;
      padding: 0.75rem 0;
      border-left: 2px solid #e5e7eb;
      padding-left: 1.5rem;
      margin-left: 0.5rem;
    }
    
    .timeline-badge {
      position: absolute;
      left: -0.5rem;
      top: 1rem;
      width: 1rem;
      height: 1rem;
      border-radius: 50%;
      border: 2px solid white;
    }
    
    /* Loading state */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }
    
    /* Smooth transitions */
    * {
      transition: all 0.2s ease;
    }
  </style>
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="left-sidebar" id="leftSidebar">
      <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main Wrapper -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php include 'header.php'; ?>

      <div class="container-fluid">
        <!-- Page Title -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
            <p class="text-muted">Welcome back! Here's what's happening with your employees today.</p>
          </div>
        </div>

        <!-- Employment Status Cards -->
        <div class="row mb-4">
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-primary h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-primary mb-1">Active</h5>
                    <h4 class="fw-bold text-primary mb-0"><?php echo $activeCount; ?></h4>
                  </div>
                  <div class="text-primary">
                    <i class="fa-solid fa-user-check fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-danger h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-danger mb-1">DEAD</h5>
                    <h4 class="fw-bold text-danger mb-0"><?php echo $deadCount; ?></h4>
                  </div>
                  <div class="text-danger">
                    <i class="fa-solid fa-user-times fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-warning h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-warning mb-1">MISSING</h5>
                    <h4 class="fw-bold text-warning mb-0"><?php echo $missingCount; ?></h4>
                  </div>
                  <div class="text-warning">
                    <i class="fa-solid fa-user-slash fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-secondary h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-secondary mb-1">RESIGNED</h5>
                    <h4 class="fw-bold text-secondary mb-0"><?php echo $resignedCount; ?></h4>
                  </div>
                  <div class="text-secondary">
                    <i class="fa-solid fa-user-minus fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-info h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-info mb-1">RETIRED</h5>
                    <h4 class="fw-bold text-info mb-0"><?php echo $retiredCount; ?></h4>
                  </div>
                  <div class="text-info">
                    <i class="fa-solid fa-user-clock fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-danger h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title fw-semibold text-danger mb-1">TERMINATED</h5>
                    <h4 class="fw-bold text-danger mb-0"><?php echo $terminatedCount; ?></h4>
                  </div>
                  <div class="text-danger">
                    <i class="fa-solid fa-user-xmark fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Sections -->
        <div class="row">
          <div class="col-lg-8 mb-4">
            <div class="card h-100">
              <div class="card-header bg-transparent border-0">
                <h5 class="card-title fw-semibold mb-0">Recent Requests</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover align-middle">
                    <thead class="table-light">
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
                      if ($recentRequests && $recentRequests->num_rows > 0):
                        while ($row = $recentRequests->fetch_assoc()): ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['emp_no']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                            <td>
                              <span class="badge 
                                <?php echo $row['status'] == 'Pending' ? 'bg-warning text-dark' : ($row['status'] == 'Printed' ? 'bg-success text-white' : 'bg-danger text-white'); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                              </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['requested_date']); ?></td>
                          </tr>
                        <?php endwhile;
                      else: ?>
                        <tr>
                          <td colspan="4" class="text-center text-muted">No recent requests found</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-header bg-transparent border-0">
                <h5 class="card-title fw-semibold mb-0">Upcoming Deadlines</h5>
              </div>
              <div class="card-body">
                <ul class="timeline-widget">
                  <li class="timeline-item">
                    <span class="timeline-badge bg-success"></span>
                    <div>
                      <strong>Employee Training</strong>
                      <br>
                      <small class="text-muted">15 Dec 2024</small>
                    </div>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-warning"></span>
                    <div>
                      <strong>Policy Review</strong>
                      <br>
                      <small class="text-muted">20 Dec 2024</small>
                    </div>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-danger"></span>
                    <div>
                      <strong>Quarterly Review</strong>
                      <br>
                      <small class="text-muted">31 Dec 2024</small>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Mobile Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarToggle = document.getElementById('headerCollapse');
      const sidebar = document.getElementById('leftSidebar');
      const overlay = document.getElementById('sidebarOverlay');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
          overlay.classList.toggle('show');
        });
      }
      
      if (overlay) {
        overlay.addEventListener('click', function() {
          sidebar.classList.remove('show');
          overlay.classList.remove('show');
        });
      }
      
      // Close sidebar on window resize if screen becomes larger
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          sidebar.classList.remove('show');
          overlay.classList.remove('show');
        }
      });
    });
  </script>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
</body>

</html>
