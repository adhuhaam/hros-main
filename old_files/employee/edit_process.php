<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Check if the employee ID is provided
if (!isset($_GET['emp_no'])) {
    header("Location: index.php?error=Employee ID is required");
    exit;
}

$emp_no = $_GET['emp_no'];

// Fetch the employee data
$sql = "SELECT * FROM employees WHERE emp_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=Employee not found");
    exit;
}

$employee = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'name', 'gender', 'designation', 'xpat_designation', 'xpat_join_date', 'department', 'nationality', 'passport_nic_no', 'passport_nic_no_expires', 'dob', 'wp_no', 'date_of_join', 'contact_number', 'contact_number_foregn',
        'emergency_contact_number', 'emergency_contact_name', 'employment_status', 'work_site', 'insurance_provider', 'recruiting_agency', 'emp_email', 'permanent_address', 'basic_salary', 'salary_currency', 'termination_date',
        'level', 'company'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    // Update SQL Query
    $updateSql = "UPDATE employees SET 
        name = ?, gender = ?, designation = ?, xpat_designation = ?, xpat_join_date = ?, 
        department = ?, nationality = ?, passport_nic_no = ?, passport_nic_no_expires = ?, dob = ?, 
        wp_no = ?, date_of_join = ?, contact_number = ?, contact_number_foregn = ?, 
        emergency_contact_number = ?, emergency_contact_name = ?, employment_status = ?, work_site = ?, 
        insurance_provider = ?, recruiting_agency = ?, emp_email = ?, permanent_address = ?, 
        basic_salary = ?, salary_currency = ?, termination_date = ?, level = ?, company = ? 
        WHERE emp_no = ?";

    $stmt = $conn->prepare($updateSql);

    // ✅ Correct `bind_param` with 27 placeholders
    $stmt->bind_param(
        'ssssssssssssssssssssssssss', // 27 placeholders (all as 's' for simplicity)
        $data['name'],
        $data['gender'],
        $data['designation'],
        $data['xpat_designation'],
        $data['xpat_join_date'],
        $data['department'],
        $data['nationality'],
        $data['passport_nic_no'],
        $data['passport_nic_no_expires'],
        $data['dob'],
        $data['wp_no'],
        $data['date_of_join'],
        $data['contact_number'],
        $data['contact_number_foregn'],
        $data['emergency_contact_number'],
        $data['emergency_contact_name'],
        $data['employment_status'],
        $data['work_site'],
        $data['insurance_provider'],
        $data['recruiting_agency'],
        $data['emp_email'],
        $data['permanent_address'],
        $data['basic_salary'],          // Using 's' as requested
        $data['salary_currency'],
        $data['termination_date'],
        $data['level'],
        $data['company'],
        $emp_no                         // WHERE condition
    );

    if ($stmt->execute()) {
        header("Location: index.php?success=Employee updated successfully");
        exit;
    } else {
        header("Location: edit.php?emp_no=$emp_no&error=Error updating employee: " . $stmt->error);
        exit;
    }
}
?>
