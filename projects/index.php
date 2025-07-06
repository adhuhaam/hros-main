<?php
include '../db.php';
include '../session.php';

// Initialize search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $query = "SELECT * FROM projects WHERE name LIKE '%$search_query%' OR client LIKE '%$search_query%'";
} else {
    $query = "SELECT * FROM projects";
}

// Fetch projects from the database
$result = $conn->query($query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Projects</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    .lead {
      color: #aaa;
    }

    .wrapper {
      margin: 10vh;
    }

    .card {
      border: none;
      transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
      overflow: hidden;
      border-radius: 20px;
      min-height: 200px;
      box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);
    }

    .card:hover {
      transform: scale(1.08);
      box-shadow: 0 0 5px -2px rgba(0, 0, 0, 0.3);
    }

    .card-img-overlay {
      background: rgb(255, 186, 33);
      background: linear-gradient(0deg, rgba(255, 186, 33, 0.5) 0%, rgba(255, 186, 33, 1) 100%);
      transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
    }
  </style>
</head>

<body>
  <!-- Page Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?> <!-- Sidebar included -->
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php include '../header.php'; ?>
      <!-- End Header -->

      <div class="container-fluid">
        <h1 class="text-center my-4">
          <a href="add.php" class="btn btn-primary">+</a> Projects
        </h1>

        <!-- Search Bar -->
        <form method="GET" action="" class="mb-4">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or client" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
          </div>
        </form>

        <!-- Projects -->
        <div class="row">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                  <img src="../assets/images/projects/<?php echo json_decode($row['images'])[0] ?? 'default.jpg'; ?>" alt="Project Image" class="card-img-top card-img">
                  <div class="card-body">
                    <h6 class="card-title fw-medium text-primary" style="font-size: 12px;"><?php echo htmlspecialchars($row['name']); ?></h6>
                    <p class="card-text fw-lighter" style="font-size: 11px;">Client: <?php echo htmlspecialchars($row['client']); ?><br>
                    Status: <?php echo htmlspecialchars($row['status']); ?><br>
                    <p class="card-text fw-lighter" style="font-size: 8px;"><?php echo date("d-M-Y", strtotime($row['started_date'])) . " to " . date("d-M-Y", strtotime($row['end_date'])); ?></p>
                    <a href="details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View Project</a>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-pen"></i></a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ey Hama yageen tha?');"><i class="fa-solid fa-trash"></i></a>

                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-12">
              <p class="text-center">No projects found.</p>
            </div>
          <?php endif; ?>
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
