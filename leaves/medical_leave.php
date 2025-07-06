<?php 
include '../db.php';
include '../session.php';

// Messages
$error_msg = $_GET['error'] ?? "";
$success_msg = $_GET['success'] ?? "";

// Vars
$is_eligible = false;
$emp_no = $_GET['emp_no'] ?? "";
$start_date = $_GET['start_date'] ?? "";
$medical_balance = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_eligibility'])) {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];

    // Check employee exists
    $employee_query = $conn->prepare("SELECT emp_no, date_of_join FROM employees WHERE emp_no = ?");
    $employee_query->bind_param("s", $emp_no);
    $employee_query->execute();
    $employee = $employee_query->get_result()->fetch_assoc();

    if (!$employee) {
        $error_msg = "Invalid Employee ID.";
    } else {
        // Get balance
        $balance_query = $conn->prepare("SELECT balance FROM leave_balances WHERE emp_no = ? AND leave_type_id = 2");
        $balance_query->bind_param("s", $emp_no);
        $balance_query->execute();
        $balance_result = $balance_query->get_result();
        $medical_balance = $balance_result->fetch_assoc()['balance'] ?? 0;

        if ($medical_balance < 1) {
            $error_msg = "Insufficient balance for Medical Leave.";
        } else {
            $success_msg = "✅ Eligible. Available Balance: $medical_balance day(s).";
            $is_eligible = true;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Medical Leave Application</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <?php include '../header.php'; ?>
        <div class="container-fluid">
            <a href="index.php" class="btn btn-info mt-4">← Back to Leave Records</a>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Medical Leave Application</h5>

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_msg); ?></div>
                    <?php endif; ?>

                    <!-- Check Eligibility Form -->
                    <form method="POST" class="shadow p-4 bg-light rounded">
                        <input type="hidden" name="check_eligibility" value="1">

                        <div class="mb-3">
                            <label class="form-label">Employee ID:</label>
                            <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($emp_no); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Check Eligibility</button>
                    </form>

                    <!-- Show Leave Form if Eligible -->
                    <?php if ($is_eligible): ?>
                        <form method="POST" action="submit_medical_leave.php" enctype="multipart/form-data" class="shadow p-4 bg-light rounded mt-3">
                            <input type="hidden" name="employee_id" value="<?= htmlspecialchars($emp_no); ?>">
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date); ?>">

                            <div class="mb-3">
                                <label class="form-label">Number of Days:</label>
                                <input type="number" name="num_days" class="form-control" max="<?= $medical_balance; ?>" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Remarks:</label>
                                <textarea name="remarks" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Medical/Reference Document (Required):</label>
                                <input type="file" name="medical_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Submit Medical Leave</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
