<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';
include '../session.php';

function getLangCodeByNationality($nationality) {
    $map = [
        'BANGLADESHI' => 'bn',
        'INDIAN' => 'hi',
        'NEPALESE' => 'np',
        'PAKISTANI' => 'pk',
        'SRI LANKAN' => 'ta',
        'TELUGU' => 'te',
        'MALDIVIAN' => 'en'
    ];
    return $map[strtoupper($nationality)] ?? 'en';
}

if (!isset($_GET['emp_no'])) {
    echo "Employee not specified.";
    exit;
}

$emp_no = $_GET['emp_no'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE emp_no = ?");
$stmt->bind_param("s", $emp_no);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

if (!$employee) {
    echo "Employee not found.";
    exit;
}

$lang = getLangCodeByNationality($employee['nationality']);
$templateFile = __DIR__ . "/templates/contract_{$lang}.html";
if (!file_exists($templateFile)) {
    $templateFile = __DIR__ . "/templates/contract_en.html";
}

$template = file_get_contents($templateFile);
$replacements = [
    '{{ name }}' => $employee['name'],
    '{{ emp_no }}' => $employee['emp_no'],
    '{{ nationality }}' => $employee['nationality'],
    '{{ passport_nic_no }}' => $employee['passport_nic_no'] ?? 'N/A',
    '{{ designation }}' => $employee['designation'],
    '{{ level }}' => $employee['level'],
    '{{ today }}' => date('d F Y'),
    '{{ join_date }}' => date('d F Y', strtotime($employee['date_of_join']))
];

foreach ($replacements as $key => $value) {
    $template = str_replace($key, htmlspecialchars($value), $template);
}

$template = preg_replace_callback('/@if\((.*?)\)(.*?)@endif/s', function ($match) use ($employee) {
    extract($employee);
    try {
        return eval("return ($match[1]) ? \"$match[2]\" : '';");
    } catch (Throwable $e) {
        return '';
    }
}, $template);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Preview Contract</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <style>
      body { padding: 2rem; background-color: #f8f9fa; }
      .contract-box {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: auto;
      }
    </style>
</head>
<body>
  <div class="contract-box">
    <?= $template ?>
    <div class="mt-4">
      <a href="save_contract.php?emp_no=<?= $employee['emp_no'] ?>" class="btn btn-success">Save & Generate PDF</a>
      <a href="renew.php" class="btn btn-secondary">Back</a>
    </div>
  </div>
</body>
</html>
