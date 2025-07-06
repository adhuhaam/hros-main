<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Check if the employee ID is provided
if (!isset($_GET['emp_no'])) {
    echo "Employee ID is required.";
    exit;
}

$emp_no = $_GET['emp_no'];

// Fetch the employee data
$sql = "SELECT * FROM employees WHERE emp_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Employee not found.";
    exit;
}

$employee = $result->fetch_assoc();

// Update the employee data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $designation = $_POST['designation'] ?? null;
    $xpat_designation = $_POST['xpat_designation'] ?? null;
    $xpat_join_date = $_POST['xpat_join_date'] ?? null;
    $department = $_POST['department'] ?? null;
    $nationality = $_POST['nationality'] ?? null;
    $passport_nic_no = $_POST['passport_nic_no'] ?? null;
    $passport_nic_no_expires = $_POST['passport_nic_no_expires'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $wp_no = $_POST['wp_no'] ?? null;
    $date_of_join = $_POST['date_of_join'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;
    $emergency_contact_number = $_POST['emergency_contact_number'] ?? null;
    $emergency_contact_name = $_POST['emergency_contact_name'] ?? null;
    $employment_status = $_POST['employment_status'] ?? 'Active';
    $work_site = $_POST['work_site'] ?? null;
    $insurance_provider = $_POST['insurance_provider'] ?? null;
    $recruiting_agency = $_POST['recruiting_agency'] ?? null;
    $emp_email = $_POST['emp_email'] ?? null;
    $company_email = $_POST['company_email'] ?? null;
    $permanent_address = $_POST['permanent_address'] ?? null;
    $persent_address = $_POST['persent_address'] ?? null;
    $basic_salary = $_POST['basic_salary'] ?? 0.00;
    $salary_currency = $_POST['salary_currency'] ?? 'MVR';
    $termination_date = $_POST['termination_date'] ?? null;
    $level = $_POST['level'] ?? null;
    $company = $_POST['company'] ?? null;

    // Prepare the SQL update query
    $updateSql = "UPDATE employees SET  name = ?, gender = ?, designation = ?, xpat_designation = ?, xpat_join_date = ?, department = ?, nationality = ?, passport_nic_no = ?, passport_nic_no_expires = ?, dob = ?, wp_no = ?, 
                  date_of_join = ?, contact_number = ?, emergency_contact_number = ?, emergency_contact_name = ?,   employment_status = ?, work_site = ?, insurance_provider = ?, recruiting_agency = ?, emp_email = ?, company_email = ?,
                  permanent_address = ?, persent_address = ?, basic_salary = ?, salary_currency = ?, termination_date = ?, level = ?, company = ? WHERE emp_no = ?";
    $updateStmt = $conn->prepare($updateSql);

    // Bind parameters to the query
    $updateStmt->bind_param(
        'sssssssssssssssssssssssssdsss',
        $name,
        $gender,
        $designation,
        $xpat_designation,
        $xpat_join_date,
        $department,
        $nationality,
        $passport_nic_no,
        $passport_nic_no_expires,
        $dob,
        $wp_no,
        $date_of_join,
        $contact_number,
        $emergency_contact_number,
        $emergency_contact_name,
        $employment_status,
        $work_site,
        $insurance_provider,
        $recruiting_agency,
        $emp_email,
        $company_email,
        $permanent_address,
        $persent_address,
        $basic_salary,
        $salary_currency,
        $termination_date,
        $level,
        $company,
        $emp_no
    );

    // Execute the query and handle the result
    if ($updateStmt->execute()) {
        header("Location: index.php?success=Employee updated successfully");
        exit;
    } else {
        $error = "Error updating employee: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Employee</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Section -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <div class="body-wrapper">
        <!-- Header -->
           <?php include '../header.php'; ?>
           
      <div class="container-fluid" style="max-width: 100%;">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Edit Employee</h5>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="row">
                  <div class="col-md-6 mb-3">
                    <label for="emp_no" class="form-label text-primary">Employee Number</label>
                    <input type="text" name="emp_no" id="emp_no" value="<?php echo htmlspecialchars($employee['emp_no']); ?>" class="form-control" readonly>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="designation" class="form-label text-primary">Designation</label>
                    <input type="text" name="designation" id="designation" value="<?php echo htmlspecialchars($employee['designation']); ?>" class="form-control" required>
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label text-primary">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($employee['name']); ?>" class="form-control" required>
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label text-primary">Gender</label>
                    <select name="gender" id="gender" class="form-control" required>
                      <option value="Male" <?php echo $employee['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                      <option value="Female" <?php echo $employee['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="dob" class="form-label text-primary">Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($employee['dob']); ?>" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="employment_status" class="form-label text-primary">Employment Status</label>
                    <select name="employment_status" id="employment_status" class="form-control">
                      <?php foreach (['Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'] as $status): ?>
                        <option value="<?php echo $status; ?>" <?php echo $employee['employment_status'] === $status ? 'selected' : ''; ?>>
                          <?php echo $status; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  
                  
                  
                  <div class="col-md-12 mb-3">
                    <label for="permanent_address" class="form-label text-primary">Permanent Address</label>
                    <textarea name="permanent_address" id="permanent_address" class="form-control"><?php echo htmlspecialchars($employee['permanent_address']); ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label for="persent_address" class="form-label text-primary">Persent Address</label>
                    <textarea name="persent_address" id="persent_address" class="form-control"><?php echo htmlspecialchars($employee['persent_address']); ?></textarea>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="emp_email" class="form-label text-primary">Employee Email</label>
                    <input type="email" name="emp_email" id="emp_email" value="<?php echo htmlspecialchars($employee['emp_email']); ?>" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="company_email" class="form-label text-primary">Company Email</label>
                    <input type="email" name="company_email" id="company_email" value="<?php echo htmlspecialchars($employee['company_email']); ?>" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="contact_number" class="form-label text-primary">Employee Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($employee['contact_number']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_number" class="form-label text-primary">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="<?php echo htmlspecialchars($employee['emergency_contact_number']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_name" class="form-label text-primary">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="<?php echo htmlspecialchars($employee['emergency_contact_name']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="xpat_designation" class="form-label text-primary">Expat Designation</label>
                    <input type="text" name="xpat_designation" id="xpat_designation" value="<?php echo htmlspecialchars($employee['xpat_designation']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="xpat_join_date" class="form-label text-primary">Expat Join Date</label>
                    <input type="date" name="xpat_join_date" id="xpat_join_date" value="<?php echo htmlspecialchars($employee['xpat_join_date']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="department" class="form-label text-primary">Department</label>
                    <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($employee['department']); ?>" class="form-control" required>
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="nationality" class="form-label text-primary">Nationality</label>
                    <input type="text" name="nationality" id="nationality" value="<?php echo htmlspecialchars($employee['nationality']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="passport_nic_no" class="form-label text-primary">Passport/NIC No</label>
                    <input type="text" name="passport_nic_no" id="passport_nic_no" value="<?php echo htmlspecialchars($employee['passport_nic_no']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="passport_nic_no_expires" class="form-label text-primary">Passport Expiry Date</label>
                    <input type="date" name="passport_nic_no_expires" id="passport_nic_no_expires" value="<?php echo htmlspecialchars($employee['passport_nic_no_expires']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="wp_no" class="form-label text-primary">WP No</label>
                    <input type="text" name="wp_no" id="wp_no" value="<?php echo htmlspecialchars($employee['wp_no']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="date_of_join" class="form-label text-primary">Date of Join</label>
                    <input type="date" name="date_of_join" id="date_of_join" value="<?php echo htmlspecialchars($employee['date_of_join']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="work_site" class="form-label text-primary">Work Site</label>
                    <input type="text" name="work_site" id="work_site" value="<?php echo htmlspecialchars($employee['work_site']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="insurance_provider" class="form-label text-primary">Insurance Provider</label>
                    <input type="text" name="insurance_provider" id="insurance_provider" value="<?php echo htmlspecialchars($employee['insurance_provider']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="recruiting_agency" class="form-label text-primary">Recruiting Agency</label>
                    <input type="text" name="recruiting_agency" id="recruiting_agency" value="<?php echo htmlspecialchars($employee['recruiting_agency']); ?>" class="form-control">
                  </div>
                
                  <div class="col-md-6 mb-3">
                    <label for="basic_salary" class="form-label text-primary">Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" id="basic_salary" value="<?php echo htmlspecialchars($employee['basic_salary']); ?>" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="salary_currency" class="form-label text-primary">Salary Currency</label>
                    <select name="salary_currency" id="salary_currency" class="form-control" required>
                      <option value="MVR" <?php echo $employee['salary_currency'] === 'MVR' ? 'selected' : ''; ?>>MVR</option>
                      <option value="USD" <?php echo $employee['salary_currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="level" class="form-label text-primary">Level</label>
                    <select name="level" id="level" class="form-control" required>
                      <option value="junior" <?php echo $employee['level'] === 'junior' ? 'selected' : ''; ?>>junior</option>
                      <option value="senior" <?php echo $employee['level'] === 'senior' ? 'selected' : ''; ?>>senior</option>
                    </select>
                  </div>
                  
                   <div class="col-md-6 mb-3">
                    <label for="company" class="form-label text-primary">Company</label>
                    <select name="company" id="company" class="form-control" required>
                      <option value="RASHEED CARPENTRY AND CONSTRUCTION PVT LTD" <?php echo $employee['company'] === 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD' ? 'selected' : ''; ?>>RASHEED CARPENTRY AND CONSTRUCTION PVT LTD</option>
                      <option value="NAZRASH COMPANY PVT LTD" <?php echo $employee['company'] === 'NAZRASH COMPANY PVT LTD' ? 'selected' : ''; ?>>NAZRASH COMPANY PVT LTD</option>
                    </select>
                  </div>
                
                  <!--div class="col-md-6 mb-3">
                    <label for="salary_currency" class="form-label text-primary">Salary Currency</label>
                    <input type="text" name="salary_currency" id="salary_currency" value="<?php echo htmlspecialchars($employee['salary_currency']); ?>" class="form-control">
                  </div--->
                
                  <div class="col-md-6 mb-3">
                    <label for="termination_date" class="form-label text-primary">Termination Date</label>
                    <input type="date" name="termination_date" id="termination_date" value="<?php echo htmlspecialchars($employee['termination_date']); ?>" class="form-control">
                  </div>
                
                  <div class="col-lg-12">
                    <button type="submit" class="d-flex btn btn-success">Update Employee</button>
                  </div>
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
