<?php
include '../db.php';
include '../session.php';

// Fetch work permit details for the selected record
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT wpf.*, e.name AS employee_name, e.emp_no AS employee_no 
            FROM work_permit_fees wpf 
            INNER JOIN employees e ON wpf.emp_no = e.emp_no 
            WHERE wpf.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $work_permit = $result->fetch_assoc();

    if (!$work_permit) {
        die('Work Permit record not found.');
    }
}

// Update work permit record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $expiry_date = $_POST['expiry_date'];
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $sql = "UPDATE work_permit_fees SET status = ?, expiry_date = ?, remarks = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $status, $expiry_date, $remarks, $id);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Error updating work permit record: ' . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Work Permit</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <?php include '../header.php'; ?>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Update Work Permit Record</h5>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= $work_permit['id'] ?>">

                        <div class="mb-3">
                            <label for="employee_no" class="form-label">Employee No</label>
                            <input type="text" id="employee_no" class="form-control" value="<?= $work_permit['employee_no'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control" value="<?= $work_permit['employee_name'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Work Permit Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Pending" <?= $work_permit['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Collection Created" <?= $work_permit['status'] === 'Collection Created' ? 'selected' : '' ?>>Collection Created</option>
                                <option value="Paid" <?= $work_permit['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="Completed" <?= $work_permit['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="<?= $work_permit['expiry_date'] ?>" >
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="4"><?= htmlspecialchars($work_permit['remarks']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>
</html>
