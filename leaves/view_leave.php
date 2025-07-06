<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid Request";
    exit;
}

$leave_id = $_GET['id'];

// Fetch leave record details with ticket info
$leave_query = $conn->prepare("
    SELECT lr.*, e.emp_no, e.name AS employee_name, e.designation, lt.name AS leave_type, 
           dep.id AS departure_ticket_id, dep.destination AS dep_destination, dep.departure_date AS dep_date,
           arr.id AS arrival_ticket_id, arr.destination AS arr_destination, arr.departure_date AS arr_date
    FROM leave_records lr
    JOIN employees e ON lr.emp_no = e.emp_no
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    LEFT JOIN employee_tickets dep ON lr.departure_ticket_id = dep.id
    LEFT JOIN employee_tickets arr ON lr.arrival_ticket_id = arr.id
    WHERE lr.id = ?
");
$leave_query->bind_param("i", $leave_id);
$leave_query->execute();
$leave_record = $leave_query->get_result()->fetch_assoc();

if (!$leave_record) {
    echo "Leave record not found";
    exit;
}

// Fetch destinations
$destinations_query = $conn->query("SELECT id, destination_name FROM employee_tickets_destination ORDER BY destination_name ASC");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Leave Details</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <?php include '../header.php'; ?>

      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4>Leave Details</h4>
            <a href="index.php" class="btn btn-light btn-sm">Back</a>
          </div>

          <div class="card-body">
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
              <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <!-- Employee & Leave Info -->
            <table class="table table-bordered">
              <tr><th>Employee No</th><td><?php echo $leave_record['emp_no']; ?></td></tr>
              <tr><th>Employee Name</th><td><?php echo $leave_record['employee_name']; ?></td></tr>
              <tr><th>Designation</th><td><?php echo $leave_record['designation']; ?></td></tr>
              <tr><th>Leave Type</th><td><?php echo $leave_record['leave_type']; ?></td></tr>
              <tr><th>Start Date</th><td><?php echo date('d-M-Y', strtotime($leave_record['start_date'])); ?></td></tr>
              <tr><th>End Date</th><td><?php echo date('d-M-Y', strtotime($leave_record['end_date'])); ?></td></tr>
              <tr><th>Actual arrival date</th><td><?php echo date('d-M-Y', strtotime($leave_record['actual_arrival_date'])); ?></td></tr>
            </table>

            <!-- Departure Ticket -->
            <h5 class="text-primary mt-4">Departure Ticket</h5>
            <?php if ($leave_record['departure_ticket_id']): ?>
              <p><strong>Destination:</strong> <?php echo $leave_record['dep_destination']; ?></p>
              <p><strong>Departure Date:</strong> <?php echo date('d-M-Y', strtotime($leave_record['dep_date'])); ?></p>
            <?php else: ?>
              <form method="POST" action="create_ticket.php">
                <input type="hidden" name="leave_id" value="<?php echo $leave_id; ?>">
                <input type="hidden" name="emp_no" value="<?php echo $leave_record['emp_no']; ?>">
                <input type="hidden" name="ticket_type" value="Departure">

                <label>Destination:</label>
                <select name="destination" class="form-control" required>
                  <option value="">Select Destination</option>
                  <?php while ($dest = $destinations_query->fetch_assoc()): ?>
                    <option value="<?php echo $dest['destination_name']; ?>"><?php echo $dest['destination_name']; ?></option>
                  <?php endwhile; ?>
                  <option value="other">Other</option>
                </select>

                <label>Departure Date:</label>
                <input type="date" name="departure_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($leave_record['start_date'])); ?>" required>

                <button type="submit" class="btn btn-success mt-2">Create Departure Ticket</button>
              </form>
            <?php endif; ?>

            <hr>

            <!-- Arrival Ticket -->
            <h5 class="text-primary mt-4">Arrival Ticket</h5>
            <?php if ($leave_record['arrival_ticket_id']): ?>
              <p><strong>Destination:</strong> <?php echo $leave_record['arr_destination']; ?></p>
              <p><strong>Arrival Date:</strong> <?php echo date('d-M-Y', strtotime($leave_record['arr_date'])); ?></p>
            <?php else: ?>
              <form method="POST" action="create_ticket.php">
                <input type="hidden" name="leave_id" value="<?php echo $leave_id; ?>">
                <input type="hidden" name="emp_no" value="<?php echo $leave_record['emp_no']; ?>">
                <input type="hidden" name="ticket_type" value="Arrival">

                <label>Destination:</label>
                <select name="destination" class="form-control" required>
                  <option value="">Select Destination</option>
                  <?php
                  $destinations_query->data_seek(0);
                  while ($dest = $destinations_query->fetch_assoc()): ?>
                    <option value="<?php echo $dest['destination_name']; ?>"><?php echo $dest['destination_name']; ?></option>
                  <?php endwhile; ?>
                  <option value="other">Other</option>
                </select>

                <label>Arrival Date:</label>
                <input type="date" name="arrival_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($leave_record['end_date'])); ?>" required>

                <button type="submit" class="btn btn-info mt-2">Create Arrival Ticket</button>
              </form>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary mt-4">Back to Leave Records</a>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
  $(document).ready(function () {
    $('select[name="destination"]').change(function () {
      if ($(this).val() === 'other') {
        $(this).closest('form').find('[name="other_destination"]').show();
      } else {
        $(this).closest('form').find('[name="other_destination"]').hide();
      }
    });
  });
</script>
</body>
</html>
