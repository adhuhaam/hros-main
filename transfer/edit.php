<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php?error=Invalid Transfer ID");
    exit();
}

// Fetch existing transfer data
$query = "SELECT * FROM island_transfers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$transfer = $stmt->get_result()->fetch_assoc();
if (!$transfer) {
    header("Location: index.php?error=Transfer Not Found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $_POST['emp_no'];
    $destination_from = $_POST['destination_from'];
    $destination_to = $_POST['destination_to'];
    $transfer_date = $_POST['transfer_date'];

    if ($destination_from == $destination_to) {
        $error = "Destination From and Destination To cannot be the same.";
    } else {
        $updateQuery = "UPDATE island_transfers SET emp_no = ?, destination_from = ?, destination_to = ?, transfer_date = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("iiisi", $emp_no, $destination_from, $destination_to, $transfer_date, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=Transfer Updated");
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
    <title>Edit Island Transfer</title>
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
                        <h5 class="card-title">Edit Island Transfer</h5>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Employee</label>
                                <select name="emp_no" id="emp_no" required class="form-control select2">
                                    <option value="">Select Employee</option>
                                    <?php while ($row = $employees->fetch_assoc()): ?>
                                        <option value="<?= $row['emp_no'] ?>" <?= ($row['emp_no'] == $transfer['emp_no']) ? 'selected' : '' ?>>
                                            <?= $row['emp_no'] ?> - <?= $row['name'] ?> (<?= $row['designation'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>From Project</label>
                                <select name="destination_from" id="destination_from" required class="form-control select2">
                                    <option value="">Select Project</option>
                                    <?php while ($row = $projects->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $transfer['destination_from']) ? 'selected' : '' ?>>
                                            <?= $row['name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>To Project</label>
                                <select name="destination_to" id="destination_to" required class="form-control select2">
                                    <option value="">Select Project</option>
                                    <?php $projects->data_seek(0); while ($row = $projects->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $transfer['destination_to']) ? 'selected' : '' ?>>
                                            <?= $row['name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Transfer Date</label>
                                <input type="date" name="transfer_date" value="<?= date('Y-m-d', strtotime($transfer['transfer_date'])) ?>" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-warning">Update Transfer</button>
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
                placeholder: "Select an option",
                allowClear: true
            });
        });
    </script>
</body>
</html>
