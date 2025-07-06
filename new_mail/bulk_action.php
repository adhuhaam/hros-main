<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

include '../db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate input
if (!isset($_POST['selected_emps']) || !is_array($_POST['selected_emps'])) {
    die('No employees selected.');
}

$selectedEmps = $_POST['selected_emps'];
$action = $_POST['action'] ?? 'mail1'; // default action if not explicitly passed

if ($action !== 'mail1') {
    die('Invalid action specified.');
}

// Fetch employee records
$placeholders = implode(',', array_fill(0, count($selectedEmps), '?'));
$stmt = $conn->prepare("SELECT emp_no, name, designation, nationality, date_of_join FROM employees WHERE emp_no IN ($placeholders)");
$stmt->bind_param(str_repeat('s', count($selectedEmps)), ...$selectedEmps);
$stmt->execute();
$result = $stmt->get_result();
$employees = $result->fetch_all(MYSQLI_ASSOC);

if (empty($employees)) {
    die('No valid employees found.');
}

// Build email body
$body = "
<p><strong>Dear Members of RCC,</strong></p>
<p>We are pleased to announce that the following individual(s) have joined <strong>Rasheed Carpentry & Construction Pvt Ltd</strong>.</p>
<p>We extend a warm welcome and count on your continued support in helping them integrate successfully into their roles. Please find their details below:</p>

<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; font-family: Arial, sans-serif; width: 100%;'>
<thead>
<tr style='background-color: #004080; color: #fff;'>
    <th>S/N</th>
    <th>EMP ID</th>
    <th>NAME</th>
    <th>DESIGNATION</th>
    <th>NATIONALITY</th>
    <th>JOINING DATE</th>
    <th>WORK START DATE</th>
</tr>
</thead>
<tbody>";

$count = 1;
foreach ($employees as $emp) {
    $empNo = strtoupper(htmlspecialchars($emp['emp_no']));
    $name = strtoupper(htmlspecialchars($emp['name']));
    $designation = strtoupper(htmlspecialchars($emp['designation']));
    $nationality = strtoupper(htmlspecialchars($emp['nationality']));
    $joinDateRaw = $emp['date_of_join'];
    $joinDate = date('d-M-Y', strtotime($joinDateRaw));
    $startDate = date('l', strtotime($joinDateRaw)) === 'Thursday'
        ? date('d-M-Y', strtotime($joinDateRaw . ' +2 days'))
        : date('d-M-Y', strtotime($joinDateRaw . ' +1 day'));

    $body .= "<tr>
        <td>{$count}</td>
        <td>{$empNo}</td>
        <td>{$name}</td>
        <td>{$designation}</td>
        <td>{$nationality}</td>
        <td>{$joinDate}</td>
        <td>{$startDate}</td>
    </tr>";
    $count++;
}

$body .= "</tbody></table><br>";

$body .= "
<p>Kind regards,<br><strong>HR DEPARTMENT</strong></p>
<p style='font-size: 13px;'>
<strong>RASHEED CARPENTRY & CONSTRUCTION PVT LTD</strong><br>
M. Nector, Asaree Hingun, Male’ Maldives<br>
T: (960) 3317878<br>
F: (960) 3313544<br>
W: <a href='https://rcc.com.mv'>rcc.com.mv</a><br>
E: <a href='mailto:hr@rcc.com.mv'>hr@rcc.com.mv</a>
</p>";

// Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'rccmaldives.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-reply@rccmaldives.com';
    $mail->Password = 'Ompl@65482*'; // Suggest moving to .env or config.php
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@rccmaldives.com', 'HR DEPARTMENT | NEW STAFF');
    $mail->addAddress('newjoiners@rcc.com.mv');
    // Optional CC
    // $mail->addCC('hr@rcc.com.mv');

    $mail->isHTML(true);
    $mail->Subject = 'NEW STAFF MEMBER JOINED  ' . date('F Y');
    $mail->Body = $body;

    $mail->send();

    // Redirect after success
    header('Location: index.php?mail=success');
    exit();
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    header('Location: index.php?mail=fail');
    exit();
}
