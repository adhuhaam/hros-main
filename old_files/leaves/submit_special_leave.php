<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];
    $annual_days = intval($_POST['annual_days']);
    $emergency_days = intval($_POST['emergency_days']);
    $remarks = $_POST['remarks'];
    $total_days = $annual_days + $emergency_days;

    // Prevent overlapping leave dates
    $overlap_query = $conn->prepare("
        SELECT * FROM leave_records 
        WHERE emp_no = ? AND leave_type_id IN (1, 3, 6)
        AND (start_date <= ? AND end_date >= ?)
    ");
    $overlap_query->bind_param("sss", $emp_no, $start_date, $start_date);
    $overlap_query->execute();
    $overlap_result = $overlap_query->get_result();

    if ($overlap_result->num_rows > 0) {
        header("Location: special_leave.php?error=Your selected dates overlap with an existing leave.");
        exit;
    }

    // Fetch holidays
    $leave_year = date('Y', strtotime($start_date));
    $holidays = [];
    $holiday_query = $conn->prepare("SELECT holiday_date FROM holidays WHERE YEAR(holiday_date) = ?");
    $holiday_query->bind_param("i", $leave_year);
    $holiday_query->execute();
    $holiday_result = $holiday_query->get_result();
    while ($row = $holiday_result->fetch_assoc()) {
        $holidays[] = $row['holiday_date'];
    }

    // ✅ Fix: Calculate correct leave end date (excluding Fridays & holidays)
    $valid_days = 0;
    $current_date = new DateTime($start_date);

    while ($valid_days < $total_days) {
        $day_of_week = $current_date->format('w'); // 5 = Friday
        $formatted_date = $current_date->format('Y-m-d');

        // ✅ Only count working days (not Fridays or holidays)
        if ($day_of_week != 5 && !in_array($formatted_date, $holidays)) {
            $valid_days++;
        }

        // Move to the next day
        $current_date->modify('+1 day');
    }

    // ✅ Fix: Correctly set the leave end date
    $current_date->modify('-1 day'); // Step back to the actual last leave day
    $end_date = $current_date->format('Y-m-d');

    // Deduct leave balance
    $update_annual = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = 1");
    $update_annual->bind_param("is", $annual_days, $emp_no);
    $update_annual->execute();

    $update_emergency = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = 3");
    $update_emergency->bind_param("is", $emergency_days, $emp_no);
    $update_emergency->execute();

    // Insert leave request
    $insert_leave = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, end_date, num_days, remarks, status) 
                                    VALUES (?, 6, ?, ?, ?, ?, 'Pending')");
    $insert_leave->bind_param("sssis", $emp_no, $start_date, $end_date, $total_days, $remarks);
    $insert_leave->execute();

    header("Location: special_leave.php?success=Special Leave application submitted successfully. Your leave will end on " . date('d-M-Y', strtotime($end_date)) . ".");
    exit;
}
?>
