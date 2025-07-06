<?php
session_start();
if ($_SESSION['role'] !== 'director') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.min.css">
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" 
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="body-wrapper">
            <!-- Header -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-between">
                        <h3 class="fw-semibold mb-0 ms-6">Director Dashboard</h3>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    </div>
                </nav>
            </header>

            <!-- Container -->
            <div class="container-fluid mt-4">
                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">Manage Warnings</h5>
                                <p class="mb-3">Review and finalize warnings.</p>
                                <a href="view_warnings_director.php" class="btn btn-primary">View Warnings</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">Generate Reports</h5>
                                <p class="mb-3">View and generate detailed reports.</p>
                                <a href="../reports/index.php" class="btn btn-success">Reports</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Recent Activities</h5>
                        <ul class="list-unstyled">
                            <li>✔️ Warning #123 reviewed and finalized.</li>
                            <li>✔️ Monthly performance report generated.</li>
                            <li>✔️ Employee satisfaction survey results reviewed.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
</body>
</html>
