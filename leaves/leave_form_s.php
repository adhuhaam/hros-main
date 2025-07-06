<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Validate 'id' parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: Invalid Request. Leave ID is missing.";
    exit;
}

$leave_id = $_GET['id'];

$leave_query = $conn->prepare("
    SELECT lr.*, 
           e.emp_no, 
           e.name AS employee_name, 
           e.designation, 
           e.nationality,
           e.date_of_join,
           e.passport_nic_no AS passport_no, 
           e.passport_nic_no_expires AS passport_expiry_date,
           e.department, 
           e.contact_number, 
           e.emergency_contact_number, 
           e.emp_email,
           lt.id AS leave_type_id,
           lt.name AS leave_type,
           et.destination AS departure_destination,
           vs.visa_expiry_date,
           wpf.expiry_date AS work_permit_expiry
    FROM leave_records lr
    JOIN employees e ON lr.emp_no = e.emp_no
    JOIN leave_types lt ON lr.leave_type_id = lt.id
    LEFT JOIN employee_tickets et ON lr.departure_ticket_id = et.id
    LEFT JOIN visa_sticker vs ON lr.emp_no = vs.emp_no
    LEFT JOIN work_permit_fees wpf ON lr.emp_no = wpf.emp_no
    WHERE lr.id = ?
");

$leave_query->bind_param("i", $leave_id);
$leave_query->execute();
$leave_record = $leave_query->get_result()->fetch_assoc();



if (!$leave_record) {
    echo "Error: Leave record with ID $leave_id not found.";
    exit;
}

// Fetch leave balance
$balance_query = $conn->prepare("
    SELECT balance 
    FROM leave_balances 
    WHERE emp_no = ? AND leave_type_id = ?
");
$balance_query->bind_param("si", $leave_record['emp_no'], $leave_record['leave_type_id']);
$balance_query->execute();
$balance_result = $balance_query->get_result();
$leave_balance = $balance_result->fetch_assoc()['balance'] ?? 'N/A';


// Fetch logged-in user's staff_name
$userQuery = "SELECT staff_name, des FROM users WHERE id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $_SESSION['user_id']);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

$staff_name = $userData['staff_name'] ?? 'N/A'; // Fallback to 'N/A' if staff_name is not found
$staff_des = $userData['des'] ?? 'N/A'; // Fallback to 'N/A' if staff_name is not found




// Fetch previous leave records for the employee
$emp_no = $leave_record['emp_no'];
$previous_leaves_query = $conn->prepare("
    SELECT start_date, end_date, num_days, status 
    FROM leave_records 
    WHERE emp_no = ? AND id != ? 
    ORDER BY start_date DESC
");
$previous_leaves_query->bind_param("si", $emp_no, $leave_id);
$previous_leaves_query->execute();
$previous_leaves_result = $previous_leaves_query->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
    
    
        body {
            background: white;
            font-family: "Poppins", serif;
            font-size: 10px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        h1, h2, h3, h4, h5, h6 {
            text-align: center;
            margin: 0;
        }
        .section {
            margin-bottom: 5px;
        }
        .section-title {
            background: #f0f0f0;
            padding: 2px 6px;
            font-family: "Poppins", serif;
            font-weight: bold;
            border-bottom: 0.5px solid #808080;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #808080;
            padding: 2px;
        }
        .signature-box {
            height: 45px;
            border: 1px solid #808080;
        }
        .signature-box-app {
            height: 60px;
            border: 1px solid #808080;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        
        textarea {
        max-width: 100%;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="row row-cols-2">
        <div class="col-lg-4">
            <img src="rcc_logo.png" class="" height="50px">
        </div>
         <div class="col-lg-8">
            <h6 class="text-end text-primary">Leave Approval Form</h6>
        </div>
    </div>
    <hr>

    <!-- Employee Information -->
<div class="section">
    <div class="section-title">Employee Information</div>
    <table>
        <tr>
            <th>Employee No:</th>
            <td><?= htmlspecialchars($leave_record['emp_no']); ?></td>
            <th>Employee Name:</th>
            <td><?= htmlspecialchars($leave_record['employee_name']); ?></td>
            <th>Passport / NIC No:</th>
            <td><?= htmlspecialchars($leave_record['passport_no']); ?></td>
        </tr>
        <tr>
            <th>Designation:</th>
            <td><?= htmlspecialchars($leave_record['designation']); ?></td>
            <th>Department:</th>
            <td><?= htmlspecialchars($leave_record['department'] ?? 'N/A'); ?></td>
            <th>Nationality:</th>
            <td><?= htmlspecialchars($leave_record['nationality']); ?></td>
        </tr>
    </table>

    <!-- Expiry Dates -->
    <table>
        <tr>
            <th>Date Of Join:</th>
            <td>
                <?= !empty($leave_record['date_of_join']) 
                    ? date('d-M-Y', strtotime($leave_record['date_of_join'])) 
                    : '<span class="badge bg-secondary">Not Available</span>'; ?>
            </td>

            <th>Passport/NIC Exp. Date:</th>
            <td>
                <?= !empty($leave_record['passport_expiry_date']) 
                    ? date('d-M-Y', strtotime($leave_record['passport_expiry_date'])) 
                    : '<span class="badge bg-secondary">Not Available</span>'; ?>
            </td>

            <th>Visa Expiry Date:</th>
            <td>
                <?= !empty($leave_record['visa_expiry_date']) 
                    ? date('d-M-Y', strtotime($leave_record['visa_expiry_date'])) 
                    : '<span class="badge bg-secondary">Not Available</span>'; ?>
            </td>
        </tr>
    </table>
</div>

<br>
  
  
  
  
  <!-- Leave Details -->
<div class="section">
    <div class="section-title">Leave Details</div>
    <table>
        <tr>
            <th>Leave Type</th>
            <td><?= htmlspecialchars($leave_record['leave_type']); ?></td>
            <th>Start Date</th>
            <td><?= date('d-M-Y', strtotime($leave_record['start_date'])); ?></td>
            <th>End Date</th>
            <td><?= date('d-M-Y', strtotime($leave_record['end_date'])); ?></td>
        </tr>
        <tr>
            <th>Total Days</th>
            <td><?= htmlspecialchars($leave_record['num_days']); ?></td>
            <th colspan="1">Leave Balance</th>
            <td colspan="1"><?= $leave_balance ?> Days</td>
        
            <th colspan="1">Destination</th>
            <td colspan="1">
                <?= !empty($leave_record['departure_destination']) 
                    ? htmlspecialchars($leave_record['departure_destination']) 
                    : '<span class="badge bg-secondary">Not Assigned</span>'; ?>
            </td>
        </tr>
    </table>
</div>

    
    
    
    <!-- Previous Leave Records 
    <div class="section">
        <div class="section-title">Previous Leave Records</div>
        <table>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Number of Days</th>
                <th>Status</th>
            </tr>
            <?php if ($previous_leaves_result->num_rows > 0): ?>
                <?php while ($prev_leave = $previous_leaves_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d-M-Y', strtotime($prev_leave['start_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($prev_leave['end_date'])); ?></td>
                        <td><?= htmlspecialchars($prev_leave['num_days']); ?></td>
                        <td><span class="badge bg-<?= ($prev_leave['status'] == 'Approved') ? 'success' : (($prev_leave['status'] == 'Pending') ? 'warning' : 'danger'); ?>">
                            <?= htmlspecialchars($prev_leave['status']); ?></span></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No previous leave records found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
-->




    <div class="section">
        
        <div class="section-title">Previous Leave Records</div>
       
            <textarea class=" col-12 textarea"></textarea>
      
            
    </div>
    
    
    
    
    
    <br>
    
        <!-- Contact Details -->
        <div class="section">
            <div class="section-title">Contact Details While on Leave</div>
            <table>
                <tr>
                    <th style="width: 10%; word-wrap: break-word;">Mobile</th>
                    <td style="width: 20%; word-wrap: break-word;"><?= htmlspecialchars($leave_record['contact_number'] ?? 'N/A'); ?></td>
                    <th style="width: 20%; word-wrap: break-word;">WhatsApp / Viber</th>
                    <td style="width: 20%; word-wrap: break-word;"><?= htmlspecialchars($leave_record['emergency_contact_number'] ?? 'N/A'); ?></td>
                    <th style="width: 10%; word-wrap: break-word;">Email</th>
                    <td><?= htmlspecialchars($leave_record['emp_email'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Work Handover Section -->
        <div class="section">
            <div class="row">
                <div class="col-lg-6">
                    <p class=" text-start section-title">WORK HANDOVER TO:</p>
                </div>
                <div class="col-lg-6">
                    <p class="text-start section-title">Project: </p>
                </div>
            </div>
            
            
            <table>
                <tr>
                    <th>Pending Works</th>
                    <th>Status</th>
                    <th>Action Needed</th>
                    <th>Due date</th>
                    <th>Hand Over by</th>
                    <th>Take Over by</th>
                    
                </tr>
                <tr class="signature-box">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="signature-box">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="signature-box">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
            </table>
        </div>
        
            <!-- Prepared by  -->
        <div class="section">
            <div class="section-title">Prepared and checked by</div>
            <table style="table-layout: fixed; width: 100%;"> <!-- Ensures equal width for all columns -->
                <tr>
                    <th style="width: 20%;">Name</th>
                    <th style="width: 20%; ">Signature</th>
                </tr>
                <tr>
                    <td  class="signature-box"> <?= htmlspecialchars($staff_name) ?></td>
                    <td  class="signature-box" > </td>
                </tr>
            </table>
        </div>



    <!-- Approvals -->
<div class="section">
    <div class="section-title">Approvals & Signatures</div>
    <table style="table-layout: fixed; width: 100%;"> <!-- Ensures equal width for all columns -->
        <tr class="text-center">
            <th style="width: 20%; word-wrap: break-word;">Requested By (Employee)</th>
            <th style="width: 20%; word-wrap: break-word;">Approved By (HOD)</th>
            <th style="width: 20%; word-wrap: break-word;">Approved By (HRM)</th>
            <th style="width: 20%; word-wrap: break-word;">Approved By (Project Director)</th>
            <th style="width: 20%; word-wrap: break-word;">Approved By (MD/Chairman)</th>
        </tr>
        <tr>
            <td class="signature-box-app"></td>
            <td class="signature-box-app"></td>
            <td class="signature-box-app"></td>
            <td class="signature-box-app"></td>
            <td class="signature-box-app"></td>
        </tr>
    </table>
</div>


    <button class="btn btn-primary no-print w-100" onclick="window.print()">Print Leave Application Form</button>
</div>
<!--script>
    window.print();
</script-->
</body>
</html>
