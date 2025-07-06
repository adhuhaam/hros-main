<?php
include '../db.php';
include '../session.php';

// Fetch card data based on ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM card_print WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $card = $result->fetch_assoc();

    if (!$card) {
        die("Record not found.");
    }
} else {
    die("ID not specified.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $printType = $conn->real_escape_string($_POST['print_type']);
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
    $status = $conn->real_escape_string($_POST['status']);
    $paymentStatus = $conn->real_escape_string($_POST['payment_status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $handoverDate = !empty($_POST['handover_date']) ? $conn->real_escape_string($_POST['handover_date']) : null;

    $updateSql = "UPDATE card_print 
                  SET print_type = ?, price = ?, status = ?, payment_status = ?, remarks = ?, handover_date = ? 
                  WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sdssssi", $printType, $price, $status, $paymentStatus, $remarks, $handoverDate, $id);
    $success = $stmt->execute();

    if ($success) {
        header("Location: index.php?message=updated");
        exit;
    } else {
        $error = "Failed to update record: " . $stmt->error;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Card Request</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <?php include '../sidebar.php'; ?>
    <!-- Sidebar End -->

    <div class="body-wrapper">
      <!-- Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item">
                
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Header End -->

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Card Request</h5>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Update Form -->
            <form method="POST" class="row g-3">
              <div class="col-md-6">
                <label for="print_type" class="form-label">Print Type</label>
                <select id="print_type" name="print_type" class="form-select" required>
                  <option value="Work Permit Card" <?php echo $card['print_type'] == 'Work Permit Card' ? 'selected' : ''; ?>>Work Permit Card</option>
                  <option value="Access Card" <?php echo $card['print_type'] == 'Access Card' ? 'selected' : ''; ?>>Access Card</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($card['price']); ?>" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                  <option value="Pending" <?php echo $card['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Printed" <?php echo $card['status'] == 'Printed' ? 'selected' : ''; ?>>Printed</option>
                  <option value="Handed Over" <?php echo $card['status'] == 'Handed Over' ? 'selected' : ''; ?>>Handed Over</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select id="payment_status" name="payment_status" class="form-select" required>
                  <option value="Not Received" <?php echo $card['payment_status'] == 'Not Received' ? 'selected' : ''; ?>>Not Received</option>
                  <option value="Pending" <?php echo $card['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Received" <?php echo $card['payment_status'] == 'Received' ? 'selected' : ''; ?>>Received</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="handover_date" class="form-label">Handover Date</label>
                <input type="date" id="handover_date" name="handover_date" value="<?php echo ($card['handover_date'] != '0000-00-00') ? $card['handover_date'] : ''; ?>" class="form-control">
              </div>
              <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($card['remarks']); ?></textarea>
              </div>
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Update Request</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
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
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
