<?php
session_start();
include '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $problem = $conn->real_escape_string($_POST['problem']);
    $employee_statement = $conn->real_escape_string($_POST['employee_statement']);

    // Check if employee exists
    $emp_check_sql = "SELECT name, designation FROM employees WHERE emp_no = ?";
    $emp_check_stmt = $conn->prepare($emp_check_sql);
    $emp_check_stmt->bind_param("s", $emp_no);
    $emp_check_stmt->execute();
    $emp_check_result = $emp_check_stmt->get_result();

    if ($emp_check_result->num_rows > 0) {
        // Employee exists, insert warning
        $sql = "INSERT INTO warnings (emp_no, problem, employee_statement, status, created_at) 
                VALUES (?, ?, ?, 'Pending HOD Review', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $emp_no, $problem, $employee_statement);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit();
        } else {
            $error = "Error adding warning: " . $conn->error;
        }
    } else {
        $error = "Error: Employee not found. Please enter a valid Employee No.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Warning</title>
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
                        <h3 class="fw-semibold mb-0">Add Warning</h3>
                        <a href="index.php" class="btn btn-primary">Back to Warnings</a>
                    </div>
                </nav>
            </header>

            <!-- Container -->
            <div class="container-fluid mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Add Warning</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" class="form-control" required>
                                <small id="emp_details" class="text-success"></small>
                            </div>
                            <div class="mb-3">
                                <label for="problem" class="form-label">Problem:</label>
                                <textarea name="problem" id="problem" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="employee_statement" class="form-label">Employee Statement:</label>
                                <textarea name="employee_statement" id="employee_statement" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Warning</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX to Fetch Employee Details -->
    <script>
        $(document).ready(function() {
            $('#emp_no').on('keyup', function() {
                const empNo = $(this).val();
                if (empNo.length > 0) {
                    $.ajax({
                        url: 'fetch_employee.php',
                        method: 'GET',
                        data: { emp_no: empNo },
                        success: function(response) {
                            $('#emp_details').html(response);
                        },
                        error: function() {
                            $('#emp_details').html('<span class="text-danger">Unable to fetch employee details.</span>');
                        }
                    });
                } else {
                    $('#emp_details').html('');
                }
            });
        });
    </script>
</body>
</html>
