<?php
include '../db.php';
include '../session.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $doc_type = $conn->real_escape_string($_POST['doc_type']);
    $target_dir = "../assets/document/";

    $front_file_name = null;
    $back_file_name = null;
    $unique_suffix = time() . "_" . uniqid();

    // Check if record already exists
    $check = $conn->prepare("SELECT * FROM employee_documents WHERE emp_no = ? AND doc_type = ?");
    $check->bind_param("ss", $emp_no, $doc_type);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();

    if ($existing) {
        if (!empty($existing['front_file_name']) && file_exists($target_dir . $existing['front_file_name'])) {
            unlink($target_dir . $existing['front_file_name']);
        }
        if (!empty($existing['back_file_name']) && file_exists($target_dir . $existing['back_file_name'])) {
            unlink($target_dir . $existing['back_file_name']);
        }
    }

    // === Photo ===
    if ($doc_type === 'Photo' && !empty($_FILES["photo"]["name"])) {
        $ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $front_file_name = $emp_no . "_" . $doc_type . "_" . $unique_suffix . "." . $ext;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $front_file_name)) {
            $error = "Error uploading the photo.";
        }
    }

    // === Work Permit Document ===
    if ($doc_type === 'Work Permit Document' && !empty($_FILES["document"]["name"])) {
        $ext = pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION);
        if ($ext !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            $front_file_name = $emp_no . "_" . $doc_type . "_" . $unique_suffix . "." . $ext;
            if (!move_uploaded_file($_FILES["document"]["tmp_name"], $target_dir . $front_file_name)) {
                $error = "Error uploading document.";
            }
        }
    }

    // === Passport or Work Permit Card ===
    if (in_array($doc_type, ['Passport', 'Work Permit Card'])) {
        // Front
        if (!empty($_FILES["front_document"]["name"])) {
            $ext = pathinfo($_FILES["front_document"]["name"], PATHINFO_EXTENSION);
            $front_file_name = $emp_no . "_" . $doc_type . "_front_" . $unique_suffix . "." . $ext;
            if (!move_uploaded_file($_FILES["front_document"]["tmp_name"], $target_dir . $front_file_name)) {
                $error = "Error uploading the front document.";
            }
        }

        // Back
        if (!empty($_FILES["back_document"]["name"])) {
            $ext = pathinfo($_FILES["back_document"]["name"], PATHINFO_EXTENSION);
            $back_file_name = $emp_no . "_" . $doc_type . "_back_" . $unique_suffix . "." . $ext;
            if (!move_uploaded_file($_FILES["back_document"]["tmp_name"], $target_dir . $back_file_name)) {
                $error = "Error uploading the back document.";
            }
        }
    }

    // === Save to DB ===
    if (!isset($error)) {
        if ($existing) {
            $update = $conn->prepare("UPDATE employee_documents SET front_file_name = ?, back_file_name = ?, uploaded_at = NOW() WHERE emp_no = ? AND doc_type = ?");
            $update->bind_param("ssss", $front_file_name, $back_file_name, $emp_no, $doc_type);
            $update->execute();
        } else {
            $insert = $conn->prepare("INSERT INTO employee_documents (emp_no, doc_type, front_file_name, back_file_name) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $emp_no, $doc_type, $front_file_name, $back_file_name);
            $insert->execute();
        }
        header("Location: index.php");
        exit();
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Document</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <?php include '../header.php'; ?>
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold"><i class="fa-solid fa-file fs-5"></i>&nbsp; Add Document</h5>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control" placeholder="Enter Employee No">
                            </div>
                            <div class="mb-3">
                                <label for="doc_type" class="form-label">Document Type:</label>
                                <select name="doc_type" id="doc_type" required class="form-control">
                                    <option value="" disabled selected>-- Select Document Type --</option>
                                    <option value="Photo">Photo</option>
                                    <option value="Work Permit Document">Work Permit Document</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Work Permit Card">Work Permit Card</option>
                                </select>
                            </div>
                            <div id="photo" class="file-group d-none">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Upload Photo (JPG, JPEG, PNG):</label>
                                    <input type="file" name="photo" id="photo" class="form-control">
                                </div>
                            </div>
                            <div id="work-permit-document" class="file-group d-none">
                                <div class="mb-3">
                                    <label for="document" class="form-label">Upload Work Permit Document (PDF):</label>
                                    <input type="file" name="document" id="document" class="form-control">
                                </div>
                            </div>
                            <div id="passport-card" class="file-group d-none">
                                <div class="mb-3">
                                    <label for="front_document" class="form-label">Upload Front Side:</label>
                                    <input type="file" name="front_document" id="front_document" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="back_document" class="form-label">Upload Back Side:</label>
                                    <input type="file" name="back_document" id="back_document" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Add Document</button>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/Hide fields based on document type
        document.getElementById('doc_type').addEventListener('change', function () {
            const type = this.value;
            document.getElementById('photo').classList.toggle('d-none', type !== 'Photo');
            document.getElementById('work-permit-document').classList.toggle('d-none', type !== 'Work Permit Document');
            document.getElementById('passport-card').classList.toggle('d-none', type !== 'Passport' && type !== 'Work Permit Card');
        });
    </script>
</body>

</html>
