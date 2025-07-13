<?php
include 'db.php'; // Database connection

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch user details
$sql = "SELECT staff_name, des, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_name = trim($_POST['staff_name']);
    $staff_des = trim($_POST['des']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($staff_name) || empty($email)) {
        $message = "Name and email are required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET staff_name = ?, des = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $staff_name, $staff_des, $email, $hashed_password, $user_id);
        } else {
            $update_sql = "UPDATE users SET staff_name = ?, des = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $staff_name, $staff_des, $email, $user_id);
        }

        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
            $user['staff_name'] = htmlspecialchars($staff_name);
            $user['des'] = htmlspecialchars($staff_des);
            $user['email'] = htmlspecialchars($email);
        } else {
            $message = "Failed to update profile: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="body-wrapper">
       <?php include 'header.php'; ?>
      <div class="container-fluid">
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Profile</h5>

            <?php if (!empty($message)): ?>
              <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" class="shadow p-4 bg-light rounded">
              <div class="mb-3">
                <label for="staff_name" class="form-label">Name:</label>
                <input type="text" name="staff_name" id="staff_name" required class="form-control" value="<?php echo htmlspecialchars($user['staff_name']); ?>">
              </div>
              <div class="mb-3">
                <label for="des" class="form-label">Designation:</label>
                <input type="text" name="des" id="des" required class="form-control" value="<?php echo htmlspecialchars($user['des']); ?>">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" required class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current password">
              </div>
              <button type="submit" class="btn btn-success w-100">Update Profile</button>
            </form>
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
