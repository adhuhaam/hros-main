<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'kioskdb.php'; // Ensure correct DB connection
header('Content-Type: application/json');

// Function to safely get count
function getCount($conn, $query) {
    $result = $conn->query($query);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;
}

// ✅ Use the correct database
$recruitDB = 'rccmgvfd_recruit';

// ✅ Fetch Overall Recruitment Stats
$response = [
    'totalCandidates'   => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates"),
    'pendingCandidates' => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Pending'"),
    'shortlisted'       => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Shortlisted'"),
    'hiredCandidates'   => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Hired'"),
    'ticketIssued'      => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Ticket Issued'"),
    'arrivedCandidates' => getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Arrived'"),
    'employedCandidates'=> getCount($conn, "SELECT COUNT(*) AS total FROM {$recruitDB}.candidates WHERE status='Employed'"),
];

// ✅ Fetch Candidates by Agency
$agencyQuery = $conn->query("
    SELECT u.name AS agency_name, 
           COUNT(c.id) AS total 
    FROM {$recruitDB}.candidates c 
    JOIN {$recruitDB}.users u ON c.agent_id = u.id 
    GROUP BY c.agent_id
");

$response['agencies'] = [];
if ($agencyQuery && $agencyQuery->num_rows > 0) {
    while ($row = $agencyQuery->fetch_assoc()) {
        $response['agencies'][] = [
            'agency' => $row['agency_name'],
            'total'  => $row['total']
        ];
    }
}

// ✅ Fetch Candidates by Designation
$designationQuery = $conn->query("
    SELECT d.rcc_designation AS designation, 
           COUNT(c.id) AS total 
    FROM {$recruitDB}.candidates c 
    LEFT JOIN {$recruitDB}.designations d ON c.designation_id = d.id 
    GROUP BY c.designation_id
");

$response['designations'] = [];
if ($designationQuery && $designationQuery->num_rows > 0) {
    while ($row = $designationQuery->fetch_assoc()) {
        $response['designations'][] = [
            'designation' => $row['designation'] ?? 'Unknown',
            'total'       => $row['total']
        ];
    }
}

// ✅ Fetch New Candidates Added Today (With Photo)
$newCandidatesQuery = $conn->query("
    SELECT c.name, 
           d.rcc_designation AS designation, 
           c.candidate_image 
    FROM {$recruitDB}.candidates c
    LEFT JOIN {$recruitDB}.designations d ON c.designation_id = d.id
    WHERE DATE(c.created_at) = CURDATE()
");

$response['newCandidates'] = [];
if ($newCandidatesQuery && $newCandidatesQuery->num_rows > 0) {
    while ($row = $newCandidatesQuery->fetch_assoc()) {
        $response['newCandidates'][] = [
            'name' => $row['name'],
            'designation' => $row['designation'] ?? 'Unknown',
            'photo' => !empty($row['candidate_image']) ? "/uploads/candidates/" . $row['candidate_image'] : "/assets/images/default-avatar.png"
        ];
    }
}

// ✅ Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
