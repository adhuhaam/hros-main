<?php
session_start();
include '../db.php';
include '../session.php';

// Ensure 'emp_no' is provided in the query string
if (!isset($_GET['emp_no'])) {
    header('Location: index.php?error=InvalidRequest');
    exit();
}

$emp_no = $_GET['emp_no'];

// Fetch employee details to verify existence
$sql = "SELECT * FROM employees WHERE emp_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php?error=EmployeeNotFound');
    exit();
}

// Handle delete confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Delete related documents
            $doc_query = "DELETE FROM employee_documents WHERE emp_no = ?";
            $doc_stmt = $conn->prepare($doc_query);
            $doc_stmt->bind_param('s', $emp_no);
            $doc_stmt->execute();

            // Delete employee record
            $delete_query = "DELETE FROM employees WHERE emp_no = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param('s', $emp_no);
            $delete_stmt->execute();

            // Commit transaction
            $conn->commit();

            // Redirect to the employee list with success message
            header('Location: index.php?success=EmployeeDeleted');
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error = "Failed to delete employee: " . $e->getMessage();
        }
    } else {
        // Redirect if deletion is cancelled
        header('Location: index.php');
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delete Employee</title>
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
        <div class="card mt-5">
          <div class="card-body">
            <h3 class="text-danger">Delete Employee</h3>
            <p>Are you sure you want to delete the employee record for <strong><?php echo htmlspecialchars($emp_no); ?></strong>?</p>
            <form method="POST">
              <div class="d-flex justify-content-start">
                <button type="submit" name="confirm" value="yes" class="btn btn-danger me-3">Yes, Delete</button>
                <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
              </div>
            </form>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Required Scripts -->
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
</body>

</html>
