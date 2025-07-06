<?php
include '../db.php';
include '../session.php';


$imported = $_GET['imported'] ?? null;
$skipped = $_GET['skipped'] ?? null;

if ($imported !== null || $skipped !== null): ?>
  <div class="alert alert-success alert-dismissible fade show mx-4 mt-4" role="alert">
    ✅ <strong>Import Complete:</strong>
    <?= (int)$imported ?> record(s) imported,
    <?= (int)$skipped ?> skipped.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif;

// Fetch all records
$query = "
  SELECT a.*, e.name 
  FROM attendance_records a 
  LEFT JOIN employees e ON a.emp_no = e.emp_no 
  ORDER BY a.year DESC, a.month DESC, a.day DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Records</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css"> <!-- Use your theme's CSS -->
  <style>
    .table th, .table td {
      vertical-align: middle;
      text-align: center;
    }
    .search-box {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <div class="container-fluid" style="max-width:100%;">
        <div class="card">
          <div class="card-body">
            <h4 class="fw-semibold mb-4">All Attendance Records</h4>
            <a class="btn btn-primary mb-2" href="upload.php">upload</a>

            <!-- Search Bar -->
            <input type="text" id="searchInput" class="form-control search-box" placeholder="Search by name, emp_no, site, etc.">

            <!-- Records Table -->
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Emp No</th>
                    <th>Name</th>
                    <th>Day</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Work In</th>
                    <th>Work Out</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="recordsTable">
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['emp_no']) ?></td>
                      <td><?= htmlspecialchars($row['name']) ?></td>
                      <td><?= htmlspecialchars($row['day']) ?></td>
                      <td><?= htmlspecialchars($row['month']) ?></td>
                      <td><?= htmlspecialchars($row['year']) ?></td>
                      <td><?= htmlspecialchars($row['work_in']) ?></td>
                      <td><?= htmlspecialchars($row['work_out']) ?></td>
                      <td><?= htmlspecialchars($row['status']) ?></td>
                      <td>
                        <a href="edit_record.php?id=<?= $row['record_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_record.php?id=<?= $row['record_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
                      </td>
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

  <script>
    // Simple search filter
    document.getElementById('searchInput').addEventListener('keyup', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#recordsTable tr');
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
