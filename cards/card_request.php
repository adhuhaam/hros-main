<?php
include '../db.php';
include '../session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $_POST['emp_no'];
    $print_type = $_POST['print_type'];
    $price = $_POST['price'];
    $remarks = $_POST['remarks'];
    $requested_date = $_POST['requested_date'];

    $sql = "INSERT INTO card_print (emp_no, print_type, price, remarks, requested_date)
            VALUES ('$emp_no', '$print_type', '$price', '$remarks', '$requested_date')";
    $conn->query($sql);

    header('Location: card_print.php');
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Request Card</title>
  <link rel="stylesheet" href="assets/css/styles.min.css">
</head>

<body>
  <div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <div class="container mt-5">
        <h1 class="text-center">Request New Card</h1>
        <form method="POST">
          <label>Employee No:</label>
          <input type="text" name="emp_no" class="form-control" required>

          <label>Print Type:</label>
          <select name="print_type" class="form-control" required>
            <option value="Work Permit Card">Work Permit Card</option>
            <option value="Access Card">Access Card</option>
          </select>

          <label>Price:</label>
          <input type="number" name="price" class="form-control" required>

          <label>Remarks:</label>
          <textarea name="remarks" class="form-control"></textarea>

          <label>Requested Date:</label>
          <input type="date" name="requested_date" class="form-control" required>

          <button type="submit" class="btn btn-success mt-3">Submit Request</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
