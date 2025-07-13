<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// Fetch existing record
$query = "SELECT * FROM employee_documents WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Document not found");
}

$document = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $conn->real_escape_string($_POST['emp_no']);
    $doc_type = $conn->real_escape_string($_POST['doc_type']);
    $target_dir = "../assets/document/";

    $front_file_name = $document['front_file_name'];
    $back_file_name = $document['back_file_name'];

    $unique_suffix = time() . "_" . uniqid();

    // === Work Permit Document ===
    if ($doc_type === 'Work Permit Document' && !empty($_FILES["document"]["name"])) {
        $ext = pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION);
        $file_name = $emp_no . "_" . $doc_type . "_" . $unique_suffix . "." . $ext;

        if ($ext !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } elseif (move_uploaded_file($_FILES["document"]["tmp_name"], $target_dir . $file_name)) {
            if (!empty($front_file_name) && file_exists($target_dir . $front_file_name)) {
                unlink($target_dir . $front_file_name);
            }
            $front_file_name = $file_name;
            $back_file_name = null;
        } else {
            $error = "Error uploading the Work Permit Document.";
        }
    }

    // === Passport or Work Permit Card ===
    if (in_array($doc_type, ['Passport', 'Work Permit Card'])) {
        // Front
        if (!empty($_FILES["front_document"]["name"])) {
            $ext = pathinfo($_FILES["front_document"]["name"], PATHINFO_EXTENSION);
            $new_front = $emp_no . "_" . $doc_type . "_front_" . $unique_suffix . "." . $ext;

            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $error = "Only JPG, JPEG, and PNG are allowed for the front side.";
            } elseif (move_uploaded_file($_FILES["front_document"]["tmp_name"], $target_dir . $new_front)) {
                if (!empty($front_file_name) && file_exists($target_dir . $front_file_name)) {
                    unlink($target_dir . $front_file_name);
                }
                $front_file_name = $new_front;
            } else {
                $error = "Error uploading the front document.";
            }
        }

        // Back
        if (!empty($_FILES["back_document"]["name"])) {
            $ext = pathinfo($_FILES["back_document"]["name"], PATHINFO_EXTENSION);
            $new_back = $emp_no . "_" . $doc_type . "_back_" . $unique_suffix . "." . $ext;

            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $error = "Only JPG, JPEG, and PNG are allowed for the back side.";
            } elseif (move_uploaded_file($_FILES["back_document"]["tmp_name"], $target_dir . $new_back)) {
                if (!empty($back_file_name) && file_exists($target_dir . $back_file_name)) {
                    unlink($target_dir . $back_file_name);
                }
                $back_file_name = $new_back;
            } else {
                $error = "Error uploading the back document.";
            }
        }
    }

    // === Update Database ===
    if (!isset($error)) {
        $update = $conn->prepare("UPDATE employee_documents SET emp_no = ?, doc_type = ?, front_file_name = ?, back_file_name = ?, uploaded_at = NOW() WHERE id = ?");
        $update->bind_param("ssssi", $emp_no, $doc_type, $front_file_name, $back_file_name, $id);

        if ($update->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Database update failed: " . $conn->error;
        }
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Document</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end">
                        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </nav>
            </header>
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold"><i class="fa-solid fa-file fs-5"></i>&nbsp; Edit Document</h5>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="emp_no" class="form-label">Employee No:</label>
                                <input type="text" name="emp_no" id="emp_no" required class="form-control"
                                    value="<?php echo htmlspecialchars($document['emp_no']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="doc_type" class="form-label">Document Type:</label>
                                <select name="doc_type" id="doc_type" required class="form-control">
                                    <option value="Work Permit Document" <?php echo $document['doc_type'] === 'Work Permit Document' ? 'selected' : ''; ?>>Work Permit Document</option>
                                    <option value="Passport" <?php echo $document['doc_type'] === 'Passport' ? 'selected' : ''; ?>>Passport</option>
                                    <option value="Work Permit Card" <?php echo $document['doc_type'] === 'Work Permit Card' ? 'selected' : ''; ?>>Work Permit Card</option>
                                </select>
                            </div>
                            <div id="work-permit-document" class="file-group <?php echo $document['doc_type'] !== 'Work Permit Document' ? 'd-none' : ''; ?>">
                                <div class="mb-3">
                                    <label for="document" class="form-label">Replace Work Permit Document (PDF):</label>
                                    <input type="file" name="document" id="document" class="form-control">
                                    <?php if ($document['front_file_name']): ?>
                                        <small>Current File: <a href="../assets/document/<?php echo $document['front_file_name']; ?>" target="_blank">View</a></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div id="passport-card" class="file-group <?php echo $document['doc_type'] === 'Work Permit Document' ? 'd-none' : ''; ?>">
                                <div class="mb-3">
                                    <label for="front_document" class="form-label">Replace Front Side:</label>
                                    <input type="file" name="front_document" id="front_document" class="form-control">
                                    <?php if ($document['front_file_name']): ?>
                                        <small>Current File: <a href="../assets/document/<?php echo $document['front_file_name']; ?>" target="_blank">View</a></small>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label for="back_document" class="form-label">Replace Back Side:</label>
                                    <input type="file" name="back_document" id="back_document" class="form-control">
                                    <?php if ($document['back_file_name']): ?>
                                        <small>Current File: <a href="../assets/document/<?php echo $document['back_file_name']; ?>" target="_blank">View</a></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Update Document</button>
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
            document.getElementById('work-permit-document').classList.toggle('d-none', type !== 'Work Permit Document');
            document.getElementById('passport-card').classList.toggle('d-none', type === 'Work Permit Document');
        });
    </script>
</body>

</html>
