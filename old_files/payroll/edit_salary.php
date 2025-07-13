<?php
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Salary ID is required.");
}

$salary_id = $_GET['id'];

// Fetch salary details
$query = "SELECT * FROM salary_income WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $salary_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $salary = $result->fetch_assoc();
} else {
    die("Invalid Salary ID.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
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

    $updateQuery = "UPDATE salary_income SET 
                    date = ?, basic_salary = ?, service_allowance = ?, island_allowance = ?, attendance_allowance = ?, 
                    salary_arrear_other = ?, safety_allowance = ?, pump_brick_batching = ?, food_and_tea = ?, 
                    long_term_service_allowance = ?, living_allowance = ?, ot = ?, ot_arrears = ?, 
                    phone_allowance = ?, petrol_allowance = ?, pension = ? 
                    WHERE id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sdddddddddddddddi", $date, $basic_salary, $service_allowance, $island_allowance, $attendance_allowance,
                      $salary_arrear_other, $safety_allowance, $pump_brick_batching, $food_and_tea,
                      $long_term_service_allowance, $living_allowance, $ot, $ot_arrears,
                      $phone_allowance, $petrol_allowance, $pension, $salary_id);

    if ($stmt->execute()) {
        $success = "Salary details updated successfully.";
        header("Location: salary_setup.php?status=success");
        exit();
    } else {
        $error = "Error updating salary: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Salary</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="container">
    <h2>Edit Salary Details</h2>

    <?php if (isset($success)) echo "<p class='alert alert-success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $salary['id']; ?>">

        <div class="row">
            <div class="col-md-6">
                <label>Employee No</label>
                <input type="text" class="form-control" value="<?php echo $salary['emp_no']; ?>" readonly>
            </div>
            <div class="col-md-6">
                <label>Salary Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo $salary['date']; ?>" readonly>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Basic Salary</label>
                <input type="number" name="basic_salary" class="form-control" step="0.01" value="<?php echo $salary['basic_salary']; ?>" required>
            </div>
            <div class="col-md-4">
                <label>Service Allowance</label>
                <input type="number" name="service_allowance" class="form-control" step="0.01" value="<?php echo $salary['service_allowance']; ?>">
            </div>
            <div class="col-md-4">
                <label>Island Allowance</label>
                <input type="number" name="island_allowance" class="form-control" step="0.01" value="<?php echo $salary['island_allowance']; ?>">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Attendance Allowance</label>
                <input type="number" name="attendance_allowance" class="form-control" step="0.01" value="<?php echo $salary['attendance_allowance']; ?>">
            </div>
            <div class="col-md-4">
                <label>Salary Arrear & Other</label>
                <input type="number" name="salary_arrear_other" class="form-control" step="0.01" value="<?php echo $salary['salary_arrear_other']; ?>">
            </div>
            <div class="col-md-4">
                <label>Safety Allowance</label>
                <input type="number" name="safety_allowance" class="form-control" step="0.01" value="<?php echo $salary['safety_allowance']; ?>">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Pump/Brick/Batching</label>
                <input type="number" name="pump_brick_batching" class="form-control" step="0.01" value="<?php echo $salary['pump_brick_batching']; ?>">
            </div>
            <div class="col-md-4">
                <label>Food & Tea</label>
                <input type="number" name="food_and_tea" class="form-control" step="0.01" value="<?php echo $salary['food_and_tea']; ?>">
            </div>
            <div class="col-md-4">
                <label>Long-Term Service Allowance</label>
                <input type="number" name="long_term_service_allowance" class="form-control" step="0.01" value="<?php echo $salary['long_term_service_allowance']; ?>">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Living Allowance</label>
                <input type="number" name="living_allowance" class="form-control" step="0.01" value="<?php echo $salary['living_allowance']; ?>">
            </div>
            <div class="col-md-4">
                <label>OT</label>
                <input type="number" name="ot" class="form-control" step="0.01" value="<?php echo $salary['ot']; ?>">
            </div>
            <div class="col-md-4">
                <label>OT Arrears</label>
                <input type="number" name="ot_arrears" class="form-control" step="0.01" value="<?php echo $salary['ot_arrears']; ?>">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Phone Allowance</label>
                <input type="number" name="phone_allowance" class="form-control" step="0.01" value="<?php echo $salary['phone_allowance']; ?>">
            </div>
            <div class="col-md-4">
                <label>Petrol Allowance</label>
                <input type="number" name="petrol_allowance" class="form-control" step="0.01" value="<?php echo $salary['petrol_allowance']; ?>">
            </div>
            <div class="col-md-4">
                <label>Pension</label>
                <input type="number" name="pension" class="form-control" step="0.01" value="<?php echo $salary['pension']; ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Changes</button>
    </form>
</div>

</body>
</html>