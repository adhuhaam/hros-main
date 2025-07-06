<?php
include '../db.php';

// Get project ID and employee IDs from the form submission
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$employee_ids = isset($_POST['employee_ids']) ? $_POST['employee_ids'] : [];

// Check if project ID and employee IDs are valid
if ($project_id > 0 && !empty($employee_ids)) {
    // Loop through each employee ID and assign to the project
    foreach ($employee_ids as $employee_id) {
        // Avoid duplicate entries
        $checkQuery = $conn->prepare("SELECT * FROM employee_project_allocations WHERE project_id = ? AND employee_id = ?");
        $checkQuery->bind_param("is", $project_id, $employee_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows === 0) {
            // Insert into employee_project_allocations table
            $insertQuery = $conn->prepare("INSERT INTO employee_project_allocations (project_id, employee_id) VALUES (?, ?)");
            $insertQuery->bind_param("is", $project_id, $employee_id);
            $insertQuery->execute();
        }
    }
}

// Redirect back to the project index page
header("Location: index.php");
exit();
?>
