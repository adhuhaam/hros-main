<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RCC Handbook</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2f5e1c1c0.js" crossorigin="anonymous"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .card-hover:hover { transform: translateY(-2px); transition: all 0.2s ease-in-out; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

  <!-- Topbar -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <div class="flex items-center space-x-3">
        <img src="../assets/images/logos/logo.svg" alt="RCC" class="w-10 h-10 object-contain" />
        <span class="text-xl font-bold text-blue-900 tracking-wide">RCC Handbook</span>
      </div>
      <nav class="space-x-4">
        <a href="../../index.php" class="text-blue-700 hover:underline font-medium">Home</a>
        <a href="view.php" class="text-blue-700 hover:underline font-medium">View Handbook</a>
        <a href="add.php" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">
          <i class="fas fa-plus-circle mr-1"></i> Add Entry
        </a>
      </nav>
    </div>
  </header>

  <!-- Content -->
  <main class="max-w-6xl mx-auto p-6">
    <?php
    $sections = $conn->query("SELECT * FROM handbook_sections ORDER BY sort_order ASC, section_number ASC");
    while ($sec = $sections->fetch_assoc()) {
        echo "<div class='bg-white rounded-lg shadow card-hover p-5 mb-8'>";
        echo "<div class='flex justify-between items-center mb-3'>";
        echo "<h2 class='text-xl font-bold text-blue-800'>{$sec['section_number']} - {$sec['title']}</h2>";
        echo "<a href='delete.php?id={$sec['id']}&type=section' class='text-red-600 text-sm hover:underline' onclick='return confirm(\"Delete entire section?\")'><i class='fas fa-trash-alt'></i> Delete Section</a>";
        echo "</div>";

        $subs = $conn->prepare("SELECT * FROM handbook_subsections WHERE section_id = ? ORDER BY sort_order ASC, subsection_number ASC");
        $subs->bind_param("i", $sec['id']);
        $subs->execute();
        $subResult = $subs->get_result();

        while ($sub = $subResult->fetch_assoc()) {
            echo "<div class='mt-4 border-t pt-4'>";
            echo "<div class='flex justify-between items-center'>";
            echo "<h3 class='text-lg font-semibold'>{$sub['subsection_number']} - {$sub['title']}</h3>";
            echo "<a href='delete.php?id={$sub['id']}&type=subsection' class='text-red-500 text-sm hover:underline' onclick='return confirm(\"Delete this subsection?\")'><i class='fas fa-trash'></i> Delete</a>";
            echo "</div>";

            $details = $conn->prepare("SELECT * FROM handbook_details WHERE subsection_id = ? ORDER BY sort_order ASC, detail_number ASC");
            $details->bind_param("i", $sub['id']);
            $details->execute();
            $detResult = $details->get_result();

            while ($d = $detResult->fetch_assoc()) {
                echo "<div class='ml-4 mt-3 p-4 bg-gray-50 rounded-lg border'>";
                echo "<h4 class='font-medium text-gray-800'>{$d['detail_number']} - {$d['title']}</h4>";
                echo "<p class='text-sm text-gray-600 whitespace-pre-line mt-1'>" . nl2br($d['content']) . "</p>";
                if (!empty($d['image_path'])) {
                    echo "<img src='{$d['image_path']}' class='mt-2 max-w-xs rounded border' />";
                }
                echo "<div class='mt-3 text-sm space-x-4'>";
                echo "<a href='edit.php?id={$d['id']}' class='text-blue-600 hover:underline'><i class='fas fa-edit'></i> Edit</a>";
                echo "<a href='delete.php?id={$d['id']}&type=detail' class='text-red-600 hover:underline' onclick='return confirm(\"Delete this detail?\")'><i class='fas fa-trash'></i> Delete</a>";
                echo "</div></div>";
            }

            echo "</div>";
        }

        echo "</div>";
    }
    ?>
  </main>

</body>
</html>
