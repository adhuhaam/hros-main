<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];
    $num_days = intval($_POST['num_days']);
    $remarks = $_POST['remarks'];

    // Leave Type ID for Umrah Leave (update if needed)
    $leave_type_id = 8;

    // Check for overlapping leave
    $check_overlap = $conn->prepare("SELECT id FROM leave_records WHERE emp_no = ? AND start_date <= ? AND end_date >= ?");
    $check_overlap->bind_param("sss", $emp_no, $start_date, $start_date);
    $check_overlap->execute();
    if ($check_overlap->get_result()->num_rows > 0) {
        header("Location: umrah_leave.php?error=Leave dates overlap with an existing record.");
        exit;
    }

    // Calculate end date excluding Fridays & holidays
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
    $current_date->modify('-1 day');
    $end_date = $current_date->format('Y-m-d');

    // Insert record
    $insert = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, end_date, num_days, remarks, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $insert->bind_param("sissis", $emp_no, $leave_type_id, $start_date, $end_date, $num_days, $remarks);
    if ($insert->execute()) {
        header("Location: umrah_leave.php?success=Umrah Leave submitted successfully. Ends on " . date('d-M-Y', strtotime($end_date)));
    } else {
        header("Location: umrah_leave.php?error=Error while submitting leave.");
    }
}
?>
