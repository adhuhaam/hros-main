<?php
include '../db.php';

if (isset($_GET['emp_no'])) {
    $emp_no = $conn->real_escape_string($_GET['emp_no']);
    $sql = "SELECT name, designation FROM employees WHERE emp_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo "*" . htmlspecialchars($employee['name']) . " | " . htmlspecialchars($employee['designation']);
    } else {
        echo "<span class='text-danger'>No employee found with this number.</span>";
    }
}
?>
