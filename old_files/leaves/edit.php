<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
    echo "Invalid Request";
    exit;
}

$leave_id = $_GET['id'];

// Fetch leave record
$leave_query = $conn->prepare("SELECT * FROM leave_records WHERE id = ?");
$leave_query->bind_param("i", $leave_id);
$leave_query->execute();
$leave_record = $leave_query->get_result()->fetch_assoc();

if (!$leave_record) {
    echo "Leave record not found";
    exit;
}

// Fetch all leave types
$leave_types_query = $conn->prepare("SELECT * FROM leave_types");
$leave_types_query->execute();
$leave_types = $leave_types_query->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Leave Record</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
  <!-- Sidebar -->
  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <header class="app-header">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <h4 class="navbar-brand fw-semibold">Edit Leave Record</h4>
        </div>
      </nav>
    </header>

    <div class="container-fluid mb-4">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="update_leave.php" class="shadow-sm p-4 rounded bg-light">
  <input type="hidden" name="id" value="<?php echo $leave_record['id']; ?>">

  <div class="mb-3">
    <label for="employee_id" class="form-label fw-semibold">Employee ID:</label>
    <input type="text" name="employee_id" id="employee_id" class="form-control" value="<?php echo $leave_record['emp_no']; ?>" readonly>
  </div>

  <div class="mb-3">
    <label for="leave_type_id" class="form-label fw-semibold">Leave Type:</label>
    <select name="leave_type_id" id="leave_type_id" class="form-select">
      <?php while ($row = $leave_types->fetch_assoc()): ?>
        <option value="<?php echo $row['id']; ?>" <?php echo $leave_record['leave_type_id'] == $row['id'] ? 'selected' : ''; ?>>
          <?php echo $row['name']; ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="start_date" class="form-label fw-semibold">Start Date:</label>
    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $leave_record['start_date']; ?>" required>
  </div>

  <div class="mb-3">
    <label for="num_days" class="form-label fw-semibold">Number of Days:</label>
    <input type="number" name="num_days" id="num_days" class="form-control" value="<?php echo $leave_record['num_days']; ?>" required>
  </div>

  <div class="mb-3">
    <label for="remarks" class="form-label fw-semibold">Remarks:</label>
    <textarea name="remarks" id="remarks" class="form-control" rows="4"><?php echo $leave_record['remarks']; ?></textarea>
  </div>

  <div class="mb-3">
    <label for="status" class="form-label fw-semibold">Status:</label>
    <select name="status" id="status" class="form-select">
      <option value="Pending" <?php echo $leave_record['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
      <option value="Approved" <?php echo $leave_record['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
        <option value="Pending Leave Arrival" <?php echo $leave_record['status'] == 'Pending Leave Arrival' ? 'selected' : ''; ?>>Pending Leave Arrival</option>
      <option value="Rejected" <?php echo $leave_record['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
      <option value="Departed" <?php echo $leave_record['status'] == 'Departed' ? 'selected' : ''; ?>>Departed</option>
    </select>
  </div>

  <button type="submit" class="btn btn-success w-100">Update Leave Record</button>
</form>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#start_date, #num_days').on('change', function() {
      calculateEndDate();
    });

    function calculateEndDate() {
      let startDate = $('#start_date').val();
      let numDays = parseInt($('#num_days').val());

      if (!startDate || isNaN(numDays) || numDays <= 0) return;

      let leaveYear = new Date(startDate).getFullYear();

      $.ajax({
        url: 'fetch_holidays.php',
        type: 'POST',
        data: { year: leaveYear },
        dataType: 'json',
        success: function(response) {
          let holidays = response.holidays.map(date => new Date(date).toISOString().split('T')[0]); 
          let validDays = 0;
          let currentDate = new Date(startDate);

          while (validDays < numDays) {
            let dayOfWeek = currentDate.getDay();
            let formattedDate = currentDate.toISOString().split('T')[0];

            if (dayOfWeek !== 5 && !holidays.includes(formattedDate)) {
              validDays++;
            }

            currentDate.setDate(currentDate.getDate() + 1);
          }

          let finalDate = new Date(currentDate);
          $('#end_date').val(finalDate.toISOString().split('T')[0]);
        },
        error: function() {
          alert('Error fetching holidays.');
        }
      });
    }
  });
</script>
</body>
</html>
