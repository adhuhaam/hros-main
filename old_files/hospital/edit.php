<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
    die("No record ID specified.");
}

$id = $conn->real_escape_string($_GET['id']);
$sql = "SELECT * FROM opd_records WHERE id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    die("Record not found.");
}

$record = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $project_name = $conn->real_escape_string($_POST['project_name']);
    $invoice_no = $conn->real_escape_string($_POST['invoice_no']);
    $medication_detail = $conn->real_escape_string($_POST['medication_detail']);
    $medication_amount = $conn->real_escape_string($_POST['medication_amount']);

    $update_sql = "
        UPDATE opd_records 
        SET 
            emp_no = '$emp_no', 
            project_name = '$project_name', 
            invoice_no = '$invoice_no', 
            medication_detail = '$medication_detail', 
            medication_amount = '$medication_amount'
        WHERE id = '$id'
    ";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit OPD Record</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
    <div class="container">
        <h2>Edit OPD Record</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="emp_no" class="form-label">Employee No:</label>
                <input type="text" name="emp_no" id="emp_no" class="form-control" value="<?php echo $record['emp_no']; ?>" required readonly disabled>
            </div>
            <div class="mb-3">
                <label for="project_name" class="form-label">Project Name:</label>
                <input type="text" name="project_name" id="project_name" class="form-control" value="<?php echo $record['project_name']; ?>" required readonly disabled>
            </div>
            <div class="mb-3">
                <label for="invoice_no" class="form-label">Invoice No:</label>
                <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="<?php echo $record['invoice_no']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="medication_detail" class="form-label">Medication Detail:</label>
                <textarea name="medication_detail" id="medication_detail" class="form-control" required><?php echo $record['medication_detail']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="medication_amount" class="form-label">Medication Amount:</label>
                <input type="number" step="0.01" name="medication_amount" id="medication_amount" class="form-control" value="<?php echo $record['medication_amount']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Record</button>
        </form>
    </div>
</body>

</html>
