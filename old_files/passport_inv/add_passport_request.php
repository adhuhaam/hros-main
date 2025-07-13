<?php
include('../db.php');
include('../session.php'); // Ensure session management

date_default_timezone_set('Indian/Maldives'); // Set Maldivian Time

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>Error: You must be logged in to perform this action.</div>";
    exit;
}

// Get logged-in user's ID from session
$user_id = $_SESSION['user_id'];
$handed_over_by = 'Unknown User';

// Fetch staff_name from users table based on user_id
$user_query = "SELECT staff_name FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
if ($user_row = mysqli_fetch_assoc($user_result)) {
    $handed_over_by = $user_row['staff_name'];
}
mysqli_stmt_close($stmt);

$emp_no = isset($_GET['emp_no']) ? $_GET['emp_no'] : ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_no = $_POST['emp_no'];
    $taken_by = $_POST['taken_by'];
    $purpose = $_POST['purpose'];
    $taken_by_date = date('Y-m-d H:i:s'); // Maldives Time

    // Check last direction
    $query = "SELECT direction FROM passport_inventory WHERE emp_no = ? ORDER BY id DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $emp_no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $last_direction = mysqli_fetch_assoc($result)['direction'] ?? 'IN';
    mysqli_stmt_close($stmt);

    if ($last_direction == 'OUT') {
        echo "<div class='alert alert-danger'>This passport is already OUT!</div>";
    } else {
        // Insert passport OUT record
        $insert_query = "INSERT INTO passport_inventory (emp_no, direction, taken_by, purpose, handed_over_by, taken_by_date)
                         VALUES (?, 'OUT', ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "sssss", $emp_no, $taken_by, $purpose, $handed_over_by, $taken_by_date);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Redirect to index.php
        header("Location: index.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script> <!-- QR Code Scanner -->
</head>
<body class="container mt-4">
    <h2>Passport OUT - Scan QR</h2>

    <!-- QR Scanner -->
    <div id="qr-reader" style="width: 300px;"></div>

    <!-- Form -->
    <form method="POST">
        <label>Scanned EMP No:</label>
        <input type="text" id="emp_no" name="emp_no" class="form-control" value="<?= htmlspecialchars($emp_no) ?>" required readonly>

        <label>Who is Taking:</label>
        <input type="text" name="taken_by" class="form-control" required>

        <label>Purpose:</label>
        <input type="text" name="purpose" class="form-control" required>

        <!-- Display the logged-in user's name -->
        <label>Handed Over By:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($handed_over_by) ?>" disabled>

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>

    <script>
        let qrScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });

        qrScanner.render(function(decodedText) {
            document.getElementById("emp_no").value = decodedText;
            qrScanner.clear();
        });
    </script>
</body>
</html>
