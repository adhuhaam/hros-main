<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
  die('Invalid request');
}

$id = $_GET['id'];
$error = '';
$success = '';

$entry = $conn->query("SELECT mg.*, e.name FROM mailing_group mg JOIN employees e ON mg.emp_no = e.emp_no WHERE mg.id = '$id' LIMIT 1");

if ($entry->num_rows === 0) {
  die("Entry not found.");
}

$data = $entry->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tags = $_POST['tags'];
  $status = $_POST['status'];

  $update = $conn->prepare("UPDATE mailing_group SET tags = ?, status = ? WHERE id = ?");
  $update->bind_param("ssi", $tags, $status, $id);

  if ($update->execute()) {
    $success = "Mail group entry updated successfully.";
    header('Location: index.php');
    exit;
  } else {
    $error = "Error: " . $conn->error;
  }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Mailing Recipient</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-center">
            <a href="index.php" class="btn btn-secondary">← Back</a>
          </div>
        </nav>
      </header>

      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Mailing Group Recipient</h5>

            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="bg-light p-4 rounded shadow-sm">
              <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" readonly class="form-control" value="<?= $data['name'] ?> (<?= $data['emp_no'] ?>)">
              </div>

              <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" id="tags" class="form-control" value="<?= htmlspecialchars($data['tags']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                  <option value="Active" <?= $data['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= $data['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>

              <button type="submit" class="btn btn-success w-100">Update Recipient</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
