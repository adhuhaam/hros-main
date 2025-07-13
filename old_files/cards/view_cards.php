<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM card_print WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $card = $result->fetch_assoc();
    } else {
        echo "Card request not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Card Request</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
  <div class="page-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <div class="container mt-5">
        <h1 class="text-center">Card Request Details</h1>
        <table class="table table-bordered">
          <tr>
            <th>ID</th>
            <td><?php echo $card['id']; ?></td>
          </tr>
          <tr>
            <th>Employee No</th>
            <td><?php echo $card['emp_no']; ?></td>
          </tr>
          <tr>
            <th>Print Type</th>
            <td><?php echo $card['print_type']; ?></td>
          </tr>
          <tr>
            <th>Price</th>
            <td><?php echo $card['price']; ?></td>
          </tr>
          <tr>
            <th>Status</th>
            <td><?php echo $card['status']; ?></td>
          </tr>
          <tr>
            <th>Payment Status</th>
            <td><?php echo $card['payment_status']; ?></td>
          </tr>
          <tr>
            <th>Requested Date</th>
            <td><?php echo $card['requested_date']; ?></td>
          </tr>
          <tr>
            <th>Handover Date</th>
            <td><?php echo $card['handover_date']; ?></td>
          </tr>
          <tr>
            <th>Remarks</th>
            <td><?php echo $card['remarks']; ?></td>
          </tr>
        </table>
        <a href="index.php" class="btn btn-primary">Back to List</a>
      </div>
    </div>
  </div>
</body>

</html>
