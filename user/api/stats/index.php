<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

try {
    // Check database connection
    if (!$conn) {
        die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
    }

    // Initialize stats array
    $stats = [];

    // Fetch employee statistics (include all statuses for employees)
    $query = "SELECT 
                COUNT(*) AS total_employees,
                SUM(CASE WHEN employment_status = 'Active' THEN 1 ELSE 0 END) AS active_employees,
                SUM(CASE WHEN employment_status = 'Terminated' THEN 1 ELSE 0 END) AS terminated_employees,
                SUM(CASE WHEN employment_status = 'Resigned' THEN 1 ELSE 0 END) AS resigned_employees,
                SUM(CASE WHEN employment_status = 'Rejoined' THEN 1 ELSE 0 END) AS rejoined_employees,
                SUM(CASE WHEN employment_status = 'Dead' THEN 1 ELSE 0 END) AS dead_employees,
                SUM(CASE WHEN employment_status = 'Retired' THEN 1 ELSE 0 END) AS retired_employees,
                SUM(CASE WHEN employment_status = 'Missing' THEN 1 ELSE 0 END) AS missing_employees
              FROM employees";
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Employee stats query failed.', 'error' => $conn->error]);
        exit;
    }
    $stats['employees'] = $result->fetch_assoc();

    // Fetch bank account records statistics for Active employees only
    $query = "SELECT 
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_accounts,
                SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) AS scheduled_accounts,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_accounts
              FROM bank_account_records
              WHERE emp_no IN (SELECT emp_no FROM employees WHERE employment_status = 'Active')";
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Bank accounts query failed.', 'error' => $conn->error]);
        exit;
    }
    $stats['bank_accounts'] = $result->fetch_assoc();

    // Fetch medical examinations statistics for Active employees only
    $query = "SELECT 
                COUNT(*) AS total_examinations,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_examinations,
                SUM(CASE WHEN status = 'Medical Center Visited' THEN 1 ELSE 0 END) AS medical_center_visited,
                SUM(CASE WHEN status = 'Uploaded' THEN 1 ELSE 0 END) AS uploaded_examinations,
                SUM(CASE WHEN status = 'Incomplete' THEN 1 ELSE 0 END) AS incomplete_examinations,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_examinations
              FROM medical_examinations
              WHERE employee_id IN (SELECT emp_no FROM employees WHERE employment_status = 'Active')";
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Medical examinations query failed.', 'error' => $conn->error]);
        exit;
    }
    $stats['medical_examinations'] = $result->fetch_assoc();

   // Fetch passport renewals statistics for Active employees only
        $query = "SELECT 
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) AS scheduled,
                    SUM(CASE WHEN status = 'Went to embassy' THEN 1 ELSE 0 END) AS went_to_embassy,
                    SUM(CASE WHEN status = 'Applied' THEN 1 ELSE 0 END) AS applied,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
                    SUM(CASE WHEN status = 'Incomplete' THEN 1 ELSE 0 END) AS incomplete,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN status = 'Received new passport' THEN 1 ELSE 0 END) AS received_new_passport
                  FROM passport_renewals
                  WHERE emp_no IN (SELECT emp_no FROM employees WHERE employment_status = 'Active')";
        $result = $conn->query($query);
        
        if (!$result) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Passport renewals query failed.',
                'error' => $conn->error,
            ]);
            exit;
        }
        
        $stats['passport_renewals'] = $result->fetch_assoc();


    // Fetch visa sticker statistics for Active employees only
    $query = "SELECT 
                COUNT(*) AS total_visas,
                SUM(CASE WHEN visa_status = 'Pending' THEN 1 ELSE 0 END) AS pending_visas,
                SUM(CASE WHEN visa_status = 'Pending Approval' THEN 1 ELSE 0 END) AS pending_approval_visas,
                SUM(CASE WHEN visa_status = 'Ready for Submission' THEN 1 ELSE 0 END) AS ready_submission_visas,
                SUM(CASE WHEN visa_status = 'Ready for Collection' THEN 1 ELSE 0 END) AS ready_collection_visas,
                SUM(CASE WHEN visa_status = 'Completed' THEN 1 ELSE 0 END) AS completed_visas
              FROM visa_sticker
              WHERE emp_no IN (SELECT emp_no FROM employees WHERE employment_status = 'Active')";
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Visa stickers query failed.', 'error' => $conn->error]);
        exit;
    }
    $stats['visa_stickers'] = $result->fetch_assoc();

    // Return the stats as JSON
    echo json_encode(['status' => 'success', 'data' => $stats]);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch statistics.', 'error' => $e->getMessage()]);
}
?>
