<?php
session_start();
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch Employees for Selection
$employees = $conn->query("SELECT emp_no, name FROM employees ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Salary Slip</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>
<body>

<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
        <div class="container-fluid">
            <h2>Generate Salary Slip</h2>
            <form id="salarySlipForm" action="pay_slip.php" method="GET">
                <div class="row g-3">
                    <!-- Employee Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Select Employee</label>
                        <select name="emp_no" id="emp_no" class="form-control select2" required>
                            <option value="">-- Select Employee --</option>
                            <?php while ($emp = $employees->fetch_assoc()): ?>
                                <option value="<?php echo $emp['emp_no']; ?>">
                                    <?php echo $emp['name']; ?> (<?php echo $emp['emp_no']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Month Selection -->
                    <div class="col-md-3">
                        <label class="form-label">Payroll Month</label>
                        <select name="month" class="form-control" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo ($m == date('m')) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Year Selection -->
                    <div class="col-md-3">
                        <label class="form-label">Payroll Year</label>
                        <select name="year" class="form-control" required>
                            <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($y == date('Y')) ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Get Payslip</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Search and select an employee",
            allowClear: true
        });
    });

    // Convert month & year into payroll date format
    $('#salarySlipForm').on('submit', function(e) {
        var month = $('select[name="month"]').val();
        var year = $('select[name="year"]').val();
        var payrollDate = year + '-' + ('0' + month).slice(-2) + '-01';
        $('<input>').attr({type: 'hidden', name: 'date', value: payrollDate}).appendTo(this);
    });
</script>

</body>
</html>