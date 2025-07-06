<?php
// save_contract.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';
require '../vendor/autoload.php'; // DomPDF

use Dompdf\Dompdf;

if (!isset($_GET['emp_no'])) {
    die("No employee selected.");
}

$emp_no = $_GET['emp_no'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE emp_no = ?");
$stmt->bind_param("s", $emp_no);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

if (!$employee) {
    die("Employee not found.");
}

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

$lang = getLangCodeByNationality($employee['nationality']);
$templateFile = __DIR__ . "/templates/contract_{$lang}.html";
if (!file_exists($templateFile)) {
    $templateFile = __DIR__ . "/templates/contract_en.html";
}

$html = file_get_contents($templateFile);
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
    $html = str_replace($key, htmlspecialchars($value), $html);
}

$html = preg_replace_callback('/@if\((.*?)\)(.*?)@endif/s', function ($match) use ($employee) {
    extract($employee);
    try {
        return eval("return ($match[1]) ? \"$match[2]\" : '';");
    } catch (Throwable $e) {
        return '';
    }
}, $html);

$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->setPaper('A4', 'portrait');
$pdf->render();

$folder = __DIR__ . "/uploads/contracts/";
if (!is_dir($folder)) mkdir($folder, 0777, true);

$filename = $employee['emp_no'] . '_renewal_' . date('Ymd') . '.pdf';
file_put_contents($folder . $filename, $pdf->output());

$stmt = $conn->prepare("INSERT INTO e_contracts (emp_no, contract_type, contract_title, contract_file, issued_date, status, variant) VALUES (?, 'Renewal', ?, ?, ?, 'Active', 'renewal')");
$title = 'Renewal Contract - ' . $employee['name'];
$issued_date = date('Y-m-d');
$filepath = 'uploads/contracts/' . $filename;
$stmt->bind_param("ssss", $employee['emp_no'], $title, $filepath, $issued_date);
$stmt->execute();

header("Location: index.php?success=1");
exit;
