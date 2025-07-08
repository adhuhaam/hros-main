<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);

    // Fetch employee details using prepared statement
    $stmt = $conn->prepare("SELECT name, designation FROM employees WHERE emp_no = ?");
    $stmt->bind_param("s", $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'name' => $employee['name'],
            'designation' => $employee['designation'],
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Employee not found.',
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}
?>
