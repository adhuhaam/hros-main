include '../email/send_mail.php'; // path to your send_mail.php

// Prepare employee details for email
$empDetails = "
  <h2>New Employee Joined</h2>
  <p><strong>Name:</strong> $name</p>
  <p><strong>Employee No:</strong> $emp_no</p>
  <p><strong>Designation:</strong> $designation</p>
  <p><strong>Department:</strong> $department</p>
  <p><strong>Joining Date:</strong> $date_of_join</p>
";

// Fetch mailing group recipients tagged with `new-join`
$getRecipients = $conn->prepare("
  SELECT e.company_email 
  FROM mailing_group m 
  JOIN employees e ON m.emp_no = e.emp_no 
  WHERE m.status = 'Active' AND m.tags LIKE ?
");
$tag = '%new-join%';
$getRecipients->bind_param("s", $tag);
$getRecipients->execute();
$res = $getRecipients->get_result();

$emails = [];
while ($r = $res->fetch_assoc()) {
  if (!empty($r['company_email'])) {
    $emails[] = $r['company_email'];
  }
}

// Send mail
if (!empty($emails)) {
  sendMail("New Employee Joined – $name", $empDetails, $emails, 'automatic', 'System');
}
