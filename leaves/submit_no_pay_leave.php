<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];
    $num_days = intval($_POST['num_days']);
    $remarks = $_POST['remarks'];

    // Prevent overlapping leave dates
    $overlap_query = $conn->prepare("
        SELECT * FROM leave_records 
        WHERE emp_no = ? AND leave_type_id = 7 
        AND (start_date <= ? AND end_date >= ?)
    ");
    $overlap_query->bind_param("sss", $emp_no, $start_date, $start_date);
    $overlap_query->execute();
    $overlap_result = $overlap_query->get_result();

    if ($overlap_result->num_rows > 0) {
        header("Location: no_pay_leave.php?error=Your selected dates overlap with an existing No Pay Leave.");
        exit;
    }

    // Calculate end date (excluding Fridays & holidays)
    $leave_year = date('Y', strtotime($start_date));
    $holidays = [];
    $holiday_query = $conn->prepare("SELECT holiday_date FROM holidays WHERE YEAR(holiday_date) = ?");
    $holiday_query->bind_param("i", $leave_year);
    $holiday_query->execute();
    $holiday_result = $holiday_query->get_result();
    while ($row = $holiday_result->fetch_assoc()) {
        $holidays[] = $row['holiday_date'];
    }

    $valid_days = 0;
    $current_date = new DateTime($start_date);
    while ($valid_days < $num_days) {
        $day_of_week = $current_date->format('w'); // 5 = Friday
        $formatted_date = $current_date->format('Y-m-d');
        if ($day_of_week != 5 && !in_array($formatted_date, $holidays)) {
            $valid_days++;
        }
        $current_date->modify('+1 day');
    }
    $current_date->modify('-1 day'); // Adjust for exact end date
    $end_date = $current_date->format('Y-m-d');

    // Insert leave request
    $insert_leave = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, end_date, num_days, remarks, status) 
                                    VALUES (?, 7, ?, ?, ?, ?, 'Pending')");
    $insert_leave->bind_param("sssis", $emp_no, $start_date, $end_date, $num_days, $remarks);

    if ($insert_leave->execute()) {
        header("Location: no_pay_leave.php?success=No Pay Leave application submitted successfully. Your leave will end on " . date('d-M-Y', strtotime($end_date)) . ".");
        exit;
    } else {
        header("Location: no_pay_leave.php?error=Error submitting leave application.");
        exit;
    }
}
?>
