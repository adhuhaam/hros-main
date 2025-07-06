<?php
include '../db.php';
include '../session.php';

// Fetch loan details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT sl.*, e.name AS employee_name, e.designation FROM salary_loans sl 
                            JOIN employees e ON sl.emp_no = e.emp_no WHERE sl.id = $id");
    $loan = $result->fetch_assoc();
    if (!$loan) {
        die("Loan record not found.");
    }
} else {
    die("Invalid request.");
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Loan</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
        <?php include '../sidebar.php'; ?>
    </aside>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Loan Details</h5>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>ID</th>
                  <td><?php echo $loan['id']; ?></td>
                </tr>
                <tr>
                  <th>Employee No</th>
                  <td><?php echo $loan['emp_no']; ?></td>
                </tr>
                <tr>
                  <th>Employee Name</th>
                  <td><?php echo $loan['employee_name']; ?></td>
                </tr>
                <tr>
                  <th>Designation</th>
                  <td><?php echo $loan['designation']; ?></td>
                </tr>
                <tr>
                  <th>Amount</th>
                  <td><?php echo $loan['amount']; ?></td>
                </tr>
                <tr>
                  <th>Purpose</th>
                  <td><?php echo $loan['purpose']; ?></td>
                </tr>
                <tr>
                  <th>Currency</th>
                  <td><?php echo $loan['currency']; ?></td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td><?php echo $loan['status']; ?></td>
                </tr>
                <tr>
                  <th>Applied Date</th>
                  <td><?php echo date('d-M-Y', strtotime($loan['applied_date'])); ?></td>
                </tr>
                <tr>
                  <th>Approved Date</th>
                  <td><?php echo $loan['approved_date'] ? date('d-M-Y', strtotime($loan['approved_date'])) : 'N/A'; ?></td>
                </tr>
                <tr>
                  <th>Received</th>
                  <td><?php echo $loan['received'] ? 'Yes' : 'No'; ?></td>
                </tr>
                <tr>
                  <th>Received Date</th>
                  <td><?php echo $loan['received_date'] ? date('d-M-Y', strtotime($loan['received_date'])) : 'N/A'; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
