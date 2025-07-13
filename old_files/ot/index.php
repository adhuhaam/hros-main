<?php
include '../db.php';
include '../session.php';

session_start();
$msg = '';
if (isset($_SESSION['ot_msg'])) {
  $msg = $_SESSION['ot_msg'];
  unset($_SESSION['ot_msg']);
}

// Summary
$summary = $conn->query("
  SELECT COUNT(*) AS total, 
         SUM(approved_hrs) AS total_hrs,
         SUM(amount) AS total_amt,
         COUNT(DISTINCT emp_no) AS employees
  FROM ot_records
")->fetch_assoc();

// Data
$records = $conn->query("SELECT * FROM ot_records ORDER BY ot_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>OT Records</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <div class="container-fluid pt-4">

      <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $msg; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="d-flex justify-content-between mb-3">
        <h4>OT Records - Current Upload</h4>
        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="upload_ot_handler.php">
          <input type="file" name="file" id="fileInput" class="form-control d-inline-block w-auto" required>
          <button type="submit" id="uploadBtn" class="btn btn-primary ms-2" style="display:none;">Upload Now</button>
        </form>
      </div>

      <div id="previewTable" class="mb-5"></div>

      <div class="row mb-4">
        <div class="col-md-3"><div class="card p-3"><b>Total Entries:</b><br><?php echo $summary['total']; ?></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Total Approved Hours:</b><br><?php echo $summary['total_hrs']; ?></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Total Amount:</b><br>MVR <?php echo $summary['total_amt']; ?></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Unique Employees:</b><br><?php echo $summary['employees']; ?></div></div>
      </div>

      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>EMP No</th>
            <th>Date</th>
            <th>OT Type</th>
            <th>Requested By</th>
            <th>Requested Hrs</th>
            <th>Approved Hrs</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $records->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['emp_no']; ?></td>
              <td><?php echo date('Y-m-d', strtotime($row['ot_date'])); ?></td>
              <td><?php echo $row['ot_type']; ?></td>
              <td><?php echo $row['requested_by']; ?></td>
              <td><?php echo $row['requested_hrs']; ?></td>
              <td><?php echo $row['approved_hrs']; ?></td>
              <td>MVR <?php echo $row['amount']; ?></td>
              <td><span class="badge bg-success"><?php echo $row['status']; ?></span></td>
              <td><?php echo $row['reason']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<!-- XLSX Preview Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- Cleaned XLSX Preview Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.getElementById('fileInput').addEventListener('change', function (e) {
  const file = e.target.files[0];
  const reader = new FileReader();
  reader.onload = function (e) {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, { type: 'array' });
    const sheet = workbook.Sheets[workbook.SheetNames[0]];
    let rawHtml = XLSX.utils.sheet_to_html(sheet, { header: "OT Preview", editable: false });

    // Wrap in jQuery so we can manipulate it
    const $html = $('<div>').html(rawHtml);

    // Remove header/total rows and strip leading 00 from emp_no
    $html.find('table tbody tr').each(function () {
      const firstCell = $(this).find('td').first();
      const text = firstCell.text().trim();

      // Remove row if it's a header or total
      if (text.includes("EMP NO") || text.includes("Total")) {
        $(this).remove();
        return;
      }

      // Remove leading 00 from EMP NO
      if (/^00\d+/.test(text)) {
        const cleaned = text.replace(/^00+/, '');
        firstCell.text(cleaned);
      }
    });

    $('#previewTable').html($html.html());
    $('#uploadBtn').show();
  };
  reader.readAsArrayBuffer(file);
});
</script>

</body>
</html>
