<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Ensure 'emp_no' is provided in the query string
if (!isset($_GET['emp_no'])) {
    header('Location: index.php?error=InvalidRequest');
    exit();
}

$emp_no = $_GET['emp_no'];

// Fetch employee details
$sql = "SELECT * FROM employees WHERE emp_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php?error=EmployeeNotFound');
    exit();
}

$employee = $result->fetch_assoc();

// Fetch employee documents
$doc_query = "SELECT * FROM employee_documents WHERE emp_no = ?";
$doc_stmt = $conn->prepare($doc_query);
$doc_stmt->bind_param('s', $emp_no);
$doc_stmt->execute();
$documents = $doc_stmt->get_result();




// Fetch accommodation details for the employee
$accommodation = null;

$acc_stmt = $conn->prepare("
    SELECT 
        b.bed_number, 
        r.room_number, 
        f.floor_number, 
        ab.building_name
    FROM accommodation_beds b
    JOIN accommodation_rooms r ON b.room_id = r.id
    JOIN accommodation_floors f ON r.floor_id = f.id
    JOIN accommodation_buildings ab ON f.building_id = ab.id
    WHERE b.occupied_by = ?
    LIMIT 1
");
$acc_stmt->bind_param('s', $emp_no);
$acc_stmt->execute();
$acc_result = $acc_stmt->get_result();
$accommodation = $acc_result->fetch_assoc();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Profile</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Section -->
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <!-- Header Section -->
      <?php include '../header.php'; ?>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h2 class="card-title text-center">Employee Profile</h2>
            
            <!-- Employee Photo -->
            <div class="text-center mb-4">
              <?php 
              // Display photo if available
              $photo = 'default.png'; // Default photo
              while ($doc = $documents->fetch_assoc()) {
                  if (!empty($doc['photo_file_name'])) {
                      $photo = $doc['photo_file_name'];
                      break;
                  }
              }
              $documents->data_seek(0); // Reset pointer for reuse
              ?>
              <img src="../assets/document/<?php echo htmlspecialchars($photo); ?>" 
                alt="Employee Photo" class="img-fluid rounded " width="130">
              <h4 class="mt-3"><?php echo htmlspecialchars($employee['name'] ?? 'N/A'); ?></h4>
              <span class="badge bg-success"><?php echo htmlspecialchars($employee['employment_status'] ?? 'N/A'); ?></span>
              <span class="badge bg-warning"><?php echo htmlspecialchars($employee['level'] ?? 'N/A'); ?> staff</span>
              <?php if ($employee['employment_status'] !== 'Active'): ?>
                <div class="mt-2"><strong>Termination Date:</strong> <?php echo isset($employee['termination_date']) ? date('d-M-Y', strtotime($employee['termination_date'])) : 'N/A'; ?></div>
              <?php endif; ?>
            </div>
            
            
            
            

            <!-- Personal Information Card -->
            <div class="card mb-2">
              <div class="card-header">
                <h5>Personal Information</h5>
              </div>
              <div class="card-body">
                <table class="table">
                  <tr><th>Name</th><td><?php echo htmlspecialchars($employee['name'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Gender</th><td><?php echo htmlspecialchars($employee['gender'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Date of Birth</th><td><?php echo isset($employee['dob']) ? date('d-M-Y', strtotime($employee['dob'])) : 'N/A'; ?></td></tr>
                  <tr><th>Nationality</th><td><?php echo htmlspecialchars($employee['nationality'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Passport Number</th><td><?php echo htmlspecialchars($employee['passport_nic_no'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Passport Expiry</th><td><?php echo isset($employee['passport_nic_no_expires']) ? date('d-M-Y', strtotime($employee['passport_nic_no_expires'])) : 'N/A'; ?></td></tr>
                  <tr><th>Phone Number</th><td><?php echo htmlspecialchars($employee['contact_number'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Email</th><td><?php echo htmlspecialchars($employee['emp_email'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Work Permit Number</th><td><?php echo htmlspecialchars($employee['wp_no'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Permanent Address</th><td><?php echo htmlspecialchars($employee['permanent_address'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Present Address</th><td><?php echo htmlspecialchars($employee['persent_address'] ?? 'N/A'); ?></td></tr>
                </table>
              </div>
            </div>




        <!-- Employment Information Card -->
            <div class="card mb-2">
              <div class="card-header">
                <h5>Employment Information</h5>
              </div>
              <div class="card-body">
                <table class="table">
                    <tr><th>Employee Number</th><td><?php echo htmlspecialchars($employee['emp_no'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Designation</th><td><?php echo htmlspecialchars($employee['designation'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Expat Designation</th><td><?php echo htmlspecialchars($employee['xpat_designation'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Expat Join Date</th><td><?php echo isset($employee['xpat_join_date']) ? date('d-M-Y', strtotime($employee['xpat_join_date'])) : 'N/A'; ?></td></tr>
                  <tr><th>Department</th><td><?php echo htmlspecialchars($employee['department'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Date of Joining</th><td><?php echo isset($employee['date_of_join']) ? date('d-M-Y', strtotime($employee['date_of_join'])) : 'N/A'; ?></td></tr>
                  <tr><th>Work Site</th><td><?php echo htmlspecialchars($employee['work_site'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Insurance Provider</th><td><?php echo htmlspecialchars($employee['insurance_provider'] ?? 'N/A'); ?></td></tr>
                  <tr><th>Recruiting Agency</th><td><?php echo htmlspecialchars($employee['recruiting_agency'] ?? 'N/A'); ?></td></tr>
                 <tr><th>Salary</th><td><?php echo isset($employee['basic_salary']) ? number_format($employee['basic_salary'], 2) . ' ' . htmlspecialchars($employee['salary_currency'] ?? '') : 'N/A';  ?></td> </tr>

                </table>
              </div>
            </div>
            
            
            
            
            <!-- Contact Information Card -->
            <div class="card mb-4">
              <div class="card-header">
                <h5>Contact Information</h5>
              </div>
              <div class="card-body">
                <table class="table">
                  
                  <tr><th>Emergency Contact</th><td><?php echo htmlspecialchars($employee['emergency_contact_number'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($employee['emergency_contact_name'] ?? 'N/A'); ?>)</td></tr>
                  
                </table>
              </div>
            </div>
            
            <!-- ACCOMODATION DETAILS HERE -->
            <div class="card mb-4">
              <div class="card-header">
                <h5>Accommodation Details</h5>
              </div>
              <div class="card-body">
                <?php if ($accommodation): ?>
                  <table class="table table-striped">
                    <tr>
                      <th>Building</th>
                      <td><?= htmlspecialchars($accommodation['building_name']) ?></td>
                    </tr>
                    <tr>
                      <th>Floor</th>
                      <td>Floor <?= htmlspecialchars($accommodation['floor_number']) ?></td>
                    </tr>
                    <tr>
                      <th>Room</th>
                      <td>Room <?= htmlspecialchars($accommodation['room_number']) ?></td>
                    </tr>
                    <tr>
                      <th>Bed</th>
                      <td>Bed <?= htmlspecialchars($accommodation['bed_number']) ?></td>
                    </tr>
                  </table>
                <?php else: ?>
                  <p class="text-muted">No accommodation assigned.</p>
                <?php endif; ?>
              </div>
            </div>

            

            <!-- Employee Documents Card -->
            <div class="card">
              <div class="card-header">
                <h5>Documents</h5>
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <?php 
                  while ($doc = $documents->fetch_assoc()):
                    $docType = htmlspecialchars($doc['doc_type']);
                    $frontFile = htmlspecialchars($doc['front_file_name'] ?? '');
                    $backFile = htmlspecialchars($doc['back_file_name'] ?? '');
                    $photoFile = htmlspecialchars($doc['photo_file_name'] ?? '');
                  ?>
                    <?php if ($docType === 'Photo' && $photoFile): ?>
                      <li class="list-group-item">
                        <i class="ti ti-file"></i> 
                        <a href="../assets/document/<?php echo $photoFile; ?>" target="_blank">
                          <?php echo $docType; ?>
                        </a>
                      </li>
                    <?php else: ?>
                      <?php if ($frontFile): ?>
                        <li class="list-group-item">
                          <i class="ti ti-file"></i> 
                          <a href="../assets/document/<?php echo $frontFile; ?>" target="_blank">
                            <?php echo $docType; ?> (Front)
                          </a>
                        </li>
                      <?php endif; ?>
                      <?php if ($backFile): ?>
                        <li class="list-group-item">
                          <i class="ti ti-file"></i> 
                          <a href="../assets/document/<?php echo $backFile; ?>" target="_blank">
                            <?php echo $docType; ?> (Back)
                          </a>
                        </li>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endwhile; ?>
                </ul>
              </div>
            </div>

            <!-- Back to Index Button -->
            <div class="mt-4 text-end">
              <div class="mt-4 text-end">
                  <a href="edit.php?emp_no=<?php echo htmlspecialchars($employee['emp_no']); ?>" class="btn btn-primary">
                    <i class="ti ti-edit"></i> Edit
                  </a>
                  <a href="index.php" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Back to List</a>
                </div>

            </div>
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
