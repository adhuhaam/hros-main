<?php
include '../db.php';
include '../session.php';

$record_id = $_GET['id'] ?? null;
if (!$record_id) {
    die("Invalid request");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect updated values
    $work_in = $_POST['work_in'] ?? '';
    $work_out = $_POST['work_out'] ?? '';
    $present_absent = $_POST['present_absent'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    $status = $_POST['status'] ?? '';

    $stmt = $conn->prepare("UPDATE attendance_records SET work_in = ?, work_out = ?, present_absent = ?, remarks = ?, status = ? WHERE record_id = ?");
    $stmt->bind_param("sssssi", $work_in, $work_out, $present_absent, $remarks, $status, $record_id);

    if ($stmt->execute()) {
        header("Location: index.php?updated=1");
        exit;
    } else {
        $error = "Failed to update record.";
    }
}

// Fetch current record
$stmt = $conn->prepare("SELECT * FROM attendance_records WHERE record_id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
if (!$record) die("Record not found");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Attendance Record</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid pt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-semibold mb-4">Edit Attendance Record</h4>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Work In</label>
                                <input type="time" name="work_in" class="form-control" value="<?= htmlspecialchars($record['work_in']) ?>">
                            </div>
                            <div class="col">
                                <label>Work Out</label>
                                <input type="time" name="work_out" class="form-control" value="<?= htmlspecialchars($record['work_out']) ?>">
                            </div>
                            <div class="col">
                                <label>Present / Absent</label>
                                <input type="text" name="present_absent" class="form-control" value="<?= htmlspecialchars($record['present_absent']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="<?= htmlspecialchars($record['remarks']) ?>">
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($record['status']) ?>">
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
