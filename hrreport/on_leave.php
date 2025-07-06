<?php
include '../db.php';

// Build SQL with joins (no date filter) and include nationality
$sql = "SELECT lr.*, e.name, e.nationality, e.designation, lt.name AS leave_type_name
        FROM leave_records lr
        LEFT JOIN employees e ON lr.emp_no = e.emp_no
        LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
        WHERE lr.status = 'Departed'";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

echo '<h5 class="mb-3">Staffs Currently on Leave (Status: Departed)</h5>';
echo '<div class="table-responsive">';
echo '<table class="table table-bordered table-hover align-middle">';
echo '<thead class="table-light">
        <tr>
          <th>S/N</th>
          <th>Emp No</th>
          <th>Name</th>
          <th>Nationality</th>
          <th>Designation</th>
          <th>Leave Type</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
        </tr>
      </thead><tbody>';

$i = 1;
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo '<td>' . $i++ . '</td>';
  echo '<td>' . htmlspecialchars($row['emp_no'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['name'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['nationality'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['designation'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['leave_type_name'] ?? '-') . '</td>';
  echo '<td>' . (!empty($row['start_date']) ? date('d-M-Y', strtotime($row['start_date'])) : '-') . '</td>';
  echo '<td>' . (!empty($row['end_date']) ? date('d-M-Y', strtotime($row['end_date'])) : '-') . '</td>';
  echo '<td><span class="badge bg-primary">' . htmlspecialchars($row['status'] ?? '-') . '</span></td>';
  echo '</tr>';
}

echo '</tbody></table>';
echo '</div>';

$stmt->close();
$conn->close();
?>
