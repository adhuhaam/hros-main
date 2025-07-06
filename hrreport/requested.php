<?php
include '../db.php';

// Get date range
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Build SQL with JOINs including nationality
$sql = "SELECT lr.*, e.name, e.nationality, e.designation, lt.name AS leave_type_name
        FROM leave_records lr
        LEFT JOIN employees e ON lr.emp_no = e.emp_no
        LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
        WHERE (lr.status = 'Pending' OR lr.status = 'Approved')";

if (!empty($fromDate) && !empty($toDate)) {
  $sql .= " AND lr.applied_date BETWEEN ? AND ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $fromDate, $toDate);
} else {
  $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

echo '<h5>Staffs Requested for Leave (Pending & Approved)</h5>';
echo '<table class="table table-bordered table-striped">';
echo '<thead>
        <tr>
          <th>S/N</th>
          <th>Emp No</th>
          <th>Name</th>
          <th>Nationality</th>
          <th>Designation</th>
          <th>Leave Type</th>
          <th>Applied Date</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>';

$i = 1;
while ($row = $result->fetch_assoc()) {
  $badgeClass = ($row['status'] == 'Approved') ? 'success' : 'warning text-dark';

  echo '<tr>';
  echo '<td>' . $i++ . '</td>';
  echo '<td>' . htmlspecialchars($row['emp_no'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['name'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['nationality'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['designation'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['leave_type_name'] ?? '-') . '</td>';
  echo '<td>' . (!empty($row['applied_date']) ? date('d-M-Y', strtotime($row['applied_date'])) : '-') . '</td>';
  echo '<td>' . (!empty($row['start_date']) ? date('d-M-Y', strtotime($row['start_date'])) : '-') . '</td>';
  echo '<td>' . (!empty($row['end_date']) ? date('d-M-Y', strtotime($row['end_date'])) : '-') . '</td>';
  echo '<td><span class="badge bg-' . $badgeClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
  echo '</tr>';
}

echo '</tbody></table>';

$stmt->close();
$conn->close();
?>
