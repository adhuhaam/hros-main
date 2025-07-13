<?php
include '../db.php';
session_start(); // Start the session to store messages

$id = $_POST['id'] ?? null;
$medical_center_name = $_POST['medical_center_name'];
$date_of_medical = $_POST['date_of_medical'];
$status = $_POST['status'];
$uploaded_xpat = isset($_POST['uploaded_xpat']) ? 1 : 0;

// Validate inputs
if (!$id || empty($medical_center_name) || empty($date_of_medical) || empty($status)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: edit.php?id=$id");
    exit;
}

// Handle file upload
$medical_document = $_POST['existing_medical_document'] ?? null; // Retain existing document if no new upload

if (!empty($_FILES['medical_document']['name'])) {
    $original_filename = $_FILES['medical_document']['name']; // Get the original file name
    $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION); // Get file extension
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png']; // Allowed file types

    // Validate file extension
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        $_SESSION['error'] = "Invalid file type. Only PDF, JPG, JPEG, and PNG are allowed.";
        header("Location: edit.php?id=$id");
        exit;
    }

    // Generate a unique file name with timestamp
    $timestamp = time();
    $sanitized_filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $original_filename);
    $medical_document = $timestamp . "_" . $sanitized_filename;

    $upload_dir = "../assets/medicals/";

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Move the uploaded file
    if (!move_uploaded_file($_FILES['medical_document']['tmp_name'], $upload_dir . $medical_document)) {
        $_SESSION['error'] = "Failed to upload the medical document.";
        header("Location: edit.php?id=$id");
        exit;
    }
}

try {
    // Directly insert the value for debugging
    $query = "UPDATE medical_examinations
              SET medical_center_name = '$medical_center_name', 
                  date_of_medical = '$date_of_medical', 
                  status = '$status', 
                  medical_document = '$medical_document', 
                  uploaded_xpat = $uploaded_xpat
              WHERE id = $id";

    // Debugging SQL Query
    error_log("SQL Query: " . $query);

    if ($conn->query($query)) {
        $_SESSION['success'] = "Medical record updated successfully.";
    } else {
        throw new Exception("Error updating medical record: " . $conn->error);
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: index.php");
exit;

?>
