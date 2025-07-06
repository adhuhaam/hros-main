<?php
require '../vendor/autoload.php';
include '../db.php';

use TCPDF;

$emp_no = $_GET['emp_no'] ?? '';
$stmt = $conn->prepare("SELECT emp_no, name, designation FROM employees WHERE emp_no = ?");
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) die("Invalid employee.");

// Data
$emp_no = strtoupper($employee['emp_no']);
$name = strtoupper($employee['name']);
$designation = strtoupper($employee['designation'] ?? '');

// PDF Setup
$pdf = new TCPDF('P', 'mm', [40, 50], true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(2, 2, 2); // narrow margin
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();


// Header (wrapped and centered)
$pdf->SetXY(0, 2);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(40, 3, 'RASHEED CARPENTRY & CONSTRUCTION PVT LTD', 0, 'C');
$pdf->SetFont('helvetica', '', 4);
$pdf->MultiCell (36, 3, 'M.Nector 1st, Asaree Hingun, Malé, 20258', 0, 'C');

// Divider line
$pdf->Line(2, 11, 38, 11);

// Name + Designation
$pdf->SetY(12);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(36, 2, $name, 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(36, 1, $designation, 0, 1, 'C');


// QR Code
$qrSize = 26;
$qrX = 3;
$qrY = 20;
$pdf->write2DBarcode($emp_no, 'QRCODE,H', $qrX, $qrY, $qrSize, $qrSize, [
    'border' => 0,
    'padding' => 0,
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
], 'N');
$pdf->Image('logo.png', 31, 40, 8);

// Vertical emp_no text
$pdf->StartTransform();
$pdf->Rotate(90, 26, 26);
$pdf->SetFont('helvetica', 'B', 21);
$pdf->Text(13, 30, $emp_no);
$pdf->StopTransform();

// Output PDF
$pdf->Output('employee_qr.pdf', 'I');
?>
