<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// Handle preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load route path
$path = isset($_GET['path']) ? explode('/', trim($_GET['path'], '/')) : [];

// Dispatch to modules
switch ($path[0] ?? '') {
    case 'auth':
        require __DIR__ . '/auth/index.php';
        break;
    case 'leaves':
        require __DIR__ . '/leaves/index.php';
        break;
    case 'loans':
        require __DIR__ . '/loans/index.php';
        break;
    case 'cards':
        require __DIR__ . '/cards/index.php';
        break;
    case 'notifications':
        require __DIR__ . '/notifications/index.php';
        break;
    case 'attendance_records':
        require __DIR__ . '/attendance_records/index.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Invalid endpoint"]);
        break;
}
?>
