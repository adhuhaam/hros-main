<?php 
include '../db.php';
include '../session.php';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maternity Leave Application</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar_position="fixed" data-header_position="fixed">

  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid">
        <a href="index.php" class="btn btn-info mt-4">View All Leave Records</a>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Maternity Leave Application</h5>

                <form id="eligibilityForm" method="POST" class="shadow p-4 bg-light rounded">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee ID:</label>
                        <input type="text" name="employee_id" id="employee_id" class="form-control" placeholder="Enter Employee ID" required>
                    </div>

                    <div id="employeeDetails" class="mb-3 d-none text-primary">
                        <label class="form-label">Available Balance:</label>
                        <input type="text" id="leave_balance" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Leave Start Date:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Check Eligibility</button>
                </form>

                <div id="eligibilityResponse" class="mt-4"></div>

                <form id="applyLeaveForm" method="POST" action="submit_maternity_leave.php" class="shadow p-4 bg-light rounded mt-3 d-none">
                    <input type="hidden" name="employee_id" id="app_employee_id">
                    <input type="hidden" name="start_date" id="app_start_date">

                    <div class="mb-3">
                        <label for="num_days" class="form-label">Number of Days:</label>
                        <input type="number" name="num_days" id="num_days" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks:</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Submit Leave Application</button>
                </form>

            </div>
        </div>
    </div>
  </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#eligibilityForm').submit(function(e) {
            e.preventDefault();
            const employeeId = $('#employee_id').val().trim();
            const startDate = $('#start_date').val();

            if (!employeeId || !startDate) {
                $('#eligibilityResponse').html('<p class="text-danger">Please fill in all fields before submitting.</p>');
                return;
            }

            $.ajax({
                url: 'submit_maternity_leave.php',
                type: 'POST',
                data: { employee_id: employeeId, start_date: startDate, check_eligibility: true },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#eligibilityResponse').html(`<div class="alert alert-success">${response.message}</div>`);
                        $('#applyLeaveForm').removeClass('d-none');
                        $('#app_employee_id').val(employeeId);
                        $('#app_start_date').val(startDate);
                        $('#leave_balance').val(response.balance);
                    } else {
                        $('#eligibilityResponse').html(`<div class="alert alert-danger">${response.message}</div>`);
                        $('#applyLeaveForm').addClass('d-none');
                    }
                }
            });
        });
    });
</script>

</body>
</html>
