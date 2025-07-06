<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
include '../db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$from = $_GET['from_date'] ?? '';
$to = $_GET['to_date'] ?? '';

$spreadsheet = new Spreadsheet();
$sheetIndex = 0;

function writeSheet($spreadsheet, $title, $query, $params = [], $conn) {
    global $sheetIndex;

    $cleanTitle = preg_replace('/[\\/?*:\[\]]/', '-', $title);
    $cleanTitle = substr($cleanTitle, 0, 31);

    $sheet = ($sheetIndex === 0) ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle($cleanTitle);

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $columns = array_keys($result->fetch_assoc());
        $result->data_seek(0);

        foreach ($columns as $col => $name) {
            $cell = Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, ucfirst(str_replace('_', ' ', $name)));
        }

        $rowIndex = 2;
        while ($row = $result->fetch_assoc()) {
            foreach ($columns as $col => $name) {
                $cell = Coordinate::stringFromColumnIndex($col + 1) . $rowIndex;
                $sheet->setCellValue($cell, $row[$name]);
            }
            $rowIndex++;
        }
    }

    $sheetIndex++;
}

// ✅ On Leave (no date filter)
writeSheet($spreadsheet, 'On Leave', "
    SELECT lr.emp_no, e.name, e.nationality, e.designation, lt.name AS leave_type,
           lr.start_date, lr.end_date, lr.status
    FROM leave_records lr
    LEFT JOIN employees e ON lr.emp_no = e.emp_no
    LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status = 'Departed'
", [], $conn);

// ✅ Returned (filter ONLY by actual_arrival_date)
writeSheet($spreadsheet, 'Returned', "
    SELECT lr.emp_no, e.name, e.nationality, e.designation, lt.name AS leave_type,
           lr.start_date, lr.end_date, lr.actual_arrival_date, lr.status
    FROM leave_records lr
    LEFT JOIN employees e ON lr.emp_no = e.emp_no
    LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status = 'Arrived'
      AND lr.actual_arrival_date BETWEEN ? AND ?
", [$from, $to], $conn);

// ✅ Requested
writeSheet($spreadsheet, 'Requested', "
    SELECT lr.emp_no, e.name, e.nationality, e.designation, lt.name AS leave_type,
           lr.applied_date, lr.start_date, lr.end_date, lr.status
    FROM leave_records lr
    LEFT JOIN employees e ON lr.emp_no = e.emp_no
    LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status IN ('Pending', 'Approved') 
      AND lr.applied_date BETWEEN ? AND ?
", [$from, $to], $conn);

// ✅ Terminated / Resigned
writeSheet($spreadsheet, 'Terminated - Resigned', "
    SELECT emp_no, name, nationality, designation, employment_status, termination_date 
    FROM employees 
    WHERE employment_status IN ('Terminated', 'Resigned', 'Missing', 'Retired') 
      AND termination_date BETWEEN ? AND ?
", [$from, $to], $conn);

// ✅ Pending Arrivals (no date filter)
writeSheet($spreadsheet, 'Pending Arrivals', "
    SELECT lr.emp_no, e.name, e.nationality, e.designation, lt.name AS leave_type,
           lr.start_date, lr.end_date, lr.status
    FROM leave_records lr
    LEFT JOIN employees e ON lr.emp_no = e.emp_no
    LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
    WHERE lr.status = 'Pending Leave Arrival'
      AND (lr.actual_arrival_date IS NULL OR lr.actual_arrival_date = '')
", [], $conn);

// ✅ Summary sheet
$summary_stmt = $conn->prepare("
    SELECT
        (SELECT COUNT(*) FROM leave_records WHERE status = 'Departed') AS on_leave,
        (SELECT COUNT(*) FROM leave_records WHERE status = 'Arrived' AND actual_arrival_date BETWEEN ? AND ?) AS returned,
        (SELECT COUNT(*) FROM leave_records WHERE status IN ('Pending','Approved') AND applied_date BETWEEN ? AND ?) AS requested,
        (SELECT COUNT(*) FROM employees WHERE employment_status IN ('Terminated', 'Resigned', 'Missing', 'Retired') AND termination_date BETWEEN ? AND ?) AS terminated_count,
        (SELECT COUNT(*) FROM leave_records WHERE status = 'Pending Leave Arrival' AND (actual_arrival_date IS NULL OR actual_arrival_date = '')) AS pending
");

$summary_stmt->bind_param(
    'ssssss',
    $from, $to,   // returned
    $from, $to,   // requested
    $from, $to    // terminated
);

$summary_stmt->execute();
$summary_result = $summary_stmt->get_result();
$summary_data = $summary_result->fetch_assoc();

$summary_sheet = $spreadsheet->createSheet();
$summary_sheet->setTitle('Summary');
$row = 1;
foreach ($summary_data as $label => $value) {
    $summary_sheet->setCellValue('A' . $row, strtoupper(str_replace('_', ' ', $label)));
    $summary_sheet->setCellValue('B' . $row, $value);
    $row++;
}

// ✅ Output
$spreadsheet->setActiveSheetIndex(0);
$filename = "HR_Report_" . date('Ymd_His') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
