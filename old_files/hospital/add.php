<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php'; // Adjusted path for database connection

session_start(); // Ensure the session is started
if (!isset($_SESSION['username'])) {
    // Redirect to login page
    header('Location: ../login.php');
    exit();
}

// Fetch all projects from the projects table
$projects = [];
$sql = "SELECT id, name FROM projects";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $project_id = $conn->real_escape_string($_POST['project_id']);
    $invoice_no = $conn->real_escape_string($_POST['invoice_no']);
    $medication_detail = $conn->real_escape_string($_POST['medication_detail']);
    $medication_amount = $conn->real_escape_string($_POST['medication_amount']);
    $consultation_date = $conn->real_escape_string($_POST['consultation_date']);
    $entered_by = $_SESSION['username']; // Get logged-in user

    $sql = "INSERT INTO opd_records (emp_no, project_name, invoice_no, medication_detail, medication_amount, consultation_date, entered_by) 
            SELECT '$emp_no', name, '$invoice_no', '$medication_detail', '$medication_amount', '$consultation_date', '$entered_by'
            FROM projects WHERE id = '$project_id'";

    if ($conn->query($sql) === TRUE) {
        $success = "Record added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add OPD Record</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>

        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg">
                    <a href="index.php" class="btn btn-primary">View Records</a>
                </nav>
            </header>
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Add OPD Record</h5>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php elseif (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" class="shadow p-4 bg-light rounded">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control"
                                    placeholder="Enter Employee No">
                                <ul id="employee-list" class="dropdown-menu" style="display:none;"></ul>
                            </div>
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project Name:</label>
                                <select name="project_id" id="project_id" class="form-control" required>
                                    <option value="" disabled selected>-- Select Project --</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>">
                                            <?php echo $project['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="invoice_no" class="form-label">Invoice No:</label>
                                <input type="text" name="invoice_no" id="invoice_no" required class="form-control"
                                    placeholder="Enter Invoice No">
                            </div>
                            <div class="mb-3">
                                <label for="medication_detail" class="form-label">Medication Detail:</label>
                                <textarea name="medication_detail" id="medication_detail" required class="form-control"
                                    placeholder="Enter Medication Details"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="medication_amount" class="form-label">Medication Amount:</label>
                                <input type="number" step="0.01" name="medication_amount" id="medication_amount" required class="form-control"
                                    placeholder="Enter Medication Amount">
                            </div>
                            <div class="mb-3">
                                <label for="consultation_date" class="form-label">Consultation Date:</label>
                                <input type="date" name="consultation_date" id="consultation_date" required class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Add Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
