<?php
include 'db.php';

// Get today's date
$today = date('Y-m-d');

// Step 1: Select all leave_records where end_date has passed and status is 'Approved'
$query = $conn->prepare("
    SELECT DISTINCT emp_no 
    FROM leave_records 
    WHERE end_date < ? AND status = 'Approved'
");
$query->bind_param("s", $today);
$query->execute();
$result = $query->get_result();

$updated = 0;

while ($row = $result->fetch_assoc()) {
    $emp_no = $row['emp_no'];

    // Step 2: Update employees table
    $update_emp = $conn->prepare("
        UPDATE employees 
        SET employment_status = 'Pending Leave Arrival' 
        WHERE emp_no = ?
    ");
    $update_emp->bind_param("s", $emp_no);
    $update_emp->execute();

    // Step 3: Update leave_records table for that emp_no and passed end_dates
    $update_leave = $conn->prepare("
        UPDATE leave_records 
        SET status = 'Pending Leave Arrival'
        WHERE emp_no = ? AND end_date < ? AND status = 'Approved'
    ");
    $update_leave->bind_param("ss", $emp_no, $today);
    $update_leave->execute();

    if ($update_emp->affected_rows > 0 || $update_leave->affected_rows > 0) {
        $updated++;
    }
}

echo "✅ Updated $updated employee(s) and leave record(s) to 'Pending Leave Arrival' as of $today.";
?>
