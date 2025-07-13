<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
    header("Location: index.php?error=No Ticket ID Provided");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM employee_tickets WHERE id = $id");

if ($result->num_rows == 0) {
    header("Location: index.php?error=Ticket Not Found");
    exit();
}

$row = $result->fetch_assoc();

// Fetch all destinations
$destinations = $conn->query("SELECT * FROM employee_tickets_destination ORDER BY destination_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $ticket_type = $conn->real_escape_string($_POST['ticket_type']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
    $return_date = $conn->real_escape_string($_POST['return_date']);
    $ticket_status = $conn->real_escape_string($_POST['ticket_status']);
    $remarks = isset($_POST['remarks']) ? $conn->real_escape_string($_POST['remarks']) : '';

    // Handle "Other" Destination
    if ($_POST['destination'] == 'other') {
        $destination = $conn->real_escape_string($_POST['other_destination']);
    } else {
        $destination_id = intval($_POST['destination']);
        $destination_result = $conn->query("SELECT destination_name FROM employee_tickets_destination WHERE id = $destination_id");
        $destination = $destination_result->fetch_assoc()['destination_name'];
    }

    // File Upload Handling
    $upload_dir = "../assets/tickets/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }

    // Ticket File Upload
    $ticket_file_path = $row['ticket_file']; // Keep existing file if not replaced
    if (!empty($_FILES['ticket_file']['name'])) {
        $ticket_filename = time() . "_ticket_" . basename($_FILES['ticket_file']['name']);
        $ticket_target = $upload_dir . $ticket_filename;
        if (move_uploaded_file($_FILES['ticket_file']['tmp_name'], $ticket_target)) {
            $ticket_file_path = $ticket_target;
        }
    }

    // Invoice File Upload
    $invoice_file_path = $row['invoice_file']; // Keep existing file if not replaced
    if (!empty($_FILES['invoice_file']['name'])) {
        $invoice_filename = time() . "_invoice_" . basename($_FILES['invoice_file']['name']);
        $invoice_target = $upload_dir . $invoice_filename;
        if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $invoice_target)) {
            $invoice_file_path = $invoice_target;
        }
    }

    // Update SQL Query
    $sql = "UPDATE employee_tickets SET 
            emp_no = '$emp_no',
            ticket_type = '$ticket_type',
            destination = '$destination',
            departure_date = '$departure_date',
            return_date = '$return_date',
            ticket_status = '$ticket_status',
            remarks = '$remarks',
            ticket_file = '$ticket_file_path',
            invoice_file = '$invoice_file_path'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?success=Ticket Updated Successfully");
    } else {
        $error = "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Ticket</title>
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
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Edit Ticket</h5>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data" class="p-4 bg-light rounded">
                        <div class="mb-3">
                            <label class="form-label">Employee No</label>
                            <input type="text" name="emp_no" value="<?php echo $row['emp_no']; ?>" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ticket Type</label>
                            <select name="ticket_type" class="form-control">
                                <option value="Annual Leave" <?php echo ($row['ticket_type'] == 'Annual Leave') ? 'selected' : ''; ?>>Annual Leave</option>
                                <option value="Official Travel" <?php echo ($row['ticket_type'] == 'Official Travel') ? 'selected' : ''; ?>>Official Travel</option>
                                <option value="Other" <?php echo ($row['ticket_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Destination</label>
                            <select name="destination" id="destination" class="form-control" onchange="toggleOtherDestination()">
                                <option value="" disabled>-- Select Destination --</option>
                                <?php while ($dest = $destinations->fetch_assoc()): ?>
                                    <option value="<?php echo $dest['id']; ?>" <?php echo ($row['destination'] == $dest['destination_name']) ? 'selected' : ''; ?>>
                                        <?php echo $dest['destination_name']; ?>
                                    </option>
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
                            <input type="date" name="departure_date" value="<?php echo $row['departure_date']; ?>"  class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Return Date</label>
                            <input type="date" name="return_date" value="<?php echo $row['return_date']; ?>"  class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="ticket_status" class="form-control">
                                <option value="Pending" <?php echo ($row['ticket_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Reservation Sent" <?php echo ($row['ticket_status'] == 'Reservation Sent') ? 'selected' : ''; ?>>Reservation Sent</option>
                                <option value="Ticket Received" <?php echo ($row['ticket_status'] == 'Ticket Received') ? 'selected' : ''; ?>>Ticket Received</option>
                                <option value="Departed" <?php echo ($row['ticket_status'] == 'Departed') ? 'selected' : ''; ?>>Departed</option>
                                <option value="Arrived" <?php echo ($row['ticket_status'] == 'Arrived') ? 'selected' : ''; ?>>Arrived</option>
                                <option value="Pending Arrival" <?php echo ($row['ticket_status'] == 'Pending Arrival') ? 'selected' : ''; ?>>Pending Arrival</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control"><?php echo $row['remarks']; ?></textarea>
                        </div>
                        
                         <div class="mb-3">
                            <label class="form-label">Ticket File</label>
                            <input type="file" name="ticket_file" class="form-control">
                            <?php if (!empty($row['ticket_file'])): ?>
                                <p>Current File: <a href="<?php echo $row['ticket_file']; ?>" target="_blank">View Ticket</a></p>
                            <?php endif; ?>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label">Invoice File</label>
                            <input type="file" name="invoice_file" class="form-control">
                            <?php if (!empty($row['invoice_file'])): ?>
                                <p>Current File: <a href="<?php echo $row['invoice_file']; ?>" target="_blank">View Invoice</a></p>
                            <?php endif; ?>
                        </div>
                        
                        
                        <button type="submit" class="btn btn-success">Update Ticket</button>
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
