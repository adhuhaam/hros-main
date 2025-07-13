<?php
include '../db.php';
include '../session.php';

$result = $conn->query("SELECT lt.*, emp.name FROM leave_records lt JOIN employees emp ON lt.emp_no = emp.emp_no WHERE lt.leave_type_id = 1");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Leave Tickets</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
        <?php include '../header.php'; ?>

        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Manage Annual Leave Tickets</h5>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Departure Ticket</th>
                                <th>Arrival Ticket</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['departure_ticket_id']; ?></td>
                                    <td><?php echo $row['arrival_ticket_id']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td>
                                        <a href="edit_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="delete_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
