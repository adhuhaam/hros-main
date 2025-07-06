<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    $emp_no = $_POST['emp_no'];
    $direction = $_POST['direction'];
    $handed_over_by = $_POST['handed_over_by'];
    $handover_date = $_POST['handover_date'];
    $delivered_to = $_POST['delivered_to'];
    $taken_by = $_POST['taken_by'];
    $taken_by_date = $_POST['taken_by_date'];
    $received_by = $_POST['received_by'];
    $received_by_date = $_POST['received_by_date'];
    $purpose = $_POST['purpose'];
    $remarks = $_POST['remarks'];

    if ($action === 'add') {
        // Insert new record
        $sql = "INSERT INTO passport_inventory (emp_no, direction, handed_over_by, handover_date, delivered_to, 
                taken_by, taken_by_date, received_by, received_by_date, purpose, remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $emp_no, $direction, $handed_over_by, $handover_date, $delivered_to,
                          $taken_by, $taken_by_date, $received_by, $received_by_date, $purpose, $remarks);

        if ($stmt->execute()) {
            header("Location: view_passport_inventory.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } elseif ($action === 'update' && isset($_POST['id'])) {
        // Update existing record
        $id = $_POST['id'];
        $sql = "UPDATE passport_inventory SET 
                emp_no = ?, direction = ?, handed_over_by = ?, handover_date = ?, delivered_to = ?, 
                taken_by = ?, taken_by_date = ?, received_by = ?, received_by_date = ?, 
                purpose = ?, remarks = ? 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", $emp_no, $direction, $handed_over_by, $handover_date, $delivered_to,
                          $taken_by, $taken_by_date, $received_by, $received_by_date, $purpose, $remarks, $id);

        if ($stmt->execute()) {
            header("Location: view_passport_inventory.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid request.";
}
?>
