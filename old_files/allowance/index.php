<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

// Fetch allowances
$sql = "SELECT * FROM employee_allowances ORDER BY updated_at DESC";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Employee Allowances</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="navbar-collapse justify-content-end">
                    <a href="edit.php" class="btn btn-primary">Add Allowance</a>
                </div>
            </nav>
        </header>
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold">Employee Allowances</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee No</th>
                                <th>Allowance Type</th>
                                <th>Allowance Name</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['emp_no']; ?></td>
                                    <td><?php echo $row['allowance_type']; ?></td>
                                    <td><?php echo $row['allowance_name']; ?></td>
                                    <td><?php echo number_format($row['amount'], 2); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
