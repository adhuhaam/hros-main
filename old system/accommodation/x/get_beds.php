<?php
include '../db.php';

$room_id = $_GET['id'];
$result = $conn->query("
    SELECT b.*, e.name AS emp_name
    FROM accommodation_beds b
    LEFT JOIN employees e ON b.occupied_by = e.emp_no
    WHERE b.room_id = $room_id
");

echo "<div class='card mt-4'>";
echo "<div class='card-body'>";
echo "<div class='d-flex justify-content-between align-items-center mb-3'>";
echo "<h5 class='card-title mb-0'>🛏 Beds</h5>";
echo "<a href='add_bed.php?room_id=$room_id' class='btn btn-sm btn-primary'>➕ Add Bed</a>";
echo "</div>";

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-hover'>";
    echo "<thead><tr><th>Bed Number</th><th>Status</th><th>Action</th></tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        $status = $row['occupied_by']
            ? "<span class='badge bg-danger'>Occupied by {$row['emp_name']}</span>"
            : "<span class='badge bg-success'>Vacant</span>";

        $action = $row['occupied_by']
            ? "<button class='btn btn-sm btn-outline-danger unassign-btn' data-id='{$row['id']}'>Unassign</button>"
            : "<div class='d-flex align-items-center'>
                   <input type='text' class='form-control form-control-sm emp-input me-2' placeholder='Emp No' style='width: 100px;'>
                   <button class='btn btn-sm btn-outline-success assign-btn' data-id='{$row['id']}'>Assign</button>
               </div>";

        echo "<tr>
                <td>{$row['bed_number']}</td>
                <td>$status</td>
                <td>$action</td>
              </tr>";
    }

    echo "</tbody></table></div>";
} else {
    echo "<p class='text-muted'>No beds found in this room.</p>";
}

echo "</div></div>";
