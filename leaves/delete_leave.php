<?php
include '../db.php';
include '../session.php';

if (isset($_GET['id'])) {
    $leave_id = $_GET['id'];

    // Fetch the leave record details before deleting
    $leave_record_query = $conn->prepare("SELECT emp_no, leave_type_id, num_days FROM leave_records WHERE id = ?");
    $leave_record_query->bind_param("i", $leave_id);
    $leave_record_query->execute();
    $leave_record = $leave_record_query->get_result()->fetch_assoc();

    if ($leave_record) {
        $emp_no = $leave_record['emp_no'];
        $leave_type_id = $leave_record['leave_type_id'];
        $num_days = $leave_record['num_days'];

        // Start transaction to ensure consistency
        $conn->begin_transaction();

        // Restore the leave balance
        $restore_balance_query = $conn->prepare("UPDATE leave_balances SET balance = balance + ? WHERE emp_no = ? AND leave_type_id = ?");
        $restore_balance_query->bind_param("isi", $num_days, $emp_no, $leave_type_id);

        if ($restore_balance_query->execute()) {
            // Delete the leave record
            $delete_query = $conn->prepare("DELETE FROM leave_records WHERE id = ?");
            $delete_query->bind_param("i", $leave_id);

            if ($delete_query->execute()) {
                // Commit transaction
                $conn->commit();

                // Redirect back with a success message
                header('Location: index.php?success=Leave record deleted and balance restored successfully.');
                exit;
            } else {
                // Rollback in case of failure
                $conn->rollback();
                header('Location: index.php?error=Failed to delete the leave record.');
                exit;
            }
        } else {
            // Rollback in case of failure
            $conn->rollback();
            header('Location: index.php?error=Failed to restore leave balance.');
            exit;
        }
    } else {
        // Handle invalid leave ID
        header('Location: index.php?error=Invalid leave record.');
        exit;
    }
} else {
    // Handle missing leave ID
    header('Location: index.php?error=No leave ID provided.');
    exit;
}
?>
