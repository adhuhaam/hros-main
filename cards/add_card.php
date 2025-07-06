<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $print_type = $conn->real_escape_string($_POST['print_type']);
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $requested_date = $conn->real_escape_string($_POST['requested_date']);

    // Check for duplicate entry based on emp_no and print_type
    $checkSql = "SELECT * FROM card_print WHERE emp_no = ? AND print_type = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ss", $emp_no, $print_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "A request for this employee with the same print type already exists.";
    } else {
        // Insert into database
        $insertSql = "INSERT INTO card_print (emp_no, print_type, price, remarks, requested_date)
                      VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssdss", $emp_no, $print_type, $price, $remarks, $requested_date);

        if ($stmt->execute()) {
            // Redirect to card_print.php on success
            header('Location: ../cards/');
            exit();
        } else {
            // Display an error message
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Card Print Request</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="../cards/" class="btn btn-primary">View All Requests</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Card Print Request</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label for="emp_no" class="form-label">Employee No:</label>
                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No">
              </div>
              <div class="mb-3">
                <label for="print_type" class="form-label">Print Type:</label>
                <select name="print_type" id="print_type" required class="form-control">
                  <option value="" disabled selected>-- Select Print Type --</option>
                  <option value="Work Permit Card">Work Permit Card</option>
                  <option value="Access Card">Access Card</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Enter Price (optional)">
              </div>
              <div class="mb-3">
                <label for="remarks" class="form-label">Remarks:</label>
                <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter any remarks (optional)"></textarea>
              </div>
              <div class="mb-3">
                <label for="requested_date" class="form-label">Requested Date:</label>
                <input type="date" name="requested_date" id="requested_date" required class="form-control">
              </div>
              <button type="submit" class="btn btn-success w-100">Add Request</button>
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
