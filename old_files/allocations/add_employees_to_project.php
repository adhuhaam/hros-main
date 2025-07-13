<?php
include '../db.php';

$project_id = $_GET['project_id'];
$project = $conn->query("SELECT * FROM projects WHERE id = $project_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../header.php'; ?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
         <?php include '../header.php'; ?>
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <form method="POST" action="assign_employees.php">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        <div class="mb-3">
                            <label for="employee_select" class="form-label">Select Employees:</label>
                            <select id="employee_select" name="employee_ids[]" class="form-control" multiple>
                                <!-- Options will be dynamically loaded via AJAX -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Assign Selected Employees</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Initialize Select2
    $('#employee_select').select2({
        placeholder: "Search and select employees",
        ajax: {
            url: 'search_employees.php',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    query: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.emp_no,
                            text: item.emp_no + " - " + item.name
                        };
                    })
                };
            },
            cache: true
        }
    });
});
</script>
</body>
</html>
