<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_id = $_POST['leave_id'] ?? '';
    $emp_no = $_POST['emp_no'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? ''; // Departure or Arrival
    $destination = ($_POST['destination'] == 'other') ? ($_POST['other_destination'] ?? '') : ($_POST['destination'] ?? '');
    $departure_date = $_POST['departure_date'] ?? ''; // For Departure Ticket
    $arrival_date = $_POST['arrival_date'] ?? '';     // For Arrival Ticket
    $remarks = $_POST['remarks'] ?? '';

    // Validate required fields
    if (empty($leave_id) || empty($emp_no) || empty($ticket_type) || empty($destination)) {
        header("Location: view_leave.php?id=$leave_id&error=All required fields must be filled.");
        exit();
    }

    // Determine date based on ticket type
    $travel_date = ($ticket_type === 'Departure') ? $departure_date : $arrival_date;

    if (empty($travel_date)) {
        header("Location: view_leave.php?id=$leave_id&error=Please provide the date for the $ticket_type ticket.");
        exit();
    }

    // Insert ticket into employee_tickets table
    $insert_ticket_query = $conn->prepare("
        INSERT INTO employee_tickets (emp_no, ticket_type, destination, departure_date, ticket_status, remarks) 
        VALUES (?, ?, ?, ?, 'Pending', ?)
    ");
    $insert_ticket_query->bind_param("sssss", $emp_no, $ticket_type, $destination, $travel_date, $remarks);

    if ($insert_ticket_query->execute()) {
        $ticket_id = $conn->insert_id;

        // Assign ticket to the leave record
        if ($ticket_type === 'Departure') {
            $update_leave_query = $conn->prepare("UPDATE leave_records SET departure_ticket_id = ? WHERE id = ?");
        } else {
            $update_leave_query = $conn->prepare("UPDATE leave_records SET arrival_ticket_id = ? WHERE id = ?");
        }

        $update_leave_query->bind_param("ii", $ticket_id, $leave_id);

        if ($update_leave_query->execute()) {
            header("Location: view_leave.php?id=$leave_id&success=$ticket_type ticket successfully created and assigned!");
            exit();
        } else {
            header("Location: view_leave.php?id=$leave_id&error=Failed to assign the ticket to the leave record.");
            exit();
        }
    } else {
        header("Location: view_leave.php?id=$leave_id&error=Error creating $ticket_type ticket. Please try again.");
        exit();
    }
}
?>
