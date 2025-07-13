<?php
// Include the sendNotification function
function sendNotification($token, $title, $body) {
    $url = "https://fcm.googleapis.com/fcm/send";

    $fields = [
        "to" => $token,
        "notification" => [
            "title" => $title,
            "body" => $body,
            "sound" => "default"
        ],
        "data" => [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "message" => $body
        ]
    ];

    $headers = [
        "Authorization: key=AIzaSyDH44igD-g-4147TUuVHKx_ZT4yTw5T31E", // Replace with your FCM Server Key
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die("FCM Send Error: " . curl_error($ch));
    }

    curl_close($ch);
    return $result;
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input data
    $token = $_POST['token'] ?? null;
    $title = $_POST['title'] ?? null;
    $body = $_POST['body'] ?? null;

    // Validate input
    if (empty($token) || empty($title) || empty($body)) {
        $errorMessage = "Token, title, and body are required.";
    } else {
        // Send the notification
        try {
            $response = sendNotification($token, $title, $body);
            $successMessage = "Notification sent successfully.";
        } catch (Exception $e) {
            $errorMessage = "Failed to send notification. Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        form {
            margin-bottom: 20px;
        }
        input, textarea, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Send Notification</h1>

    <?php if (!empty($successMessage)): ?>
        <div class="message success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="token">Device Token:</label>
        <input type="text" id="token" name="token" placeholder="Enter the device token" required>

        <label for="title">Notification Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter the notification title" required>

        <label for="body">Notification Body:</label>
        <textarea id="body" name="body" rows="4" placeholder="Enter the notification body" required></textarea>

        <button type="submit">Send Notification</button>
    </form>
</body>
</html>
