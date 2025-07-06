<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $leave_type_id = $_POST['leave_type_id'];
    $start_date = $_POST['start_date'];
    $num_days = intval($_POST['num_days']);
    $remarks = $_POST['remarks'] ?? '';
    $status = $_POST['status'];

    // Fetch existing leave record to get old values
    $leave_query = $conn->prepare("SELECT emp_no, num_days AS old_days, leave_type_id AS old_type FROM leave_records WHERE id = ?");
    $leave_query->bind_param("i", $id);
    $leave_query->execute();
    $old_data = $leave_query->get_result()->fetch_assoc();

    if (!$old_data) {
        echo json_encode(["success" => false, "message" => "Original leave record not found."]);
        exit;
    }

    $emp_no = $old_data['emp_no'];
    $old_days = intval($old_data['old_days']);
    $old_type_id = intval($old_data['old_type']);

    // ✅ 1. Revert old balance
    $revert_balance = $conn->prepare("UPDATE leave_balances SET balance = balance + ? WHERE emp_no = ? AND leave_type_id = ?");
    $revert_balance->bind_param("isi", $old_days, $emp_no, $old_type_id);
    $revert_balance->execute();

    // ✅ 2. Deduct new balance
    $deduct_balance = $conn->prepare("UPDATE leave_balances SET balance = balance - ? WHERE emp_no = ? AND leave_type_id = ?");
    $deduct_balance->bind_param("isi", $num_days, $emp_no, $leave_type_id);
    $deduct_balance->execute();

    // ✅ 3. Recalculate end_date (skip Fridays & holidays)
    $current_year = date('Y', strtotime($start_date));
    $holiday_query = $conn->prepare("SELECT holiday_date FROM holidays WHERE YEAR(holiday_date) = ?");
    $holiday_query->bind_param("i", $current_year);
    $holiday_query->execute();
    $holidays = array_column($holiday_query->get_result()->fetch_all(MYSQLI_ASSOC), 'holiday_date');

    $valid_days = 0;
    $current_date = new DateTime($start_date);

    while ($valid_days < $num_days) {
        $day = $current_date->format('w');
        $date_str = $current_date->format('Y-m-d');

        if ($day != 5 && !in_array($date_str, $holidays)) {
            $valid_days++;
        }

        $current_date->modify('+1 day');
    }

    $current_date->modify('-1 day');
    $end_date = $current_date->format('Y-m-d');

    // ✅ 4. Check for overlap
    $overlap_query = $conn->prepare("
        SELECT id FROM leave_records 
        WHERE id != ? AND emp_no = ? 
        AND (start_date BETWEEN ? AND ? OR end_date BETWEEN ? AND ?) 
        AND status = 'Approved'
    ");
    $overlap_query->bind_param("isssss", $id, $emp_no, $start_date, $end_date, $start_date, $end_date);
    $overlap_query->execute();
    if ($overlap_query->get_result()->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Leave period overlaps with another approved leave."]);
        exit;
    }

    // ✅ 5. Final update
    $update = $conn->prepare("
        UPDATE leave_records 
        SET leave_type_id = ?, start_date = ?, end_date = ?, num_days = ?, remarks = ?, status = ? 
        WHERE id = ?
    ");
    $update->bind_param("ississi", $leave_type_id, $start_date, $end_date, $num_days, $remarks, $status, $id);

    if ($update->execute()) {
        header("Location: index.php?message=Leave record updated successfully. New balance applied.");
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Error updating leave."]);
    }
}

?>
