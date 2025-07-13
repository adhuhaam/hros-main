<?php
include '../db.php';
include '../session.php';

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']['tmp_name'])) {
    $file = $_FILES['file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    // Clean table before inserting new batch (optional)
    $conn->query("DELETE FROM ot_records WHERE MONTH(uploaded_at) = MONTH(CURRENT_DATE()) AND YEAR(uploaded_at) = YEAR(CURRENT_DATE())");

    $stmt = $conn->prepare("INSERT INTO ot_records (emp_no, ot_date, ot_type, requested_by, requested_hrs, approved_hrs, amount, reason, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        if (str_contains($row[0], 'EMP') || str_contains($row[0], 'Total')) continue;

        $stmt->bind_param(
            "ssssddsss",
            $row[0],
            date('Y-m-d', strtotime($row[1])),
            $row[2], $row[3],
            floatval($row[4]), floatval($row[5]),
            floatval($row[6]), $row[7], $row[8]
        );
        $stmt->execute();
    }
    $msg = "OT Records imported successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload OT Records</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<?php include '../sidebar.php'; ?>
<div class="body-wrapper">
  <div class="container-fluid pt-4">
    <h4 class="mb-3">Upload OT Records (Excel)</h4>

    <?php if ($msg): ?>
      <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <input type="file" name="file" required class="form-control">
      </div>
      <button class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>
</body>
</html>
