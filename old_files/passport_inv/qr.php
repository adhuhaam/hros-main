<?php
require 'vendor/autoload.php'; // Include Chillerlan QR Code library
include('../db.php'); // Include database connection

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Fetch all employees
$query = "SELECT emp_no, name FROM employees"; // Fetch emp_no and name
$result = mysqli_query($conn, $query);

$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $formatted_emp_no = "RCC-" . $row['emp_no'];
    $employees[] = [
        'qr_data' => $formatted_emp_no,
        'display_emp_no' => $formatted_emp_no,
        'name' => $row['name'] ?: 'N/A' // Show "N/A" if no name is found
    ];
}

// QR Code Options
$options = new QROptions([
    'eccLevel' => QRCode::ECC_L, // Low error correction
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'scale' => 5, // Adjust size
]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee QR Codes</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css"> <!-- Use existing project styles -->
    <style>
        body {
            text-align: center;
            margin: 20px;
        }

        .qr-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0px;
        }

        .qr-item {
            border: 1px solid #ccc;
            padding: 0.1px;
            text-align: center;
            width: 200px;
        }

        .qr-text {
            font-size: 14px;
            font-weight: bold;
            margin-top: 0.1px;
        }

        @media print {
            body {
                visibility: hidden;
            }

            .qr-container {
                visibility: visible;
            }
        }
    </style>
</head>

<body>

    <h2>Employee QR Codes</h2>
    <button onclick="window.print()" class="btn btn-primary mb-3">Print QR Codes</button>

    <div class="qr-container">
        <?php foreach ($employees as $employee): ?>
        <div class="qr-item">
            <img src="<?= (new QRCode($options))->render($employee['qr_data']); ?>" alt="QR Code">
            <p class="qr-text"><?= htmlspecialchars($employee['display_emp_no']) ?><br>
                <i style="font-size:8px;"><?= htmlspecialchars($employee['name']) ?></i>
            </p>

        </div>
        <?php endforeach; ?>
    </div>

</body>

</html>