<?php
include '../db.php';

if (isset($_GET['emp_no'])) {
    $emp_no = $conn->real_escape_string($_GET['emp_no']);

    $sql = "SELECT name, designation, level FROM employees WHERE emp_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Employee not found"]);
    }
}
?>
