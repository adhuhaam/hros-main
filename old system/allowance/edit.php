<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$allowance = ['emp_no' => '', 'allowance_type' => '', 'allowance_name' => '', 'amount' => ''];

if ($id) {
    $query = "SELECT * FROM employee_allowances WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $allowance = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $_POST['emp_no'];
    $allowance_type = $_POST['allowance_type'];
    $allowance_name = $_POST['allowance_name'];
    $amount = floatval($_POST['amount']);

    if ($id) {
        $query = "UPDATE employee_allowances SET emp_no=?, allowance_type=?, allowance_name=?, amount=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdi", $emp_no, $allowance_type, $allowance_name, $amount, $id);
    } else {
        $query = "INSERT INTO employee_allowances (emp_no, allowance_type, allowance_name, amount) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssd", $emp_no, $allowance_type, $allowance_name, $amount);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo $id ? 'Edit Allowance' : 'Add Allowance'; ?></title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="navbar-collapse justify-content-end">
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </div>
            </nav>
        </header>
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold"><?php echo $id ? 'Edit Allowance' : 'Add Allowance'; ?></h5>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" class="shadow p-4 bg-light rounded">
                        <div class="mb-3">
                            <label for="emp_no" class="form-label">Employee No:</label>
                            <input type="text" name="emp_no" id="emp_no" required class="form-control" value="<?php echo $allowance['emp_no']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="allowance_type" class="form-label">Allowance Type:</label>
                            <select name="allowance_type" id="allowance_type" required class="form-control">
                                <option value="Fixed" <?php echo $allowance['allowance_type'] == 'Fixed' ? 'selected' : ''; ?>>Fixed</option>
                                <option value="Variable" <?php echo $allowance['allowance_type'] == 'Variable' ? 'selected' : ''; ?>>Variable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="allowance_name" class="form-label">Allowance Name:</label>
                            <input type="text" name="allowance_name" id="allowance_name" required class="form-control" value="<?php echo $allowance['allowance_name']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount:</label>
                            <input type="number" step="0.01" name="amount" id="amount" required class="form-control" value="<?php echo $allowance['amount']; ?>">
                        </div>
                        <button type="submit" class="btn btn-success w-100"><?php echo $id ? 'Update' : 'Add'; ?> Allowance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
