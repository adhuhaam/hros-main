<?php
include '../db.php';
include '../session.php';

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']['tmp_name'])) {
    $file = $_FILES['file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $stmt = $conn->prepare("INSERT INTO ot_records (
            emp_no, ot_date, ot_type, requested_by,
            requested_hrs, approved_hrs, amount, reason, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Optional: Clear current month’s existing entries
        $conn->query("DELETE FROM ot_records WHERE MONTH(uploaded_at) = MONTH(CURRENT_DATE()) AND YEAR(uploaded_at) = YEAR(CURRENT_DATE())");

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            $emp_no = trim($row[0]);

            // Skip if header/total/empty row
            if (stripos($emp_no, 'EMP') !== false || stripos($emp_no, 'Total') !== false || empty($emp_no)) continue;

            // Remove leading 00 from emp_no
            $emp_no = ltrim($emp_no, '0');

            // Parse fields safely
            $ot_date = !empty($row[1]) ? date('Y-m-d', strtotime($row[1])) : null;
            $ot_type = $row[2] ?? '';
            $requested_by = $row[3] ?? '';
            $requested_hrs = floatval($row[4]);
            $approved_hrs = floatval($row[5]);
            $amount = floatval($row[6]);
            $reason = $row[7] ?? '';
            $status = $row[8] ?? '';

            // Skip if missing critical info
            if (!$emp_no || !$ot_date) continue;

            $stmt->bind_param(
                "ssssddsss",
                $emp_no, $ot_date, $ot_type, $requested_by,
                $requested_hrs, $approved_hrs, $amount, $reason, $status
            );
            $stmt->execute();
        }

        $_SESSION['ot_msg'] = "OT records imported successfully.";
    } catch (Exception $e) {
        $_SESSION['ot_msg'] = "Error: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
