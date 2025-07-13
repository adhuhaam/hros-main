<?php
include '../db.php';
include '../session.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Salary Setup Addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_no = $_POST['emp_no'];
    $date = $_POST['date']; // Format: YYYY-MM-01
    $basic_salary = $_POST['basic_salary'];
    $service_allowance = $_POST['service_allowance'];
    $island_allowance = $_POST['island_allowance'];
    $attendance_allowance = $_POST['attendance_allowance'];
    $salary_arrear_other = $_POST['salary_arrear_other'];
    $safety_allowance = $_POST['safety_allowance'];
    $pump_brick_batching = $_POST['pump_brick_batching'];
    $food_and_tea = $_POST['food_and_tea'];
    $long_term_service_allowance = $_POST['long_term_service_allowance'];
    $living_allowance = $_POST['living_allowance'];
    $ot = $_POST['ot'];
    $ot_arrears = $_POST['ot_arrears'];
    $phone_allowance = $_POST['phone_allowance'];
    $petrol_allowance = $_POST['petrol_allowance'];
    $pension = $_POST['pension'];

    // Insert new salary record
    $insertQuery = "INSERT INTO salary_income (emp_no, date, basic_salary, service_allowance, island_allowance, attendance_allowance, 
                    salary_arrear_other, safety_allowance, pump_brick_batching, food_and_tea, long_term_service_allowance, living_allowance, 
                    ot, ot_arrears, phone_allowance, petrol_allowance, pension) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssdddddddddddddd", $emp_no, $date, $basic_salary, $service_allowance, $island_allowance, 
                      $attendance_allowance, $salary_arrear_other, $safety_allowance, $pump_brick_batching, 
                      $food_and_tea, $long_term_service_allowance, $living_allowance, $ot, $ot_arrears, 
                      $phone_allowance, $petrol_allowance, $pension);

    if ($stmt->execute()) {
        $success = "Salary setup successfully added for " . date('F Y', strtotime($date));
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// Fetch Employees for Dropdown
$employees = $conn->query("SELECT emp_no, name FROM employees WHERE employment_status = 'Active' ORDER BY name ASC");

// Fetch Salary Setup Data with Pagination & Search
$search = $_GET['search'] ?? '';
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$query = "SELECT s.*, e.name 
          FROM salary_income s 
          JOIN employees e ON s.emp_no = e.emp_no
          WHERE e.name LIKE ? OR s.emp_no LIKE ?
          ORDER BY s.date DESC 
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$searchLike = "%$search%";
$stmt->bind_param("ssii", $searchLike, $searchLike, $limit, $offset);
$stmt->execute();
$salaryData = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Setup</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        
<div class="container">
    <h2>Salary Setup</h2>

    <!-- Search Form -->
    <form method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search by Employee Name or ID" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Success/Error Messages -->
    <?php if (isset($success)) echo "<p class='alert alert-success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>

    <!-- Salary Setup Form -->
<form method="POST">
    <div class="row">
        <div class="col-md-4">
            <label>Employee</label>
            <select name="emp_no" class="form-control" required>
                <option value="">Select Employee</option>
                <?php while ($row = $employees->fetch_assoc()): ?>
                    <option value="<?php echo $row['emp_no']; ?>"><?php echo $row['name']; ?> (<?php echo $row['emp_no']; ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Payroll Month</label>
            <input type="month" name="date" class="form-control" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Basic Salary</label>
            <input type="number" name="basic_salary" class="form-control" step="0.01" required>
        </div>
        <div class="col-md-4">
            <label>Service Allowance</label>
            <input type="number" name="service_allowance" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Island Allowance</label>
            <input type="number" name="island_allowance" class="form-control" step="0.01">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Attendance Allowance</label>
            <input type="number" name="attendance_allowance" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Salary Arrear / Other</label>
            <input type="number" name="salary_arrear_other" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Safety Allowance</label>
            <input type="number" name="safety_allowance" class="form-control" step="0.01">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Pump / Brick / Batching</label>
            <input type="number" name="pump_brick_batching" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Food & Tea</label>
            <input type="number" name="food_and_tea" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Long-Term Service Allowance</label>
            <input type="number" name="long_term_service_allowance" class="form-control" step="0.01">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Living Allowance</label>
            <input type="number" name="living_allowance" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Overtime</label>
            <input type="number" name="ot" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Overtime Arrears</label>
            <input type="number" name="ot_arrears" class="form-control" step="0.01">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Phone Allowance</label>
            <input type="number" name="phone_allowance" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Petrol Allowance</label>
            <input type="number" name="petrol_allowance" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Pension</label>
            <input type="number" name="pension" class="form-control" step="0.01">
        </div>
    </div>

    <button type="submit" class="btn btn-success mt-3">Save Salary Setup</button>
</form>

    <!-- Salary Setup Table -->
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Month</th>
                <th>Basic Salary</th>
                <th>OT</th>
                <th>Pension</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $salaryData->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo date('F Y', strtotime($row['date'])); ?></td>
                    <td><?php echo $row['basic_salary']; ?></td>
                    <td><?php echo $row['ot']; ?></td>
                    <td><?php echo $row['pension']; ?></td>
                    <td>
                        <a href="edit_salary.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>