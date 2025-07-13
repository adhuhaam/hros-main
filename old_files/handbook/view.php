<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RCC Employee Handbook</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2f5e1c1c0.js" crossorigin="anonymous"></script>
  <style>
    .highlight { background-color: #fef3c7; }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">
  <!-- Topbar -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <div class="flex items-center gap-3">
        <img src="../assets/images/logos/logo-white.png" alt="RCC" class="w-10 h-10" />
        <span class="text-xl font-bold text-blue-900">Employee Handbook</span>
      </div>
      <input type="text" id="searchInput" placeholder="Search..." class="px-4 py-2 border rounded w-64 focus:outline-none focus:ring focus:border-blue-300">
    </div>
  </header>

  <!-- Content -->
  <main class="max-w-5xl mx-auto p-6 space-y-8" id="handbookContent">
    <?php
    $sections = $conn->query("SELECT * FROM handbook_sections ORDER BY sort_order ASC, section_number ASC");
    while ($sec = $sections->fetch_assoc()) {
        echo "<section class='bg-white rounded-lg shadow p-5'>";
        echo "<h2 class='text-xl font-bold text-blue-800 mb-2'>{$sec['section_number']} - {$sec['title']}</h2>";

        $subs = $conn->prepare("SELECT * FROM handbook_subsections WHERE section_id = ? ORDER BY sort_order ASC, subsection_number ASC");
        $subs->bind_param("i", $sec['id']);
        $subs->execute();
        $subResult = $subs->get_result();

        while ($sub = $subResult->fetch_assoc()) {
            echo "<div class='mt-4'>";
            echo "<h3 class='text-lg font-semibold text-gray-800'>{$sub['subsection_number']} - {$sub['title']}</h3>";

            $details = $conn->prepare("SELECT * FROM handbook_details WHERE subsection_id = ? ORDER BY sort_order ASC, detail_number ASC");
            $details->bind_param("i", $sub['id']);
            $details->execute();
            $detResult = $details->get_result();

            while ($d = $detResult->fetch_assoc()) {
                echo "<div class='ml-4 mt-3 p-4 bg-gray-50 rounded border handbook-item'>";
                echo "<h4 class='font-medium text-gray-700'>{$d['detail_number']} - {$d['title']}</h4>";
                echo "<p class='text-sm text-gray-700 whitespace-pre-line mt-1'>" . nl2br($d['content']) . "</p>";
                if (!empty($d['image_path'])) {
                    echo "<img src='{$d['image_path']}' class='mt-2 max-w-xs rounded border'>";
                }
                echo "</div>";
            }

            echo "</div>";
        }

        echo "</section>";
    }
    ?>
  </main>

  <!-- Search Script -->
  <script>
    const searchInput = document.getElementById("searchInput");
    const items = document.querySelectorAll(".handbook-item");

    searchInput.addEventListener("input", () => {
      const keyword = searchInput.value.toLowerCase();
      items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(keyword)) {
          item.style.display = "block";
          item.classList.add("highlight");
        } else {
          item.style.display = "none";
          item.classList.remove("highlight");
        }
      });
    });
  </script>
</body>
</html>
