<?php
include '../db.php';

// Force status to 'Expired' if visa_expiry_date is today (no exceptions)
$stmt = $conn->prepare("
  UPDATE work_visa
  SET visa_status = 'Expired'
  WHERE visa_expiry_date = CURDATE()
");
$stmt->execute();
$expiredCount = $stmt->affected_rows;

// Otherwise, set status to 'Expiring Soon' for visas expiring in 1–30 days from now
$stmt2 = $conn->prepare("
  UPDATE work_visa
  SET visa_status = 'Expiring Soon'
  WHERE visa_expiry_date > CURDATE()
    AND visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND visa_status != 'Pending'
");
$stmt2->execute();
$soonCount = $stmt2->affected_rows;

// Result output
echo "✅ $expiredCount visa(s) marked as 'Expired' today.\n";
echo "⚠️ $soonCount visa(s) marked as 'Expiring Soon'.\n";
?>
