<?php
include '../db.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Updated SQL to include nationality
$sql = "SELECT emp_no, name, designation, termination_date, employment_status, nationality
        FROM employees 
        WHERE employment_status IN ('Terminated', 'Resigned', 'Missing', 'Retired')";

if (!empty($fromDate) && !empty($toDate)) {
  $sql .= " AND termination_date BETWEEN ? AND ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $fromDate, $toDate);
} else {
  $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

echo '<h5>Staffs Terminated / Resigned / Missing / Retired</h5>';
echo '<table class="table table-bordered table-striped">';
echo '<thead>
        <tr>
          <th>S/N</th>
          <th>Emp No</th>
          <th>Name</th>
          <th>Designation</th>
          <th>Nationality</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>';

$i = 1;
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo '<td>' . $i++ . '</td>';
  echo '<td>' . htmlspecialchars($row['emp_no'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['name'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['designation'] ?? '-') . '</td>';
  echo '<td>' . htmlspecialchars($row['nationality'] ?? '-') . '</td>';
  echo '<td>' . (!empty($row['termination_date']) ? date('d-M-Y', strtotime($row['termination_date'])) : '-') . '</td>';
  echo '<td><span class="badge bg-danger">' . htmlspecialchars($row['employment_status']) . '</span></td>';
  echo '</tr>';
}

echo '</tbody></table>';

$stmt->close();
$conn->close();
?>
