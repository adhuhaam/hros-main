<?php
include '../db.php';

$id = $_GET['id'] ?? null;
if (!$id) die('ID required.');

// Get visa_sticker
$vs = $conn->prepare("SELECT * FROM visa_sticker WHERE id = ?");
$vs->bind_param('i', $id);
$vs->execute();
$visa = $vs->get_result()->fetch_assoc();

if (!$visa) die('Visa record not found.');

$emp_no = $visa['emp_no'];

// Get employee data
$emp = $conn->prepare("SELECT passport_nic_no, passport_nic_no_expires FROM employees WHERE emp_no = ?");
$emp->bind_param('s', $emp_no);
$emp->execute();
$employee = $emp->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Visa + Passport</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-2xl mx-auto p-6 mt-10 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Visa & Passport (Emp: <?= $emp_no ?>)</h2>
    <form method="POST" action="update.php" class="space-y-4">
      <input type="hidden" name="id" value="<?= $visa['id'] ?>">
      <input type="hidden" name="emp_no" value="<?= $emp_no ?>">

      <div>
        <label class="block mb-1 font-semibold">Visa Expiry Date</label>
        <input type="date" name="visa_expiry_date" class="w-full border px-3 py-2 rounded"
               value="<?= $visa['visa_expiry_date'] ?>">
      </div>

      <div>
        <label class="block mb-1 font-semibold">Visa Status</label>
        <select name="visa_status" class="w-full border px-3 py-2 rounded">
          <?php
          $statuses = ['Pending','Pending Approval','Ready for Submission','Ready for Collection','Completed'];
          foreach ($statuses as $status) {
              $selected = ($visa['visa_status'] === $status) ? 'selected' : '';
              echo "<option value=\"$status\" $selected>$status</option>";
          }
          ?>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Passport/NIC No</label>
        <input type="text" name="passport_nic_no" class="w-full border px-3 py-2 rounded"
               value="<?= htmlspecialchars($employee['passport_nic_no'] ?? '') ?>">
      </div>

      <div>
        <label class="block mb-1 font-semibold">Passport/NIC Expiry</label>
        <input type="date" name="passport_nic_no_expires" class="w-full border px-3 py-2 rounded"
               value="<?= $employee['passport_nic_no_expires'] ?>">
      </div>

      <div class="flex justify-between">
        <a href="index.php" class="text-gray-600 hover:underline">← Back</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
      </div>
    </form>
  </div>
</body>
</html>
