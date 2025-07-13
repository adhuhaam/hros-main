<?php
include '../db.php';

$required_roles = ['Admin', 'HR Manager', 'Information Officer']; // Define required roles for the page
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $bank_name = $conn->real_escape_string($_POST['bank_name']);
    $bank_acc_no = !empty($_POST['bank_acc_no']) ? $conn->real_escape_string($_POST['bank_acc_no']) : null; // Optional
    $currency = $conn->real_escape_string($_POST['currency']); // Required
    $status = $conn->real_escape_string($_POST['status']);
    $entry_date = $conn->real_escape_string($_POST['entry_date']);
    $form_filled = isset($_POST['form_filled']) ? 1 : 0;
    $scheduled_date = !empty($_POST['scheduled_date']) ? $conn->real_escape_string($_POST['scheduled_date']) : null;
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    // Insert into database
    $insertSql = "INSERT INTO bank_account_records (emp_no, bank_name, bank_acc_no, currency, status, entry_date, form_filled, scheduled_date, phone, email)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ssssssssss", $emp_no, $bank_name, $bank_acc_no, $currency, $status, $entry_date, $form_filled, $scheduled_date, $phone, $email);

    if ($stmt->execute()) {
        // Redirect to dashboard on success
        header('Location: index.php');
        exit();
    } else {
        // Display an error message
        $error = "Error: " . $conn->error;
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Bank Record</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
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
                        <a href="../bank/" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </nav>
            </header>

            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body shadow">
                        <h5 class="card-title fw-semibold mb-4">Add Bank Record</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST" class="p-4 rounded">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label text-dark">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label text-dark">Employee Phone:<br><small style="font-size: 10px;" class="text-danger fw-light">Verify if the Phone is the latest and current Phone number </small></label>
                                <input type="text" name="phone" id="phone" required class="form-control" placeholder="Enter Phone Number">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label text-dark">Employee Email:<br><small style="font-size: 10px;" class="text-danger  fw-light">Verify if the email is the latest and current email </small></label>
                                <input type="email" name="email" id="email" required class="form-control" placeholder="Enter Email">
                            </div>
                            <div class="mb-3">
                                <label for="bank_name" class="form-label text-dark">Bank Name:</label>
                                <input type="text" name="bank_name" id="bank_name" required class="form-control" placeholder="Enter Bank Name">
                            </div>
                            <div class="mb-3">
                                <label for="bank_acc_no" class="form-label text-dark">Bank Account Number (Optional):</label>
                                <input type="text" name="bank_acc_no" id="bank_acc_no" class="form-control" placeholder="Enter Bank Account Number">
                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label text-dark">Currency:</label>
                                <select name="currency" id="currency" required class="form-control">
                                    <option value="MVR" selected>MVR</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label text-dark">Status:</label>
                                <select name="status" id="status" required class="form-control">
                                    <option value="" disabled selected>-- Select Status --</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Scheduled">Scheduled</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="entry_date" class="form-label text-dark">Entry Date:</label>
                                <input type="date" name="entry_date" id="entry_date" required class="form-control">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="form_filled" id="form_filled" class="form-check-input">
                                <label for="form_filled" class="form-check-label">Bank Form Filled?</label>
                            </div>
                            <div class="mb-3">
                                <label for="scheduled_date" class="form-label text-dark">Scheduled Date (optional):</label>
                                <input type="date" name="scheduled_date" id="scheduled_date" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Add Record</button>
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
