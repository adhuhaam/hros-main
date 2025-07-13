<?php
include '../db.php';
include '../session.php';

// Fetch loan details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("
        SELECT sl.*, e.name AS employee_name, e.designation, e.department, e.date_of_join, 
               ba.bank_acc_no
        FROM salary_loans sl
        JOIN employees e ON sl.emp_no = e.emp_no
        LEFT JOIN bank_account_records ba ON ba.emp_no = sl.emp_no
        WHERE sl.id = $id
    ");
    $loan = $result->fetch_assoc();
    if (!$loan) {
        die("Loan record not found.");
    }
} else {
    die("Invalid request.");
}
?>




<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    
    <title>Loan & Cash Advance Form</title>
    <meta name="author" content="mdidi" />
   <link rel="stylesheet" href="style.css" />
</head>
<body class="w3-container">
   
    <!-- Header -->
    <div class="row">
        <div class="col-6 columnx">
            <img height="40px" class="logo" src="../assets/images/logos/rcc_logo.png">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        </div>
        
        <div class="col-6 columnx header">
            
            Cash Advance & Travel Authorization Form<br> <p style="text-align: right; color: red; font-size: 8pt;">Confidential</p>
        </div>
    </div>
    
   

    <!-- Employee Details -->
    <table>
        
        <tr>
            <th colspan="4" class="section-title">1. Employee Details</th>
        </tr>
        <tr>
            <td class="b">Employee ID:</td>
            <td class="b"><?php echo $loan['emp_no']; ?></td>
            <td class="b">Name:</td>
            <td class="b"><?php echo $loan['employee_name']; ?></td>
            </tr>
        <tr>
            <td class="b">Job Title:</td>
            <td><?php echo $loan['designation']; ?></td>
            <td class="b">Department:</td>
            <td><?php echo $loan['department']; ?></td>
        </tr>
       
        
        <tr>
            <td class="b">Bank Account:</td>
            <td><?php echo !empty($loan['bank_acc_no']) ? $loan['bank_acc_no'] : 'Not Available'; ?>
</td>
            <td class="b">Joined Date:</td>
            <td><?php echo date('d-M-Y', strtotime($loan['date_of_join'])); ?></td>
        </tr>
    </table>

    <!-- Travel Purpose -->
    <table>
        <tr>
            <th colspan="6" class="section-title">2. Travel Purpose</th>
        </tr>
        <tr>
            <td class=" checkbox-cell"><input type="checkbox" /> Business</td>
            <td class=" checkbox-cell"><input type="checkbox" /> Training</td>
            <td class=" checkbox-cell"><input type="checkbox" /> Leave</td>
            <td class=" checkbox-cell"><input type="checkbox" /> Medical</td>
            <td class=" checkbox-cell"><input type="checkbox" /> Domestic Travel</td>
            <td class=" checkbox-cell"><input type="checkbox" /> Other</td>
        </tr>
        <tr>
            <td colspan="6">Remarks/Justification:</td>
        </tr>
    </table>

    <!-- Travel Criteria -->
    <table>
        <tr>
            <th colspan="1" class="section-title">3. Travel Criteria & Booking Details</th>
            <th colspan="3" class="section-title"><input type="checkbox" /> Business Class</th>
            <th colspan="3" class="section-title"><input type="checkbox" /> Economy Class</th>
        </tr>


        <tr >
            <td rowspan="1">
                <input type="checkbox" /> Invoice to Company<br>
                <input type="checkbox" /> Reimbursed on arrival<br>
                <input type="checkbox" /> Employee to pay for return sector<br><small> (Reimbursed on arrival)   </small>         
            </td>
            
        
            <td colspan="3">
                <p>From:<br>
                To:<br>
                No of Days:</p>
            </td>
           <td>
                <p>Booking Reference:<br>
                Carrier:<br>
                Route:</p>
           </td>
        </tr>
        <tr>
            <td colspan="8">Remarks if any:</td>
        </tr>
    </table>

    <!-- Cash Advance -->
    <table>
        <tr>
            <th colspan="2" class="section-title">4. Cash Advance</th>
        </tr>
        <tr>
            <td>Salary Advance Amount:</td>
            <td style="font-size: 12px"><?php echo $loan['amount']; ?>/- <?php echo $loan['currency']; ?></td>
        </tr>
        
        <tr>
            
            <td>Staff Loan:<br>
                <span style="color: red;">&nbsp;* </span>Loan Deduction in (Months):</td>
            <td style="font-size: 12px"><?php echo $loan['purpose']; ?></td>
        </tr>
        <tr>
            <td style="text-align: right;">Grand Total: <input type="checkbox" /> MVR  <input type="checkbox" /> USD</td>
            <td class="b" style="font-size: 15px"><?php echo $loan['amount']; ?>/- <?php echo $loan['currency']; ?></td>
        </tr>
        
        <tr>
            <th colspan="2" class="section-title">4. (a) OUTSTANDING DETAILS (To be checked from HR & Finance)</th>
        </tr>
        <tr> 
            <td><input type="checkbox" /> Settled</td>
            <td>Issued date:</td>
        </tr>
        <tr>
            <td><input type="checkbox" /> Not Settled</td>
            <td>Total Outstanding:</td>
        </tr>
        <th colspan="6" ><small>Remarks if any:</small></th>
        
    </table>

    <!-- Approvals -->
       <table>
           <tr>
            <th colspan="6" class="section-title">Approvals</th>
        </tr>
        
        <tr>
            <td style="height: 70px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th class="wrapx">Employee</th>
            <th class="wrapx">Guarantor <i style="font-size: 5px;"> (If applicable)</i></th>
            <th class="wrapx">Department Manager</th>
            <th class="wrapx">HR Manager</th>
            <th class="wrapx">CFO</th>
            <th class="wrapx">Chairman/MD/Director</th>
        </tr>
        <tr style="height: 5px;">
            <td style="height: 5px;">Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
        </tr>
    </table>
    
    
    
    <!-- Return Details -->
       <table>
           <tr>
            <th colspan="6" class="section-title">RETURN DETAILS (To be completed and dispatched to HR upon return)</th>
        </tr>
        <tr>
            <th class="wrapx center">Employee</th>
            <th class="wrapx center">Immediate Superior/HOD</th>
            <th class="wrapx center">Head of the Department</th>
            <th class="wrapx center">Head of HR/Admin</th>
            <th class="wrapx center">CFO</th>
            <th class="wrapx center">Received by HR</th>
        </tr>
        <tr>
            <td style="height: 70px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="height: 5px;"><small>Return Date</small></td>
            <td></td>
            <td><small>Actual Date Returned</small></td>
            <td></td>
            <td><small>No. of Additional Days</small></td>
            <td></td>
        </tr>
        
        <tr>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
            <td>Date:</td>
        </tr>
    </table>
    
        <hr>
        <div class="col-12">
            <p style="text-align: center; font-size: 8px;">M. Nectar (1st), Asaree Hingun, Male’ Maldives.  |  (T) 331-7878  |  hr@rcc.com.mv  |  rcc.com.mv</p>
        </div>
</body>
</html>
