<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HRMS Status Kiosk</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    body {
        background-color: #f4f6f9;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: scale(1.02);
    }
    .card h5 {
        font-size: 1.1rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .list-group-item {
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        padding: 8px 15px;
        border: none;
    }
    .list-group-item span {
        font-weight: bold;
    }
    .employee-photo {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 8px;
        border: 1px solid #007bff;
    }
    .marquee-container {
        padding: 10px;
        background: #007bff;
        color: white;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: bold;
        overflow: hidden;
        text-transform: uppercase;
    }
    .dashboard-title img {
        width: 50px;
        height: auto;
        margin-right: 10px;
    }
  </style>
</head>
<body>
  <div class="page-wrapper" data-layout="vertical">
    <div class="container-fluid">
      
      <div class="text-center my-4 dashboard-title">
        <h1 class="fw-bold d-flex align-items-center justify-content-center">
          <img src="/assets/images/logos/rcc_logo.png" alt="RCC Logo">
          RCC HRoS Office Kiosk Dashboard
        </h1>
      </div>

      <!-- ✅ Running Marquee for Tomorrow's Arrivals -->
      <div class="container-fluid marquee-container">
        <marquee behavior="scroll" direction="left" class="fs-5 fw-semibold py-2" id="arrivalMarquee">
          ✈️ Tomorrow's Arrivals: Loading...
        </marquee>
      </div>

      <div class="row justify-content-center">

        <!-- ✅ Employee Statistics (Grouped List) -->
        <div class="col-lg-4">
          <div class="card p-3">
            <div class="card-body">
              <h5>👥 Employee Statistics</h5>
              <ul class="list-group">
                <li class="list-group-item">Total Employees <span id="totalEmployees">Loading...</span></li>
                <li class="list-group-item">Active <span id="activeEmployees">Loading...</span></li>
                <li class="list-group-item">Resigned <span id="resignedEmployees">Loading...</span></li>
                <li class="list-group-item">Terminated <span id="terminatedEmployees">Loading...</span></li>
                <li class="list-group-item">Missing <span id="missingEmployees">Loading...</span></li>
                <li class="list-group-item">Deceased <span id="deceasedEmployees">Loading...</span></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- ✅ Financial & Banking Stats -->
        <div class="col-lg-4">
          <div class="card p-3">
            <div class="card-body">
              <h5>💰 Financial & Banking</h5>
              <ul class="list-group">
                <li class="list-group-item">Pending Bank Accounts <span id="bankPending">Loading...</span></li>
                <li class="list-group-item">Completed Bank Accounts <span id="bankCompleted">Loading...</span></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- ✅ Leave & Medical Management -->
        <div class="col-lg-4">
          <div class="card p-3">
            <div class="card-body">
              <h5>🏥 Leave & Medical</h5>
              <ul class="list-group">
                <li class="list-group-item">On Leave Today <span id="onLeaveToday">Loading...</span></li>
                <li class="list-group-item">Pending Medical Exams <span id="medicalPending">Loading...</span></li>
                <li class="list-group-item">Completed Medical Exams <span id="medicalCompleted">Loading...</span></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- ✅ Passport Expiry -->
        <div class="col-lg-4">
          <div class="card p-3">
            <div class="card-body">
              <h5>📜 Passport Expirations</h5>
              <ul class="list-group">
                <li class="list-group-item">Expiring in Next 6 Months <span id="passportExpiring">Loading...</span></li>
              </ul>
            </div>
          </div>
        </div>

      </div>

      <!-- ✅ Running Marquee for New Employees -->
      <div class="container-fluid marquee-container mt-3">
        <marquee behavior="scroll" direction="left" class="fs-5 fw-semibold py-2" id="newEmployeesMarquee">
          🎉 New Employees Today: Loading...
        </marquee>
      </div>

    </div>
  </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/app.min.js"></script>

  <script>
    function updateDashboard() {
        fetch('fetch_kiosk_data.php')
            .then(response => response.json())
            .then(data => {
                // Employee Stats
                document.getElementById('totalEmployees').innerText = data.totalEmployees;
                document.getElementById('activeEmployees').innerText = data.activeEmployees;
                document.getElementById('resignedEmployees').innerText = data.resignedEmployees;
                document.getElementById('terminatedEmployees').innerText = data.terminatedEmployees;
                document.getElementById('missingEmployees').innerText = data.missingEmployees;
                document.getElementById('deceasedEmployees').innerText = data.deceasedEmployees;

                // Financial & Banking
                document.getElementById('bankPending').innerText = data.bankPending;
                document.getElementById('bankCompleted').innerText = data.bankCompleted;

                // Leave & Medical
                document.getElementById('onLeaveToday').innerText = data.onLeaveToday;
                document.getElementById('medicalPending').innerText = data.medicalPending;
                document.getElementById('medicalCompleted').innerText = data.medicalCompleted;
                document.getElementById('passportExpiring').innerText = data.passportExpiring;

                // Marquee Updates
                let arrivalsText = data.arrivalList.length > 0 ? data.arrivalList.join(' • ') : 'No scheduled arrivals tomorrow.';
                document.getElementById('arrivalMarquee').innerHTML = '✈️ Tomorrow\'s Arrivals: ' + arrivalsText;

                let newEmployeesText = data.newEmployees.length > 0 
                    ? data.newEmployees.map(emp => `<span class="d-flex align-items-center">
                        <img src="${emp.photo}" class="employee-photo" alt="Photo"> 
                        <strong>${emp.name}</strong> (${emp.designation})
                      </span>`).join(' • ')
                    : 'No new employees today.';
                document.getElementById('newEmployeesMarquee').innerHTML = '🎉 New Employees: ' + newEmployeesText;
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Update dashboard every 10 seconds
    setInterval(updateDashboard, 10000);
    updateDashboard();
  </script>
  
  
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
