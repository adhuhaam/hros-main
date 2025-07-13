<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

// Toast trigger
$toast_message = '';
if (isset($_GET['added'])) {
    $toast_message = 'Building added successfully!';
} elseif (isset($_GET['edited'])) {
    $toast_message = 'Building updated successfully!';
}

// Fetch buildings
$buildingResult = $conn->query("SELECT * FROM accommodation_buildings");

// Room and bed summary
$summary = $conn->query("
    SELECT
        (SELECT COUNT(*) FROM  accommodation_buildings) AS total_buildings,
        (SELECT COUNT(*) FROM accommodation_rooms) AS total_rooms,
        (SELECT COUNT(*) FROM accommodation_beds) AS total_beds,
        (SELECT COUNT(*) FROM accommodation_beds WHERE occupied_by IS NOT NULL) AS occupied_beds,
        (SELECT COUNT(*) FROM accommodation_beds WHERE occupied_by IS NULL) AS vacant_beds
")->fetch_assoc();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Accommodation – Buildings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .summary-card .card {
      transition: all 0.3s;
    }
    .summary-card .card:hover {
      transform: scale(1.02);
      box-shadow: 0 0 0.75rem rgba(0, 0, 0, 0.1);
    }
    .summary-icon {
      font-size: 2rem;
      opacity: 0.7;
    }
    .building-badges .badge {
      margin-right: 5px;
      font-size: 12px;
    }
  </style>
</head>
<body>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

  <?php include '../sidebar.php'; ?>

  <div class="body-wrapper">
    <?php include '../header.php'; ?>

    <div class="container-fluid" style="max-width:100%;">

      <!-- Summary Cards -->
      <div class="row summary-card g-3 mb-4">
        <div class="col-md-2">
          <div class="card text-bg-primary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="card-title text-white mb-1">Total Buildings</h6>
                <h4 class="text-cener fs-4 text-white"><?= $summary['total_buildings'] ?></h4>
              </div>
              <div class="summary-icon"><i class="ti ti-building"></i></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-bg-secondary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="card-title text-white mb-1">Total Rooms</h6>
                <h4 class="text-cener fs-4 text-white"><?= $summary['total_rooms'] ?></h4>
              </div>
              <div class="summary-icon"><i class="ti ti-home"></i></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-bg-secondary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="card-title text-white mb-1">Total Beds</h6>
                <h4 class="text-cener fs-4 text-white"><?= $summary['total_beds'] ?></h4>
              </div>
              <div class="summary-icon"><i class="ti ti-bed"></i></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-bg-danger h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="card-title text-white mb-1">Occupied Beds</h6>
                <h4 class="text-cener fs-4 text-white"><?= $summary['occupied_beds'] ?></h4>
              </div>
              <div class="summary-icon"><i class="ti ti-user-check"></i></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-bg-success h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="card-title text-white mb-1">Vacant Beds</h6>
                <h4 class="text-cener fs-4 text-white"><?= $summary['vacant_beds'] ?></h4>
              </div>
              <div class="summary-icon"><i class="ti ti-user-exclamation"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Toast Notification -->
      <?php if ($toast_message): ?>
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body"><?= htmlspecialchars($toast_message) ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Building List -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold fs-6 text-center">RCC Staff Accommodation – Buildings</h5>

          <button class="btn btn-primary btn-sm mb-4 text-end" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
            + Add Building
          </button>

          <!-- Search Input -->
          <input type="text" id="buildingSearch" class="form-control mb-3" placeholder="Search buildings by name or location...">

          <div class="list-group" id="buildingList">
            <?php while ($row = $buildingResult->fetch_assoc()): ?>
              <?php
                $building_id = $row['id'];

                $floor_count = $conn->query("SELECT COUNT(*) AS total FROM accommodation_floors WHERE building_id = $building_id")->fetch_assoc()['total'];
                $room_count = $conn->query("
                  SELECT COUNT(*) AS total 
                  FROM accommodation_rooms r
                  JOIN accommodation_floors f ON r.floor_id = f.id
                  WHERE f.building_id = $building_id
                ")->fetch_assoc()['total'];
                $bed_stats = $conn->query("
                  SELECT 
                    SUM(CASE WHEN b.occupied_by IS NOT NULL THEN 1 ELSE 0 END) AS occupied,
                    SUM(CASE WHEN b.occupied_by IS NULL THEN 1 ELSE 0 END) AS vacant
                  FROM accommodation_beds b
                  JOIN accommodation_rooms r ON b.room_id = r.id
                  JOIN accommodation_floors f ON r.floor_id = f.id
                  WHERE f.building_id = $building_id
                ")->fetch_assoc();
              ?>
              <div class="list-group-item building-item d-flex justify-content-between align-items-center">
                <div>
                  <a href="floors.php?building_id=<?= $building_id ?>" class="text-decoration-none text-dark ">
                    <strong class="fs-5 text-primary"><?= htmlspecialchars($row['building_name']) ?></strong><br>
                    <small><?= htmlspecialchars($row['location']) ?></small>
                  </a>
                  <div class="building-badges mt-2">
                    <span class="badge bg-info text-dark">Floors: <?= $floor_count ?></span>
                    <span class="badge bg-secondary">Rooms: <?= $room_count ?></span>
                    <span class="badge bg-success">Vacant Beds: <?= $bed_stats['vacant'] ?? 0 ?></span>
                    <span class="badge bg-danger">Occupied Beds: <?= $bed_stats['occupied'] ?? 0 ?></span>
                  </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary edit-btn" 
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['building_name']) ?>"
                        data-location="<?= htmlspecialchars($row['location']) ?>"
                        data-bs-toggle="modal" data-bs-target="#editBuildingModal">
                  ✏️
                </button>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addBuildingModal" tabindex="-1" aria-labelledby="addBuildingLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" action="add_building_modal.php" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBuildingLabel">Add New Building</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Building Name</label>
              <input type="text" class="form-control" name="building_name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Add</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editBuildingModal" tabindex="-1" aria-labelledby="editBuildingLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" action="edit_building_modal.php" class="modal-content">
          <input type="hidden" name="building_id" id="edit-building-id">
          <div class="modal-header">
            <h5 class="modal-title" id="editBuildingLabel">Edit Building</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Building Name</label>
              <input type="text" class="form-control" name="building_name" id="edit-building-name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" id="edit-building-location" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).on('click', '.edit-btn', function () {
    $('#edit-building-id').val($(this).data('id'));
    $('#edit-building-name').val($(this).data('name'));
    $('#edit-building-location').val($(this).data('location'));
  });

  // Realtime building search
  document.getElementById('buildingSearch').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const items = document.querySelectorAll('.building-item');

    items.forEach(function (item) {
      const text = item.innerText.toLowerCase();
      item.style.display = text.includes(keyword) ? 'flex' : 'none';
    });
  });
</script>
</body>
</html>
