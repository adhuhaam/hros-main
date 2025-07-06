<?php
include '../db.php';
include '../session.php';

$room_id = $_GET['room_id'] ?? 0;

// Room hierarchy info
$room = $conn->query("
    SELECT r.room_number, f.id AS floor_id, f.floor_number, b.id AS building_id, b.building_name
    FROM accommodation_rooms r
    JOIN accommodation_floors f ON r.floor_id = f.id
    JOIN accommodation_buildings b ON f.building_id = b.id
    WHERE r.id = $room_id
")->fetch_assoc();

$result = $conn->query("
    SELECT 
        b.*, 
        e.name AS emp_name,
        e.passport_nic_no,
        e.designation,
        ed.photo_file_name
    FROM accommodation_beds b
    LEFT JOIN employees e ON b.occupied_by = e.emp_no
    LEFT JOIN employee_documents ed ON b.occupied_by = ed.emp_no AND ed.doc_type = 'Photo'
    WHERE b.room_id = $room_id
");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Beds – Room <?= $room['room_number'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <style>
    .emp-photo {
      width: 60px;
      height: 60px;
      border-radius: 6px;
      object-fit: cover;
    }
    .emp-card {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .emp-info {
      line-height: 1.3;
    }
    .badge-status {
      font-size: 13px;
    }
  </style>
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid">
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title fw-semibold">
            🛏 Beds – Room <?= htmlspecialchars($room['room_number']) ?> | Floor <?= $room['floor_number'] ?> | <?= htmlspecialchars($room['building_name']) ?>
          </h5>

          <a href="rooms.php?floor_id=<?= $room['floor_id'] ?>" class="btn btn-outline-secondary btn-sm mb-3">← Back to Rooms</a>
          <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addBedModal">➕ Add Bed</button>

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th># Bed Number</th>
                  <th>Status</th>
                  <th>Bed Assigned to</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="fw-bold text-center"><?= htmlspecialchars($row['bed_number']) ?></td>

                    <td>
                      <?php if ($row['occupied_by']): ?>
                        <span class="badge bg-danger badge-status">Occupied</span>
                      <?php else: ?>
                        <span class="badge bg-success badge-status">Vacant</span>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if ($row['occupied_by']): ?>
                        <div class="emp-card">
                          <img src="../assets/document/<?= htmlspecialchars($row['photo_file_name']) ?: 'default.png' ?>" class="emp-photo" alt="Photo">
                          <div class="emp-info">
                            <strong><?= htmlspecialchars($row['emp_name']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($row['designation']) ?></small><br>
                            <span class="badge bg-info text-dark">Passport: <?= htmlspecialchars($row['passport_nic_no']) ?></span>
                          </div>
                        </div>
                      <?php else: ?>
                        <em class="text-muted">No employee assigned</em>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if ($row['occupied_by']): ?>
                        <button class="btn btn-sm btn-outline-danger unassign-btn" data-id="<?= $row['id'] ?>">Unassign</button>
                      <?php else: ?>
                        <div class="d-flex align-items-center">
                          <select class="form-select form-select-sm emp-select me-2" data-bed="<?= $row['id'] ?>" style="min-width: 220px;"></select>
                          <button class="btn btn-sm btn-outline-success assign-btn" data-id="<?= $row['id'] ?>">Assign</button>
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Bed Modal -->
<div class="modal fade" id="addBedModal" tabindex="-1" aria-labelledby="addBedLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="add_bed_modal.php" class="modal-content">
      <input type="hidden" name="room_id" value="<?= $room_id ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="addBedLabel">Add New Bed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Bed Number</label>
        <input type="text" class="form-control" name="bed_number" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add Bed</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

<script>
  // Initialize all select2 fields
  $(document).ready(function () {
    $('.emp-select').each(function () {
      const select = $(this);
      select.select2({
        placeholder: "Search employee...",
        ajax: {
          url: "search_employee.php",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return { term: params.term };
          },
          processResults: function (data) {
            return {
              results: data.map(emp => ({
                id: emp.value,
                text: emp.label
              }))
            };
          },
          cache: true
        },
        minimumInputLength: 2
      });
    });
  });

  // Assign employee to bed
  $(document).on('click', '.assign-btn', function () {
    const bed_id = $(this).data('id');
    const emp_no = $(this).closest('td').find('.emp-select').val();

    if (!emp_no) return alert("Please select an employee");

    $.post('assign_bed.php', { bed_id, emp_no }, function (res) {
      alert(res);
      location.reload();
    });
  });

  // Unassign employee from bed
  $(document).on('click', '.unassign-btn', function () {
    const bed_id = $(this).data('id');
    $.post('unassign_bed.php', { bed_id }, function (res) {
      alert(res);
      location.reload();
    });
  });
</script>

</body>
</html>
