<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token in the database
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset link via email
        $resetLink = "https://hros.rccmaldives.com/reset_password.php?token=$token";

        // Send Email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'rccmaldives.com'; // Incoming server
            $mail->SMTPAuth = true;
            $mail->Username = 'no-reply@rccmaldives.com'; // Email username
            $mail->Password = 'Ompl@65482*'; // Email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('no-reply@rccmaldives.com', 'RCC HRoS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $success = "Password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/styles.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Forgot Password</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="email" class="form-label">Enter your email address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
    </div>
</body>

</html>
