<?php
include '../db.php';
include '../session.php';

$error_msg = isset($_GET['error']) ? $_GET['error'] : "";
$success_msg = isset($_GET['success']) ? $_GET['success'] : "";
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Umrah Leave Application</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <?php include '../header.php'; ?>
        <div class="container-fluid">
            <h4 class="mt-4">Apply for Umrah Leave</h4>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <form method="POST" action="submit_umrah_leave.php" class="card p-4 shadow mt-3">
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Employee ID:</label>
                    <input type="text" class="form-control" name="employee_id" required>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>

                <div class="mb-3">
                    <label for="num_days" class="form-label">Number of Days:</label>
                    <input type="number" class="form-control" name="num_days" required>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks:</label>
                    <textarea class="form-control" name="remarks" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit Umrah Leave</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
