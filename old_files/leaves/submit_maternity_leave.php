<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['employee_id'];
    $start_date = $_POST['start_date'];
    $num_days = intval($_POST['num_days']);
    $remarks = $_POST['remarks'];

    if (isset($_POST['check_eligibility'])) {
        $employee_query = $conn->prepare("SELECT gender FROM employees WHERE emp_no = ?");
        $employee_query->bind_param("s", $emp_no);
        $employee_query->execute();
        $employee = $employee_query->get_result()->fetch_assoc();

        if (!$employee || $employee['gender'] !== 'Female') {
            echo json_encode(["success" => false, "message" => "Maternity Leave is only available for female employees."]);
            exit;
        }

        $balance_query = $conn->prepare("SELECT balance FROM leave_balances WHERE emp_no = ? AND leave_type_id = 4");
        $balance_query->bind_param("s", $emp_no);
        $balance_query->execute();
        $balance_result = $balance_query->get_result();
        $balance = $balance_result->fetch_assoc()['balance'] ?? 60;

        if ($balance < 1) {
            echo json_encode(["success" => false, "message" => "You have zero balance left for Maternity Leave.", "balance" => $balance]);
            exit;
        }

        echo json_encode(["success" => true, "message" => "Eligible for Maternity Leave.", "balance" => $balance]);
        exit;
    }

    // Prevent overlapping leave dates
    $overlap_query = $conn->prepare("
        SELECT * FROM leave_records 
        WHERE emp_no = ? AND leave_type_id = 4 
        AND (start_date <= ? AND end_date >= ?)
    ");
    $overlap_query->bind_param("sss", $emp_no, $start_date, $start_date);
    $overlap_query->execute();
    $overlap_result = $overlap_query->get_result();

    if ($overlap_result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Your selected dates overlap with an existing Maternity Leave."]);
        exit;
    }

    // Deduct leave balance
    $update_balance_query = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = 4");
    $update_balance_query->bind_param("is", $num_days, $emp_no);
    $update_balance_query->execute();

    // Insert leave request
    $insert_leave = $conn->prepare("INSERT INTO leave_records (emp_no, leave_type_id, start_date, num_days, remarks, status) 
                                    VALUES (?, 4, ?, ?, ?, 'Pending')");
    $insert_leave->bind_param("ssis", $emp_no, $start_date, $num_days, $remarks);

    if ($insert_leave->execute()) {
        echo json_encode(["success" => true, "message" => "Maternity Leave application submitted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error submitting leave application."]);
    }
}
?>
