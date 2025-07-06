<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_number = $_POST['section_number'];
    $section_title = $_POST['section_title'];
    $sub_number = $_POST['subsection_number'];
    $sub_title = $_POST['subsection_title'];
    $detail_number = $_POST['detail_number'];
    $detail_title = $_POST['detail_title'];
    $content = $_POST['content'];

    $conn->begin_transaction();

    try {
        // Insert into handbook_sections
        $stmt1 = $conn->prepare("INSERT INTO handbook_sections (section_number, title) VALUES (?, ?)");
        $stmt1->bind_param("ss", $section_number, $section_title);
        $stmt1->execute();
        $section_id = $stmt1->insert_id;

        // Insert into handbook_subsections
        $stmt2 = $conn->prepare("INSERT INTO handbook_subsections (section_id, subsection_number, title) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $section_id, $sub_number, $sub_title);
        $stmt2->execute();
        $sub_id = $stmt2->insert_id;

        // Handle image
        $imgPath = '';
        if (!empty($_FILES['image']['name'])) {
            $target = "uploads/" . time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            $imgPath = $target;
        }

        // Insert into handbook_details
        $stmt3 = $conn->prepare("INSERT INTO handbook_details (subsection_id, detail_number, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt3->bind_param("issss", $sub_id, $detail_number, $detail_title, $content, $imgPath);
        $stmt3->execute();

        $conn->commit();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Handbook Entry</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-semibold mb-4">Add Handbook Entry</h2>
  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input name="section_number" required placeholder="Section Number (e.g. 10)" class="w-full p-2 border rounded" />
    <input name="section_title" required placeholder="Main Title" class="w-full p-2 border rounded" />

    <input name="subsection_number" required placeholder="Subsection Number (e.g. 10.1)" class="w-full p-2 border rounded" />
    <input name="subsection_title" required placeholder="Subsection Title" class="w-full p-2 border rounded" />

    <input name="detail_number" required placeholder="Detail Number (e.g. 10.1.1)" class="w-full p-2 border rounded" />
    <input name="detail_title" required placeholder="Detail Title" class="w-full p-2 border rounded" />

    <textarea name="content" rows="6" placeholder="Content" class="w-full border p-2 rounded"></textarea>
    <input type="file" name="image" class="w-full text-sm" />
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </form>
</div>
</body>
</html>
