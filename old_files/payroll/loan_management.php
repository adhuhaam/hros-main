<?php
session_start();
include '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Loan Approval or Rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_loan'])) {
    $loan_id = $_POST['loan_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE employee_loan SET active_status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ii", $status, $loan_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Loan status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update loan status.";
    }
    header("Location: loan_management.php");
    exit();
}

// Handle Loan Installment Processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $loan_id = $_POST['loan_id'];
    $payment_date = $_POST['payment_month']; // Format: YYYY-MM

    // Extract Year and Month Separately
    if (!empty($payment_date) && strpos($payment_date, '-') !== false) {
        list($payment_year, $payment_month) = explode("-", $payment_date);
    } else {
        $_SESSION['error'] = "Invalid payment date format.";
        header("Location: loan_management.php");
        exit();
    }

    // Fetch Loan EMI Amount
    $stmt = $conn->prepare("SELECT emi, remaining_balance FROM employee_loan WHERE id = ?");
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $loan_data = $stmt->get_result()->fetch_assoc();
    $emi_amount = $loan_data['emi'] ?? 0;
    $remaining_balance = $loan_data['remaining_balance'] ?? 0;

    if ($remaining_balance > 0 && $emi_amount <= $remaining_balance) {
        // Insert into `loan_payments` table
        $stmt2 = $conn->prepare("INSERT INTO loan_payments (loan_id, payment_month, payment_year, payment_amount, payment_status) VALUES (?, ?, ?, ?, 1)");
        $stmt2->bind_param("iiid", $loan_id, $payment_month, $payment_year, $emi_amount);
        $stmt2->execute();

        // Deduct from `employee_loan` remaining balance
        $stmt3 = $conn->prepare("UPDATE employee_loan SET remaining_balance = remaining_balance - emi, installments_paid = installments_paid + 1 WHERE id = ?");
        $stmt3->bind_param("i", $loan_id);
        $stmt3->execute();

        $_SESSION['message'] = "Loan installment processed successfully!";
    } else {
        $_SESSION['error'] = "Insufficient balance or incorrect installment amount.";
    }

    header("Location: loan_management.php");
    exit();
}

// Fetch Active Loans
$loans = $conn->query("SELECT l.*, e.name FROM employee_loan l 
                       JOIN employees e ON l.employee_id = e.emp_no 
                       ORDER BY l.created_at DESC");

// Fetch Loan Payments
$loan_payments = $conn->query("SELECT p.*, e.name, l.loan_amount FROM loan_payments p 
                               JOIN employee_loan l ON p.loan_id = l.id 
                               JOIN employees e ON l.employee_id = e.emp_no 
                               ORDER BY p.payment_year DESC, p.payment_month DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loan Management</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>

<div class="page-wrapper">
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <div class="container-fluid">
      <h2>Loan Management</h2>

      <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
      <?php elseif (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <!-- Loan Requests Table -->
      <h5>Pending Loan Requests</h5>
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Loan Amount</th>
            <th>Interest Rate</th>
            <th>EMI</th>
            <th>Installments Paid</th>
            <th>Remaining Balance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($loan = $loans->fetch_assoc()): ?>
            <tr>
              <td><?php echo $loan['name']; ?> (<?php echo $loan['employee_id']; ?>)</td>
              <td><?php echo number_format($loan['loan_amount'], 2); ?></td>
              <td><?php echo number_format($loan['interest_rate'], 2); ?>%</td>
              <td><?php echo number_format($loan['emi'], 2); ?></td>
              <td><?php echo $loan['installments_paid']; ?> / <?php echo $loan['installment_period']; ?></td>
              <td><?php echo number_format($loan['remaining_balance'], 2); ?></td>
              <td><?php echo ($loan['active_status'] == 1) ? 'Active' : 'Closed'; ?></td>
              <td>
                <form action="loan_management.php" method="POST" class="d-inline">
                  <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                  <select name="status" class="form-control d-inline w-auto">
                    <option value="1" <?php echo ($loan['active_status'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo ($loan['active_status'] == 0) ? 'selected' : ''; ?>>Closed</option>
                  </select>
                  <button type="submit" name="update_loan" class="btn btn-primary btn-sm">Update</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Loan Installment Processing -->
      <h5>Process Loan Installments</h5>
      <form action="loan_management.php" method="POST">
        <label class="form-label">Select Loan</label>
        <select name="loan_id" class="form-control" required>
          <option value="" disabled selected>-- Select Loan --</option>
          <?php
          $active_loans = $conn->query("SELECT id, employee_id, remaining_balance FROM employee_loan WHERE active_status = 1 AND remaining_balance > 0");
          while ($loan = $active_loans->fetch_assoc()):
          ?>
            <option value="<?php echo $loan['id']; ?>">Loan ID: <?php echo $loan['id']; ?> (Employee: <?php echo $loan['employee_id']; ?> | Remaining: <?php echo number_format($loan['remaining_balance'], 2); ?>)</option>
          <?php endwhile; ?>
        </select>

        <label class="form-label mt-2">Payment Month</label>
        <input type="month" name="payment_month" class="form-control" required>

        <button type="submit" name="process_payment" class="btn btn-success mt-3">Process Payment</button>
      </form>
    </div>
  </div>
</div>

<script src="../assets/js/app.min.js"></script>
</body>
</html>