<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate the date inputs
    if (!$start_date || !$end_date) {
        die("Please provide both start and end dates.");
    }

    // Prepare the SQL query to fetch records within the date range
    $query = "SELECT sl.id, sl.emp_no, e.name AS employee_name, e.designation, sl.amount, sl.purpose, sl.currency, sl.status, sl.approved_date, sl.received_date
              FROM salary_loans sl
              JOIN employees e ON sl.emp_no = e.emp_no
              WHERE sl.applied_date BETWEEN ? AND ?";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        // Create a CSV file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Employee No', 'Employee Name', 'Designation', 'Amount', 'Purpose', 'Currency', 'Status', 'Approved Date', 'Received Date']);

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['emp_no'],
                $row['employee_name'],
                $row['designation'],
                $row['amount'],
                $row['purpose'],
                $row['currency'],
                $row['status'],
                $row['approved_date'] ? $row['approved_date'] : 'N/A',
                $row['received_date'] ? $row['received_date'] : 'N/A'
            ]);
        }

        fclose($output);
        exit();
    } else {
        die("Error preparing the statement: " . $conn->error);
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Export Records</title>
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
            <h5 class="card-title fw-semibold mb-4">Export Records to CSV</h5>
            <form method="POST" action="" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required class="form-control">
              </div>
              <div class="mb-3">
                <label for="end_date" class="form-label">End Date:</label>
                <input type="date" name="end_date" id="end_date" required class="form-control">
              </div>
              <button type="submit" class="btn btn-success w-100">Export</button>
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
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
