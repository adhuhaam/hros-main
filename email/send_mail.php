<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // OR include manual PHPMailer files$template = file_get_contents('../email/template.html'); // Adjust path if needed
$template = file_get_contents('/home/rccmgvfd/hros.rccmaldives.com/email/template.html'); // Adjust path if needed


function sendMail($subject, $body, $recipients, $sendType = 'manual', $sentBy = 'System') {
  $mail = new PHPMailer(true);
  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'rccmaldives.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'no-reply@rccmaldives.com';
    $mail->Password   = 'Ompl@65482*';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('no-reply@rccmaldives.com', 'HR RCC');

    // Add multiple recipients
    foreach ($recipients as $email) {
      $mail->addAddress($email);
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $wrappedBody = "
  <div style='font-family:sans-serif; padding:20px; background:#f9fafb; border-radius:8px;'>
    <h2 style='color:#1e40af;'>$subject</h2>
    <div style='margin-top:10px; font-size:15px;'>$body</div>
    <hr style='margin-top:20px;'>
    <p style='font-size:12px; color:#999;'>Sent from HROS Mail System</p>
  </div>
";
$mail->Body = $wrappedBody;

    $mail->send();

    // Log the mail
    include '/home/rccmgvfd/hros.rccmaldives.com/email/db.php';
    $recipientsString = implode(',', $recipients);
    $stmt = $conn->prepare("INSERT INTO mail_logs (subject, body, recipients, send_type, sent_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $subject, $body, $recipientsString, $sendType, $sentBy);
    $stmt->execute();

    return true;

  } catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    return false;
  }
}
?>