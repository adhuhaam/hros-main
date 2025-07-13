<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$search = $_GET['search'] ?? '';
$success = $error = '';

// Handle Add form
if (isset($_POST['add_recipient'])) {
  $emp_no = $_POST['emp_no'];
  $tags = $_POST['tags'];
  $status = $_POST['status'];

  $exists = $conn->prepare("SELECT * FROM mailing_group WHERE emp_no = ?");
  $exists->bind_param("s", $emp_no);
  $exists->execute();
  $res = $exists->get_result();

  if ($res->num_rows > 0) {
    $error = "This employee is already subscribed.";
  } else {
    $add = $conn->prepare("INSERT INTO mailing_group (emp_no, tags, status) VALUES (?, ?, ?)");
    $add->bind_param("sss", $emp_no, $tags, $status);
    $add->execute();
    $success = "Recipient added successfully.";
  }
}

// Handle Update
if (isset($_POST['update_recipient'])) {
  $id = $_POST['edit_id'];
  $tags = $_POST['edit_tags'];
  $status = $_POST['edit_status'];

  $update = $conn->prepare("UPDATE mailing_group SET tags = ?, status = ? WHERE id = ?");
  $update->bind_param("ssi", $tags, $status, $id);
  if ($update->execute()) {
    $success = "Recipient updated successfully.";
  } else {
    $error = "Update failed.";
  }
}

$sql = "
  SELECT mg.*, e.name, e.company_email 
  FROM mailing_group mg 
  JOIN employees e ON mg.emp_no = e.emp_no
  WHERE e.name LIKE '%$search%' OR mg.emp_no LIKE '%$search%' OR mg.tags LIKE '%$search%'
  ORDER BY mg.created_at DESC
";
$result = $conn->query($sql);
$employees = $conn->query("SELECT emp_no, name FROM employees WHERE company_email IS NOT NULL ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Mailing Group</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
      @theme {
  --color-mint-500: oklch(0.72 0.11 178);
}
    </style>
</head>

<body class="bg-white text-gray-800">
  <?php include 'sidebar.php'; ?>
  <div class="ml-64 p-6">
    <!-- adjust this based on sidebar width -->
    <!-- Your page content here -->

    <div class="max-w-7xl mx-auto p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Mailing Group</h1>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ Add Recipient</button>
      </div>

      <?php if ($success): ?>
      <div class="mb-4 p-4 bg-green-100 text-green-700 rounded"><?= $success ?></div>
      <?php elseif ($error): ?>
      <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?= $error ?></div>
      <?php endif; ?>

      <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
          placeholder="Search by name, emp_no or tag" class="w-full border border-gray-300 p-2 rounded shadow-sm">
      </form>

      <div class="overflow-auto bg-white shadow-md rounded">
          

          
          
        <table class="min-w-full table-auto text-sm ">
          <thead class="bg-light-900 text-left">
            <tr>
              <th class="p-3">Employee No</th>
              <th class="p-3">Name</th>
              <th class="p-3">Email</th>
              <th class="p-3">Tags</th>
              <th class="p-3">Status</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-t">
              <td class="p-3"><?= $row['emp_no'] ?></td>
              <td class="p-3"><?= $row['name'] ?></td>
              <td class="p-3"><?= $row['company_email'] ?></td>
              <td class="p-3"><?= $row['tags'] ?></td>
              <td class="p-3">
                <span
                  class="px-2 py-1 text-xs rounded-full <?= $row['status'] === 'Active' ? 'bg-green-200 text-green-800' : 'bg-gray-300 text-gray-800' ?>">
                  <?= $row['status'] ?>
                </span>
              </td>
              <td class="p-3 space-x-2">
                
                <button type="button" onclick='openEditModal(<?= json_encode($row) ?>)'
                  class="text-blue-900 hover:underline rounded">Edit</button>
                  
                <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline"
                  onclick="return confirm('Are you sure?')">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg w-full max-w-lg p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Add Recipient</h2>
          <button onclick="document.getElementById('addModal').classList.add('hidden')"
            class="text-gray-500 hover:text-black text-xl">&times;</button>
        </div>
        <form method="POST">
          <div class="mb-4">
            <label class="block text-sm mb-1">Employee</label>
            <select name="emp_no" required class="w-full border border-gray-300 p-2 rounded">
              <option value="">-- Select --</option>
              <?php $employees->data_seek(0);
              while ($e = $employees->fetch_assoc()): ?>
              <option value="<?= $e['emp_no'] ?>"><?= $e['name'] ?> (<?= $e['emp_no'] ?>)</option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm mb-1">Tags</label>
            <input type="text" name="tags" required placeholder="e.g. new-join,payroll"
              class="w-full border border-gray-300 p-2 rounded">
          </div>
          <div class="mb-4">
            <label class="block text-sm mb-1">Status</label>
            <select name="status" required class="w-full border border-gray-300 p-2 rounded">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
          <div class="text-right">
            <button type="submit" name="add_recipient"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
      <div class="bg-white rounded-lg w-full max-w-lg p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Edit Recipient</h2>
          <button onclick="closeEditModal()" class="text-gray-500 hover:text-black text-xl">&times;</button>
        </div>
        <form method="POST">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="mb-4">
            <label class="block text-sm mb-1">Employee</label>
            <input type="text" id="edit_emp" class="w-full bg-gray-100 p-2 rounded border" readonly>
          </div>
          <div class="mb-4">
            <label class="block text-sm mb-1">Tags</label>
            <input type="text" name="edit_tags" id="edit_tags" class="w-full border p-2 rounded" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm mb-1">Status</label>
            <select name="edit_status" id="edit_status" class="w-full border p-2 rounded" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
          <div class="text-right">
            <button type="submit" name="update_recipient"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      function openEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_emp').value = data.name + ' (' + data.emp_no + ')';
        document.getElementById('edit_tags').value = data.tags;
        document.getElementById('edit_status').value = data.status;
        document.getElementById('editModal').classList.remove('hidden');
      }

      function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
      }
    </script>
  </div>
</body>

</html>