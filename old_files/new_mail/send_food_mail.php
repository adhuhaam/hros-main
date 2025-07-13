<?php
require '../db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_POST['selected_emps']) || !is_array($_POST['selected_emps'])) {
    die('<p style="color:red;">No employee selected.</p>');
}

$empNos = array_map('intval', $_POST['selected_emps']);
$placeholders = implode(',', array_fill(0, count($empNos), '?'));

$sql = "SELECT emp_no, name, nationality, designation, date_of_join FROM employees WHERE emp_no IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($empNos)), ...$empNos);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while ($row = $result->fetch_assoc()) {
    $joinTimestamp = strtotime($row['date_of_join']);
    $joinDay = (int)date('d', $joinTimestamp);
    $daysInMonth = (int)date('t', $joinTimestamp);
    $month1Label = date('F Y', $joinTimestamp);
    $month2Label = date('F Y', strtotime('+1 month', $joinTimestamp));
    $month1 = round((2000 / $daysInMonth) * ($daysInMonth - $joinDay + 1), 2);
    $month2 = (date('Y-m', $joinTimestamp) == date('Y-m') && $joinDay > 25) ? 2000 : 0;
    $total = $month1 + $month2;

    $row['month1_label'] = $month1Label;
    $row['month2_label'] = $month2 > 0 ? $month2Label : '';
    $row['month1'] = $month1;
    $row['month2'] = $month2;
    $row['total'] = $total;

    $employees[] = $row;
}

// Compose email
$body = "<p><strong>Dear All,</strong></p>
<p>You are kindly requested to arrange the disbursement of the specified allowances for the following individual(s) as listed below.</p>";
$body .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;width:100%;font-size:14px;'>";
$body .= "<tr style='background:#004080;color:white;'><th>S/N</th><th>EMP ID</th><th>Name</th><th>Designation</th><th>Nationality</th><th>Join Date</th><th>Food – {$employees[0]['month1_label']}</th>";
if ($employees[0]['month2_label']) $body .= "<th>Food  {$employees[0]['month2_label']}</th>";
$body .= "<th>Total (MVR)</th></tr>";

foreach ($employees as $i => $emp) {
    $body .= "<tr><td>" . ($i + 1) . "</td>
              <td>{$emp['emp_no']}</td>
              <td>{$emp['name']}</td>
              <td>{$emp['designation']}</td>
              <td>{$emp['nationality']}</td>
              <td>" . date('d-M-Y', strtotime($emp['date_of_join'])) . "</td>
              <td>" . number_format($emp['month1'], 2) . "</td>";
    if ($emp['month2_label']) $body .= "<td>" . number_format($emp['month2'], 2) . "</td>";
    $body .= "<td><strong>" . number_format($emp['total'], 2) . "</strong></td></tr>";
}
$body .= "</table><br><p>Kind regards,<br><strong>HR Department</strong><br>Rasheed Carpentry & Construction Pvt Ltd</p>";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'rccmaldives.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-reply@rccmaldives.com';
    $mail->Password = 'Ompl@65482*';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@rccmaldives.com', 'HR DEPARTMENT  |  FOOD ALLOWANCE');
    $mail->addAddress('foodallowance@rcc.com.mv'); //foodallowance@rcc.com.mv
    $mail->isHTML(true);
    $mail->Subject = 'New Staff Food Money';
    $mail->Body = $body;
    $mail->send();

    echo "<script>alert('Food allowance mail sent successfully.'); window.location='food_mail.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Mailer Error: " . addslashes($mail->ErrorInfo) . "'); history.back();</script>";
}
