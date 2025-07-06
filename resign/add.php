<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $resignation_date = $conn->real_escape_string($_POST['resignation_date']);
    $resign_requested_date = $conn->real_escape_string($_POST['resign_requested_date']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $statement = $conn->real_escape_string($_POST['statement']);

    // Ensure resign_requested_date is before resignation_date
    if ($resign_requested_date > $resignation_date) {
        $error = "Error: Resign Requested Date cannot be after Resignation Date.";
    } else {
        $sql = "INSERT INTO resignations (emp_no, resignation_date, resign_requested_date, remarks, statement, status) 
                VALUES ('$emp_no', '$resignation_date', '$resign_requested_date', '$remarks', '$statement', 'Pending')";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?success=Resignation request submitted successfully");
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resignation</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="index.php" class="btn btn-primary">Back to List</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">ADD Resignation</h5>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Employee No:</label>
                    <input type="text" name="emp_no" required class="form-control" placeholder="Enter Employee No">
                </div>

                <div class="mb-3">
                    <label class="form-label">Resign Requested Date:</label>
                    <input type="date" name="resign_requested_date" required class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Resignation Date:</label>
                    <input type="date" name="resignation_date" required class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks:</label>
                    <textarea name="remarks" class="form-control" placeholder="Enter any remarks (optional)"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Employee Statement:</label>
                    <textarea name="statement" required class="form-control" placeholder="Enter your reason for resignation"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Submit Resignation</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
