<?php
include '../db.php';
include '../session.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$start = ($page - 1) * $limit;

$totalQuery = "SELECT COUNT(*) AS total FROM employee_documents WHERE emp_no LIKE '%$search%' OR doc_type LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

$query = "SELECT * FROM employee_documents 
          WHERE emp_no LIKE '%$search%' OR doc_type LIKE '%$search%'
          ORDER BY uploaded_at DESC LIMIT $start, $limit";
$results = $conn->query($query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Employee Documents Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper" >
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end">
            <a href="add.php" class="btn btn-primary ms-3">Add New Document</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid" style="max-width:100%;">
        <div class="card mt-2">
          <div class="card-body">
            <h5 class="card-title fw-semibold"><i class="fa-solid fa-file fs-5"></i>&nbsp; Employee Documents</h5>

            <form method="GET" class="mb-4">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Employee No or Document Type" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
              </div>
            </form>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Employee No</th>
                  <th>Type</th>
                  <th>Photo</th>
                  <th>Front</th>
                  <th>Back</th>
                  <th>Uploaded</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['emp_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['doc_type']); ?></td>
                    <td>
                      <?php if (!empty($row['photo_file_name'])): ?>
                        <a href="../assets/document/<?php echo $row['photo_file_name']; ?>" target="_blank">View</a>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($row['front_file_name'])): ?>
                        <a href="../assets/document/<?php echo $row['front_file_name']; ?>" target="_blank">View</a>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($row['back_file_name'])): ?>
                        <a href="../assets/document/<?php echo $row['back_file_name']; ?>" target="_blank">View</a>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                    <td><?php echo date("d-M-Y H:i", strtotime($row['uploaded_at'])); ?></td>
                    <td>
                      <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                      <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

                     <!-- Pagination -->
            <nav>
              <ul class="pagination">
                <?php
                $range = 2; // Show 2 pages before and after current
                $startPage = max(1, $page - $range);
                $endPage = min($totalPages, $page + $range);
            
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '">&laquo; First</a></li>';
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&search=' . urlencode($search) . '">&lsaquo; Prev</a></li>';
                }
            
                for ($i = $startPage; $i <= $endPage; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
                }
            
                if ($page < $totalPages) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next &rsaquo;</a></li>';
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '">Last &raquo;</a></li>';
                }
                ?>
              </ul>
            </nav>


          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
