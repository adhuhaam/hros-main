<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include the database connection
include_once 'db.php'; // Update path if necessary

// Check if the database connection exists
if (!isset($conn)) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database connection not established.']);
    exit;
}

// Check the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET' || $requestMethod === 'POST') {
    // Get the input data
    $username = $requestMethod === 'GET' ? $_GET['username'] ?? '' : $_POST['username'] ?? '';
    $password = $requestMethod === 'GET' ? $_GET['password'] ?? '' : $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
        exit;
    }

    try {
        // Prepare SQL query
        $query = "SELECT username, password, role_id FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare the statement: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 0) {
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
            $stmt->close();
            exit;
        }

        $user = $result->fetch_assoc();

        // Verify the password
        if (!password_verify($password, $user['password'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
            $stmt->close();
            exit;
        }

        // Login successful
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'username' => $user['username'],
                'role_id' => $user['role_id']
            ]
        ]);
        $stmt->close();
        exit;

    } catch (Exception $e) {
        // Log error and return a generic message
        error_log($e->getMessage(), 3, '/path/to/error.log'); // Adjust the log file path
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again later.']);
        exit;
    }
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}
?>
