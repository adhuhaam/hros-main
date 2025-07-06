<?php
// Make sure DB and session are initialized
include_once '/home/rccmgvfd/hros.rccmaldives.com/email/db.php';
require '/home/rccmgvfd/hros.rccmaldives.com/email/send_mail.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function sendNewEmployeeMail($data) {
  // Template load
  $templatePath = __DIR__ . '/template.html';



  $template = file_get_contents($templatePath);

  if (!$template) {
    throw new Exception("Email template not found or unreadable.");
  }

  // Calculate START_DATE (one day after join date)
  $join_date = date('Y-m-d', strtotime($data['date_of_join']));
  $start_date = date('d-M-Y', strtotime($join_date . ' +1 day'));

  // Merge values into template
  $placeholders = [
    '{{EMP_NO}}'       => $data['emp_no'],
    '{{NAME}}'         => $data['name'],
    '{{DESIGNATION}}'  => $data['designation'],
    '{{NATIONALITY}}'  => $data['nationality'],
    '{{JOIN_DATE}}'    => date('d-M-Y', strtotime($join_date)),
    '{{START_DATE}}'   => $start_date,
    '{{HR_NAME}}'      => $_SESSION['staff_name'] ?? 'HR Team',
    '{{HR_EMAIL}}'     => 'hr@rcc.com.mv'
  ];

  foreach ($placeholders as $key => $value) {
    $template = str_replace($key, $value, $template);
  }

  // Fetch recipients from mailing group
  global $conn;
  $emails = [];
  $stmt = $conn->prepare("
    SELECT e.company_email 
    FROM mailing_group m 
    JOIN employees e ON m.emp_no = e.emp_no 
    WHERE m.status = 'Active' AND m.tags LIKE '%new-join%'
  ");

  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      if (!empty($r['company_email'])) {
        $emails[] = $r['company_email'];
      }
    }
  }

  // Send email if there are recipients
  if (!empty($emails)) {
   try {
                  return sendMail(
                    "Newly Joined Staff {$data['name']}",
                    $template,
                    $emails,
                    'automatic',
                    $_SESSION['staff_name'] ?? 'System'
                  );
                } catch (Exception $e) {
                  error_log("SendMail Exception: " . $e->getMessage());
                  throw new Exception("Failed to send email: " . $e->getMessage());
                }

  }

  return false;
}
