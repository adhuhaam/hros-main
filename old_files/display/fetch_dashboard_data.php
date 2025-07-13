<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php'; // Primary Database connection
include 'db_recruit.php'; // Second database connection

$data = [
    "totalEmployees" => 0, "activeEmployees" => 0, "terminatedEmployees" => 0, 
    "resignedEmployees" => 0, "rejoinedEmployees" => 0, "deadEmployees" => 0, 
    "retiredEmployees" => 0, "missingEmployees" => 0, "pendingAccounts" => 0, 
    "completedAccounts" => 0, "pendingResignations" => 0, "approvedResignations" => 0, 
    "rejectedResignations" => 0, "warningsPending" => 0, "warningsResolved" => 0, 
    "warningsThisMonth" => 0, "ticketsPending" => 0, "ticketsReceived" => 0, 
    "ticketsArrived" => 0, "passportPending" => 0, "passportApproved" => 0, 
    "passportReceived" => 0, "passportExpiring" => 0, "onLeaveToday" => 0, 
    "pendingLeaves" => 0, "approvedLeaves" => 0, "rejectedLeaves" => 0, 
    "workPermitsPending" => 0, "workPermitsPaid" => 0, "workPermitsExpiring" => 0,
    "salaryLoansPending" => 0, "salaryLoansApproved" => 0, "salaryLoansRejected" => 0,
    "medicalPending" => 0, "medicalCompleted" => 0, "opdTotal" => 0, "opdAmount" => 0,
    "newEmployees" => [], "arrivingCandidatesTomorrow" => []
];

// Function to safely fetch query results
function fetchCount($query) {
    global $conn;
    $result = $conn->query($query);
    return ($result && $row = $result->fetch_row()) ? (int)$row[0] : 0;
}

// Fetch arriving candidates for tomorrow with designation from second DB (`db_recruit`)
$query = "
    SELECT c.name, c.passport_number, t.arrival_date, d.rcc_designation AS designation
    FROM tickets t
    INNER JOIN candidates c ON c.id = t.candidate_id
    LEFT JOIN designations d ON c.designation_id = d.id
    WHERE DATE(t.arrival_date) = CURDATE() + INTERVAL 1 DAY
";

$result = $conn_recruit->query($query);
$data["arrivingCandidatesTomorrow"] = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["arrivingCandidatesTomorrow"][] = [
            "name" => $row["name"],
            "passport_number" => $row["passport_number"],
            "arrival_date" => $row["arrival_date"],
            "designation" => !empty($row["designation"]) ? $row["designation"] : "Not Assigned"
        ];
    }
}

try {
    // Employee Statistics
    $data["totalEmployees"] = fetchCount("SELECT COUNT(*) FROM employees");
    $data["activeEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Active'");
    $data["terminatedEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Terminated'");
    $data["resignedEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Resigned'");
    $data["rejoinedEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Rejoined'");
    $data["deadEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Dead'");
    $data["retiredEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Retired'");
    $data["missingEmployees"] = fetchCount("SELECT COUNT(*) FROM employees WHERE employment_status='Missing'");

    // Bank Accounts
    $data["pendingAccounts"] = fetchCount("SELECT COUNT(*) FROM bank_account_records WHERE status='Pending'");
    $data["completedAccounts"] = fetchCount("SELECT COUNT(*) FROM bank_account_records WHERE status='Completed'");

    // Resignations
    $data["pendingResignations"] = fetchCount("SELECT COUNT(*) FROM resignations WHERE status='Pending'");
    $data["approvedResignations"] = fetchCount("SELECT COUNT(*) FROM resignations WHERE status='Approved'");
    $data["rejectedResignations"] = fetchCount("SELECT COUNT(*) FROM resignations WHERE status='Rejected'");

    // Warnings (Disciplinary Actions)
    $data["warningsPending"] = fetchCount("SELECT COUNT(*) FROM warnings WHERE status != 'Resolved'");
    $data["warningsResolved"] = fetchCount("SELECT COUNT(*) FROM warnings WHERE status='Resolved'");
    $data["warningsThisMonth"] = fetchCount("SELECT COUNT(*) FROM warnings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");

    // Employee Tickets
    $data["ticketsPending"] = fetchCount("SELECT COUNT(*) FROM employee_tickets WHERE ticket_status='Pending'");
    $data["ticketsReceived"] = fetchCount("SELECT COUNT(*) FROM employee_tickets WHERE ticket_status='Ticket Received'");
    $data["ticketsArrived"] = fetchCount("SELECT COUNT(*) FROM employee_tickets WHERE ticket_status='Arrived'");

    // Passport Renewals
    $data["passportPending"] = fetchCount("SELECT COUNT(*) FROM passport_renewals WHERE status='Pending'");
    $data["passportApproved"] = fetchCount("SELECT COUNT(*) FROM passport_renewals WHERE status='Approved'");
    $data["passportReceived"] = fetchCount("SELECT COUNT(*) FROM passport_renewals WHERE status='Received new passport'");
    $data["passportExpiring"] = fetchCount("SELECT COUNT(*) FROM employees WHERE passport_nic_no_expires BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)");

    // Work Permits
    $data["workPermitsPending"] = fetchCount("SELECT COUNT(*) FROM work_permit_fees WHERE status='Pending'");
    $data["workPermitsPaid"] = fetchCount("SELECT COUNT(*) FROM work_permit_fees WHERE status='Paid'");
    $data["workPermitsExpiring"] = fetchCount("SELECT COUNT(*) FROM work_permit_fees WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH)");

    // OPD Records
    $data["opdTotal"] = fetchCount("SELECT COUNT(*) FROM opd_records");
    $data["opdAmount"] = fetchCount("SELECT SUM(medication_amount) FROM opd_records");

    // Leave Status
    $data["onLeaveToday"] = fetchCount("SELECT COUNT(*) FROM leave_records WHERE CURDATE() BETWEEN start_date AND end_date AND status='Approved'");
    $data["pendingLeaves"] = fetchCount("SELECT COUNT(*) FROM leave_records WHERE status='Pending'");
    $data["approvedLeaves"] = fetchCount("SELECT COUNT(*) FROM leave_records WHERE status='Approved'");
    $data["rejectedLeaves"] = fetchCount("SELECT COUNT(*) FROM leave_records WHERE status='Rejected'");

    // Newly Joined Employees
    $newEmployeeQuery = $conn->query("
        SELECT e.emp_no, e.name, e.designation, COALESCE(d.photo_file_name, '') AS photo_file
        FROM employees e
        LEFT JOIN employee_documents d ON e.emp_no = d.emp_no AND d.doc_type = 'photo'
        WHERE DATE(e.date_of_join) = CURDATE() AND e.employment_status = 'Active'
    ");

    if ($newEmployeeQuery) {
        while ($row = $newEmployeeQuery->fetch_assoc()) {
            $data['newEmployees'][] = [
                'name' => $row['name'],
                'designation' => $row['designation'],
                'photo' => !empty($row['photo_file']) ? "../assets/document/" . $row['photo_file'] : "../assets/images/default-avatar.png"
            ];
        }
    }

} catch (Exception $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
