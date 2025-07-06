<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];
    $payment_month = intval($_POST['payment_month']);
    $payment_year = intval($_POST['payment_year']);

    // Check if the payment is already made for the selected month
    $check_query = "SELECT * FROM loan_payments WHERE loan_id = ? AND payment_month = ? AND payment_year = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iii", $loan_id, $payment_month, $payment_year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Payment for this month is already processed.";
    } else {
        // Fetch loan details
        $loan_query = "SELECT * FROM employee_loan WHERE id = ?";
        $stmt = $conn->prepare($loan_query);
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
        $loan = $stmt->get_result()->fetch_assoc();

        if ($loan) {
            // Calculate interest and principal for the month
            $remaining_balance = $loan['remaining_balance'];
            $monthly_interest_rate = $loan['interest_rate'] / 12 / 100;
            $interest = round($remaining_balance * $monthly_interest_rate, 2);
            $principal = round($loan['emi'] - $interest, 2);
            $new_balance = round($remaining_balance - $principal, 2);

            // Insert the payment into the loan_payments table
            $insert_payment = "INSERT INTO loan_payments (loan_id, payment_month, payment_year, payment_status, payment_amount)
                               VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_payment);
            $payment_status = 1; // Mark as paid
            $stmt->bind_param("iiiid", $loan_id, $payment_month, $payment_year, $payment_status, $loan['emi']);

            if ($stmt->execute()) {
                // Update the employee_loan table
                $update_loan = "UPDATE employee_loan SET installments_paid = installments_paid + 1, remaining_balance = ? WHERE id = ?";
                $stmt = $conn->prepare($update_loan);
                $stmt->bind_param("di", $new_balance, $loan_id);
                $stmt->execute();

                $success = "Payment for Month $payment_month-$payment_year processed successfully!";
            } else {
                $error = "Failed to process payment.";
            }
        } else {
            $error = "Loan not found.";
        }
    }
}

// Fetch active loans for the dropdown
$loan_query = "SELECT id, employee_id FROM employee_loan WHERE active_status = 1";
$loans = $conn->query($loan_query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deduct Monthly Payment</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="container">
  <h1 class="my-4">Deduct Payment for Specific Month</h1>
  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>
  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="card p-4">
    <div class="mb-3">
      <label for="loan_id" class="form-label">Select Loan</label>
      <select id="loan_id" name="loan_id" class="form-control" required>
        <option value="" disabled selected>-- Select Loan --</option>
        <?php while ($loan = $loans->fetch_assoc()): ?>
          <option value="<?= $loan['id'] ?>">Loan ID: <?= $loan['id'] ?> | Employee ID: <?= $loan['employee_id'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="payment_month" class="form-label">Payment Month</label>
      <select id="payment_month" name="payment_month" class="form-control" required>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?= $i ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="payment_year" class="form-label">Payment Year</label>
      <input type="number" id="payment_year" name="payment_year" value="<?= date('Y') ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Process Payment</button>
  </form>
</div>
<script src="../assets/js/app.min.js"></script>
</body>
</html>
