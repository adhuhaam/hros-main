<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM employees ORDER BY emp_no DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$countSql = "SELECT COUNT(*) as total FROM employees";
$totalRows = $conn->query($countSql)->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Food Mailer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/preline@latest/dist/preline.js"></script>
</head>
<body class="bg-black text-white">
  <div class="max-w-full mx-auto p-4">

    <!-- Header -->
    <div class="bg-neutral-900 border border-neutral-800 rounded-lg shadow">
      <div class="px-4 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-x-4">
          <img src="https://rcc.mv/wp-content/uploads/2021/08/logo-blue.png" class="w-12 h-12" alt="Logo" />
          <div>
            <h1 class="text-2xl font-bold text-white animate-pulse">FOOD MAILER</h1>
            <p class="text-gray-400 text-sm">Send Food Allowance Email for New Staff</p>
          </div>
        </div>
        <div class="flex gap-x-3">
          <a href="../employee/" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">Back to HRMS</a>
          <a href="index.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">New Staff Mailer</a> | 
          <button type="submit" form="foodForm" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">SEND FOOD MAIL</button>
        </div>
      </div>
    </div>

    <!-- Employee Table -->
    <form method="POST" action="send_food_mail.php" id="foodForm" class="mt-6">
      <div class="overflow-x-auto rounded-lg border border-neutral-700 bg-neutral-900 shadow">
        <table class="min-w-full divide-y divide-neutral-700 text-sm">
          <thead class="bg-neutral-800 text-gray-400 uppercase text-xs">
            <tr>
              <th class="p-3 text-center"><input type="checkbox" id="selectAll" class="accent-blue-500" /></th>
              <th class="px-4 py-3 text-left">Emp No</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Nationality</th>
              <th class="px-4 py-3 text-left">Designation</th>
              <th class="px-4 py-3 text-left">Join Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-neutral-800">
            <?php while ($row = $result->fetch_assoc()):
              $joinDate = $row['date_of_join'];
              $today = date('Y-m-d');
              $yesterday = date('Y-m-d', strtotime('-1 day'));
              $isNew = ($joinDate === $today || $joinDate === $yesterday);
            ?>
            <tr class="hover:bg-white hover:text-black transition">
              <td class="p-3 text-center">
                <input type="checkbox" class="emp-checkbox accent-blue-500" name="selected_emps[]" value="<?= htmlspecialchars($row['emp_no']) ?>" />
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['emp_no']) ?></td>
              <td class="px-4 py-2">
                <?= htmlspecialchars($row['name']) ?>
                <?php if ($isNew): ?>
                  <span class="ml-2 bg-green-500 text-black text-xs px-2 py-0.5 rounded animate-pulse">NEW</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nationality']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['designation']) ?></td>
              <td class="px-4 py-2"><?= date('d-M-Y', strtotime($row['date_of_join'])) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </form>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center items-center gap-2 text-sm">
      <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        if ($page > 3) echo '<a href="?page=1" class="px-3 py-2 rounded bg-neutral-800">1</a><span class="text-gray-500">...</span>';
        for ($i = $start; $i <= $end; $i++):
          $active = $i == $page ? 'bg-blue-600 text-white' : 'bg-neutral-800 text-gray-200';
          echo "<a href='?page=$i' class='px-3 py-2 rounded $active'>$i</a>";
        endfor;
        if ($page < $totalPages - 2) echo "<span class='text-gray-500'>...</span><a href='?page=$totalPages' class='px-3 py-2 rounded bg-neutral-800'>$totalPages</a>";
      ?>
    </div>
  </div>

  <script>
    document.getElementById('selectAll').addEventListener('click', function () {
      document.querySelectorAll('.emp-checkbox').forEach(cb => cb.checked = this.checked);
    });
  </script>
</body>
</html>
