<?php
session_start();
include '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch warning details
if (isset($_GET['id'])) {
    $warning_id = intval($_GET['id']);
    $sql = "SELECT w.*, e.name, e.designation 
            FROM warnings w
            LEFT JOIN employees e ON w.emp_no = e.emp_no
            WHERE w.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $warning_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $warning = $result->fetch_assoc();

    if (!$warning) {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Warning</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" 
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        
        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="body-wrapper">
            <!-- Header -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-between">
                        <h3 class="fw-semibold mb-0 ms-6">Warning Details</h3>
                        <a href="index.php" class="btn btn-primary">Back to Warnings</a>
                    </div>
                </nav>
            </header>

            <!-- Container -->
            <div class="container-fluid mt-4">
                <!-- Warning Details -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Warning Details</h5>
                        <p><strong>Employee No:</strong> <?php echo htmlspecialchars($warning['emp_no']); ?></p>
                        <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($warning['name']); ?></p>
                        <p><strong>Employee Designation:</strong> <?php echo htmlspecialchars($warning['designation']); ?></p>
                        <p><strong>Problem:</strong><br> <?php echo htmlspecialchars($warning['problem']); ?></p>
                        <p><strong>Employee Statement:</strong><br> <?php echo htmlspecialchars($warning['employee_statement']); ?></p>
                    </div>
                </div>

                <!-- HOD Comment -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">HOD Comment</h5>
                        <p><?php echo htmlspecialchars($warning['hod_statement']); ?></p>
                    </div>
                </div>

                <!-- HRM Comment -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">HRM Comment</h5>
                        <p><?php echo htmlspecialchars($warning['hrm_statement']); ?></p>
                    </div>
                </div>

                <!-- Management Comment -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Management Comment</h5>
                        <p><?php echo htmlspecialchars($warning['management_comment']); ?></p>
                    </div>
                </div>

                <!-- Management Decision -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Management Decision</h5>
                        <p>
                            <span class="badge text-bg-warning">
                                <?php echo htmlspecialchars($warning['management_decision']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Generate Warning Letter -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <a href="w_letter.php?id=<?php echo $warning['id']; ?>" class="btn btn-success">
                            Generate Warning Letter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
