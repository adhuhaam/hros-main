<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'kioskdb.php'; // Ensure the correct DB connection

header('Content-Type: text/html; charset=UTF-8');

// ✅ Fetch Recruitment Overview
$recruitStatsQuery = $connRecruit->query("
    SELECT 
        COUNT(*) AS totalCandidates,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pendingCandidates,
        SUM(CASE WHEN status = 'Shortlisted' THEN 1 ELSE 0 END) AS shortlisted,
        SUM(CASE WHEN status = 'Hired' THEN 1 ELSE 0 END) AS hiredCandidates,
        SUM(CASE WHEN status = 'Ticket Issued' THEN 1 ELSE 0 END) AS ticketIssued,
        SUM(CASE WHEN status = 'Arrived' THEN 1 ELSE 0 END) AS arrivedCandidates,
        SUM(CASE WHEN status = 'Employed' THEN 1 ELSE 0 END) AS employedCandidates
    FROM candidates
") or die("Query Error: " . $connRecruit->error);

$recruitStats = $recruitStatsQuery->fetch_assoc();

// ✅ Fetch Candidates by Agency
$agencyQuery = $connRecruit->query("
    SELECT u.name AS agency_name, COUNT(c.id) AS total 
    FROM candidates c 
    JOIN users u ON c.agent_id = u.id 
    GROUP BY c.agent_id
") or die("Agency Query Error: " . $connRecruit->error);

// ✅ Fetch Candidates by Designation
$designationQuery = $connRecruit->query("
    SELECT d.rcc_designation AS designation, COUNT(c.id) AS total 
    FROM candidates c 
    LEFT JOIN designations d ON c.designation_id = d.id 
    GROUP BY c.designation_id
") or die("Designation Query Error: " . $connRecruit->error);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recruitment Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .table-container {
            max-width: 95%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
            text-transform: uppercase;
        }
        .recruit-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <h1 class="recruit-title">📊 Recruitment Dashboard</h1>

        <!-- ✅ Recruitment Overview Table -->
        <table>
            <thead>
                <tr>
                    <th>Total Candidates</th>
                    <th>Pending</th>
                    <th>Shortlisted</th>
                    <th>Hired</th>
                    <th>Ticket Issued</th>
                    <th>Arrived</th>
                    <th>Employed</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $recruitStats['totalCandidates'] ?? 0 ?></td>
                    <td><?= $recruitStats['pendingCandidates'] ?? 0 ?></td>
                    <td><?= $recruitStats['shortlisted'] ?? 0 ?></td>
                    <td><?= $recruitStats['hiredCandidates'] ?? 0 ?></td>
                    <td><?= $recruitStats['ticketIssued'] ?? 0 ?></td>
                    <td><?= $recruitStats['arrivedCandidates'] ?? 0 ?></td>
                    <td><?= $recruitStats['employedCandidates'] ?? 0 ?></td>
                </tr>
            </tbody>
        </table>

        <!-- ✅ Candidates by Agency Table -->
        <h2 class="recruit-title">🏢 Candidates by Agency</h2>
        <table>
            <thead>
                <tr>
                    <th>Agency Name</th>
                    <th>Total Candidates</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($agency = $agencyQuery->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($agency['agency_name']) ?></td>
                        <td><?= $agency['total'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- ✅ Candidates by Designation Table -->
        <h2 class="recruit-title">🔖 Candidates by Designation</h2>
        <table>
            <thead>
                <tr>
                    <th>Designation</th>
                    <th>Total Candidates</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($designation = $designationQuery->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($designation['designation'] ?? 'Unknown') ?></td>
                        <td><?= $designation['total'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>




 <script>
    function autoSwitchPage() {
        setTimeout(() => {
            window.location.href = "kiosk.php"; // Automatically switch back to kiosk.php
        }, 60000); // 1 minute interval
    }

    autoSwitchPage();
  </script>
  
  
  
</body>
</html>
