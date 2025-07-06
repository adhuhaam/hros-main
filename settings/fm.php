<?php
include '../db.php';


// Get data
$sql = "
  SELECT e.emp_no, e.name, e.passport_nic_no, e.passport_nic_no_expires,
         w.visa_number, w.visa_issue_date, w.visa_expiry_date
  FROM employees e
  LEFT JOIN work_visa w ON e.emp_no = w.emp_no
  WHERE e.employment_status = 'Active'
  ORDER BY e.name ASC
";
$result = $conn->query($sql);

// Stats
$rows = [];
$visaPresent = 0;
$visaMissing = 0;

while ($row = $result->fetch_assoc()) {
  $rows[] = $row;
  if (empty($row['visa_number'])) {
    $visaMissing++;
  } else {
    $visaPresent++;
  }
}
$total = count($rows);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Active Employees Visa Info</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    .status-card:hover { cursor: pointer; opacity: 0.9; }
    .status-card.border-highlight { border: 3px solid #fff !important; }
    .table td, .table th { vertical-align: middle; }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-navbarbg="skin6"
       >

    <div class="body-wrapper">
      <div class="container-fluid" style="max-width:100%;">

        <!-- Stat Cards -->
        <div class="row mb-3 text-center">
          <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm">
              <div class="card-body">
                <h6>Total Active Employees</h6>
                <span class="badge bg-dark"><?= $total ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm">
              <div class="card-body">
                <h6>Updated</h6>
                <span class="badge bg-primary"><?= $visaPresent ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm">
              <div class="card-body">
                <h6>To Update</h6>
                <span class="badge bg-danger"><?= $visaMissing ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter -->
        <div class="mb-3">
          <label for="visaFilter" class="form-label fw-semibold">Filter Visa Status:</label>
          <select id="visaFilter" class="form-select w-auto">
            <option value="">Show All</option>
            <option value="missing">Visa Number is NULL</option>
            <option value="present">Visa Number is Present</option>
          </select>
        </div>

        <!-- Table Card -->
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Active Employee Visa Records</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="empTable" class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                  <tr>
                    <th>Emp No</th>
                    <th>Name</th>
                    <th>Passport No</th>
                    <th>Passport Expiry</th>
                    <th>Visa No</th>
                    <th>Visa Issue</th>
                    <th>Visa Expiry</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rows as $row): ?>
                    <tr data-visa="<?= empty($row['visa_number']) ? 'missing' : 'present' ?>">
                      <td><?= htmlspecialchars($row['emp_no']) ?></td>
                      <td><?= htmlspecialchars($row['name']) ?></td>
                      <td><?= htmlspecialchars($row['passport_nic_no']) ?></td>
                      <td><?= htmlspecialchars($row['passport_nic_no_expires']) ?></td>
                      <td class="<?= empty($row['visa_number']) ? 'text-danger fw-bold' : 'text-primary fw-semibold' ?>">
                        <?= htmlspecialchars($row['visa_number'] ?: 'N/A') ?>
                      </td>
                      <td><?= htmlspecialchars($row['visa_issue_date']) ?></td>
                      <td><?= htmlspecialchars($row['visa_expiry_date']) ?></td>
                      
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  $(document).ready(function() {
    const table = $('#empTable').DataTable({ pageLength: 25 });

    $('#visaFilter').on('change', function () {
      let value = $(this).val();
      table.rows().every(function() {
        let row = $(this.node());
        let visaStatus = row.data('visa');
        if (value === '' || visaStatus === value) {
          row.show();
        } else {
          row.hide();
        }
      });
    });
  });
  </script>
</body>
</html>
