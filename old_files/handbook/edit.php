<?php
include 'db.php';

$id = $_GET['id'];
$res = $conn->prepare("SELECT * FROM handbook_details WHERE id = ?");
$res->bind_param("i", $id);
$res->execute();
$detail = $res->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $detail_number = $_POST['detail_number'];

    $imgPath = $detail['image_path'];
    if (!empty($_FILES['image']['name'])) {
        $target = "uploads/" . time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $imgPath = $target;
    }

    $stmt = $conn->prepare("UPDATE handbook_details SET title = ?, content = ?, detail_number = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $content, $detail_number, $imgPath, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Handbook Detail</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-semibold mb-4">Edit Detail</h2>
  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input name="detail_number" value="<?= htmlspecialchars($detail['detail_number']) ?>" required placeholder="Detail Number" class="w-full p-2 border rounded" />
    <input name="title" value="<?= htmlspecialchars($detail['title']) ?>" required placeholder="Title" class="w-full p-2 border rounded" />
    <textarea name="content" rows="6" class="w-full border p-2 rounded"><?= htmlspecialchars($detail['content']) ?></textarea>
    <input type="file" name="image" class="w-full text-sm" />
    <?php if (!empty($detail['image_path'])): ?>
      <img src="<?= $detail['image_path'] ?>" class="max-w-xs border rounded" />
    <?php endif; ?>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
  </form>
</div>
</body>
</html>
