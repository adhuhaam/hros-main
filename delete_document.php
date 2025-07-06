<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the document path
    $sql = "SELECT document_path FROM employee_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $document = $result->fetch_assoc();
        $document_path = $document['document_path'];

        // Delete the file
        if (file_exists($document_path)) {
            unlink($document_path);
        }

        // Delete the record from the database
        $delete_sql = "DELETE FROM employee_documents WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: manage_documents.php?success=Document deleted successfully.");
        } else {
            echo "Error deleting the document.";
        }
    } else {
        echo "Document not found.";
    }
} else {
    echo "Invalid request.";
}
?>
