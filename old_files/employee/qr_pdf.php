<?php
require '../vendor/autoload.php';
include '../db.php';

use TCPDF;

// Fetch employee data
$emp_no = $_GET['emp_no'] ?? '';
$stmt = $conn->prepare("SELECT emp_no, name, designation FROM employees WHERE emp_no = ?");
$stmt->bind_param('s', $emp_no);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Invalid employee.");
}

// Convert to uppercase
$emp_no = strtoupper($employee['emp_no']);
$name = strtoupper($employee['name']);
$designation = ucfirst(strtolower($employee['designation'] ?? ''));

// Create PDF
$pdf = new TCPDF('P', 'mm', [40, 50], true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// === QR CODE ===
$qrSize = 30;
$qrX = (37 - $qrSize) / 3;
$qrY = 3;
$style = ['border' => 0, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false];
$pdf->write2DBarcode($emp_no, 'QRCODE,H', $qrX, $qrY, $qrSize, $qrSize, $style, 'N');

// === EMP NO (Large, bottom-left) ===
$pdf->SetFont('imagine_font', '', 38);
$pdf->SetXY(2, 20);
$pdf->Cell(27, 8, $emp_no, 0, 1, 'L');

// === DESIGNATION (small, bottom-left below emp_no) ===
$pdf->SetFont('imagine_font', 'B', 'L', 8);
$pdf->SetXY(2, 41);
$pdf->Cell(36, 4, $designation, 0, 0, 'L');

// === rcc (small, bottom-left below emp_no) ===
$pdf->SetFont('Ariel', 'L', 4.5);
$pdf->SetXY(2, 44);
$pdf->Cell(36, 4, 'Rasheed Carpentry & Construction', 0, 0, 'L');

// === NAME (Vertical, rotated 90° on right) ===
$pdf->StartTransform();
$pdf->Rotate(90, 40, 37); // X, Y as pivot
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Text(28, 31.5, $name);
$pdf->StopTransform();

// === Output ===
$pdf->Output("qr_{$emp_no}.pdf", 'I');
