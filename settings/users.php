<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

// Fetch users and roles
$users = $conn->query("
  SELECT u.*, r.role_name AS role_name 
  FROM users u 
  JOIN roles r ON u.role_id = r.id 
  ORDER BY u.created_at DESC
");

$roles = $conn->query("SELECT id, role_name FROM roles");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
      <?php include '../header.php'; ?>
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-semibold mb-0">Manage Users</h4>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="usersTable" class="table table-bordered table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Role</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($user = $users->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($user['staff_name']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['des']) ?></td>
                    <td><?= htmlspecialchars($user['role_name']) ?></td>
                    <td>
                      <button class="btn btn-sm btn-warning" onclick='fillEditModal(<?= json_encode($user) ?>)' data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                      <button class="btn btn-sm btn-danger" onclick='setResetUserId(<?= $user["id"] ?>)' data-bs-toggle="modal" data-bs-target="#resetModal">Reset</button>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="update_user.php" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body row g-3">
                <input type="hidden" name="id" id="editId">
                <div class="col-md-6"><input name="username" id="editUsername" class="form-control" placeholder="Username"></div>
                <div class="col-md-6"><input name="staff_name" id="editName" class="form-control" placeholder="Full Name"></div>
                <div class="col-md-12"><input name="des" id="editDes" class="form-control" placeholder="Designation"></div>
                <div class="col-md-12"><input name="email" id="editEmail" class="form-control" placeholder="Email"></div>
                <div class="col-md-12">
                  <select name="role_id" id="editRole" class="form-select">
                    <?php mysqli_data_seek($roles, 0); while ($r = $roles->fetch_assoc()): ?>
                      <option value="<?= $r['id'] ?>"><?= $r['role_name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-success">Update</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Reset Password Modal -->
        <div class="modal fade" id="resetModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="reset_password.php" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" id="resetUserId">
                <input type="password" name="password" required class="form-control" placeholder="New Password">
              </div>
              <div class="modal-footer">
                <button class="btn btn-danger">Reset</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $('#usersTable').DataTable();
    });

    function fillEditModal(user) {
      document.getElementById('editId').value = user.id;
      document.getElementById('editUsername').value = user.username;
      document.getElementById('editName').value = user.staff_name;
      document.getElementById('editDes').value = user.des;
      document.getElementById('editEmail').value = user.email;
      document.getElementById('editRole').value = user.role_id;
    }

    function setResetUserId(userId) {
      document.getElementById('resetUserId').value = userId;
    }
  </script>
</body>
</html>
