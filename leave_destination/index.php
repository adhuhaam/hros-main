<?php
include '../db.php'; 
include '../session.php'; 

// Fetch all records
$sql = "SELECT * FROM employee_tickets_destination ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ticket Destinations</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
    <div class="page-wrapper" id="main-wrapper">
        <?php include '../sidebar.php'; ?> <!-- Include sidebar -->
        
        <div class="body-wrapper">
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Manage Ticket Destinations</h5>
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Destination</button>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Destination Name</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= $row['id']; ?></td>
                                        <td><?= htmlspecialchars($row['destination_name']); ?></td>
                                        <td><?= date('d-M-Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm editBtn" data-id="<?= $row['id']; ?>" data-name="<?= htmlspecialchars($row['destination_name']); ?>">Edit</button>
                                            <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['id']; ?>">Delete</button>
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

    <!-- Add Destination Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="process.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label>Destination Name:</label>
                        <input type="text" name="destination_name" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-success">Add</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Destination Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="process.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <label>Destination Name:</label>
                        <input type="text" name="destination_name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="process.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete_id">
                        <p>Are you sure you want to delete this destination?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".editBtn").click(function() {
                $("#edit_id").val($(this).data("id"));
                $("#edit_name").val($(this).data("name"));
                $("#editModal").modal("show");
            });

            $(".deleteBtn").click(function() {
                $("#delete_id").val($(this).data("id"));
                $("#deleteModal").modal("show");
            });
        });
    </script>
</body>
</html>
