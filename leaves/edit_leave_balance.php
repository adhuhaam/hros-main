<?php
include '../db.php';
include '../session.php';

// Check if employee number and leave type are provided
if (!isset($_GET['emp_no']) || !isset($_GET['leave_type'])) {
    echo "Invalid request.";
    exit;
}

$emp_no = $_GET['emp_no'];
$leave_type = $_GET['leave_type'];

// Fetch the current leave balance for the selected employee and leave type
$balance_query = $conn->prepare("
    SELECT lb.emp_no, e.name as employee_name, e.designation, lt.id as leave_type_id, lt.name as leave_type, lb.balance, lb.last_updated
    FROM leave_balances lb
    INNER JOIN employees e ON lb.emp_no = e.emp_no
    INNER JOIN leave_types lt ON lb.leave_type_id = lt.id
    WHERE lb.emp_no = ? AND lt.name = ?
");
$balance_query->bind_param("ss", $emp_no, $leave_type);
$balance_query->execute();
$balance_result = $balance_query->get_result();
$balance_data = $balance_result->fetch_assoc();

if (!$balance_data) {
    echo "Leave balance record not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_balance = $_POST['balance'];
    $update_query = $conn->prepare("UPDATE leave_balances SET balance = ?, last_updated = NOW() WHERE emp_no = ? AND leave_type_id = ?");
    $update_query->bind_param("isi", $new_balance, $emp_no, $balance_data['leave_type_id']);

    if ($update_query->execute()) {
        header("Location: balance.php?message=Leave balance updated successfully.");
        exit;
    } else {
        $error = "Failed to update leave balance.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Leave Balance</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-between">
                        <h4 class="fw-semibold">Edit Leave Balance</h4>
                        <a href="leave_balances.php" class="btn btn-secondary btn-sm">Back</a>
                    </div>
                </nav>
            </header>

            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Update Leave Balance</h5>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Employee No:</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($balance_data['emp_no']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Employee Name:</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($balance_data['employee_name']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Designation:</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($balance_data['designation']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Leave Type:</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($balance_data['leave_type']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Current Balance:</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($balance_data['balance']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Balance:</label>
                                <input type="number" name="balance" class="form-control" value="<?php echo htmlspecialchars($balance_data['balance']); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Update Balance</button>
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
