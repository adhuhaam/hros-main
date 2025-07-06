<?php
include '../db.php';

// Fetch all records from the bank_account_records table
$query = "
    SELECT 
        b.emp_no,
        e.name AS employee_name,
        e.passport_nic_no,
        b.email,
        b.phone,
        b.bank_name,
        b.bank_acc_no,
        b.currency,
        b.status,
        b.entry_date,
        b.form_filled,
        b.scheduled_date,
        b.created_at,
        b.updated_at
    FROM bank_account_records b
    LEFT JOIN employees e ON b.emp_no = e.emp_no
    ORDER BY b.created_at DESC";
$result = $conn->query($query);

// Set headers to download the file as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=RCC_BANK_ACCOUNTS.csv');

// Open PHP output stream
$output = fopen('php://output', 'w');

// Write the CSV column headers
fputcsv($output, [
    'Employee Number',
    'Employee Name',
    'Passport NIC Number',
    'Email',
    'Phone',
    'Bank Name',
    'Bank Account Number',
    'Currency',
    'Status',
    'Entry Date',
    'Form Filled',
    'Scheduled Date',
    'Created At',
    'Updated At'
]);

// Write rows from the database query to the CSV
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['emp_no'],
            $row['employee_name'],
            $row['passport_nic_no'],
            $row['email'],
            $row['phone'],
            $row['bank_name'],
            $row['bank_acc_no'],
            $row['currency'],
            $row['status'],
            $row['entry_date'],
            $row['form_filled'] ? 'Yes' : 'No',
            $row['scheduled_date'],
            $row['created_at'],
            $row['updated_at']
        ]);
    }
}

// Close the output stream
fclose($output);
exit();
?>
