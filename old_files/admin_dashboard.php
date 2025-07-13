<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database and any required files
try {
    include 'db.php';
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include session check
try {
    include 'session.php';
} catch (Exception $e) {
    die("Session error: " . $e->getMessage());
}

// Fetch counts for specific employment statuses with error handling
$activeCount = 0;
$deadCount = 0;
$missingCount = 0;
$resignedCount = 0;
$retiredCount = 0;
$terminatedCount = 0;
<<<<<<< HEAD
=======
$totalEmployees = 0;
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035

try {
    $activeResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'");
    if ($activeResult) {
        $activeCount = $activeResult->fetch_assoc()['total'];
    }
    
    $deadResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'DEAD'");
    if ($deadResult) {
        $deadCount = $deadResult->fetch_assoc()['total'];
    }
    
    $missingResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'MISSING'");
    if ($missingResult) {
        $missingCount = $missingResult->fetch_assoc()['total'];
    }
    
    $resignedResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'RESIGNED'");
    if ($resignedResult) {
        $resignedCount = $resignedResult->fetch_assoc()['total'];
    }
    
    $retiredResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'RETIRED'");
    if ($retiredResult) {
        $retiredCount = $retiredResult->fetch_assoc()['total'];
    }
    
    $terminatedResult = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'TERMINATED'");
    if ($terminatedResult) {
        $terminatedCount = $terminatedResult->fetch_assoc()['total'];
    }
<<<<<<< HEAD
} catch (Exception $e) {
    error_log("Database query error in admin_dashboard.php: " . $e->getMessage());
}
=======
    
    $totalResult = $conn->query("SELECT COUNT(*) AS total FROM employees");
    if ($totalResult) {
        $totalEmployees = $totalResult->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Database query error in admin_dashboard.php: " . $e->getMessage());
}

// Get current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<<<<<<< HEAD
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
=======
  <title>Admin Dashboard - HR Management System</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="assets/libs/bootstrap/dist/css/bootstrap.min.css" />
  
  <!-- Main CSS -->
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="assets/css/icons/tabler-icons/tabler-icons.min.css" />
  
  <!-- ApexCharts -->
  <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  
  <!-- Custom CSS for Dashboard -->
  <style>
    :root {
      --primary-color: #0085db;
      --secondary-color: #707a82;
      --success-color: #4bd08b;
      --warning-color: #f8c076;
      --danger-color: #c50000;
      --info-color: #46caeb;
      --light-color: #e7ecf0;
      --dark-color: #111c2d;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f5f8fb;
    }

    .page-wrapper {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .body-wrapper {
      background-color: #f5f8fb;
      min-height: calc(100vh - 70px);
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      transition: all 0.3s ease;
      background: white;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-header {
      background: transparent;
      border-bottom: 1px solid #e6ecf1;
      padding: 1.5rem;
      border-radius: 15px 15px 0 0 !important;
    }

    .card-body {
      padding: 1.5rem;
    }

    .stat-card {
      background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
      color: white;
      border-radius: 15px;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: rotate(45deg);
    }

    .stat-card.success {
      background: linear-gradient(135deg, var(--success-color) 0%, #2d7d53 100%);
    }

    .stat-card.warning {
      background: linear-gradient(135deg, var(--warning-color) 0%, #e6a700 100%);
    }

    .stat-card.danger {
      background: linear-gradient(135deg, var(--danger-color) 0%, #a00000 100%);
    }

    .stat-card.info {
      background: linear-gradient(135deg, var(--info-color) 0%, #2a798d 100%);
    }

    .stat-card.secondary {
      background: linear-gradient(135deg, var(--secondary-color) 0%, #5a6268 100%);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
      margin-bottom: 0;
    }

    .stat-icon {
      position: absolute;
      top: 1rem;
      right: 1rem;
      font-size: 2rem;
      opacity: 0.3;
    }

    .table {
      margin-bottom: 0;
    }

    .table th {
      border-top: none;
      font-weight: 600;
      color: var(--dark-color);
      background-color: #f8f9fa;
    }

    .table td {
      vertical-align: middle;
      border-top: 1px solid #e6ecf1;
    }

    .badge {
      font-size: 0.75rem;
      padding: 0.375rem 0.75rem;
      border-radius: 0.375rem;
    }

    .timeline-widget {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .timeline-item {
      position: relative;
      padding: 1rem 0;
      border-left: 2px solid #e6ecf1;
      padding-left: 1.5rem;
      margin-left: 0.5rem;
    }

    .timeline-item:last-child {
      border-left: none;
    }

    .timeline-badge {
      position: absolute;
      left: -0.5rem;
      top: 1.25rem;
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
      width: 1rem;
      height: 1rem;
      border-radius: 50%;
      border: 2px solid white;
<<<<<<< HEAD
    }
    
    /* Loading state */
=======
      box-shadow: 0 0 0 2px #e6ecf1;
    }

    .timeline-content {
      margin-left: 0.5rem;
    }

    .timeline-title {
      font-weight: 600;
      margin-bottom: 0.25rem;
      color: var(--dark-color);
    }

    .timeline-date {
      font-size: 0.875rem;
      color: var(--secondary-color);
    }

    .chart-container {
      position: relative;
      height: 300px;
      margin: 1rem 0;
    }

    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }

    .quick-action-btn {
      display: flex;
      align-items: center;
      padding: 1rem;
      background: white;
      border: 1px solid #e6ecf1;
      border-radius: 10px;
      text-decoration: none;
      color: var(--dark-color);
      transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
      background: var(--primary-color);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .quick-action-icon {
      font-size: 1.5rem;
      margin-right: 0.75rem;
      width: 2rem;
      text-align: center;
    }

    .welcome-section {
      background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
      color: white;
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .welcome-title {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
      opacity: 0.9;
      margin-bottom: 1rem;
    }

    .welcome-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }

    .welcome-stat {
      text-align: center;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }

    .welcome-stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .welcome-stat-label {
      font-size: 0.875rem;
      opacity: 0.8;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .stat-number {
        font-size: 2rem;
      }
      
      .welcome-title {
        font-size: 1.5rem;
      }
      
      .quick-actions {
        grid-template-columns: 1fr;
      }
      
      .welcome-stats {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .card-body {
        padding: 1rem;
      }
      
      .card-header {
        padding: 1rem;
      }
    }

    @media (max-width: 576px) {
      .welcome-stats {
        grid-template-columns: 1fr;
      }
      
      .stat-card {
        padding: 1rem;
      }
      
      .stat-icon {
        font-size: 1.5rem;
      }
    }

    /* Loading Animation */
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }
<<<<<<< HEAD
    
=======

    .spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
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
      <?php 
      try {
          include 'sidebar.php'; 
      } catch (Exception $e) {
          echo "<!-- Sidebar error: " . htmlspecialchars($e->getMessage()) . " -->";
      }
      ?>
    </aside>

    <!-- Main Wrapper -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php 
      try {
          include 'header.php'; 
      } catch (Exception $e) {
          echo "<!-- Header error: " . htmlspecialchars($e->getMessage()) . " -->";
      }
      ?>

      <div class="container-fluid">
<<<<<<< HEAD
        <!-- Page Title -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
            <p class="text-muted">Welcome back! Here's what's happening with your employees today.</p>
=======
        <!-- Welcome Section -->
        <div class="welcome-section">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <h1 class="welcome-title">Welcome back, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>!</h1>
              <p class="welcome-subtitle">Here's what's happening with your employees today.</p>
              <div class="welcome-stats">
                <div class="welcome-stat">
                  <div class="welcome-stat-number"><?php echo $totalEmployees; ?></div>
                  <div class="welcome-stat-label">Total Employees</div>
                </div>
                <div class="welcome-stat">
                  <div class="welcome-stat-number"><?php echo $activeCount; ?></div>
                  <div class="welcome-stat-label">Active</div>
                </div>
                <div class="welcome-stat">
                  <div class="welcome-stat-number"><?php echo date('d'); ?></div>
                  <div class="welcome-stat-label">Today</div>
                </div>
                <div class="welcome-stat">
                  <div class="welcome-stat-number"><?php echo date('M'); ?></div>
                  <div class="welcome-stat-label"><?php echo date('Y'); ?></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 text-center">
              <i class="fas fa-chart-line" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
          </div>
        </div>

        <!-- Employment Status Cards -->
        <div class="row mb-4">
          <div class="col-sm-6 col-lg-3 mb-3">
<<<<<<< HEAD
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
=======
            <div class="stat-card success">
              <div class="stat-number"><?php echo $activeCount; ?></div>
              <div class="stat-label">Active Employees</div>
              <i class="fas fa-user-check stat-icon"></i>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card danger">
              <div class="stat-number"><?php echo $deadCount; ?></div>
              <div class="stat-label">Deceased</div>
              <i class="fas fa-user-times stat-icon"></i>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card warning">
              <div class="stat-number"><?php echo $missingCount; ?></div>
              <div class="stat-label">Missing</div>
              <i class="fas fa-user-slash stat-icon"></i>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card secondary">
              <div class="stat-number"><?php echo $resignedCount; ?></div>
              <div class="stat-label">Resigned</div>
              <i class="fas fa-user-minus stat-icon"></i>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card info">
              <div class="stat-number"><?php echo $retiredCount; ?></div>
              <div class="stat-label">Retired</div>
              <i class="fas fa-user-clock stat-icon"></i>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card danger">
              <div class="stat-number"><?php echo $terminatedCount; ?></div>
              <div class="stat-label">Terminated</div>
              <i class="fas fa-user-xmark stat-icon"></i>
            </div>
          </div>
        </div>

        <!-- Charts and Analytics Section -->
        <div class="row mb-4">
          <div class="col-lg-8 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="fas fa-chart-bar me-2 text-primary"></i>
                  Employee Status Overview
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <div id="employeeChart"></div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                </div>
              </div>
            </div>
          </div>
          
<<<<<<< HEAD
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
=======
          <div class="col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="fas fa-pie-chart me-2 text-success"></i>
                  Status Distribution
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <div id="statusPieChart"></div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                </div>
              </div>
            </div>
          </div>
        </div>

<<<<<<< HEAD
        <!-- Additional Sections -->
        <div class="row">
          <div class="col-lg-8 mb-4">
            <div class="card h-100">
              <div class="card-header bg-transparent border-0">
                <h5 class="card-title fw-semibold mb-0">Recent Requests</h5>
=======
        <!-- Recent Activity and Quick Actions -->
        <div class="row">
          <div class="col-lg-8 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="fas fa-clock me-2 text-warning"></i>
                  Recent Requests
                </h5>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover align-middle">
<<<<<<< HEAD
                    <thead class="table-light">
=======
                    <thead>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                      <tr>
                        <th>Employee No</th>
                        <th>Print Type</th>
                        <th>Status</th>
                        <th>Requested Date</th>
<<<<<<< HEAD
=======
                        <th>Action</th>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $recentRequests = null;
                      try {
                          $recentRequests = $conn->query("SELECT emp_no, print_type, status, requested_date FROM card_print ORDER BY id DESC LIMIT 5");
                      } catch (Exception $e) {
                          error_log("Error fetching recent requests: " . $e->getMessage());
                      }
                      if ($recentRequests && $recentRequests->num_rows > 0):
                        while ($row = $recentRequests->fetch_assoc()): ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['emp_no']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                            <td>
                              <span class="badge 
<<<<<<< HEAD
                                <?php echo $row['status'] == 'Pending' ? 'bg-warning text-dark' : ($row['status'] == 'Printed' ? 'bg-success text-white' : 'bg-danger text-white'); ?>">
=======
                                <?php echo $row['status'] == 'Pending' ? 'bg-warning text-dark' : ($row['status'] == 'Printed' ? 'bg-success' : 'bg-danger'); ?>">
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                                <?php echo htmlspecialchars($row['status']); ?>
                              </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['requested_date']); ?></td>
<<<<<<< HEAD
=======
                            <td>
                              <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                              </button>
                            </td>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                          </tr>
                        <?php endwhile;
                      else: ?>
                        <tr>
<<<<<<< HEAD
                          <td colspan="4" class="text-center text-muted">No recent requests found</td>
=======
                          <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <br>No recent requests found
                          </td>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
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
<<<<<<< HEAD
              <div class="card-header bg-transparent border-0">
                <h5 class="card-title fw-semibold mb-0">Upcoming Deadlines</h5>
=======
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="fas fa-calendar-alt me-2 text-info"></i>
                  Upcoming Deadlines
                </h5>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
              </div>
              <div class="card-body">
                <ul class="timeline-widget">
                  <li class="timeline-item">
                    <span class="timeline-badge bg-success"></span>
<<<<<<< HEAD
                    <div>
                      <strong>Employee Training</strong>
                      <br>
                      <small class="text-muted">15 Dec 2024</small>
=======
                    <div class="timeline-content">
                      <div class="timeline-title">Employee Training</div>
                      <div class="timeline-date">15 Dec 2024</div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                    </div>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-warning"></span>
<<<<<<< HEAD
                    <div>
                      <strong>Policy Review</strong>
                      <br>
                      <small class="text-muted">20 Dec 2024</small>
=======
                    <div class="timeline-content">
                      <div class="timeline-title">Policy Review</div>
                      <div class="timeline-date">20 Dec 2024</div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                    </div>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-danger"></span>
<<<<<<< HEAD
                    <div>
                      <strong>Quarterly Review</strong>
                      <br>
                      <small class="text-muted">31 Dec 2024</small>
=======
                    <div class="timeline-content">
                      <div class="timeline-title">Quarterly Review</div>
                      <div class="timeline-date">31 Dec 2024</div>
                    </div>
                  </li>
                  <li class="timeline-item">
                    <span class="timeline-badge bg-info"></span>
                    <div class="timeline-content">
                      <div class="timeline-title">Annual Report</div>
                      <div class="timeline-date">15 Jan 2025</div>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

<<<<<<< HEAD
=======
        <!-- Quick Actions -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="fas fa-bolt me-2 text-primary"></i>
                  Quick Actions
                </h5>
              </div>
              <div class="card-body">
                <div class="quick-actions">
                  <a href="employee/" class="quick-action-btn">
                    <i class="fas fa-user-plus quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Add Employee</div>
                      <small class="text-muted">Register new employee</small>
                    </div>
                  </a>
                  
                  <a href="cards/" class="quick-action-btn">
                    <i class="fas fa-id-card quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Print Cards</div>
                      <small class="text-muted">Generate ID cards</small>
                    </div>
                  </a>
                  
                  <a href="attendance/" class="quick-action-btn">
                    <i class="fas fa-clock quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Attendance</div>
                      <small class="text-muted">View attendance records</small>
                    </div>
                  </a>
                  
                  <a href="payroll/" class="quick-action-btn">
                    <i class="fas fa-money-bill-wave quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Payroll</div>
                      <small class="text-muted">Manage payroll</small>
                    </div>
                  </a>
                  
                  <a href="leave/" class="quick-action-btn">
                    <i class="fas fa-calendar-day quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Leave Management</div>
                      <small class="text-muted">Process leave requests</small>
                    </div>
                  </a>
                  
                  <a href="reports/" class="quick-action-btn">
                    <i class="fas fa-chart-line quick-action-icon"></i>
                    <div>
                      <div class="fw-semibold">Reports</div>
                      <small class="text-muted">Generate reports</small>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
      </div>
    </div>
  </div>

<<<<<<< HEAD
  <!-- Mobile Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
=======
  <!-- Scripts -->
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>

  <!-- Dashboard Charts -->
  <script>
    $(document).ready(function() {
      // Employee Status Bar Chart
      var employeeChartOptions = {
        series: [{
          name: 'Employees',
          data: [<?php echo $activeCount; ?>, <?php echo $deadCount; ?>, <?php echo $missingCount; ?>, <?php echo $resignedCount; ?>, <?php echo $retiredCount; ?>, <?php echo $terminatedCount; ?>]
        }],
        chart: {
          type: 'bar',
          height: 280,
          fontFamily: 'Plus Jakarta Sans, sans-serif',
          toolbar: {
            show: false
          }
        },
        colors: ['#4bd08b', '#c50000', '#f8c076', '#707a82', '#46caeb', '#c50000'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 6
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: ['Active', 'Deceased', 'Missing', 'Resigned', 'Retired', 'Terminated'],
          labels: {
            style: {
              colors: '#707a82'
            }
          }
        },
        yaxis: {
          title: {
            text: 'Number of Employees',
            style: {
              color: '#707a82'
            }
          },
          labels: {
            style: {
              colors: '#707a82'
            }
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val + " employees"
            }
          }
        }
      };

      var employeeChart = new ApexCharts(document.querySelector("#employeeChart"), employeeChartOptions);
      employeeChart.render();

      // Status Distribution Pie Chart
      var statusPieOptions = {
        series: [<?php echo $activeCount; ?>, <?php echo $deadCount; ?>, <?php echo $missingCount; ?>, <?php echo $resignedCount; ?>, <?php echo $retiredCount; ?>, <?php echo $terminatedCount; ?>],
        chart: {
          type: 'donut',
          height: 280,
          fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        labels: ['Active', 'Deceased', 'Missing', 'Resigned', 'Retired', 'Terminated'],
        colors: ['#4bd08b', '#c50000', '#f8c076', '#707a82', '#46caeb', '#c50000'],
        plotOptions: {
          pie: {
            donut: {
              size: '70%',
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: '12px',
                  color: '#707a82'
                },
                value: {
                  show: true,
                  fontSize: '16px',
                  fontWeight: 600,
                  color: '#111c2d'
                },
                total: {
                  show: true,
                  label: 'Total',
                  fontSize: '14px',
                  fontWeight: 600,
                  color: '#111c2d'
                }
              }
            }
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          position: 'bottom',
          fontSize: '12px',
          colors: '#707a82'
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val + " employees"
            }
          }
        }
      };

      var statusPieChart = new ApexCharts(document.querySelector("#statusPieChart"), statusPieOptions);
      statusPieChart.render();

      // Mobile sidebar toggle
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
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
      
<<<<<<< HEAD
      // Close sidebar on window resize if screen becomes larger
=======
      // Close sidebar on window resize
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          sidebar.classList.remove('show');
          overlay.classList.remove('show');
        }
      });
<<<<<<< HEAD
    });
  </script>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
=======

      // Auto-refresh data every 5 minutes
      setInterval(function() {
        location.reload();
      }, 300000);
    });
  </script>
>>>>>>> f11b40920280bd2e51d8e5f867116b3402171035
</body>

</html>
