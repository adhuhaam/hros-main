<?php
include '../db.php';

$search = $_GET['search'] ?? '';
$query = "SELECT id, emp_no, visa_expiry_date, visa_status FROM visa_sticker";
$params = [];

if ($search !== '') {
    $query .= " WHERE emp_no LIKE ?";
    $params[] = '%' . $search . '%';
}

$query .= " ORDER BY updated_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param('s', ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Visa Sticker Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-semibold">Visa updater</h1>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2 mb-6">
      <input
        type="text"
        name="search"
        placeholder="Search by Employee No..."
        value="<?= htmlspecialchars($search) ?>"
        class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
      />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Search
      </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-blue-600 text-white text-xs uppercase">
          <tr>
            <th class="px-6 py-3">Employee No</th>
            <th class="px-6 py-3">Visa Expiry Date</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-4 font-medium"><?= $row['emp_no'] ?></td>
                <td class="px-6 py-4">
                  <?= $row['visa_expiry_date'] ? date('d-M-Y', strtotime($row['visa_expiry_date'])) : '-' ?>
                </td>

                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold
                    <?php
                      switch ($row['visa_status']) {
                        case 'Pending': echo 'bg-yellow-100 text-yellow-700'; break;
                        case 'Pending Approval': echo 'bg-orange-100 text-orange-700'; break;
                        case 'Ready for Submission': echo 'bg-purple-100 text-purple-700'; break;
                        case 'Ready for Collection': echo 'bg-indigo-100 text-indigo-700'; break;
                        case 'Completed': echo 'bg-green-100 text-green-700'; break;
                        default: echo 'bg-gray-100 text-gray-600';
                      }
                    ?>">
                    <?= $row['visa_status'] ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <a href="edit.php?id=<?= $row['id'] ?>"
                     class="text-blue-600 hover:underline font-semibold">Edit</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-6 py-6 text-center text-gray-500">No records found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
