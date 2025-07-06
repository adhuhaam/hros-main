<?php
include '../db.php';
include '../session.php';

// Get record ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch record data
$query = "SELECT * FROM bank_account_records WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure all required fields are provided
    $errors = [];

    $emp_no = isset($_POST['emp_no']) ? $conn->real_escape_string($_POST['emp_no']) : null;
    $bank_name = isset($_POST['bank_name']) ? $conn->real_escape_string($_POST['bank_name']) : null;
    $bank_acc_no = isset($_POST['bank_acc_no']) ? $conn->real_escape_string($_POST['bank_acc_no']) : null;
    $currency = isset($_POST['currency']) ? $conn->real_escape_string($_POST['currency']) : 'MVR';
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : null;
    $entry_date = isset($_POST['entry_date']) ? $conn->real_escape_string($_POST['entry_date']) : null;
    $form_filled = isset($_POST['form_filled']) ? 1 : 0;
    $scheduled_date = isset($_POST['scheduled_date']) && !empty($_POST['scheduled_date']) ? $conn->real_escape_string($_POST['scheduled_date']) : null;
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : null;
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;

    // Validate required fields
    if (!$emp_no || !$bank_name || !$status || !$entry_date || !$phone || !$email) {
        $errors[] = "All fields marked as required must be filled.";
    }

    // Check if the provided emp_no exists in the employees table
    $empCheckQuery = "SELECT emp_no FROM employees WHERE emp_no = ?";
    $empStmt = $conn->prepare($empCheckQuery);
    $empStmt->bind_param("s", $emp_no);
    $empStmt->execute();
    $empCheckResult = $empStmt->get_result();

    if ($empCheckResult->num_rows === 0) {
        $errors[] = "The provided Employee Number does not exist in the employees table.";
    }

    if (empty($errors)) {
        try {
            // Update record in database
            $updateSql = "UPDATE bank_account_records SET emp_no = ?, bank_name = ?, bank_acc_no = ?, currency = ?, status = ?, entry_date = ?, form_filled = ?, scheduled_date = ?, phone = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ssssssssssi", $emp_no, $bank_name, $bank_acc_no, $currency, $status, $entry_date, $form_filled, $scheduled_date, $phone, $email, $id);

            if ($stmt->execute()) {
                // Redirect to dashboard on success
                header('Location: index.php');
                exit();
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
        } catch (Exception $e) {
            $errors[] = "An unexpected error occurred: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Bank Record</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
    <!-- Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end">
                        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </nav>
            </header>

            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body shadow">
                        <h5 class="card-title fw-semibold mb-4">Edit Bank Record</h5>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?= implode('<br>', $errors) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" class="p-4 rounded">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control" value="<?= htmlspecialchars($record['emp_no']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name:</label>
                                <input type="text" name="bank_name" id="bank_name" required class="form-control" value="<?= htmlspecialchars($record['bank_name']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="bank_acc_no" class="form-label">Bank Account Number (Optional):</label>
                                <input type="text" name="bank_acc_no" id="bank_acc_no" class="form-control" value="<?= htmlspecialchars($record['bank_acc_no'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency:</label>
                                <select name="currency" id="currency" class="form-control">
                                    <option value="MVR" <?= $record['currency'] == 'MVR' ? 'selected' : '' ?>>MVR</option>
                                    <option value="USD" <?= $record['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status:</label>
                                <select name="status" id="status" required class="form-control">
                                    <option value="Pending" <?= $record['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Scheduled" <?= $record['status'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                    <option value="Completed" <?= $record['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="entry_date" class="form-label">Entry Date:</label>
                                <input type="date" name="entry_date" id="entry_date" required class="form-control" value="<?= $record['entry_date'] ?>">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="form_filled" id="form_filled" class="form-check-input" <?= $record['form_filled'] ? 'checked' : '' ?>>
                                <label for="form_filled" class="form-check-label">Form Filled</label>
                            </div>
                            <div class="mb-3">
                                <label for="scheduled_date" class="form-label">Scheduled Date (optional):</label>
                                <input type="date" name="scheduled_date" id="scheduled_date" class="form-control" value="<?= $record['scheduled_date'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone:</label>
                                <input type="text" name="phone" id="phone" required class="form-control" value="<?= htmlspecialchars($record['phone']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" required class="form-control" value="<?= htmlspecialchars($record['email']) ?>">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Update Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>

</html>
