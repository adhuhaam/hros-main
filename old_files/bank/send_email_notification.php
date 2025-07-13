<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../db.php';

// Fetch emails for users with ID 1, 2, and 5
$emailQuery = "SELECT email FROM users WHERE id IN (1, 2, 5)";
$emailResult = $conn->query($emailQuery);

$emails = [];
while ($row = $emailResult->fetch_assoc()) {
    $emails[] = $row['email'];
}

// Fetch bank account records with scheduled_date = tomorrow
$tomorrow = date('d-m-Y', strtotime('+1 day'));
$recordsQuery = "
    SELECT b.*, e.name AS employee_name, e.designation, e.work_site 
    FROM bank_account_records b 
    LEFT JOIN employees e ON b.emp_no = e.emp_no 
    WHERE b.scheduled_date = '$tomorrow'
";
$recordsResult = $conn->query($recordsQuery);

if ($recordsResult->num_rows > 0) {
    // Prepare HTML email content
    $emailContent = "
        <html>
        <head>
            <title>Bank Account Scheduled Visits</title>
        </head>
        <body>
            <h3>Scheduled Bank Visits for Tomorrow ({$tomorrow})</h3>
            <table border='1' cellpadding='6' cellspacing='0'>
                <thead>
                    <tr>
                        <th>Emp No</th>
                        <th>Employee Name</th>
                        <th>Designation</th>
                        <th>Bank Name</th>
                        <th>Scheduled Date</th>
                    </tr>
                </thead>
                <tbody>";

    while ($row = $recordsResult->fetch_assoc()) {
        $emailContent .= "
            <tr>
                <td>{$row['emp_no']}</td>
                <td>{$row['employee_name']}</td>
                <td>{$row['designation']}</td>
                <td>{$row['bank_name']}</td>
                <td>{$row['scheduled_date']}</td>
            </tr>";
    }

    $emailContent .= "
                </tbody>
            </table>
        </body>
        </html>";

    // Send emails via PHPMailer
    foreach ($emails as $email) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'rccmaldives.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'no-reply@rccmaldives.com'; // Your email username
            $mail->Password = 'Ompl@65482*'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('no-reply@rccmaldives.com', 'Scheduled Bank Visits');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Scheduled Bank Visits for Tomorrow";
            $mail->Body = $emailContent;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

$conn->close();
?>
