<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($conn->real_escape_string($_POST['username']));
    $password = trim($conn->real_escape_string($_POST['password']));

    $sql = "SELECT u.id, u.username, u.password, r.role_name 
            FROM users u 
            INNER JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];

            switch ($user['role_name']) {
                case 'Admin': header('Location: ../admin_dashboard.php'); break;
                case 'Information Officer': header('Location: ../info_officer_dashboard.php'); break;
                case 'Xpat Officer': header('Location: ../xpat_officer_dashboard.php'); break;
                case 'Leave Officer': header('Location: ../leave_officer_dashboard.php'); break;
                case 'HR Manager': header('Location: ../user/hrm/hrm_dashboard.php'); break;
                case 'hod': header('Location: ../user/hod/hod_dashboard.php'); break;
                case 'director': header('Location: ../user/director/director_dashboard.php'); break;
                case 'Payroll Officer': header('Location: ../payroll_dashboard.php'); break;
                case 'Other Staff': header('Location: ../other_dashboard.php'); break;
                case 'Supervisor': header('Location: ../supervisor_dashboard.php'); break;
                case 'reception': header('Location: ../reception_dashboard.php'); break;
                default: header('Location: ../other_dashboard.php'); break;
            }
            exit();
        } else {
            $error = "Invalid Employee No or password!";
        }
    } else {
        $error = "Invalid Employee No or password!";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - HROS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="assets/images/logos/favicon.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#006bad',
          }
        }
      }
    }
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
      <img src="assets/images/logos/dark-logo.svg" alt="HROS Logo" class="w-32">
        <button id="toggleTheme" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-full text-gray-800 dark:text-white">
          <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M6.05 17.95l-1.414 1.414M18.364 18.364l-1.414-1.414M6.05 6.05L4.636 7.464M12 8a4 4 0 100 8 4 4 0 000-8z" />
          </svg>
          <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M17.293 13.293a8 8 0 01-10.586-10.586A8 8 0 1017.293 13.293z" />
          </svg>
        </button>

    </div>

    <p class="text-center text-sm text-gray-500 dark:text-gray-300 mb-4">
      Welcome to HROS - Login with your credentials
    </p>

    <?php if (isset($error)): ?>
      <div class="bg-red-100 text-red-700 text-sm p-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium">Username</label>
        <input type="text" id="username" name="username" required
               class="mt-1 block w-full px-4 py-2 border rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium">Password</label>
        <input type="password" id="password" name="password" required
               class="mt-1 block w-full px-4 py-2 border rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <button type="submit"
              class="w-full bg-primary hover:bg-blue-700 text-white py-2 rounded-md font-semibold text-lg transition">
        Sign In
      </button>
    </form>

    <div class="flex justify-between mt-4 text-sm text-gray-500 dark:text-gray-300">
      <a href="forgot_password.php" class="hover:underline text-primary">Forgot Password?</a>
      <a href="index.php" class="hover:underline text-primary">Back Home</a>
    </div>
  </div>

  <!-- Dark Mode Toggle Logic -->
  <script>
  const toggle = document.getElementById('toggleTheme');
  const sunIcon = document.getElementById('icon-sun');
  const moonIcon = document.getElementById('icon-moon');

  // Init based on saved preference
  if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    sunIcon.classList.remove('hidden');
  } else {
    moonIcon.classList.remove('hidden');
  }

  toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    sunIcon.classList.toggle('hidden', !isDark);
    moonIcon.classList.toggle('hidden', isDark);
  });
</script>

</body>
</html>
