<?php
include 'db.php';
include 'header.php';
require_once 'utils/FileUploader.php';

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['emp_no'];
    $document_type = $_POST['document_type'];
    
    // Initialize secure file uploader
    $uploader = new FileUploader();
    
    // Upload file securely
    $uploadResult = $uploader->uploadFile($_FILES['document'], $emp_no, $document_type);
    
    if ($uploadResult && $uploadResult['success']) {
        // Check if the document already exists for the employee
        $check_sql = "SELECT * FROM employee_documents WHERE emp_no = ? AND document_type = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $emp_no, $document_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the existing document
            $update_sql = "UPDATE employee_documents SET document_name = ?, document_path = ?, uploaded_at = NOW() WHERE emp_no = ? AND document_type = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssss", $uploadResult['original_name'], $uploadResult['path'], $emp_no, $document_type);
        } else {
            // Insert a new document record
            $insert_sql = "INSERT INTO employee_documents (emp_no, document_name, document_type, document_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $emp_no, $uploadResult['original_name'], $document_type, $uploadResult['path']);
        }

        if ($stmt->execute()) {
            $success_message = "Document uploaded successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    } else {
        $error_message = "Error uploading the document: " . implode(', ', $uploader->getErrors());
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Employee Documents</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include 'sidebar.php'; ?>
        <div class="body-wrapper">
            <div class="container mt-5">
                <h1 class="text-center">Manage Employee Documents</h1>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
                    <div class="mb-3">
                        <label for="emp_no" class="form-label">Employee Number</label>
                        <input type="text" id="emp_no" name="emp_no" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select id="document_type" name="document_type" class="form-control" required>
                            <option value="cv">CV</option>
                            <option value="photo">Photo</option>
                            <option value="certificate">Certificate</option>
                            <option value="licence">Licence</option>
                            <option value="policereport">Police Report</option>
                            <option value="workpermit_card">Work Permit Card</option>
                            <option value="passport">Passport</option>
                            <option value="workpermit_document">Work Permit Document</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document File</label>
                        <input type="file" id="document" name="document" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </form>
                <div class="mt-5">
                    <h3>Uploaded Documents</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Document Type</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch all documents for the employee
                            if (isset($_POST['emp_no'])) {
                                $emp_no = $_POST['emp_no'];
                                $doc_sql = "SELECT * FROM employee_documents WHERE emp_no = ?";
                                $stmt = $conn->prepare($doc_sql);
                                $stmt->bind_param("s", $emp_no);
                                $stmt->execute();
                                $doc_result = $stmt->get_result();

                                while ($document = $doc_result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$document['document_name']}</td>
                                        <td>{$document['document_type']}</td>
                                        <td>{$document['uploaded_at']}</td>
                                        <td>
                                            <a href='{$document['document_path']}' target='_blank' class='btn btn-primary btn-sm'>View</a>
                                            <a href='delete_document.php?id={$document['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/app.min.js"></script>
</body>

</html>
