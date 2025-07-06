<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $emp_no = $_POST['emp_no'] ?? null;
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
    $permanent_address = $_POST['permanent_address'] ?? null;
    $basic_salary = $_POST['basic_salary'] ?? 0.00;
    $salary_currency = $_POST['salary_currency'] ?? 'MVR';
    $termination_date = $_POST['termination_date'] ?? null;

    // Prepare the SQL insert query
    $insertSql = "INSERT INTO employees (
                    emp_no, name, gender, designation, xpat_designation, xpat_join_date, department, nationality,
                    passport_nic_no, passport_nic_no_expires, dob, wp_no, date_of_join, contact_number,
                    emergency_contact_number, emergency_contact_name, employment_status, work_site, insurance_provider,
                    recruiting_agency, emp_email, permanent_address, basic_salary, salary_currency, termination_date
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);

    // Bind parameters to the query
    $stmt->bind_param(
        'sssssssssssssssssssssdsss',
        $emp_no,
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
        $permanent_address,
        $basic_salary,
        $salary_currency,
        $termination_date
    );

    // Execute the query and handle the result
    if ($stmt->execute()) {
        header("Location: index.php?success=Employee added successfully");
        exit;
    } else {
        $error = "Error adding employee: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Employee</title>
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
      <div class="container-fluid" style="max-width: 100%;">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Add New Employee</h5>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="mt-4 row">
                  <div class="col-md-6 mb-3">
                    <label for="emp_no" class="form-label">Employee Number</label>
                    <input type="text" name="emp_no" id="emp_no" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select name="gender" id="gender" class="form-control" required>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="designation" class="form-label">Designation</label>
                    <input type="text" name="designation" id="designation" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="xpat_designation" class="form-label">Expat Designation</label>
                    <input type="text" name="xpat_designation" id="xpat_designation" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="xpat_join_date" class="form-label">Expat Join Date</label>
                    <input type="date" name="xpat_join_date" id="xpat_join_date" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" name="department" id="department" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="nationality" class="form-label">Nationality</label>
                    <input type="text" name="nationality" id="nationality" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="passport_nic_no" class="form-label">Passport/NIC No</label>
                    <input type="text" name="passport_nic_no" id="passport_nic_no" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="passport_nic_no_expires" class="form-label">Passport Expiry Date</label>
                    <input type="date" name="passport_nic_no_expires" id="passport_nic_no_expires" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="wp_no" class="form-label">WP No</label>
                    <input type="text" name="wp_no" id="wp_no" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="date_of_join" class="form-label">Date of Join</label>
                    <input type="date" name="date_of_join" id="date_of_join" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" id="emergency_contact_number" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="employment_status" class="form-label">Employment Status</label>
                    <select name="employment_status" id="employment_status" class="form-control">
                      <option value="Active">Active</option>
                      <option value="Terminated">Terminated</option>
                      <option value="Resigned">Resigned</option>
                      <option value="Rejoined">Rejoined</option>
                      <option value="Dead">Dead</option>
                      <option value="Retired">Retired</option>
                      <option value="Missing">Missing</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="work_site" class="form-label">Work Site</label>
                    <input type="text" name="work_site" id="work_site" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="insurance_provider" class="form-label">Insurance Provider</label>
                    <input type="text" name="insurance_provider" id="insurance_provider" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="recruiting_agency" class="form-label">Recruiting Agency</label>
                    <input type="text" name="recruiting_agency" id="recruiting_agency" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="emp_email" class="form-label">Employee Email</label>
                    <input type="email" name="emp_email" id="emp_email" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="permanent_address" class="form-label">Permanent Address</label>
                    <textarea name="permanent_address" id="permanent_address" class="form-control"></textarea>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="basic_salary" class="form-label">Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="salary_currency" class="form-label">Salary Currency</label>
                    <select name="salary_currency" id="salary_currency" class="form-control" required>
                      <option value="USD">USD</option>
                      <option value="MVR">MVR</option>
                    </select>
                  </div>
                  
                  <!--div class="col-md-6 mb-3">
                    <label for="salary_currency" class="form-label">Salary Currency</label>
                    <input type="text" name="salary_currency" id="salary_currency" class="form-control">
                  </div-->
                  
                  <div class="col-md-6 mb-3">
                    <label for="termination_date" class="form-label">Termination Date</label>
                    <input type="date" name="termination_date" id="termination_date" class="form-control">
                  </div>
                  
                  <div class="col-12">
                    <button type="submit" class="btn btn-success">Add Employee</button>
                    <a href="index.php" class="btn btn-danger">Cancel</a>
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
