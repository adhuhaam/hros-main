<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$record_id = filter_input(INPUT_GET, 'record_id', FILTER_VALIDATE_INT);
if ($record_id === null || $record_id === false) {
    die("Invalid record ID.");
}

$query = "SELECT b.*, e.name AS employee_name, e.designation, e.date_of_join, e.permanent_address, e.basic_salary, e.salary_currency, e.work_site, e.wp_no, e.passport_nic_no 
          FROM bank_account_records b 
          JOIN employees e ON b.emp_no = e.emp_no 
          WHERE b.id = ?";
$conn->query("CREATE INDEX IF NOT EXISTS idx_bank_records_emp_no ON bank_account_records(emp_no)");
$conn->query("CREATE INDEX IF NOT EXISTS idx_employees_emp_no ON employees(emp_no)");

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
if (!$record) {
    die("Record not found.");
}

$annual_allowance = 2000;
$basic_salary = isset($record['basic_salary']) && is_numeric($record['basic_salary']) ? $record['basic_salary'] : 0;
$annual_gross_salary = ($basic_salary * 12) + $annual_allowance;

$date = date('d-M-Y');
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Verification Letter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .letter { max-width: 210mm; margin: 0 auto; padding: 20px; border: 1px solid #000; }
        .letter-header { text-align: center; font-weight: bold; font-size: 1.2em; margin-bottom: 20px; }
        table { margin-top: 20px; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="letter">
        <div class="letter-header">REQUEST FOR OPENING OF ACCOUNT</div>
        <p>The Branch Manager<br>State Bank of India<br>Maldives</p>
        <p>Date: {$date}</p>
        <p>Madam / Dear Sir,</p>
        <p>This is to confirm that the following employee is employed with us, and we confirm his identity and salary details as follows:</p>
        <table class="table table-bordered">
            <tr><th>Employee Number/ID</th><td>{$record['emp_no']}</td></tr>
            <tr><th>Employee Full Name</th><td>{$record['employee_name']}</td></tr>
            <tr><th>Work Permit Number</th><td>{$record['wp_no']}</td></tr>
            <tr><th>Passport Number</th><td>{$record['passport_nic_no']}</td></tr>
            <tr><th>Employee Designation</th><td>{$record['designation']}</td></tr>
            <tr><th>Date of Joining</th><td>{$record['date_of_join']}</td></tr>
            <tr><th>Work Location/Site</th><td>{$record['work_site']}</td></tr>
            <tr><th>Permanent Address</th><td>{$record['permanent_address']}</td></tr>
        </table>
        <h4>Employee Salary/Income Details:</h4>
        <table class="table table-bordered">
            <tr><th>Type</th><th>Currency</th><th>Amount</th></tr>
            <tr><td>Total Monthly Salary/Income*</td><td>{$record['salary_currency']}</td><td>{$basic_salary}</td></tr>
            <tr><td>Food Allowance</td><td>{$record['salary_currency']}</td><td>2,000</td></tr>
            <tr><td>Annual Gross Salary/ Income*</td><td>{$record['salary_currency']}</td><td>{$annual_gross_salary}</td></tr>
        </table>
        <p>*If Salary is paid in multiple currencies, then currency-wise salary to be provided.</p>
        <p>We request you to kindly assist him/her in opening a {$record['bank_name']} account at State Bank of India, Maldives.</p>
        <p>For any additional information, you may contact the HR Department on the following phone number331 7878 or email hr@rcc.com.mv.</p>
        <p>Yours faithfully,</p><br><br><br>
        <p><strong>Company HR</strong></p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
HTML;

header("Content-Type: text/html");
echo $html;
exit;
?>
