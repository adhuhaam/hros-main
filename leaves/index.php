<?php
include '../db.php';
include '../session.php';

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search_query = trim($_GET['search'] ?? "");
$from_date = $_GET['from_date'] ?? "";
$to_date = $_GET['to_date'] ?? "";

// Sorting
$valid_sort_columns = ['emp_no', 'name', 'visa_expiry_date', 'leave_type', 'start_date', 'status'];
$sort_by = in_array($_GET['sort_by'] ?? '', $valid_sort_columns) ? $_GET['sort_by'] : 'start_date';
$sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'asc') ? 'ASC' : 'DESC';

// Status cards
$total_applied = $conn->query("SELECT COUNT(*) AS total FROM leave_records")->fetch_assoc()['total'];
$total_pending = $conn->query("SELECT COUNT(*) AS total FROM leave_records WHERE status = 'Pending'")->fetch_assoc()['total'];
$total_approved = $conn->query("SELECT COUNT(*) AS total FROM leave_records WHERE status = 'Approved'")->fetch_assoc()['total'];

// Build query
$leave_query = "SELECT lr.*, emp.name, emp.emp_no, lt.name AS leave_type, vs.visa_expiry_date 
                FROM leave_records lr 
                JOIN employees emp ON lr.emp_no = emp.emp_no 
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                LEFT JOIN visa_sticker vs ON lr.emp_no = vs.emp_no 
                WHERE 1";

if (!empty($search_query)) {
    $leave_query .= " AND (emp.emp_no LIKE '%$search_query%' OR emp.name LIKE '%$search_query%' OR lt.name LIKE '%$search_query%' OR lr.status LIKE '%$search_query%')";
}

if (!empty($from_date) && !empty($to_date)) {
    $leave_query .= " AND (lr.start_date BETWEEN '$from_date' AND '$to_date')";
}

// Total results
$count_query = str_replace(
    "SELECT lr.*, emp.name, emp.emp_no, lt.name AS leave_type, vs.visa_expiry_date",
    "SELECT COUNT(*) AS count",
    $leave_query
);
$total_results = $conn->query($count_query)->fetch_assoc()['count'];
$total_pages = ceil($total_results / $limit);

// Sorting and pagination
$leave_query .= " ORDER BY $sort_by $sort_dir LIMIT $limit OFFSET $offset";
$leave_result = $conn->query($leave_query);

// Sort function
function sortLink($label, $column, $current_sort, $current_dir, $params) {
    $new_dir = ($current_sort === $column && $current_dir === 'asc') ? 'desc' : 'asc';
    $icon = ($current_sort === $column) ? ($current_dir === 'asc' ? ' ▲' : ' ▼') : '';
    $url = '?' . http_build_query(array_merge($params, ['sort_by' => $column, 'sort_dir' => $new_dir]));
    return "<a href=\"$url\" class=\"text-decoration-none text-primary fw-semibold\">$label$icon</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    th a { color: #0d6efd; }
    th a:hover { text-decoration: underline; }
    .table th, .table td { vertical-align: middle; }
  </style>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar_position="fixed" data-header_position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <?php include '../header.php'; ?>
    <div class="container-fluid" style="max-width:100%;">

      <!-- Filter Form -->
      <form method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-4">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
          </div>
          <div class="col-md-4">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
        </div>
      </form>

      <!-- Status Cards -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5>Total Leave Applications</h5>
              <h3><?= $total_applied ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-warning text-dark">
            <div class="card-body">
              <h5>Pending Leave Requests</h5>
              <h3><?= $total_pending ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5>Approved Leave Requests</h5>
              <h3><?= $total_approved ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Leave Buttons -->
      <div class="mb-3">
        <a href="annual_leave.php" class="btn btn-primary btn-sm">Apply Annual Leave</a>
        <a href="medical_leave.php" class="btn btn-secondary btn-sm">Apply Medical Leave</a>
        <a href="maternity_leave.php" class="btn btn-warning btn-sm">Apply Maternity Leave</a>
        <a href="paternity_leave.php" class="btn btn-info btn-sm">Apply Paternity Leave</a>
        <a href="emergency_leave.php" class="btn btn-danger btn-sm">Apply Emergency Leave</a>
        <a href="no_pay_leave.php" class="btn btn-dark btn-sm">Apply No Pay Leave</a>
        <a href="special_leave.php" class="btn btn-success btn-sm">Apply Special Leave</a>
        <a href="umrah_leave.php" class="btn btn-info btn-sm">Apply Umrah Leave</a>
      </div>

      <!-- Search -->
      <form method="GET" class="mb-4">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Search by Employee Name, Leave Type, or Status"
                 value="<?= htmlspecialchars($search_query) ?>">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>

      <!-- Leave Table -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Manage Leave Requests</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <?= sortLinkHeaders($sort_by, $sort_dir, $_GET) ?>
              <tbody>
              <?php while ($row = $leave_result->fetch_assoc()) { ?>
                <tr>
                  <td><?= htmlspecialchars($row['emp_no']) ?></td>
                  <td class="text-wrap"><?= htmlspecialchars($row['name']) ?></td>
                  <td>
                    <?= $row['visa_expiry_date'] ? date('d-M-Y', strtotime($row['visa_expiry_date'])) : '<span class="badge bg-danger">Not Available</span>' ?>
                  </td>
                  <td><?= htmlspecialchars($row['leave_type']) ?></td>
                  <td><?= date('d-M-Y', strtotime($row['start_date'])) . ' - ' . date('d-M-Y', strtotime($row['end_date'])) ?></td>
                  <td><?= $row['num_days'] ?></td>
                  <td><span class="fw-bold badge bg-secondary p-2"><?= ucfirst($row['status']) ?></span></td>
                  <td>
                    <a href="upload_files.php?leave_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Upload</a>
                    <a href="leave_form_s.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Form</a>
                    <a href="view_leave.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                    <a href="mark_arrival.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Mark Arrival</a> -
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
                    <a href="delete_leave.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                    
                  </td>
                  <td>
                    <?php if (!empty($row['departure_ticket_id']) || !empty($row['arrival_ticket_id'])) { ?>
                      <a href="view_leave.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View <i class="fa-solid fa-plane-up"></i></a>
                    <?php } else { ?>
                      <span class="badge bg-secondary">Not Ticket</span>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav>
            <ul class="pagination justify-content-center">
              <?php if ($page > 1) { ?>
                <li class="page-item">
                  <a class="page-link" href="<?= paginationLink($page - 1) ?>">Previous</a>
                </li>
              <?php } ?>
              <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="<?= paginationLink($i) ?>"><?= $i ?></a>
                </li>
              <?php } ?>
              <?php if ($page < $total_pages) { ?>
                <li class="page-item">
                  <a class="page-link" href="<?= paginationLink($page + 1) ?>">Next</a>
                </li>
              <?php } ?>
            </ul>
          </nav>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>

<?php
// Helper functions
function paginationLink($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
function sortLinkHeaders($sort_by, $sort_dir, $params) {
    $columns = ['emp_no' => 'Emp No', 'name' => 'Employee', 'visa_expiry_date' => 'Visa Expiry', 'leave_type' => 'Leave Type', 'start_date' => 'Start & End Date', 'status' => 'Status'];
    $html = '<thead class="table-light"><tr>';
    foreach ($columns as $key => $label) {
        $html .= "<th>" . sortLink($label, $key, $sort_by, $sort_dir, $params) . "</th>";
    }
    $html .= '<th>Days</th><th>Actions</th><th>Ticket</th></tr></thead>';
    return $html;
}
?>
