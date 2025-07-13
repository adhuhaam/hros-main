<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch project details
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    die('Project not found.');
}

$project = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $project_value = floatval($_POST['project_value']);
    $client = $conn->real_escape_string($_POST['client']);
    $started_date = $conn->real_escape_string($_POST['started_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $status = $conn->real_escape_string($_POST['status']);
    $description = $conn->real_escape_string($_POST['description']);

    // Handle image uploads
    $uploaded_images = json_decode($project['images'], true) ?? [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
            $target_file = '../assets/images/projects/' . $file_name;

            // Validate image type and size
            $file_type = mime_content_type($tmp_name);
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Only JPG, PNG, and GIF files are allowed.';
                break;
            }

            if ($_FILES['images']['size'][$key] > 10 * 1024 * 1024) { // 10MB limit
                $error = 'Each image must not exceed 10MB.';
                break;
            }

            // Move file to target directory
            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_images[] = $file_name;
            } else {
                $error = 'Error uploading files. Please check permissions.';
                break;
            }
        }
    }

    if (empty($error)) {
        $images_json = json_encode($uploaded_images);

        // Update project in the database
        $update_query = "UPDATE projects 
                         SET name = '$name', project_value = '$project_value', client = '$client',
                             started_date = '$started_date', end_date = '$end_date', status = '$status',
                             images = '$images_json', description = '$description' 
                         WHERE id = $project_id";

        if ($conn->query($update_query)) {
            $success = 'Project updated successfully!';
            // Reload updated project details
            $project = array_merge($project, [
                'name' => $name,
                'project_value' => $project_value,
                'client' => $client,
                'started_date' => $started_date,
                'end_date' => $end_date,
                'status' => $status,
                'images' => $images_json,
                'description' => $description
            ]);
        } else {
            $error = 'Error: ' . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Project</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <style>
    body {
      background: #f3f4f6;
    }
    .form-container {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
      background-color: #5e72e4;
      border: none;
    }
  </style>
</head>

<body>
  <!-- Page Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php include '../header.php'; ?>
      <!-- End Header -->

      <div class="container-fluid">
        <div class="form-container mx-auto my-5">
          <h1 class="text-center">Edit Project</h1>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="name" class="form-label">Project Name</label>
              <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($project['name']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="project_value" class="form-label">Project Value</label>
              <input type="number" class="form-control" id="project_value" name="project_value" value="<?php echo htmlspecialchars($project['project_value']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="client" class="form-label">Client</label>
              <input type="text" class="form-control" id="client" name="client" value="<?php echo htmlspecialchars($project['client']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="started_date" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="started_date" name="started_date" value="<?php echo htmlspecialchars($project['started_date']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-control" id="status" name="status" required>
                <option value="Active" <?php echo $project['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                <option value="Completed" <?php echo $project['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="On Hold" <?php echo $project['status'] == 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="images" class="form-label">Project Images (Max 5)</label>
              <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
              <small>Uploaded Images:</small>
              <ul>
                <?php
                $images = json_decode($project['images'], true);
                if (!empty($images)) {
                    foreach ($images as $image) {
                        echo "<img width='95px' src='../assets/images/projects/$image' target='_blank'> - </img>";
                    }
                } else {
                    echo "<li>No images uploaded.</li>";
                }
                ?>
              </ul>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Project</button>
          </form>
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
