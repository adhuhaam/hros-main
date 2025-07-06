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
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>New Staff Mailer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/preline@latest/dist/preline.js"></script>
</head>
<body class="bg-neutral-900 text-white">

<main class="max-w-full mx-auto p-4">

  <!-- Header -->
  <div class="bg-neutral-800 border border-neutral-700 rounded-lg">
    <div class="px-4 py-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 gap-4 items-center">
      <div class="flex gap-x-4 items-center">
        <img src="https://rcc.mv/wp-content/uploads/2021/08/logo-blue.png" class="w-14 h-14" alt="RCC Logo">
        <div class="animate-pulse">
          <h1 class="text-2xl font-bold text-white">N.MAILER.</h1>
          <p class="text-sm text-neutral-300">New Employee Mail.</p>
        </div>
      </div>
      <div class="flex justify-end items-center gap-x-3">
        <a href="../employee/" class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-neutral-700">Back to HRMS</a> 
        <a href="food_mail.php" class="rounded-md bg-yellow-600 px-4 py-2 text-white hover:bg-neutral-700">FoodMoney Mailer</a> |
        <button type="submit" form="mail1" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">SEND NEW STAFF MAIL</button>
      </div>
    </div>
  </div>



        <?php if (isset($_GET['mail']) && $_GET['mail'] === 'success'): ?>
          <div class="mb-4 p-4 rounded-md bg-green-100 text-green-800 border border-green-300 dark:bg-green-900 dark:text-green-100 dark:border-green-700">
            <strong>Success:</strong> New staff email sent successfully.
          </div>
        <?php elseif (isset($_GET['mail']) && $_GET['mail'] === 'fail'): ?>
          <div class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-300 dark:bg-red-900 dark:text-red-100 dark:border-red-700">
           <strong>Error:</strong> Failed to send new staff email. Please try again.
          </div>
        <?php endif; ?>






  <!-- Table -->
  <form method="POST" id="mail1" action="bulk_action.php" class="mt-6">
    <div class="overflow-x-auto border border-neutral-700 rounded-lg shadow bg-neutral-800">
      <table class="min-w-full divide-y divide-neutral-700">
        <thead class="bg-neutral-900 text-xs text-neutral-400 uppercase">
          <tr>
            <th class="p-3 text-center">
              <input type="checkbox" id="selectAll" class="accent-blue-600" aria-label="Select all">
            </th>
            <th class="px-6 py-3 text-left">Emp No</th>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Nationality</th>
            <th class="px-6 py-3 text-left">Passport/NIC</th>
            <th class="px-6 py-3 text-left">WP No</th>
            <th class="px-6 py-3 text-left">Designation</th>
            <th class="px-6 py-3 text-left">Join Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-700 text-sm">
          <?php while ($row = $result->fetch_assoc()):
            $joinDate = $row['date_of_join'];
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $isNew = ($joinDate === $today || $joinDate === $yesterday);
          ?>
          <tr class="hover:bg-neutral-700">
            <td class="p-3 text-center">
              <input type="checkbox" class="emp-checkbox accent-blue-600" name="selected_emps[]" value="<?= $row['emp_no'] ?>">
            </td>
            <td class="px-6 py-3"><?= htmlspecialchars($row['emp_no']) ?></td>
            <td class="px-6 py-3">
              <?= htmlspecialchars($row['name']) ?>
              <?php if ($isNew): ?>
                <span class="ml-2 inline-block bg-green-500 text-black text-xs px-2 py-0.5 rounded animate-pulse">NEW</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-3"><?= htmlspecialchars($row['nationality']) ?></td>
            <td class="px-6 py-3"><?= htmlspecialchars($row['passport_nic_no']) ?></td>
            <td class="px-6 py-3"><?= htmlspecialchars($row['wp_no'] ?: 'N/A') ?></td>
            <td class="px-6 py-3"><?= htmlspecialchars($row['designation']) ?></td>
            <td class="px-6 py-3"><?= $joinDate ? date('d-M-Y', strtotime($joinDate)) : 'N/A' ?></td>
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

      if ($page > 3) {
        echo '<a href="?page=1" class="px-3 py-2 rounded bg-neutral-800 text-white">1</a><span class="text-gray-500">...</span>';
      }

      for ($i = $start; $i <= $end; $i++):
        $active = $i == $page ? 'bg-blue-600 text-white' : 'bg-neutral-700 text-white';
        echo "<a href='?page=$i' class='px-3 py-2 rounded $active'>$i</a>";
      endfor;

      if ($page < $totalPages - 2) {
        echo "<span class='text-gray-500'>...</span><a href='?page=$totalPages' class='px-3 py-2 rounded bg-neutral-800 text-white'>$totalPages</a>";
      }
    ?>
  </div>

</main>

<script>
  document.getElementById('selectAll').addEventListener('click', function () {
    document.querySelectorAll('.emp-checkbox').forEach(cb => cb.checked = this.checked);
  });
</script>
</body>
</html>
