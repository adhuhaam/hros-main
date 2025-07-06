<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        $row = 0;
        $errors = [];
        $success = 0;

        // Skip the header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            $emp_no = $conn->real_escape_string($data[0]);
            $expiry_date = !empty($data[1]) ? $conn->real_escape_string($data[1]) : NULL;
            $status = $conn->real_escape_string($data[2]);
            $remarks = $conn->real_escape_string($data[3]);

            // Validate emp_no
            $empCheckSql = "SELECT emp_no FROM employees WHERE emp_no = '$emp_no' AND employment_status = 'Active' AND nationality != 'MALDIVIAN'";
            $empCheckResult = $conn->query($empCheckSql);

            if ($empCheckResult->num_rows > 0) {
                // Insert into work_permit_fees
                $insertSql = "INSERT INTO work_permit_fees (emp_no, expiry_date, status, remarks, updated_at)
                              VALUES ('$emp_no', '$expiry_date', '$status', '$remarks', NOW())
                              ON DUPLICATE KEY UPDATE expiry_date = VALUES(expiry_date), status = VALUES(status), remarks = VALUES(remarks)";
                if ($conn->query($insertSql)) {
                    $success++;
                } else {
                    $errors[] = "Row $row: Database error - " . $conn->error;
                }
            } else {
                $errors[] = "Row $row: Employee not found or does not meet criteria.";
            }
        }
        fclose($handle);
    } else {
        $errors[] = "Error uploading file.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Import Work Permit Fees</title>
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
                    <h5 class="card-title fw-semibold">Bulk Import Work Permit Fees</h5>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?= $success ?> record(s) imported successfully.
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Upload CSV File</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
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
