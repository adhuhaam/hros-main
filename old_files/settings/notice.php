<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Add or update notice
  if (isset($_POST['save_notice'])) {
    $id = $_POST['id'] ?? null;
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if ($id) {
      $sql = "UPDATE notices SET title = '$title', content = '$content' WHERE id = $id";
    } else {
      $sql = "INSERT INTO notices (title, content) VALUES ('$title', '$content')";
    }
    $conn->query($sql);
    header('Location: notice.php');
    exit();
  }

  // Delete notice
  if (isset($_POST['delete_notice'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM notices WHERE id = $id";
    $conn->query($sql);
    header('Location: notice.php');
    exit();
  }
}

// Fetch notices
$notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Notices</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
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
            <div class="container mt-5">
              <h1 class="text-center">Manage Notices</h1>
              <!-- Add/Edit Notice Form -->
              <form action="notice.php" method="POST" class="mb-4">
                <input type="hidden" name="id" id="notice_id">
                <div class="mb-3">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="content" class="form-label">Content</label>
                  <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="save_notice" class="btn btn-primary">Save Notice</button>
              </form>

              <!-- Notices Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $notices->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['title']); ?></td>
                      <td><?php echo htmlspecialchars($row['content']); ?></td>
                      <td>
                        <button class="btn btn-warning btn-sm"
                          onclick="editNotice(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <form action="notice.php" method="POST" class="d-inline">
                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <button type="submit" name="delete_notice" class="btn btn-danger btn-sm">Delete</button>
                        </form>
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
    function editNotice(notice) {
      document.getElementById('notice_id').value = notice.id;
      document.getElementById('title').value = notice.title;
      document.getElementById('content').value = notice.content;
    }
  </script>
</body>

</html>