<?php
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
        WHERE emp_no = ? AND leave_type_id = 5 
        AND (start_date <= ? AND end_date >= ?)
    ");
    $overlap_query->bind_param("sss", $emp_no, $start_date, $start_date);
    $overlap_query->execute();
    $overlap_result = $overlap_query->get_result();

    if ($overlap_result->num_rows > 0) {
        header("Location: paternity_leave.php?error=Your selected dates overlap with an existing Paternity Leave.");
        exit;
    }

    // Fetch available leave balance
    $balance_query = $conn->prepare("SELECT balance FROM leave_balances WHERE emp_no = ? AND leave_type_id = 5");
    $balance_query->bind_param("s", $emp_no);
    $balance_query->execute();
    $balance_result = $balance_query->get_result();
    $balance = $balance_result->fetch_assoc()['balance'] ?? 0;

    if ($num_days > $balance) {
        header("Location: paternity_leave.php?error=You cannot apply for more days than your available balance ($balance days).");
        exit;
    }

    // Deduct leave balance
    $update_balance_query = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = 5");
    $update_balance_query->bind_param("is", $num_days, $emp_no);
    $update_balance_query->execute();

    // Insert leave request
    $insert_leave = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, num_days, remarks, status) 
                                    VALUES (?, 5, ?, ?, ?, 'Pending')");
    $insert_leave->bind_param("ssis", $emp_no, $start_date, $num_days, $remarks);

    if ($insert_leave->execute()) {
        header("Location: paternity_leave.php?success=Paternity Leave application submitted successfully.");
        exit;
    } else {
        header("Location: paternity_leave.php?error=Error submitting leave application.");
        exit;
    }
}
?>
