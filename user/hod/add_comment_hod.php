<?php
session_start();
include '../db.php';

// Ensure only HOD can access
if ($_SESSION['role'] !== 'hod') {
    header('Location: ../login.php');
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $warning_id = intval($_POST['warning_id']);
    $hod_statement = $conn->real_escape_string($_POST['hod_statement']);

    // Update the HOD comment and status in the warnings table
    $sql = "UPDATE warnings 
            SET hod_statement = ?, status = 'Pending HRM Review', updated_at = NOW() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $hod_statement, $warning_id);
        if ($stmt->execute()) {
            // Redirect to the HOD's warnings list
            header('Location: view_warnings_hod.php');
            exit();
        } else {
            $error = "Error updating comment: " . $stmt->error;
        }
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}

// Fetch the warning details along with employee details
if (isset($_GET['warning_id'])) {
    $warning_id = intval($_GET['warning_id']);
    $sql = "SELECT w.*, e.name, e.designation 
            FROM warnings w
            LEFT JOIN employees e ON w.emp_no = e.emp_no
            WHERE w.id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $warning_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $warning = $result->fetch_assoc();

        if (!$warning) {
            header('Location: view_warnings_hod.php');
            exit();
        }
    } else {
        $error = "Error fetching warning details: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment (HOD)</title>
    <link rel="stylesheet" href="../../assets/css/styles.min.css">
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
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
                        <h3 class="fw-semibold mb-0 ms-3">Add Comment (HOD)</h3>
                        <a href="view_warnings_hod.php" class="btn btn-primary">Back to Warnings</a>
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

                <!-- Add Comment Form -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Your Comment</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <input type="hidden" name="warning_id" value="<?php echo $warning_id; ?>">
                            <div class="mb-3">
                                <label for="hod_statement" class="form-label">Comment:</label>
                                <textarea name="hod_statement" id="hod_statement" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
</body>
</html>
