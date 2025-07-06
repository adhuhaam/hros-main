<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Show form if emp_no not submitted
if (!isset($_GET['emp_no'])):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print Employee QR</title>
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.1.0/qz-tray.js"></script>

    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 12px rgba(0,0,0,0.1); }
        input[type="text"] { padding: 10px; font-size: 16px; width: 250px; }
        button { padding: 10px 20px; font-size: 16px; background-color: #006bad; color: white; border: none; margin-left: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <form method="GET" action="qr_printv1.php">
        <input type="text" name="emp_no" id="emp_no" placeholder="Enter Employee No" required
               onkeydown="if(event.key==='Enter'){this.form.submit();}">
        <button type="submit">Print</button>
    </form>
</body>
</html>

<?php
else:
    // Redirect to qr_print.php in a new tab, then reload this page
    $emp_no = urlencode($_GET['emp_no']);
    echo "<script>
        window.open('qr_printv1.php?emp_no={$emp_no}', '_blank');
        window.location.href = 'qr_makev1.php';
    </script>";
endif;
