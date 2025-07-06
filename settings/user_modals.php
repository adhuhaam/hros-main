<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="create_user.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <input required name="emp_no" class="form-control" placeholder="Employee No">
        </div>
        <div class="col-md-6">
          <input required name="username" class="form-control" placeholder="Username">
        </div>
        <div class="col-md-12">
          <input name="staff_name" class="form-control" placeholder="Full Name">
        </div>
        <div class="col-md-12">
          <input name="des" class="form-control" placeholder="Designation">
        </div>
        <div class="col-md-12">
          <input name="email" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-6">
          <input required name="password" type="password" class="form-control" placeholder="Password">
        </div>
        <div class="col-md-6">
          <select name="role_id" class="form-select" required>
            <option value="">Select Role</option>
            <?php mysqli_data_seek($roles, 0); while($r = $roles->fetch_assoc()): ?>
              <option value="<?= $r['id'] ?>"><?= $r['role_name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save User</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="update_user.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3">
        <input type="hidden" name="id" id="editId">
        <div class="col-md-6">
          <input name="username" id="editUsername" class="form-control" placeholder="Username">
        </div>
        <div class="col-md-6">
          <input name="staff_name" id="editName" class="form-control" placeholder="Full Name">
        </div>
        <div class="col-md-12">
          <input name="des" id="editDes" class="form-control" placeholder="Designation">
        </div>
        <div class="col-md-12">
          <input name="email" id="editEmail" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-12">
          <select name="role_id" id="editRole" class="form-select">
            <?php mysqli_data_seek($roles, 0); while($r = $roles->fetch_assoc()): ?>
              <option value="<?= $r['id'] ?>"><?= $r['role_name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Update User</button>
      </div>
    </form>
  </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="reset_password.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="resetUserId">
        <input type="password" required name="password" class="form-control" placeholder="New Password">
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger">Reset</button>
      </div>
    </form>
  </div>
</div>
