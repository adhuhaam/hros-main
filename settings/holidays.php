<?php
session_start();
include '../db.php';
include '../session.php';


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_holiday'])) {
    $holiday_name = $conn->real_escape_string($_POST['holiday_name']);
    $holiday_date = $conn->real_escape_string($_POST['holiday_date']);

    $sql = "INSERT INTO holidays (holiday_name, holiday_date) VALUES ('$holiday_name', '$holiday_date')";
    if ($conn->query($sql)) {
      $_SESSION['message'] = "Holiday added successfully!";
    } else {
      $_SESSION['error'] = "Error: " . $conn->error;
    }
  }

  if (isset($_POST['update_holiday'])) {
    $holiday_id = intval($_POST['holiday_id']);
    $holiday_name = $conn->real_escape_string($_POST['holiday_name']);
    $holiday_date = $conn->real_escape_string($_POST['holiday_date']);

    $sql = "UPDATE holidays SET holiday_name = '$holiday_name', holiday_date = '$holiday_date' WHERE id = $holiday_id";
    if ($conn->query($sql)) {
      $_SESSION['message'] = "Holiday updated successfully!";
    } else {
      $_SESSION['error'] = "Error: " . $conn->error;
    }
  }

  if (isset($_POST['delete_holiday'])) {
    $holiday_id = intval($_POST['holiday_id']);

    $sql = "DELETE FROM holidays WHERE id = $holiday_id";
    if ($conn->query($sql)) {
      $_SESSION['message'] = "Holiday deleted successfully!";
    } else {
      $_SESSION['error'] = "Error: " . $conn->error;
    }
  }
}

// Fetch holidays from the database
$holidays = $conn->query("SELECT * FROM holidays ORDER BY holiday_date ASC");
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Holidays</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Manage Holidays</h5>

            <?php if (isset($_SESSION['message'])): ?>
              <div class="alert alert-success">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?>
              </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
              </div>
            <?php endif; ?>

            <!-- Add Holiday Form -->
            <form method="POST" class="mb-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="holiday_name" class="form-label">Holiday Name</label>
                    <input type="text" id="holiday_name" name="holiday_name" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="holiday_date" class="form-label">Holiday Date</label>
                    <input type="date" id="holiday_date" name="holiday_date" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <button type="submit" name="add_holiday" class="btn btn-primary w-100 mt-4">Add Holiday</button>
                </div>
              </div>
            </form>

            <!-- Holidays Table -->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Holiday Name</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($holidays->num_rows > 0): ?>
                    <?php while ($holiday = $holidays->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo $holiday['id']; ?></td>
                        <td><?php echo htmlspecialchars($holiday['holiday_name']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($holiday['holiday_date'])); ?></td>
                        <td>
                          <!-- Edit Button -->
                          <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editModal<?php echo $holiday['id']; ?>">Edit</button>

                          <!-- Delete Button -->
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="holiday_id" value="<?php echo $holiday['id']; ?>">
                            <button type="submit" name="delete_holiday" class="btn btn-danger btn-sm">Delete</button>
                          </form>

                          <!-- Edit Modal -->
                          <div class="modal fade" id="editModal<?php echo $holiday['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $holiday['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="editModalLabel<?php echo $holiday['id']; ?>">Edit Holiday</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <form method="POST">
                                  <div class="modal-body">
                                    <input type="hidden" name="holiday_id" value="<?php echo $holiday['id']; ?>">
                                    <div class="mb-3">
                                      <label for="holiday_name" class="form-label">Holiday Name</label>
                                      <input type="text" name="holiday_name" class="form-control"
                                        value="<?php echo htmlspecialchars($holiday['holiday_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                      <label for="holiday_date" class="form-label">Holiday Date</label>
                                      <input type="date" name="holiday_date" class="form-control"
                                        value="<?php echo $holiday['holiday_date']; ?>" required>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_holiday" class="btn btn-primary">Save
                                      Changes</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center">No holidays found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

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