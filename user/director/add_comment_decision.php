<?php
session_start();
include '../db.php';

// Ensure only the Director can access
if ($_SESSION['role'] !== 'director') {
    header('Location: ../login.php');
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $warning_id = intval($_POST['warning_id']);
    $management_comment = $conn->real_escape_string($_POST['management_comment']);
    $management_decision = $conn->real_escape_string($_POST['management_decision']);

    // Update the management comment, decision, and status in the warnings table
    $sql = "UPDATE warnings 
            SET management_comment = ?, management_decision = ?, status = 'Resolved', updated_at = NOW() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $management_comment, $management_decision, $warning_id);
        if ($stmt->execute()) {
            // Redirect to Director's warnings list
            header('Location: view_warnings_director.php');
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
            header('Location: view_warnings_director.php');
            exit();
        }
    } else {
        $error = "Error fetching warning details: " . $conn->error;
    }

    // Fetch previous warnings for the same employee
    $emp_no = $warning['emp_no'];
    $prev_warnings_sql = "
        SELECT w.id, w.problem, w.status, w.created_at, w.employee_statement, 
               w.hod_statement, w.hrm_statement, w.management_comment, w.management_decision
        FROM warnings w
        WHERE w.emp_no = ? AND w.id != ?";
    $prev_stmt = $conn->prepare($prev_warnings_sql);

    if ($prev_stmt) {
        $prev_stmt->bind_param("si", $emp_no, $warning_id);
        $prev_stmt->execute();
        $prev_warnings = $prev_stmt->get_result();
    } else {
        $error = "Error fetching previous warnings: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment or Decision</title>
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
                        <h3 class="fw-semibold mb-0 ms-6">Review Warning</h3>
                        <a href="view_warnings_director.php" class="btn btn-primary">Back to Warnings</a>
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
                        <blockquote><?php echo htmlspecialchars($warning['hod_statement']); ?></blockquote>
                    </div>
                </div>

                <!-- HRM Comment -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">HRM Comment</h5>
                        <blockquote><?php echo htmlspecialchars($warning['hrm_statement']); ?></blockquote>
                    </div>
                </div>

                <!-- Previous Warnings -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Previous Warnings for Employee</h5>
                        <?php if ($prev_warnings->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table align-middle table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Problem</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($prev_warning = $prev_warnings->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($prev_warning['created_at']))); ?></td>
                                                <td><?php echo htmlspecialchars($prev_warning['problem']); ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        <?php echo htmlspecialchars($prev_warning['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                            data-bs-target="#warningDetailsModal<?php echo $prev_warning['id']; ?>">
                                                        View Details
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="warningDetailsModal<?php echo $prev_warning['id']; ?>" 
                                                         tabindex="-1" aria-labelledby="warningDetailsLabel<?php echo $prev_warning['id']; ?>" 
                                                         aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="warningDetailsLabel<?php echo $prev_warning['id']; ?>">
                                                                        Warning Details (ID: <?php echo $prev_warning['id']; ?>)
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" 
                                                                            aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Problem:</strong> <?php echo htmlspecialchars($prev_warning['problem']); ?></p>
                                                                    <p><strong>Employee Statement:</strong> <?php echo htmlspecialchars($prev_warning['employee_statement']); ?></p>
                                                                    <p><strong>HOD Comment:</strong> <?php echo htmlspecialchars($prev_warning['hod_statement']); ?></p>
                                                                    <p><strong>HRM Comment:</strong> <?php echo htmlspecialchars($prev_warning['hrm_statement']); ?></p>
                                                                    <p><strong>Management Decision:</strong> <?php echo htmlspecialchars($prev_warning['management_decision']); ?></p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No previous warnings found for this employee.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Add Comment or Decision Form -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Add Comment or Final Decision</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <input type="hidden" name="warning_id" value="<?php echo $warning_id; ?>">
                            <div class="mb-3">
                                <label for="management_comment" class="form-label">Comment:</label>
                                <textarea name="management_comment" id="management_comment" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="management_decision" class="form-label">Final Decision:</label>
                                <select name="management_decision" id="management_decision" class="form-control" required>
                                    <option value="" disabled selected>-- Select Decision --</option>
                                    <option value="Warning">Warning</option>
                                    <option value="Termination">Termination</option>
                                    <option value="Fine">Fine</option>
                                    <option value="Warning with Fine">Warning with Fine</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Decision</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
</body>
</html>
