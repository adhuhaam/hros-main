<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$status_filter = $_GET['status'] ?? null;

// Status counts
$statuses = ['Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Completed'];
$statusCounts = [];

foreach ($statuses as $status) {
    $sql = "
        SELECT COUNT(*) AS count
        FROM visa_sticker vs
        INNER JOIN employees e ON vs.emp_no = e.emp_no
        WHERE vs.visa_status = '{$status}'
        AND e.employment_status = 'Active'
        AND e.nationality != 'MALDIVIAN'";
    $result = $conn->query($sql);
    $statusCounts[$status] = $result->fetch_assoc()['count'];
}

$expSoonSql = "
    SELECT COUNT(*) AS count
    FROM visa_sticker vs
    INNER JOIN employees e ON vs.emp_no = e.emp_no
    WHERE vs.visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
    AND vs.visa_expiry_date IS NOT NULL
    AND e.employment_status = 'Active'
    AND e.nationality != 'MALDIVIAN'";
$expSoonCount = $conn->query($expSoonSql)->fetch_assoc()['count'];

// Build base query
$sql = "
    SELECT e.emp_no, e.name, e.passport_nic_no, e.passport_nic_no_expires,
           vs.id AS visa_id, vs.visa_status, vs.visa_expiry_date, vs.remarks
    FROM visa_sticker vs
    INNER JOIN employees e ON vs.emp_no = e.emp_no
    WHERE e.employment_status = 'Active'
    AND e.nationality != 'MALDIVIAN'
";

// Apply status filters
if ($status_filter && in_array($status_filter, $statuses)) {
    $sql .= " AND vs.visa_status = '{$status_filter}'";
} elseif ($status_filter === 'ExpiringSoon') {
    $sql .= " AND vs.visa_expiry_date IS NOT NULL AND vs.visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)";
}

$rows = $conn->query($sql);
$data = [];

while ($r = $rows->fetch_assoc()) {
    $data[] = [
        'emp_no' => $r['emp_no'],
        'name' => $r['name'],
        'passport_nic_no' => $r['passport_nic_no'],
        'passport_nic_no_expires' => $r['passport_nic_no_expires'],
        'visa_expiry_date' => $r['visa_expiry_date'],
        'visa_status' => $r['visa_status'] ?? 'Pending',
        'remarks' => $r['remarks'],
        'visa_id' => $r['visa_id']
    ];
}
?>



<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Visa Management</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

<?php include '../sidebar.php'; ?>

<div class="body-wrapper">
    <?php include '../header.php'; ?>
    
      <div class="container-fluid" style="max-width:100%;">

    <!-- Status Buttons -->
<div class="card mt-4">
    <div class="row">
        <?php foreach ($statuses as $s): ?>
            <div class="col-md-2 mb-2">
                <a href="?status=<?= urlencode($s) ?>" class="btn btn-outline-primary w-100">
                    <?= htmlspecialchars($s) ?> (<?= $statusCounts[$s] ?>)
                </a>
            </div>
        <?php endforeach; ?>
        <div class="col-md-2 mb-2">
            <a href="?status=ExpiringSoon" class="btn btn-outline-danger w-100">
                Expiring Soon (<?= $expSoonCount ?>)
            </a>
        </div>
    </div>
</div>


    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold">Visa Sticker Records</h5>
            <div class="table-responsive">
                <table id="visaTable" class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Emp No</th>
                            <th>Name</th>
                            <th>Passport No</th>
                            <th>Passport Expiry</th>
                            <th>Visa Expiry</th>
                            <th>Status</th>
                           
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $d): ?>
                        <tr>
                            <td><?= $d['emp_no'] ?></td>
                            <td><?= htmlspecialchars($d['name']) ?></td>
                            <td><?= htmlspecialchars($d['passport_nic_no']) ?></td>
                            <td><?= $d['passport_nic_no_expires'] ? date('d-M-Y', strtotime($d['passport_nic_no_expires'])) : 'N/A' ?></td>
                            <td><?= $d['visa_expiry_date'] ? date('d-M-Y', strtotime($d['visa_expiry_date'])) : 'N/A' ?></td>
                            <td><?= $d['visa_status'] ?></td>
                           <td><?= htmlspecialchars($d['remarks'] ?? '') ?></td>
                            <td>
                               <a href="update.php?id=<?= $d['visa_id'] ?>" class="btn btn-sm btn-primary">Update</a>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#visaTable').DataTable({
            responsive: true,
            pageLength: 10
        });
    });
</script>
</body>
</html>
