<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';
include '../db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Safe value getter
function safe_get($row, $index) {
    return isset($row[$index]) ? trim((string)$row[$index]) : '';
}

$previewData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_upload'])) {
        if (isset($_SESSION['preview_data'])) {
            $data = $_SESSION['preview_data'];
            $success = 0;
            $fail = 0;

            foreach ($data as $row) {
                [$emp_no, $day_type, $shift, $present_absent, $work_in, $work_out, $remarks, $island, $site, $status, $day, $month, $year] = $row;

                // Check for duplicate
                $check = $conn->prepare("SELECT record_id FROM attendance_records WHERE emp_no = ? AND day = ? AND month = ? AND year = ?");
                $check->bind_param("siii", $emp_no, $day, $month, $year);
                $check->execute();
                $checkResult = $check->get_result();

                if ($checkResult->num_rows === 0) {
                    $stmtName = $conn->prepare("SELECT name FROM employees WHERE emp_no = ?");
                    $stmtName->bind_param("s", $emp_no);
                    $stmtName->execute();
                    $result = $stmtName->get_result();
                    $emp = $result->fetch_assoc();
                    $name = $emp['name'] ?? '';

                    if ($name) {
                        $stmt = $conn->prepare("INSERT INTO attendance_records 
                            (emp_no, month, year, day, day_type, shift, present_absent, work_in, work_out, remarks, island_name, site_name, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("siiisssssssss", 
                            $emp_no,  $month, $year, $day, $day_type, $shift, $present_absent, 
                            $work_in, $work_out, $remarks, $island, $site, $status
                        );

                        $stmt->execute() ? $success++ : $fail++;
                    } else {
                        $fail++;
                    }
                } else {
                    $fail++;
                }
            }

            unset($_SESSION['preview_data']);
            echo "<div class='alert alert-success'>Imported: $success | Skipped (duplicate or invalid): $fail</div>";
        }
    } elseif (isset($_FILES['file'])) {
        $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // skip header

            $emp_no         = safe_get($row, 1);
            $day_type       = safe_get($row, 2);
            $shift          = safe_get($row, 3);
            $present_absent = safe_get($row, 4);
            $work_in        = safe_get($row, 5);
            $work_out       = safe_get($row, 6);
            $remarks        = safe_get($row, 7);
            $island         = safe_get($row, 8);
            $site           = safe_get($row, 9);
            $status         = safe_get($row, 10);
            $day            = (int)safe_get($row, 11);
            $month          = (int)safe_get($row, 12);
            $year           = (int)safe_get($row, 13);

            $previewData[] = [$emp_no, $day_type, $shift, $present_absent, $work_in, $work_out, $remarks, $island, $site, $status, $day, $month, $year];
        }

        $_SESSION['preview_data'] = $previewData;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Attendance</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid pt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-semibold mb-4">Upload Daily Attendance</h4>

                    <?php if (!empty($previewData)): ?>
                        <form method="POST">
                            <table class="table table-bordered table-striped small">
                                <thead>
                                    <tr>
                                        <th>Emp No</th>
                                        <th>Day Type</th>
                                        <th>Shift</th>
                                        <th>P/A</th>
                                        <th>Work In</th>
                                        <th>Work Out</th>
                                        <th>Remarks</th>
                                        <th>Island</th>
                                        <th>Site</th>
                                        <th>Status</th>
                                        <th>Day</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($previewData as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $col): ?>
                                                <td><?= htmlspecialchars($col) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" name="confirm_upload" class="btn btn-success mt-3">Confirm & Import</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Excel File (.xlsx or .csv)</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Preview Attendance</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
