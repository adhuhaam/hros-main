<?php
include '../db.php';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=Island_Transfers.csv");

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Employee No', 'Employee Name', 'Designation', 'From', 'To', 'Transfer Date']);

$query = "SELECT it.id, it.emp_no, e.name, e.designation, p1.name AS destination_from, p2.name AS destination_to, it.transfer_date
          FROM island_transfers it
          JOIN projects p1 ON it.destination_from = p1.id
          JOIN projects p2 ON it.destination_to = p2.id
          JOIN employees e ON it.emp_no = e.emp_no
          ORDER BY it.transfer_date DESC";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['id'], $row['emp_no'], $row['name'], $row['designation'], $row['destination_from'], $row['destination_to'], date("d-M-Y", strtotime($row['transfer_date']))]);
}

fclose($output);
exit();
?>
