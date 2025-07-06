<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';

// Fetch Total Active Employees (for all modules)
$totalActiveEmployees = $conn->query("
    SELECT COUNT(*) AS total 
    FROM employees 
    WHERE employment_status = 'Active'
")->fetch_assoc()['total'] ?? 0;

// Module 1: Passport Renewal
$passportExpiringSoon = $conn->query("
    SELECT COUNT(*) AS total 
    FROM employees 
    WHERE employment_status = 'Active'
    AND passport_nic_no_expires BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$passportPending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM passport_renewals
    WHERE status != 'Received new passport'
")->fetch_assoc()['total'] ?? 0;

$passportCompleted = $conn->query("
    SELECT COUNT(*) AS total 
    FROM passport_renewals
    WHERE status = 'Received new passport'
")->fetch_assoc()['total'] ?? 0;

// Module 2: Work Permit Fee
$workPermitExpiringSoon = $conn->query("
    SELECT COUNT(*) AS total 
    FROM work_permit_fees 
    WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$workPermitPending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM work_permit_fees
    WHERE status != 'Completed'
")->fetch_assoc()['total'] ?? 0;

$workPermitCompleted = $conn->query("
    SELECT COUNT(*) AS total 
    FROM work_permit_fees
    WHERE status = 'Paid'
")->fetch_assoc()['total'] ?? 0;

// Module 3: Visa Sticker Renewal
$visaExpiringSoon = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visa_sticker 
    WHERE visa_expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$visaPending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visa_sticker
    WHERE visa_status != 'Completed'
")->fetch_assoc()['total'] ?? 0;

$visaCompleted = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visa_sticker
    WHERE visa_status = 'Completed'
")->fetch_assoc()['total'] ?? 0;

// Module 4: Work Permit Medicals
$medicalExpiringSoon = $conn->query("
    SELECT COUNT(*) AS total 
    FROM medical_examinations 
    WHERE date_of_medical BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
")->fetch_assoc()['total'] ?? 0;

$medicalPending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM medical_examinations
    WHERE status != 'Completed'
")->fetch_assoc()['total'] ?? 0;

$medicalCompleted = $conn->query("
    SELECT COUNT(*) AS total 
    FROM medical_examinations
    WHERE status = 'Completed'
")->fetch_assoc()['total'] ?? 0;

// Module 5: Bank Account Opening
$bankPending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM bank_account_records
    WHERE status != 'Completed'
")->fetch_assoc()['total'] ?? 0;

$bankCompleted = $conn->query("
    SELECT COUNT(*) AS total 
    FROM bank_account_records
    WHERE status = 'Completed'
")->fetch_assoc()['total'] ?? 0;

// Build the table data array
$tableData = [
    [
        'label' => 'Ongoing Passport Renewal Count',
        'total_active' => $totalActiveEmployees,
        'expiring' => $passportExpiringSoon,
        'pending' => $passportPending,
        'completed' => $passportCompleted
    ],
    [
        'label' => 'Paid Work Permit Fee Count',
        'total_active' => $totalActiveEmployees,
        'expiring' => $workPermitExpiringSoon,
        'pending' => $workPermitPending,
        'completed' => $workPermitCompleted
    ],
    [
        'label' => 'Ongoing Visa Sticker Renewals Count',
        'total_active' => $totalActiveEmployees,
        'expiring' => $visaExpiringSoon,
        'pending' => $visaPending,
        'completed' => $visaCompleted
    ],
    [
        'label' => 'Completed Work Permit Medicals Count',
        'total_active' => $totalActiveEmployees,
        'expiring' => $medicalExpiringSoon,
        'pending' => $medicalPending,
        'completed' => $medicalCompleted
    ],
    [
        'label' => 'Bank Account Opening Count',
        'total_active' => $totalActiveEmployees,
        'expiring' => '-', // Bank does not expire
        'pending' => $bankPending,
        'completed' => $bankCompleted
    ]
];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HRMS Master Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../../assets/css/styles.min.css" />
  <style>
    .dashboard-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .dashboard-table th, .dashboard-table td {
      border: 1px solid #eee;
      padding: 14px;
      text-align: center;
      vertical-align: middle;
    }
    .dashboard-table th {
      background-color: #f7f9fb;
      color: #333;
      font-size: 16px;
    }
    .dashboard-title {
      font-size: 26px;
      font-weight: bold;
      margin-bottom: 30px;
      color: #2c3e50;
    }
    .highlight-total {
      font-weight: bold;
      background-color: #e0e7ff;
    }
    body {
      background-color: #f0f2f5;
    }
  </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <?php include 'sidebar.php'; ?>
    <div class="body-wrapper">
      <?php include '../../header.php'; ?>

      <div class="container-fluid">
        <div class="dashboard-title">Number Of Employees Count</div>

        <table class="dashboard-table">
          <thead>
            <tr>
              <th>Details</th>
              <th>Total Active Employees</th>
              <th>Expiring in next 3 months</th>
              <th>Pending Balance</th>
              <th>Total Completed</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tableData as $row): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['label']); ?></td>
              <td><?php echo $row['total_active']; ?></td>
              <td><?php echo is_numeric($row['expiring']) ? $row['expiring'] : '-'; ?></td>
              <td><?php echo $row['pending']; ?></td>
              <td><?php echo $row['completed']; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="highlight-total">
              <td>Total Active Employees</td>
              <td><?php echo $totalActiveEmployees; ?></td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
</div>

<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/app.min.js"></script>
<script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
</body>
</html>
