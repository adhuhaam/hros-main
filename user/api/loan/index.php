<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include_once '../db.php'; // Include your database connection

// Function to send notification via FCM
function sendNotification($deviceToken, $title, $message) {
    $url = "https://fcm.googleapis.com/fcm/send";
    $serverKey = 'AIzaSyDBDc2nOG2WODACpq1X0Y-zQTXrAAvNpRU'; // Replace with your Firebase server key

    $data = [
        "to" => $deviceToken,
        "notification" => [
            "title" => $title,
            "body" => $message,
        ],
        "data" => [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
        ]
    ];

    $headers = [
        "Authorization: key=$serverKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if ($response === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

// Function to get the device token based on the role
function getDeviceTokenByRole($role) {
    global $conn;
    $query = "SELECT device_token FROM users WHERE role_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['device_token'] ?? null;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Check if an ID is passed to fetch a specific loan request
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "
                SELECT 
                    sl.*, 
                    e.name AS employee_name, 
                    e.designation, 
                    e.department, 
                    TIMESTAMPDIFF(YEAR, e.date_of_join, CURDATE()) AS years_of_service 
                FROM salary_loans sl
                LEFT JOIN employees e ON sl.emp_no = e.emp_no
                WHERE sl.id = ?
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $loan = $result->fetch_assoc();
                echo json_encode(['status' => 'success', 'data' => $loan]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Loan request not found.']);
            }
            $stmt->close();
        } else {
            // Fetch all loan requests
            $query = "SELECT * FROM salary_loans ORDER BY created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $loans = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $loans]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new loan request
        $data = json_decode(file_get_contents('php://input'), true);

        $emp_no = $data['emp_no'] ?? '';
        $amount = $data['amount'] ?? 0;
        $purpose = $data['purpose'] ?? '';
        $currency = $data['currency'] ?? 'USD';

        if (empty($emp_no) || empty($amount) || empty($purpose)) {
            echo json_encode(['status' => 'error', 'message' => 'Employee number, amount, and purpose are required.']);
            exit;
        }

        $query = "INSERT INTO salary_loans (emp_no, amount, purpose, currency, status, applied_date) 
                  VALUES (?, ?, ?, ?, 'Pending', CURDATE())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdss", $emp_no, $amount, $purpose, $currency);

        if ($stmt->execute()) {
            // Notify HRM about the new loan request
            $deviceToken = getDeviceTokenByRole(6); // HRM role ID is assumed to be 6
            if ($deviceToken) {
                sendNotification($deviceToken, 'New Loan Request', 'A new loan request is pending for your approval.');
            }

            echo json_encode(['status' => 'success', 'message' => 'Loan request created successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create loan request.', 'error' => $conn->error]);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Update loan status
        $data = json_decode(file_get_contents('php://input'), true);

        $id = $data['id'] ?? 0;
        $status = $data['status'] ?? '';
        $approved_date = date('Y-m-d');

        // Validate inputs
        if (empty($id) || empty($status)) {
            echo json_encode(['status' => 'error', 'message' => 'Loan ID and status are required.']);
            exit;
        }

        // Allowable statuses
        $allowedStatuses = ['Pending', 'HRM Approved', 'Management Approved', 'Rejected'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid status.']);
            exit;
        }

        // Update the loan status
        $query = "UPDATE salary_loans SET status = ?, approved_date = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $status, $approved_date, $id);

        if ($stmt->execute()) {
            // Send notifications based on the updated status
            if ($status === 'HRM Approved') {
                $deviceToken = getDeviceTokenByRole(10); // Director role ID is assumed to be 10
                if ($deviceToken) {
                    sendNotification($deviceToken, 'Loan Approval Needed', 'A loan request is awaiting your approval.');
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Loan status updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update loan status.', 'error' => $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred.',
        'error' => $e->getMessage()
    ]);
}
?>
