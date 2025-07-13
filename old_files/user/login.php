<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($conn->real_escape_string($_POST['username']));
    $password = trim($conn->real_escape_string($_POST['password']));

    // Fetch user and role details
    $sql = "SELECT u.id AS user_id, u.username, u.password, r.role_name 
            FROM users u 
            INNER JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];

            // Redirect based on role
            switch ($user['role_name']) {
                case 'HR Manager':
                    header('Location: hrm/hrm_dashboard.php');
                    break;
                case 'hod':
                    header('Location: hod/dashboard_hod.php');
                    break;
                case 'director':
                    header('Location: director/dashboard_director.php');
                    break;
                default:
                    header('Location: unauthorized.php');
                    break;
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="../../assets/css/styles.min.css">
</head>
<body>
    <div class="page-wrapper d-flex justify-content-center align-items-center min-vh-100">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center">Login</h3>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
