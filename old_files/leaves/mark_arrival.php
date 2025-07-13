<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid Request");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $actual_arrival_date = $_POST['actual_arrival_date'];
    $status = $_POST['status'];

    // Fetch current leave record details
    $leave_query = $conn->prepare("SELECT * FROM leave_records WHERE id = ?");
    $leave_query->bind_param("i", $id);
    $leave_query->execute();
    $leave = $leave_query->get_result()->fetch_assoc();

    if (!$leave) {
        die("Leave record not found.");
    }

    $emp_no = $leave['emp_no'];
    $leave_type_id = $leave['leave_type_id'];
    $original_end_date = $leave['end_date'];

    // Calculate unused days
    $actual_arrival = new DateTime($actual_arrival_date);
    $original_end = new DateTime($original_end_date);

    if ($actual_arrival < $original_end) {
        $interval = $actual_arrival->diff($original_end);
        $unused_days = $interval->days;

        // Re-add unused days to leave balance
        $update_balance = $conn->prepare("UPDATE leave_balances SET balance = balance + ? WHERE emp_no = ? AND leave_type_id = ?");
        $update_balance->bind_param("isi", $unused_days, $emp_no, $leave_type_id);
        $update_balance->execute();
    }

    // Update arrival date and status
    $update = $conn->prepare("UPDATE leave_records SET actual_arrival_date = ?, status = ? WHERE id = ?");
    $update->bind_param("ssi", $actual_arrival_date, $status, $id);

    if ($update->execute()) {
        header("Location: index.php?success=Arrival info and leave balance updated.");
        exit;
    } else {
        $error = "Failed to update arrival.";
    }
} else {
    // Display form
    $id = $_GET['id'];
    $query = $conn->prepare("SELECT * FROM leave_records WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $leave = $query->get_result()->fetch_assoc();

    if (!$leave) {
        die("Leave record not found");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Arrival</title>
    <link href="../assets/css/styles.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h4 class="mb-4">Mark Actual Arrival</h4>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST" action="mark_arrival.php">
        <input type="hidden" name="id" value="<?= $leave['id']; ?>">

        <div class="mb-3">
            <label for="actual_arrival_date" class="form-label">Actual Arrival Date:</label>
            <input type="date" name="actual_arrival_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Update Status:</label>
            <select name="status" class="form-select" required>
                <option value="Arrived">Arrived</option>
                <option value="Pending Leave Arrival">Pending Leave Arrival</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
