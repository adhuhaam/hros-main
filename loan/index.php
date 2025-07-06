<?php
include '../db.php';
include '../session.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $deleteSql = "DELETE FROM salary_loans WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            } else {
                $error = "Failed to delete record.";
            }
        } else {
            $error = "Failed to prepare the delete statement.";
        }
    } else {
        $error = "Invalid ID provided for deletion.";
    }
}

// Pagination and Search Handling
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 30;
$offset = ($page - 1) * $limit;

$whereClause = "WHERE e.name LIKE '%$search%' OR sl.emp_no LIKE '%$search%'";
$queryCount = "SELECT COUNT(*) as total FROM salary_loans sl JOIN employees e ON sl.emp_no = e.emp_no $whereClause";
$resultCount = $conn->query($queryCount);
$totalRows = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query = "SELECT sl.*, e.name AS employee_name, e.designation FROM salary_loans sl 
          JOIN employees e ON sl.emp_no = e.emp_no $whereClause LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Salary Loans/Advances</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
        <?php include '../sidebar.php'; ?>
    </aside>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="add_loan.php" class="btn btn-primary">Add New Request</a>
          </div>
        </nav>
      </header>
      <div class="container-fluid" style="max-width: 100%;">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4"><i class="fa-solid fa-hand-holding-dollar"> &nbsp;</i>  Salary Loans/Advances</h5>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"> <?php echo $error; ?> </div>
            <?php endif; ?>
            <form method="GET" class="mb-3">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Employee Name or ID" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </form>
            <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Employee No</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Amount</th>
                  <th>Purpose</th>
                  <th>Currency</th>
                  <th>Applied Date</th>
                  <th>Approved Date</th>
                  <th>Received</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['emp_no']; ?></td>
                  <td><?php echo $row['employee_name']; ?></td>
                  <td><?php echo $row['designation']; ?></td>
                  <td><?php echo $row['amount']; ?></td>
                  <td><?php echo $row['purpose']; ?></td>
                  <td><?php echo $row['currency']; ?></td>
                  <td><?php echo date('d-M-Y', strtotime($row['applied_date'])); ?></td>
                  <td><?php echo $row['approved_date'] ? date('d-M-Y', strtotime($row['approved_date'])) : 'N/A'; ?></td>
                  <td>
                    <input type="checkbox" disabled <?php echo $row['received'] ? 'checked' : ''; ?> />
                  </td>
                  <td>
                    <span class="badge <?php 
                      switch($row['status']) {
                        case 'Pending': echo 'bg-warning'; break;
                        case 'Approved': echo 'bg-success'; break;
                        case 'Rejected': echo 'bg-danger'; break;
                        default: echo 'bg-secondary';
                      }
                    ?>">
                      <?php echo $row['status']; ?>
                    </span>
                  </td>
                  <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="view_loan.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">View</a>
                    <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Print Form</a>
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <nav>
              <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                  <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
              </ul>
            </nav>
            <?php else: ?>
            <p>No records found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
