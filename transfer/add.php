<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $_POST['emp_no'];
    $destination_from = $_POST['destination_from'];
    $destination_to = $_POST['destination_to'];
    $transfer_date = $_POST['transfer_date'] ?? date('Y-m-d'); // Default to today

    if ($destination_from == $destination_to) {
        $error = "Destination From and Destination To cannot be the same.";
    } else {
        $query = "INSERT INTO island_transfers (emp_no, destination_from, destination_to, transfer_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiis", $emp_no, $destination_from, $destination_to, $transfer_date);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=Transfer Added");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Fetch Employees & Projects
$employees = $conn->query("SELECT emp_no, name, designation FROM employees ORDER BY name ASC");
$projects = $conn->query("SELECT id, name FROM projects ORDER BY name ASC");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Island Transfer</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Add Island Transfer</h5>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Employee</label>
                                <select name="emp_no" id="emp_no" required class="form-control select2">
                                    <option value="">Select Employee</option>
                                    <?php while ($row = $employees->fetch_assoc()): ?>
                                        <option value="<?= $row['emp_no'] ?>"><?= $row['emp_no'] ?> - <?= $row['name'] ?> (<?= $row['designation'] ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>From Project</label>
                                <select name="destination_from" required class="form-control">
                                    <option value="">Select Project</option>
                                    <?php while ($row = $projects->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>To Project</label>
                                <select name="destination_to" required class="form-control">
                                    <option value="">Select Project</option>
                                    <?php $projects->data_seek(0); while ($row = $projects->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Transfer Date</label>
                                <input type="date" name="transfer_date" value="<?= date('Y-m-d') ?>" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-success">Add Transfer</button>
                            <a href="index.php" class="btn btn-secondary">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery & Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select an Employee",
                allowClear: true
            });
        });
    </script>
</body>
</html>
