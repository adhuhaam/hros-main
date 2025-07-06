<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$record_id = filter_input(INPUT_GET, 'record_id', FILTER_VALIDATE_INT);
if ($record_id === null || $record_id === false) {
    die("Invalid record ID.");
}

// Fetch record data from bank_account_records and employees tables
$query = "SELECT b.*, 
                 e.name AS employee_name, 
                 e.xpat_designation, 
                 e.designation, 
                 e.company, 
                 e.xpat_join_date, 
                 e.permanent_address, 
                 e.basic_salary, 
                 e.salary_currency, 
                 e.work_site, 
                 e.wp_no, 
                 e.passport_nic_no,
                 e.contact_number, 
                 e.emp_email
          FROM bank_account_records b 
          JOIN employees e ON b.emp_no = e.emp_no 
          WHERE b.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Fetch logged-in user's staff_name and designation
$userQuery = "SELECT staff_name, des FROM users WHERE id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $_SESSION['user_id']);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

$staff_name = $userData['staff_name'] ?? 'N/A';
$staff_des = $userData['des'] ?? 'N/A';

$food_allowance = 2000;
$basic_salary = (float)$record['basic_salary'];
$currency = filter_var($record['salary_currency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$annual_gross_salary = ($currency === 'USD') ? ($basic_salary * 12) : (($basic_salary + $food_allowance) * 12);

// Determine the letterhead image based on the company
$company = strtoupper(trim($record['company']));
$letterhead = ($company === 'NAZRASH COMPANY PVT LTD') ? 'naz_letter_head.png' : 'letter_head.png';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Opening Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="sbi.css" rel="stylesheet">
</head>

<body>
           <button class="btn btn-primary print-button" onclick="window.print()">Print</button>
        <div class="a4-container">
            <img src="<?= $letterhead ?>" alt="Logo" class="letterhead">


        <p>Manager<br>Maldives Islamic Bank<br>Male', Maldives</p>
        <p><?= date('d-M-Y') ?></p>
        
        <h6 class="fw-normal">Subject: Employment Confirmation letter</h6>
        <p>Dear sir,<br>This is to confirm that the following employee is employed BY Rasheed Carpentry and Construction Pvt Ltd. We hereby confirm his identity details as follows:</p>
        <h6 class="fw-normal">Employee Personal Details</h6>
        <table>
            <tr>
                <th>Employee Number/ID</th>
                <td><?= filter_var($record['emp_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Employee Full Name</th>
                <td><?= filter_var($record['employee_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td><?= filter_var($record['contact_number'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Employee Email</th>
                <td><?= filter_var($record['emp_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>

            <tr>
                <th>Work Permit Number</th>
                <td><?= filter_var($record['wp_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Passport Number</th>
                <td><?= filter_var($record['passport_nic_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Employee Designation</th>
                <td><?= filter_var($record['xpat_designation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Date of Joining</th>
                <td><?= date('d-M-Y', strtotime($record['xpat_join_date'])) ?></td>
            </tr>
            <tr>
                <th>Work Location/Site</th>
                <td><?= filter_var($record['work_site'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
            <tr>
                <th>Residential Address in Maldives</th>
                <td>G.Pool Dream, K.Male', Maldives</td>
            </tr>
            <tr>
                <th>Permanent Address</th>
                <td><?= filter_var($record['permanent_address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
            </tr>
        </table>

        <h6 class="fw-normal">Employee Salary / Income Details</h6>
        <table>
            <tr>
                <th>Total Monthly Salary</th>
                <td class="text-center"><?= $currency ?></td>
                <td class="text-end"><?= number_format($basic_salary, 2) ?></td>
            </tr>
            <tr>
                <th>Food Allowance</th>
                <td class="text-center">MVR</td>
                <td class="text-end">2,000.00</td>
            </tr>
            <tr>
                <th>Annual Gross Salary/Income</th>
                <td class="text-center"><?= $currency ?></td>
                <td class="text-end"><?= number_format($annual_gross_salary, 2) ?></td>
            </tr>
        </table>

        <div class="signature">
            <p>It is kindly requested to assist him in Making MVR account for the mentioned employee above.</p>
            <p>For any additional information, you may contact the HR Department on the following phone number <b>+ (960) 3317878</b> or<b> hr@rcc.com.mv</b>.</p>
            <br>
            <p>Thank you,
            <br>Yours Faithfully,</p>
            <br><br><br>
            <p>----------------------------</p>
            <p><strong><?= htmlspecialchars($staff_name) ?></strong>
            <br><?= htmlspecialchars($staff_des) ?>
            
        </div>

        <hr>
        <div class="col-lg-12 text-center">
            <p class="text-primary">M. Nectar, Asaree Hingun, Male’ Maldives.<br>(T) 331-7878  (E) inquiries@rcc.com.mv (W) rcc.com.mv</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<script>
    window.print();
</script>
