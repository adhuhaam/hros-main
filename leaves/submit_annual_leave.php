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

    // Fetch employee details
    $employee_query = $conn->prepare("SELECT date_of_join FROM employees WHERE emp_no = ?");
    $employee_query->bind_param("s", $emp_no);
    $employee_query->execute();
    $employee = $employee_query->get_result()->fetch_assoc();

    if (!$employee) {
        header("Location: annual_leave.php?error=Invalid Employee ID.");
        exit;
    }

    $join_date = new DateTime($employee['date_of_join']);
    $eligibility_date = (clone $join_date)->modify('+1 year')->format('Y-m-d');

    if ($start_date < $eligibility_date) {
        header("Location: annual_leave.php?error=Annual Leave eligibility starts from " . date('d-M-Y', strtotime($eligibility_date)) . ".");
        exit;
    }

    // Fetch leave balance
    $balance_query = $conn->prepare("SELECT balance FROM leave_balances WHERE emp_no = ? AND leave_type_id = 1");
    $balance_query->bind_param("s", $emp_no);
    $balance_query->execute();
    $balance_result = $balance_query->get_result();
    $balance = $balance_result->fetch_assoc()['balance'] ?? 0;

    if ($num_days > $balance) {
        header("Location: annual_leave.php?error=You cannot apply for more days than your available balance ($balance days).");
        exit;
    }

    // Check for overlapping leave dates
    $overlap_query = $conn->prepare("
        SELECT * FROM leave_records 
        WHERE emp_no = ? AND leave_type_id = 1 
        AND (start_date <= ? AND end_date >= ?)
    ");
    $overlap_query->bind_param("sss", $emp_no, $start_date, $start_date);
    $overlap_query->execute();
    $overlap_result = $overlap_query->get_result();

    if ($overlap_result->num_rows > 0) {
        header("Location: annual_leave.php?error=Your selected dates overlap with an existing Annual Leave.");
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

    // Calculate correct end date (excluding Fridays & holidays)
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
    
    // Adjust end date if it falls on a Friday or a holiday
    while ($current_date->format('w') == 5 || in_array($current_date->format('Y-m-d'), $holidays)) {
        $current_date->modify('+1 day');
    }

    $end_date = $current_date->format('Y-m-d');

    // Deduct leave balance
    $update_balance_query = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = 1");
    $update_balance_query->bind_param("is", $num_days, $emp_no);
    $update_balance_query->execute();

    // Insert leave request (without ticket details)
    $insert_leave = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, end_date, num_days, remarks, status) 
                                    VALUES (?, 1, ?, ?, ?, ?, 'Pending')");
    $insert_leave->bind_param("sssis", $emp_no, $start_date, $end_date, $num_days, $remarks);

    if ($insert_leave->execute()) {
        header("Location: annual_leave.php?success=Annual Leave application submitted successfully. Your leave will end on " . date('d-M-Y', strtotime($end_date)) . ".");
        exit;
    } else {
        header("Location: annual_leave.php?error=Error submitting leave application.");
        exit;
    }
}
?>
