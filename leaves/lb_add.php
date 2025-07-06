<?php 
include '../db.php'; // Updated relative path
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $leave_type_id = intval($_POST['leave_type_id']);
    $balance = intval($_POST['balance']);

    // Check if the record exists
    $checkSql = "SELECT * FROM leave_balances WHERE emp_no = ? AND leave_type_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("si", $emp_no, $leave_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the record if it exists
        $updateSql = "UPDATE leave_balances 
                      SET balance = balance + ?, last_updated = CURRENT_TIMESTAMP 
                      WHERE emp_no = ? AND leave_type_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("isi", $balance, $emp_no, $leave_type_id);

        if ($stmt->execute()) {
            $message = "Leave balance updated successfully.";
        } else {
            $error = "Error updating leave balance: " . $conn->error;
        }
    } else {
        // Insert a new record if it doesn't exist
        $insertSql = "INSERT INTO leave_balances (emp_no, leave_type_id, balance, last_updated) 
                      VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sii", $emp_no, $leave_type_id, $balance);

        if ($stmt->execute()) {
            $message = "Leave balance added successfully.";
        } else {
            $error = "Error adding leave balance: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add or Update Leave Balance</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end">
                        <a href="balance.php" class="btn btn-primary">View All Leave Balances</a>
                    </div>
                </nav>
            </header>
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Add or Update Leave Balance</h5>
                        <?php if (isset($message)): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php elseif (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" class="shadow p-4 bg-light rounded">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No">
                            </div>
                            <div class="mb-3">
                                <label for="leave_type_id" class="form-label">Leave Type:</label>
                                <select name="leave_type_id" id="leave_type_id" required class="form-control">
                                    <option value="" disabled selected>-- Select Leave Type --</option>
                                    <?php
                                    // Fetch leave types from the `leave_types` table
                                    $query = "SELECT id, name FROM leave_types";
                                    $result = $conn->query($query);
                            
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>No leave types available</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="balance" class="form-label">Add Balance:</label>
                                <input type="number" name="balance" id="balance" required class="form-control" placeholder="Enter balance to add">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
