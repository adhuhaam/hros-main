<?php
session_start();
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Addition of Deduction
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_deduction'])) {
    $emp_no = $_POST['emp_no'];
    $deduction_date = $_POST['deduction_date'];
    $other_deduction = $_POST['other_deduction'] ?? 0.00;
    $salary_advance = $_POST['salary_advance'] ?? 0.00;
    $loan = $_POST['loan'] ?? 0.00;
    $pension = $_POST['pension'] ?? 0.00;
    $medical_deduction = $_POST['medical_deduction'] ?? 0.00;
    $no_pay = $_POST['no_pay'] ?? 0.00;
    $late = $_POST['late'] ?? 0.00;

    // Insert into salary_deductions table
    $stmt = $conn->prepare("INSERT INTO salary_deductions (emp_no, date, other_deduction, salary_advance, loan, pension, medical_deduction, no_pay, late) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddddddd", $emp_no, $deduction_date, $other_deduction, $salary_advance, $loan, $pension, $medical_deduction, $no_pay, $late);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Deduction added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add deduction.";
    }
    header("Location: manage_deductions.php");
    exit();
}

// Handle Deletion of Deduction
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM salary_deductions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Deduction deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete deduction.";
    }
    header("Location: manage_deductions.php");
    exit();
}

// Fetch deductions
$deductions = $conn->query("SELECT d.*, e.name FROM salary_deductions d JOIN employees e ON d.emp_no = e.emp_no ORDER BY d.date DESC");
$employees = $conn->query("SELECT emp_no, name FROM employees ORDER BY name ASC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Deductions</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <div class="container-fluid">
      <h2>Manage Salary Deductions</h2>

      <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
      <?php elseif (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <!-- Add Deduction Form -->
      <div class="card p-3 mb-4">
        <h5>Add New Deduction</h5>
        <form action="manage_deductions.php" method="POST">
          <label class="form-label">Select Employee</label>
          <select name="emp_no" id="employeeSelect" class="form-control" required>
            <option value="" disabled selected>-- Search Employee --</option>
            <?php
            $employees = $conn->query("SELECT emp_no, name FROM employees ORDER BY name ASC");
            while ($emp = $employees->fetch_assoc()):
            ?>
              <option value="<?php echo $emp['emp_no']; ?>"><?php echo $emp['name']; ?> (<?php echo $emp['emp_no']; ?>)</option>
            <?php endwhile; ?>
          </select>

          <label class="form-label mt-2">Deduction Date</label>
          <input type="date" name="deduction_date" class="form-control" required>

          <label class="form-label mt-2">Other Deduction</label>
          <input type="number" step="0.01" name="other_deduction" class="form-control">

          <label class="form-label mt-2">Salary Advance</label>
          <input type="number" step="0.01" name="salary_advance" class="form-control">

          <label class="form-label mt-2">Loan</label>
          <input type="number" step="0.01" name="loan" class="form-control">

          <label class="form-label mt-2">Pension</label>
          <input type="number" step="0.01" name="pension" class="form-control">

          <label class="form-label mt-2">Medical Deduction</label>
          <input type="number" step="0.01" name="medical_deduction" class="form-control">

          <label class="form-label mt-2">No Pay</label>
          <input type="number" step="0.01" name="no_pay" class="form-control">

          <label class="form-label mt-2">Late</label>
          <input type="number" step="0.01" name="late" class="form-control">

          <button type="submit" name="add_deduction" class="btn btn-success mt-3">Add Deduction</button>
        </form>
      </div>

      <!-- Deductions Table -->
      <h5>Deduction History</h5>
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Other Deduction</th>
            <th>Salary Advance</th>
            <th>Loan</th>
            <th>Pension</th>
            <th>Medical</th>
            <th>No Pay</th>
            <th>Late</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $deductions->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['name']; ?> (<?php echo $row['emp_no']; ?>)</td>
              <td><?php echo date('d-M-Y', strtotime($row['date'])); ?></td>
              <td><?php echo number_format($row['other_deduction'], 2); ?></td>
              <td><?php echo number_format($row['salary_advance'], 2); ?></td>
              <td><?php echo number_format($row['loan'], 2); ?></td>
              <td><?php echo number_format($row['pension'], 2); ?></td>
              <td><?php echo number_format($row['medical_deduction'], 2); ?></td>
              <td><?php echo number_format($row['no_pay'], 2); ?></td>
              <td><?php echo number_format($row['late'], 2); ?></td>
              <td>
                <a href="manage_deductions.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Initialize Select2 -->
<script>
  $(document).ready(function() {
    $('#employeeSelect').select2({
      width: '100%',
      placeholder: "Search Employee",
      allowClear: true
    });
  });
</script>

<script src="../assets/js/app.min.js"></script>
</body>
</html>