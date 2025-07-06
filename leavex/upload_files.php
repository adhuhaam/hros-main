<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['leave_id'])) {
    die("Invalid Request");
}

$leave_id = intval($_GET['leave_id']);
$error_msg = "";
$success_msg = "";

// File upload directory
$upload_dir = "../files/";

// Ensure the directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_form = null;
    $departure_sheet = null;

    // Upload Leave Form if provided
    if (!empty($_FILES['leave_form']['name']) && $_FILES['leave_form']['error'] === UPLOAD_ERR_OK) {
        $leave_form = "leave_" . $leave_id . "_" . time() . "_" . basename($_FILES['leave_form']['name']);
        $leave_path = $upload_dir . $leave_form;

        if (move_uploaded_file($_FILES['leave_form']['tmp_name'], $leave_path)) {
            $success_msg .= "Leave Form uploaded successfully! ";

            // Insert new record for leave form
            $insert_query = $conn->prepare("
                INSERT INTO leave_files (leave_id, leave_form) 
                VALUES (?, ?)
            ");
            $insert_query->bind_param("is", $leave_id, $leave_form);
            $insert_query->execute();
        } else {
            $error_msg .= "Error uploading Leave Form. ";
        }
    }

    // Upload Departure Sheet if provided
    if (!empty($_FILES['departure_sheet']['name']) && $_FILES['departure_sheet']['error'] === UPLOAD_ERR_OK) {
        $departure_sheet = "departure_" . $leave_id . "_" . time() . "_" . basename($_FILES['departure_sheet']['name']);
        $departure_path = $upload_dir . $departure_sheet;

        if (move_uploaded_file($_FILES['departure_sheet']['tmp_name'], $departure_path)) {
            $success_msg .= "Departure Sheet uploaded successfully! ";

            // Insert new record for departure sheet
            $insert_query = $conn->prepare("
                INSERT INTO leave_files (leave_id, departure_sheet) 
                VALUES (?, ?)
            ");
            $insert_query->bind_param("is", $leave_id, $departure_sheet);
            $insert_query->execute();
        } else {
            $error_msg .= "Error uploading Departure Sheet. ";
        }
    }
}

// Fetch uploaded files
$file_query = $conn->prepare("SELECT leave_form, departure_sheet FROM leave_files WHERE leave_id = ?");
$file_query->bind_param("i", $leave_id);
$file_query->execute();
$file_result = $file_query->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Leave Files</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>
<body>
<div class="page-wrapper">
    <?php include '../sidebar.php'; ?>

    <div class="body-wrapper">
        <?php include '../header.php'; ?>

        <div class="container-fluid">
            <a href="index.php" class="btn btn-info mt-4">Back to Leave Records</a>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Upload Leave Files</h5>

                    <?php if (!empty($error_msg)) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
                    <?php } ?>

                    <?php if (!empty($success_msg)) { ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
                    <?php } ?>

                   

                    <!-- Display Uploaded Files -->
                    <?php if ($file_result) { ?>
                        <h5 class="text-primary mt-4">Uploaded Files:</h5>
                        <ul class="list-group">
                            <?php foreach ($file_result as $file) { ?>
                                <?php if (!empty($file['leave_form']) && file_exists($upload_dir . $file['leave_form'])) { ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo $upload_dir . htmlspecialchars($file['leave_form']); ?>" target="_blank">
                                            📄 Leave Form (View)
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($file['departure_sheet']) && file_exists($upload_dir . $file['departure_sheet'])) { ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo $upload_dir . htmlspecialchars($file['departure_sheet']); ?>" target="_blank">
                                            📄 Departure Sheet (View)
                                        </a>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
