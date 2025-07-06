<?php
include '../db.php';
include '../session.php';

// Fetch visa details or employee details for the selected record
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "
        SELECT e.emp_no AS employee_no, e.name AS employee_name, 
               COALESCE(vs.visa_expiry_date, '') AS visa_expiry_date, 
               COALESCE(vs.visa_status, 'Pending') AS visa_status, 
               COALESCE(vs.remarks, '') AS remarks
        FROM employees e
        LEFT JOIN visa_sticker vs ON e.emp_no = vs.emp_no
        WHERE e.emp_no = ? AND e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $visa = $result->fetch_assoc();

    if (!$visa) {
        die('Employee record not found or not eligible for visa processing.');
    }
}

// Update visa record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $visa_status = $_POST['visa_status'];
    $visa_expiry_date = $_POST['visa_expiry_date'];
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // Ensure there's a visa record before updating, otherwise insert a new one
    $checkSql = "SELECT COUNT(*) AS count FROM visa_sticker WHERE emp_no = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recordExists = $result->fetch_assoc()['count'] > 0;

    if ($recordExists) {
        $sql = "UPDATE visa_sticker SET visa_status = ?, visa_expiry_date = ?, remarks = ?, updated_at = NOW() WHERE emp_no = ?";
    } else {
        $sql = "INSERT INTO visa_sticker (emp_no, visa_status, visa_expiry_date, remarks, updated_at) VALUES (?, ?, ?, ?, NOW())";
    }

    $stmt = $conn->prepare($sql);
    if ($recordExists) {
        $stmt->bind_param('sssi', $visa_status, $visa_expiry_date, $remarks, $id);
    } else {
        $stmt->bind_param('ssss', $id, $visa_status, $visa_expiry_date, $remarks);
    }

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Error updating visa record: ' . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Visa</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <!-- Header -->
        <?php include '../header.php'; ?>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Update Visa Record</h5>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= $visa['employee_no'] ?>">

                        <div class="mb-3">
                            <label for="employee_no" class="form-label">Employee No</label>
                            <input type="text" id="employee_no" class="form-control" value="<?= $visa['employee_no'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control" value="<?= $visa['employee_name'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="visa_status" class="form-label">Visa Status</label>
                            <select name="visa_status" id="visa_status" class="form-control" required>
                                <option value="Pending" <?= $visa['visa_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Pending Approval" <?= $visa['visa_status'] === 'Pending Approval' ? 'selected' : '' ?>>Pending Approval</option>
                                <option value="Ready for Submission" <?= $visa['visa_status'] === 'Ready for Submission' ? 'selected' : '' ?>>Ready for Submission</option>
                                <option value="Ready for Collection" <?= $visa['visa_status'] === 'Ready for Collection' ? 'selected' : '' ?>>Ready for Collection</option>
                                <option value="Completed" <?= $visa['visa_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="visa_expiry_date" class="form-label">Visa Expiry Date</label>
                            <input type="date" name="visa_expiry_date" id="visa_expiry_date" class="form-control" value="<?= $visa['visa_expiry_date'] ?>">
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="4"><?= htmlspecialchars($visa['remarks']) ?></textarea>
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
