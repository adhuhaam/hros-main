<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy session if exists
    session_unset();
    session_destroy();

    // Redirect to login page
    header('Location: ../login.php');
    exit();
}


// Define session timeout duration (3 hours in seconds)
$session_timeout = 30 * 60; // 1800 seconds

// Check if session is already set
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];

    // If elapsed time exceeds timeout, destroy the session and redirect to login
    if ($elapsed_time > $session_timeout) {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header("Location: ../login.php?timeout=1");
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();


?>