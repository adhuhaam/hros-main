<?php
session_start();
include '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch warning details
if (isset($_GET['id'])) {
    $warning_id = intval($_GET['id']);
    $sql = "SELECT w.*, e.name, e.designation 
            FROM warnings w
            LEFT JOIN employees e ON w.emp_no = e.emp_no
            WHERE w.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $warning_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $warning = $result->fetch_assoc();

    if (!$warning) {
        echo "Warning not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warning Letter</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <style>
        /* A4 Paper Dimensions */
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
        }

        .letter-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
            background-color: #fff;
        }

        .letter-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .letter-body {
            line-height: 1.6;
            text-align: justify;
        }

        .letter-footer {
            margin-top: 50px;
        }

        .signature {
            margin-top: 50px;
        }

        /* Hide elements like buttons during print */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="letter-container">
        <!-- Letter Header -->
        <div class="letter-header">
            <img src="../assets/letter_head.png" class="img-fluid">
            <hr>
        </div>

        <!-- Letter Body -->
        <div class="letter-body">
            <p><strong>Date:</strong> <?php echo date("d/m/Y"); ?></p>
            <p><strong>Ref No:</strong> <?php echo htmlspecialchars($warning['id']); ?></p>
            <p><strong>To:</strong> <?php echo htmlspecialchars($warning['name']); ?></p>
            <p><strong>Designation:</strong> <?php echo htmlspecialchars($warning['designation']); ?></p>
            <br>
            <p>Dear <?php echo htmlspecialchars($warning['name']); ?>,</p>
            <p>
                This is an official warning letter issued in response to the following matter:
            </p>
            <p><strong>Problem:</strong> <?php echo htmlspecialchars($warning['problem']); ?></p>
            <p>
                We have reviewed the matter and received the following statements:
            </p>
            <p><strong>Employee Statement:</strong> <?php echo htmlspecialchars($warning['employee_statement']); ?></p>
            <p><strong>HOD Comment:</strong> <?php echo htmlspecialchars($warning['hod_statement']); ?></p>
            <p><strong>HRM Comment:</strong> <?php echo htmlspecialchars($warning['hrm_statement']); ?></p>
            <p><strong>Management Decision:</strong> <?php echo htmlspecialchars($warning['management_decision']); ?></p>
            <br>
            <p>
                Please consider this letter as a formal warning regarding this matter. 
                It is imperative that such issues do not recur in the future, as any further violations may result in disciplinary action, 
                including but not limited to termination of employment.
            </p>
        </div>

        <!-- Letter Footer -->
        <div class="letter-footer">
            <p>Sincerely,</p>
            <div class="signature">
                <p><strong>Management</strong></p>
                <p>Company Name</p>
            </div>
        </div>
    </div>

    <!-- Back and Print Buttons -->
    <div class="text-center mt-4 no-print">
        <a href="view_warning.php?id=<?php echo $warning['id']; ?>" class="btn btn-primary">Back to Warning Details</a>
        <button onclick="window.print()" class="btn btn-success">Print</button>
    </div>

    <!-- Scripts -->
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
