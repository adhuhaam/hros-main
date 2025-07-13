<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$floor_id = $_GET['floor_id'] ?? 0;

// Get floor + building details
$floor = $conn->query("
    SELECT f.floor_number, b.id AS building_id, b.building_name
    FROM accommodation_floors f
    JOIN accommodation_buildings b ON f.building_id = b.id
    WHERE f.id = $floor_id
")->fetch_assoc();

$result = $conn->query("SELECT * FROM accommodation_rooms WHERE floor_id = $floor_id");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rooms – Floor <?= $floor['floor_number'] ?> in <?= $floor['building_name'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
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
          <h5 class="card-title fw-semibold">🚪 Rooms – Floor <?= $floor['floor_number'] ?> (<?= $floor['building_name'] ?>)</h5>

          <!-- Buttons -->
          <a href="floors.php?building_id=<?= $floor['building_id'] ?>" class="btn btn-outline-secondary btn-sm mb-3">← Back to Floors</a>
          <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addRoomModal">➕ Add Room</button>

          <!-- Room List -->
          <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
              <a href="beds.php?room_id=<?= $row['id'] ?>" class="list-group-item list-group-item-action">
                Room <?= $row['room_number'] ?>
              </a>
            <?php endwhile; ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="add_room_modal.php" class="modal-content">
      <input type="hidden" name="floor_id" value="<?= $floor_id ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="addRoomLabel">Add New Room</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Room Number</label>
        <input type="text" name="room_number" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add Room</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

</body>
</html>
