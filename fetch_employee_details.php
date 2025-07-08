THIS SHOULD BE A LINTER ERROR<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);

    // Fetch employee details
    $query = "SELECT name, designation FROM employees WHERE emp_no = '$emp_no'";
    $result = $conn->query($query);

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
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}
?>
