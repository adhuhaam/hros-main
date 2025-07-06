<?php
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Input validation
    if (empty($emp_no) || empty($username) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if emp_no exists in the employees table
        $empCheckSql = "SELECT emp_no FROM employees WHERE emp_no = ?";
        $stmt = $conn->prepare($empCheckSql);
        $stmt->bind_param("s", $emp_no);
        $stmt->execute();
        $empResult = $stmt->get_result();

        if ($empResult->num_rows === 0) {
            $error = "Employee number does not exist in the employees table.";
        } else {
            // Check if username already exists in employee_login table
            $userCheckSql = "SELECT username FROM employee_login WHERE username = ?";
            $stmt = $conn->prepare($userCheckSql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $userResult = $stmt->get_result();

            if ($userResult->num_rows > 0) {
                $error = "Username is already taken.";
            } else {
                // Insert into employee_login table
                $insertSql = "INSERT INTO employee_login (emp_no, username, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insertSql);
                $stmt->bind_param("sss", $emp_no, $username, $hashed_password);

                if ($stmt->execute()) {
                    $success = "User registered successfully.";
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Register User</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="emp_no" class="form-label">Employee Number</label>
                <input type="text" class="form-control" id="emp_no" name="emp_no" placeholder="Enter Employee Number" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</body>
</html>
