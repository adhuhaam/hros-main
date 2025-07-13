<?php
include '../db.php';
include '../session.php';

// Path to the error log file (adjust this path as needed)
$log_file = 'error_log'; // For Apache (Linux)
// $log_file = '/var/log/nginx/error.log'; // For NGINX
// $log_file = 'C:/xampp/apache/logs/error.log'; // For XAMPP (Windows)

// Read the last 1000 lines of the log file
$logs = '';
if (file_exists($log_file)) {
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $last_lines = array_slice($lines, -1000); // Display last 1000 lines

    foreach ($last_lines as $line) {
        $logs .= htmlspecialchars($line) . "\n"; // Sanitize for security
    }
} else {
    $logs = "Error log file not found.";
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error Log Viewer</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
 <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
        <aside class="left-sidebar">
            <?php include '../sidebar.php'; ?>
        </aside>

        <div class="body-wrapper">
            <?php include '../header.php'; ?>

            <div class="container-fluid">
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title fw-semibold text-primary mb-4">
                            <i class="fa fa-exclamation-triangle text-danger"></i> Server Error Log - medical module
                        </h4>

                        <div class="log-container" style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px; max-height: 500px; overflow-y: scroll;">
                            <pre style="font-size: 14px; color: #333; white-space: pre-wrap; word-wrap: break-word;"><?= $logs ?></pre>
                        </div>
                        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>

</html>
