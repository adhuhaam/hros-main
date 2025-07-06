<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'kioskdb.php'; // Using the existing DB connection
header('Content-Type: application/json');

// Function to safely get count
function getCount($conn, $query) {
    $result = $conn->query($query);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;
}

// 📌 **Employee Statistics**
$response = [
    'totalEmployees'       => getCount($conn, "SELECT COUNT(*) AS total FROM employees"),
    'activeEmployees'      => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Active'"),
    'resignedEmployees'    => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Resigned'"),
    'terminatedEmployees'  => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Terminated'"),
    'deceasedEmployees'    => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Dead'"),
    'retiredEmployees'     => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Retired'"),
    'missingEmployees'     => getCount($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status='Missing'"),

    // 📌 **Bank Account Statistics**
    'bankPending'         => getCount($conn, "SELECT COUNT(*) AS total FROM bank_account_records WHERE status='Pending'"),
    'bankCompleted'       => getCount($conn, "SELECT COUNT(*) AS total FROM bank_account_records WHERE status='Completed'"),

    // 📌 **Leave Statistics (Employees on Leave Today)**
    'onLeaveToday'        => getCount($conn, "
        SELECT COUNT(*) AS total 
        FROM leave_records 
        WHERE CURDATE() BETWEEN start_date AND end_date
        AND status = 'Approved'
    "),

      // 📌 Fetching Expiring Passports from Employees Table (Next 6 Months)
    'passportExpiring' => getCount($conn, "
        SELECT COUNT(*) AS total 
        FROM employees 
        WHERE passport_nic_no_expires BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
    "),

    // 📌 **Medical Examinations**
    'medicalPending'      => getCount($conn, "SELECT COUNT(*) AS total FROM medical_examinations WHERE status='Pending'"),
    'medicalCompleted'    => getCount($conn, "SELECT COUNT(*) AS total FROM medical_examinations WHERE status='Completed'")
];

// 📌 **Fetch Tomorrow's Arrivals from Recruit Database**
$tomorrowDate = date('Y-m-d', strtotime('+1 day'));
$arrivalsQuery = $connRecruit->query("
    SELECT c.name, d.rcc_designation 
    FROM tickets t
    JOIN candidates c ON t.candidate_id = c.id
    LEFT JOIN designations d ON c.designation_id = d.id
    WHERE t.arrival_date = '$tomorrowDate'
");

$response['arrivalList'] = [];
if ($arrivalsQuery && $arrivalsQuery->num_rows > 0) {
    while ($row = $arrivalsQuery->fetch_assoc()) {
        $response['arrivalList'][] = $row['name'] . " (" . ($row['rcc_designation'] ?? 'No Designation') . ")";
    }
}

// 📌 **Fetch Active New Employees Added Today with Photos**
$newEmployeeQuery = $conn->query("
    SELECT e.emp_no, e.name, e.designation, COALESCE(d.photo_file_name, '') AS photo_file
    FROM employees e
    LEFT JOIN employee_documents d ON e.emp_no = d.emp_no AND d.doc_type = 'photo'
    WHERE DATE(e.date_of_join) = CURDATE()
    AND e.employment_status = 'Active'
");

$response['newEmployees'] = [];
if ($newEmployeeQuery && $newEmployeeQuery->num_rows > 0) {
    while ($row = $newEmployeeQuery->fetch_assoc()) {
        $response['newEmployees'][] = [
            'name' => $row['name'],
            'designation' => $row['designation'],
            'photo' => !empty($row['photo_file']) ? "../assets/document/" . $row['photo_file'] : "../assets/images/default-avatar.png"
        ];
    }
}

// 📌 **Fetch Random Active Employees for Display**
$activeEmployeeQuery = $conn->query("
    SELECT e.emp_no, e.name, e.designation, COALESCE(d.photo_file_name, '') AS photo_file
    FROM employees e
    LEFT JOIN employee_documents d ON e.emp_no = d.emp_no AND d.doc_type = 'photo'
    WHERE e.employment_status = 'Active'
    ORDER BY RAND() 
    LIMIT 10
");

$response['activeEmployeesList'] = [];
if ($activeEmployeeQuery && $activeEmployeeQuery->num_rows > 0) {
    while ($row = $activeEmployeeQuery->fetch_assoc()) {
        $response['activeEmployeesList'][] = [
            'name' => $row['name'],
            'designation' => $row['designation'],
            'photo' => !empty($row['photo_file']) ? "../assets/document/" . $row['photo_file'] : "../assets/images/default-avatar.png"
        ];
    }
}

// 📌 **Return JSON response**
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
