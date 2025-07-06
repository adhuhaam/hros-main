<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php'; // Primary Database connection
include 'db_recruit.php'; // Second database connection

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS Summary Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> <!-- jQuery UI -->

    <link rel="stylesheet" href="dd.css">
    
    <script>
        function updateDashboard() {
            $.ajax({
                url: 'fetch_dashboard_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    Object.keys(data).forEach(key => {
                        $('#' + key).text(data[key]);
                    });

                    // Update Newly Joined Employees
                    let newEmployeesText = data.newEmployees.length > 0 
                        ? data.newEmployees.map(emp => `
                            <li class='list-group-item d-flex align-items-center'>
                                <img src="${emp.photo}" class="employee-photo" alt="Photo"> 
                                <strong>${emp.name}</strong> (${emp.designation})
                            </li>`
                        ).join('') 
                        : '<li class="list-group-item">No new employees today.</li>';
                    
                    $('#newEmployeesList').html(newEmployeesText);

                    // Update Candidates Arriving Tomorrow
                    let arrivingCandidatesText = data.arrivingCandidatesTomorrow.length > 0 
                        ? data.arrivingCandidatesTomorrow.map(candidate => `
                            <li class='list-group-item'>
                                <strong>${candidate.name}</strong> (${candidate.designation}, ${candidate.passport_number}) - ${candidate.arrival_date}
                            </li>`
                        ).join('') 
                        : '<li class="list-group-item">No candidates arriving tomorrow.</li>';
                    
                    $('#arrivingCandidatesList').html(arrivingCandidatesText);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $(document).ready(function() {
            setInterval(updateDashboard, 10000);
            updateDashboard();
            $("#sortable").sortable({
                animation: 150
            });
            $("#sortable .card").resizable({
                handles: "se", // Enable resizing from bottom-right corner
                animate: true,
                animateDuration: "fast",
                animateEasing: "easeOutBounce"
            });
            $("#sortable").disableSelection();
        });
    </script>

    <style>
        #sortable {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
        }
        .card {
            cursor: grab;
            flex: 1 1 calc(33.333% - 10px);
            min-width: 280px;
            resize: both;
            overflow: auto;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container-fluid dashboard-container">
        <div class="dashboard-title">📊 HRMS Dashboard</div>
        <div id="sortable" class="row mt-4"> <!-- Drag & Drop Wrapper -->

          
            <!-- Employees -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>👥 Employees</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Total <span id="totalEmployees">Loading...</span></li>
                        <li class="list-group-item">Active <span id="activeEmployees">Loading...</span></li>
                        <li class="list-group-item">Terminated <span id="terminatedEmployees">Loading...</span></li>
                        <li class="list-group-item">Resigned <span id="resignedEmployees">Loading...</span></li>
                        <li class="list-group-item">Rejoined <span id="rejoinedEmployees">Loading...</span></li>
                        <li class="list-group-item">Dead <span id="deadEmployees">Loading...</span></li>
                        <li class="list-group-item">Retired <span id="retiredEmployees">Loading...</span></li>
                        <li class="list-group-item">Missing <span id="missingEmployees">Loading...</span></li>
                    </ul>
                </div>
            </div>

            <!-- Newly Joined Employees -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>🎉 Newly Joined Employees Today</h5>
                    <ul class="list-group" id="newEmployeesList">
                        <li class="list-group-item">Loading...</li>
                    </ul>
                </div>
            </div>
            
            
            <!-- Candidates Arriving Tomorrow -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>✈️ Candidates Arriving Tomorrow</h5>
                    <ul class="list-group" id="arrivingCandidatesList">
                        <li class="list-group-item">Loading...</li>
                    </ul>
                </div>
            </div>

            <!-- Leave Status -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>Leave & Status</h5>
                    <ul class="list-group">
                        <li class="list-group-item">On Leave Today <span id="onLeaveToday">Loading...</span></li>
                        <li class="list-group-item">Pending Approval <span id="pendingLeaves">Loading...</span></li>
                        <li class="list-group-item">Approved <span id="approvedLeaves">Loading...</span></li>
                        <li class="list-group-item">Rejected <span id="rejectedLeaves">Loading...</span></li>
                    </ul>
                </div>
            </div>
            
            
          
            <!-- Work Permits -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>📜 Work Permit Status</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="workPermitsPending">Loading...</span></li>
                        <li class="list-group-item">Paid & Completed <span id="workPermitsPaid">Loading...</span></li>
                        <li class="list-group-item">Expiring in 2 Months <span id="workPermitsExpiring">Loading...</span></li>
                    </ul>
                </div>
            </div>

            <!-- OPD Records -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>🏥 GENERAL OPD Records</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Total OPD <span id="opdTotal">Loading...</span></li>
                        <li class="list-group-item">Total Amount <span id="opdAmount">Loading...</span></li>
                    </ul>
                </div>
            </div>
            
            
            
            <!-- Bank Accounts -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>🏦 Bank Account opening</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="pendingAccounts">Loading...</span></li>
                        <li class="list-group-item">Completed <span id="completedAccounts">Loading...</span></li>
                    </ul>
                </div>
            </div>
            
            <!-- Resignations -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>📄 Resignations</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="pendingResignations">Loading...</span></li>
                        <li class="list-group-item">Approved <span id="approvedResignations">Loading...</span></li>
                        <li class="list-group-item">Rejected <span id="rejectedResignations">Loading...</span></li>
                    </ul>
                </div>
            </div>
            
           <!-- Warnings (Disciplinary Actions) -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>⚠️ Disciplinary Actions</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="warningsPending">Loading...</span></li>
                        <li class="list-group-item">Resolved <span id="warningsResolved">Loading...</span></li>
                    </ul>
                </div>
            </div>

            
            
            <!-- Employee Tickets -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>🎫 Employee Tickets</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="ticketsPending">Loading...</span></li>
                        <li class="list-group-item">Received <span id="ticketsReceived">Loading...</span></li>
                        <li class="list-group-item">Arrived <span id="ticketsArrived">Loading...</span></li>
                    </ul>
                </div>
            </div>

           <!-- Passport Renewals -->
            <div class="card p-3">
                <div class="card-body">
                    <h5>🛂 Passport Renewals</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Pending <span id="passportPending">Loading...</span></li>
                        <li class="list-group-item">Approved <span id="passportApproved">Loading...</span></li>
                        <li class="list-group-item">Received <span id="passportReceived">Loading...</span></li>
                        <li class="list-group-item">Expiring in 6 Months <span id="passportExpiring">Loading...</span></li>
                    </ul>
                </div>
            </div>

            


            
            
            
            
            
        </div> <!-- End of sortable wrapper -->
    </div>
</body>
</html>
