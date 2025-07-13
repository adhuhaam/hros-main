<?php
include '../db.php';

// Check if the export request is made
if (isset($_POST['export'])) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    // Validate date range
    if (empty($start_date) || empty($end_date)) {
        die("Please select a valid date range.");
    }

    // Fetch records based on the date_of_medical column
    $sql = "
        SELECT 
            opd_records.emp_no,
            employees.name AS employee_name,
            opd_records.project_name,
            opd_records.invoice_no,
            opd_records.medication_detail,
            opd_records.medication_amount,
            opd_records.consultation_date AS date_of_medical,
            users.staff_name AS entered_by
        FROM 
            opd_records
        LEFT JOIN 
            users 
        ON 
            opd_records.entered_by = users.username
        LEFT JOIN 
            employees 
        ON 
            opd_records.emp_no = employees.emp_no
        WHERE 
            opd_records.consultation_date BETWEEN '$start_date' AND '$end_date'
        ORDER BY 
            opd_records.consultation_date ASC
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Set headers to download the file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="opd_records_' . date('YmdHis') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add CSV column headers
        fputcsv($output, [
            'Employee No', 
            'Employee Name', 
            'Project Name', 
            'Invoice No', 
            'Medication Detail', 
            'Medication Amount (MVR)', 
            'Date of Medical', 
            'Entered By'
        ]);

        // Add rows to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['emp_no'],
                $row['employee_name'] ?: 'Unknown',
                $row['project_name'],
                $row['invoice_no'],
                $row['medication_detail'],
                number_format($row['medication_amount'], 2),
                date('d-M-Y', strtotime($row['date_of_medical'])), // Formatting date_of_medical
                $row['entered_by'] ?: 'Unknown User'
            ]);
        }

        // Close the output stream
        fclose($output);
        exit();
    } else {
        die("No records found for the selected date range.");
    }
}
?>
