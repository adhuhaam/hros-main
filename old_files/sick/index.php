<?php
include '../db.php'; 
include '../session.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $from_date = $conn->real_escape_string($_POST['from_date']);
    $no_of_days = (int) $conn->real_escape_string($_POST['no_of_days']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $created_at = date('Y-m-d H:i:s');

    // Fetch employee level
    $empQuery = $conn->prepare("SELECT level FROM employees WHERE emp_no = ?");
    $empQuery->bind_param("s", $emp_no);
    $empQuery->execute();
    $empResult = $empQuery->get_result();
    $empData = $empResult->fetch_assoc();

    if ($empData) {
        $level = strtolower($empData['level']);
        $isJunior = ($level === 'junior');
        $requiresMC = $isJunior || ($level === 'senior' && $no_of_days > 1);

        // Validate Reference Document if required
        if ($requiresMC && empty($_FILES['reference_doc']['name'])) {
            $error = "MC Document is required for this leave request.";
        } else {
            $upload_dir = 'uploads/';
            $reference_doc = '';

            if (!empty($_FILES['reference_doc']['name'])) {
                $file_name = basename($_FILES['reference_doc']['name']);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['reference_doc']['tmp_name'], $target_file)) {
                    $reference_doc = $file_name;
                } else {
                    $error = "File upload failed.";
                }
            }

            if (!isset($error)) {
                $stmt = $conn->prepare("INSERT INTO sick_leaves (emp_no, from_date, no_of_days, reason, reference_doc, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisss", $emp_no, $from_date, $no_of_days, $reason, $reference_doc, $created_at);

                if ($stmt->execute()) {
                    $success = "Sick leave added successfully.";
                } else {
                    $error = "Error: " . $stmt->error;
                }
            }
        }
    } else {
        $error = "Employee not found.";
    }
}

// Fetch sick leave records
$sql = "SELECT * FROM sick_leaves ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sick Leaves</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-light">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSickLeaveModal">Add Sick Leave</button>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title">Sick Leaves</h5>
            <?php if (isset($error)) { ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <?php if (isset($success)) { ?>
              <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Employee No</th>
                    <th>From Date</th>
                    <th>Number of Days</th>
                    <th>Reason</th>
                    <th>Reference Document</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) { ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $row['emp_no']; ?></td>
                        <td><?php echo date('d-M-Y', strtotime($row['from_date'])); ?></td>
                        <td><?php echo $row['no_of_days']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td>
                          <?php if ($row['reference_doc']) { ?>
                            <a href="uploads/<?php echo $row['reference_doc']; ?>" target="_blank">View Document</a>
                          <?php } else {
                            echo 'N/A';
                          } ?>
                        </td>
                        <td><?php echo date('d-M-Y H:i:s', strtotime($row['created_at'])); ?></td>
                      </tr>
                  <?php }
                  } else { ?>
                    <tr>
                      <td colspan="7" class="text-center">No records found</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Sick Leave Modal -->
  <div class="modal fade" id="addSickLeaveModal" tabindex="-1" aria-labelledby="addSickLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="addSickLeaveModalLabel">Add Sick Leave</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="emp_no" class="form-label">Employee Number</label>
              <input type="text" name="emp_no" id="emp_no" class="form-control" required placeholder="Enter Employee No">
              <small id="emp_details" class="text-muted"></small>
            </div>
            <div class="mb-3">
              <label for="reason" class="form-label">Reason</label>
              <input type="text" name="reason" id="reason" class="form-control" required placeholder="Enter reason">
            </div>
            <div class="mb-3">
              <label for="from_date" class="form-label">From Date</label>
              <input type="date" name="from_date" id="from_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="no_of_days" class="form-label">Number of Days</label>
              <input type="number" name="no_of_days" id="no_of_days" class="form-control" required placeholder="Enter number of days">
            </div>
            <div class="mb-3">
              <label for="reference_doc" class="form-label">MC Document (Optional)</label>
              <input type="file" name="reference_doc" id="reference_doc" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('emp_no').addEventListener('input', function () {
      const empNo = this.value.trim();

      if (empNo) {
        fetch(`fetch.php?emp_no=${empNo}`)
          .then(response => response.json())
          .then(data => {
            const empDetails = document.getElementById('emp_details');
            const referenceDoc = document.getElementById('reference_doc');
            const noOfDays = document.getElementById('no_of_days');

            if (data.error) {
              empDetails.textContent = data.error;
              empDetails.classList.add('text-danger');
              empDetails.classList.remove('text-success');
            } else {
              empDetails.textContent = `${data.name} | ${data.designation} | Level: ${data.level}`;
              empDetails.classList.add('text-success');
              empDetails.classList.remove('text-danger');

              noOfDays.addEventListener('input', function () {
                const days = parseInt(noOfDays.value, 10);
                if (data.level === 'junior' || (data.level === 'senior' && days > 1)) {
                  referenceDoc.required = true;
                  referenceDoc.parentElement.querySelector('label').textContent = "MC Document (Required)";
                } else {
                  referenceDoc.required = false;
                  referenceDoc.parentElement.querySelector('label').textContent = "MC Document (Optional)";
                }
              });
            }
          })
          .catch(error => console.error('Error fetching employee details:', error));
      } else {
        document.getElementById('emp_details').textContent = '';
      }
    });
  </script>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
