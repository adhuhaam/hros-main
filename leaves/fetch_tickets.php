<?php
include '../db.php';

// Fetch available tickets that are NOT assigned
$query = $conn->query("
    SELECT et.id, et.emp_no, et.destination, et.departure_date 
    FROM employee_tickets et 
    LEFT JOIN leave_records lr ON et.id = lr.ticket_id 
    WHERE et.ticket_status IN ('Pending', 'Reservation Sent') 
    AND lr.ticket_id IS NULL
");

$tickets = [];
while ($row = $query->fetch_assoc()) {
    $tickets[] = $row;
}

echo json_encode(['tickets' => $tickets]);
?>
