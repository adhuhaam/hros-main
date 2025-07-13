<?php 
include '../db.php';
include '../session.php';

// Capture messages from submit_paternity_leave.php
$error_msg = isset($_GET['error']) ? $_GET['error'] : "";
$success_msg = isset($_GET['success']) ? $_GET['success'] : "";

// Initialize variables
$is_eligible = false;
$emp_no = isset($_GET['emp_no']) ? $_GET['emp_no'] : "";
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_eligibility'])) {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];

    // Fetch employee details and ensure it's a Male employee
    $employee_query = $conn->prepare("SELECT gender FROM employees WHERE emp_no = ?");
    $employee_query->bind_param("s", $emp_no);
    $employee_query->execute();
    $employee = $employee_query->get_result()->fetch_assoc();

    if (!$employee || $employee['gender'] !== 'Male') {
        $error_msg = "Paternity Leave is only available for male employees.";
    } else {
        // Fetch leave balance
        $balance_query = $conn->prepare("SELECT balance FROM leave_balances WHERE emp_no = ? AND leave_type_id = 5");
        $balance_query->bind_param("s", $emp_no);
        $balance_query->execute();
        $balance_result = $balance_query->get_result();
        $balance = $balance_result->fetch_assoc()['balance'] ?? 0;

        if ($balance < 1) {
            $error_msg = "You have zero balance left for Paternity Leave.";
        } else {
            $success_msg = "Eligible for Paternity Leave. You have $balance days available.";
            $is_eligible = true;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Paternity Leave Application</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar_position="fixed" data-header_position="fixed">

  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid">
        <a href="index.php" class="btn btn-info mt-4">View All Leave Records</a>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Paternity Leave Application</h5>

                <?php if (!empty($error_msg)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php } ?>

                <?php if (!empty($success_msg)) { ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php } ?>

                <!-- Check Eligibility Form -->
                <form method="POST" class="shadow p-4 bg-light rounded">
                    <input type="hidden" name="check_eligibility" value="1">
                    
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee ID:</label>
                        <input type="text" name="employee_id" class="form-control" value="<?php echo htmlspecialchars($emp_no); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Leave Start Date:</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Check Eligibility</button>
                </form>

                <!-- Show Leave Form if Eligible -->
                <?php if ($is_eligible) { ?>
                    <form method="POST" action="submit_paternity_leave.php" class="shadow p-4 bg-light rounded mt-3">
                        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($emp_no); ?>">
                        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">

                        <div class="mb-3">
                            <label for="num_days" class="form-label">Number of Days:</label>
                            <input type="number" name="num_days" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks:</label>
                            <textarea name="remarks" class="form-control" rows="4"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Submit Leave Application</button>
                    </form>
                <?php } ?>

            </div>
        </div>
    </div>
  </div>
</div>

</body>
</html>
