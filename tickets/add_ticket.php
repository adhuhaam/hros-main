<?php
include '../db.php';
include '../session.php';
// Fetch all destinations
$destinations = $conn->query("SELECT * FROM employee_tickets_destination ORDER BY destination_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $ticket_type = $conn->real_escape_string($_POST['ticket_type']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
    $return_date = $conn->real_escape_string($_POST['return_date']);
    $ticket_status = $conn->real_escape_string($_POST['ticket_status']);
    $remarks = isset($_POST['remarks']) ? $conn->real_escape_string($_POST['remarks']) : '';

    // Check if "Other" was selected
    if ($_POST['destination'] == 'other') {
        $destination = $conn->real_escape_string($_POST['other_destination']);
    } else {
        // Fetch destination from the database
        $destination_id = intval($_POST['destination']);
        $destination_result = $conn->query("SELECT destination_name FROM employee_tickets_destination WHERE id = $destination_id");
        $destination = $destination_result->fetch_assoc()['destination_name'];
    }

    // Insert into employee_tickets table
    $sql = "INSERT INTO employee_tickets (emp_no, ticket_type, destination, departure_date, return_date, ticket_status, remarks)
            VALUES ('$emp_no', '$ticket_type', '$destination', '$departure_date', '$return_date', '$ticket_status', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?success=Ticket Added Successfully");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Ticket</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <script>
        function toggleOtherDestination() {
            var destinationSelect = document.getElementById("destination");
            var otherDestinationInput = document.getElementById("other_destination_div");
            if (destinationSelect.value === "other") {
                otherDestinationInput.style.display = "block";
            } else {
                otherDestinationInput.style.display = "none";
            }
        }
    </script>
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Add Ticket</h5>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" class="p-4 bg-light rounded">
                        <div class="mb-3">
                            <label class="form-label">Employee No</label>
                            <input type="text" name="emp_no" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ticket Type</label>
                            <select name="ticket_type" class="form-control">
                                <option value="Annual Leave">Annual Leave</option>
                                <option value="Official Travel">Official Travel</option>
                                <option value="Resignation">Resignation</option>
                                <option value="Termination">Termination</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Destination</label>
                            <select name="destination" id="destination" class="form-control" onchange="toggleOtherDestination()">
                                <option value="" disabled selected>-- Select Destination --</option>
                                <?php while ($row = $destinations->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['destination_name']; ?></option>
                                <?php endwhile; ?>
                                <option value="other">Other (Enter Manually)</option>
                            </select>
                        </div>
                        <!-- Other Destination Input -->
                        <div class="mb-3" id="other_destination_div" style="display: none;">
                            <label class="form-label">Enter Destination</label>
                            <input type="text" name="other_destination" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departure Date</label>
                            <input type="date" name="departure_date" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Return Date</label>
                            <input type="date" name="return_date" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ticket Status</label>
                            <select name="ticket_status" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Reservation Sent">Reservation Sent</option>
                                <option value="Ticket Received">Ticket Received</option>
                                <option value="Departed">Departed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Request</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
