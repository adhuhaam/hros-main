<?php
include '../db.php';
include '../session.php';

// Status list and colors
$statuses = ['Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Completed', 'Expired', 'Expiring Soon'];
$badgeColors = [
  'Pending' => 'secondary',
  'Pending Approval' => 'warning',
  'Ready for Submission' => 'info',
  'Ready for Collection' => 'success',
  'Completed' => 'success',
  'Expiring Soon' => 'danger',
  'Expired' => 'danger'
];


// Count per status
$statusCounts = [];
foreach ($statuses as $status) {
  $stmt = $conn->prepare("
    SELECT COUNT(*) AS count 
    FROM work_visa w
    JOIN employees e ON e.emp_no = w.emp_no
    WHERE w.visa_status = ? AND e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
  ");
  $stmt->bind_param("s", $status);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $statusCounts[$status] = $res['count'];
}

// Expiry cards
$exp3 = $conn->query("
  SELECT COUNT(*) AS total
  FROM work_visa w
  JOIN employees e ON e.emp_no = w.emp_no
  WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
    AND w.visa_expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'];

$exp6 = $conn->query("
  SELECT COUNT(*) AS total
  FROM work_visa w
  JOIN employees e ON e.emp_no = w.emp_no
  WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
    AND w.visa_expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
")->fetch_assoc()['total'];

// Main query
$result = $conn->query("
  SELECT e.emp_no, e.name, e.passport_nic_no , w.visa_number, w.visa_expiry_date, w.visa_status
  FROM employees e
  LEFT JOIN work_visa w ON e.emp_no = w.emp_no
  WHERE e.employment_status = 'Active' AND e.nationality != 'MALDIVIAN'
  ORDER BY e.name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Work Visa Management</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .status-card:hover { cursor: pointer; opacity: 0.9; }
    .status-card.border-highlight { border: 3px solid #fff !important; }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
       data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <div class="container-fluid" style="max-width:100%;">

        <!-- Status Cards -->
        <div class="row ">
          
          <!-- All card -->
            <div class="col-1">
              <div class="card  bg-light card-outline-primary status-card" data-status="__all__">
                <div class="card-body">
                  <h6 class="card-title text-dark">All</h6>
                  <h5 class="card-text text-dark"><?= array_sum($statusCounts) ?></h5>
                </div>
              </div>
            </div>

          <!-- Status cards -->
            <?php foreach ($statuses as $status): ?>
              <div class="col-sm-2">
                <div class="card text-white bg-<?= $badgeColors[$status] ?> status-card" data-status="<?= $status ?>">
                  <div class="card-body">
                    <h6 class="card-title"><?= $status ?></h6>
                    <h5 class="card-text"><?= $statusCounts[$status] ?? 0 ?></h5>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            
            <!-- Expiry 3-month -->
            <div class="col-sm-3">
              <div class="card text-bg-dark mb-2">
                <div class="card-body">
                  <h6 class="card-title text-light">Expiring in 3 Months</h6>
                  <h5 class="card-text text-light"><?= $exp3 ?></h5>
                </div>
              </div>
            </div>
            
            <!-- Expiry 6-month -->
            <div class="col-sm-3">
              <div class="card text-bg-dark mb-2">
                <div class="card-body">
                  <h6 class="card-title text-light">Expiring in 6 Months</h6>
                  <h5 class="card-text text-light"><?= $exp6 ?></h5>
                </div>
              </div>
            </div>
            
           </div>
           
        <!-- Visa Table -->
        <div class="card">
          <div class="card-header"><h5>Work Visa Records</h5></div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="visaTable" class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th>Emp No</th>
                    <th>Name</th>
                    <th>Visa Number</th>
                    <th>pp Number</th>
                    <th> Visa Expiry Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['emp_no'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['visa_number'] ?? '<span class="text-muted">N/A</span>' ?></td>
                    <td><?= $row['passport_nic_no'] ?? '<span class="text-muted">N/A</span>' ?></td>
                    <td><?= $row['visa_expiry_date'] ? date('d-M-Y', strtotime($row['visa_expiry_date'])) : '<span class="text-muted">N/A</span>' ?></td>
                    <td><span class="badge bg-<?= $badgeColors[$row['visa_status']] ?? 'dark' ?>"><?= $row['visa_status'] ?></span></td>
                    <td><a href="update.php?emp_no=<?= $row['emp_no'] ?>" class="btn btn-sm btn-info">Edit</a></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
  $(function () {
    const table = $('#visaTable').DataTable({ pageLength: 25 });
    let lastClicked = null;

    $('.status-card').on('click', function () {
      const status = $(this).data('status');

      $('.status-card').removeClass('border-highlight');

      if (status === '__all__') {
        table.column(5).search('').draw(); // Status column index
        lastClicked = null;
        $(this).addClass('border-highlight');
        return;
      }

      if (lastClicked === status) {
        table.column(5).search('').draw();
        lastClicked = null;
      } else {
        table.column(5).search(status).draw();
        lastClicked = status;
        $(this).addClass('border-highlight');
      }
    });
  });
</script>
           
                
</body>
</html>
