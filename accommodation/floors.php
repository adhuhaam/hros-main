<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$building_id = $_GET['building_id'] ?? 0;

// Get building name
$building = $conn->query("SELECT building_name FROM accommodation_buildings WHERE id = $building_id")->fetch_assoc();
$result = $conn->query("SELECT * FROM accommodation_floors WHERE building_id = $building_id");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Floors – <?= htmlspecialchars($building['building_name']) ?></title>
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
          <h5 class="card-title fw-semibold">🏬 Floors in <?= htmlspecialchars($building['building_name']) ?></h5>

          <!-- Back & Add Floor -->
          <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">← Back to Buildings</a>
          <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addFloorModal">➕ Add Floor</button>

          <!-- Floor List -->
          <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>Floor <?= $row['floor_number'] ?></strong>
                </div>
                <div>
                  <a href="rooms.php?floor_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary me-2">View Rooms</a>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal<?= $row['id'] ?>">➕ Add Room</button>
                </div>
              </div>

              <!-- Add Room Modal -->
              <div class="modal fade" id="addRoomModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="addRoomLabel<?= $row['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <form method="post" action="add_room_modal.php" class="modal-content">
                    <input type="hidden" name="floor_id" value="<?= $row['id'] ?>">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addRoomLabel<?= $row['id'] ?>">Add Room to Floor <?= $row['floor_number'] ?></h5>
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
            <?php endwhile; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Floor Modal -->
<div class="modal fade" id="addFloorModal" tabindex="-1" aria-labelledby="addFloorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="add_floor_modal.php" class="modal-content">
      <input type="hidden" name="building_id" value="<?= $building_id ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="addFloorLabel">Add New Floor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Floor Number</label>
        <input type="number" name="floor_number" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add Floor</button>
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
