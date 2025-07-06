<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id'])) {
  echo "No ID provided.";
  exit;
}

$id = $_GET['id'];

$sql = "SELECT r.*, e.name, e.designation, e.department, e.date_of_join, e.nationality, e.dob, e.passport_nic_no
        FROM retirement_records r
        JOIN employees e ON r.emp_no = e.emp_no
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "No record found.";
  exit;
}

$data = $result->fetch_assoc();
?>

<!-------  DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Retirement Request Form</title>
  <style>
    @media print {
      .no-print { display: none; }
    }

    body {
      font-family: Arial, sans-serif;
      width: 21cm;
      min-height: 29.7cm;
      margin: 0 auto;
      padding: 2cm;
      box-sizing: border-box;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      text-transform: uppercase;
      font-size: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }

    td {
      padding: 8px;
      vertical-align: top;
    }

    .label {
      width: 25%;
      font-weight: bold;
    }

    .signature-box {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
    }

    .signature {
      width: 30%;
      text-align: center;
    }

    .signature-line {
      margin-top: 60px;
      border-top: 1px solid #000;
      width: 100%;
    }

    .section {
      margin-top: 25px;
    }

    textarea {
      width: 100%;
      height: 80px;
      padding: 8px;
      border: 1px solid #000;
      box-sizing: border-box;
      font-size: 14px;
    }

    .checkbox-group {
      margin-top: 20px;
    }

    .checkbox-group label {
      margin-right: 30px;
    }

    .print-btn {
      text-align: right;
      margin-bottom: 20px;
    }

    .print-btn button {
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .title-bar {
      text-align: center;
      font-weight: bold;
      font-size: 16px;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

  </style>
</head>
<body>

<div class="no-print print-btn">
  <button onclick="window.print()">ЁЯЦия╕П Print</button>
</div>

<h2>RASHEED CARPENTRY AND CONSTRUCTIONS PVT LTD</h2>
<div class="title-bar">RETIREMENT REQUEST FORM</div>

<table>
  <tr><td class="label">ID:</td><td><?= $data['id'] ?></td></tr>
  <tr><td class="label">Name:</td><td><?= htmlspecialchars($data['name']) ?></td></tr>
  <tr><td class="label">Department:</td><td><?= htmlspecialchars($data['department']) ?></td></tr>
  <tr><td class="label">Designation:</td><td><?= htmlspecialchars($data['designation']) ?></td></tr>
  <tr><td class="label">Date of Join:</td><td><?= date('d-M-Y', strtotime($data['date_of_join'])) ?></td></tr>
  <tr><td class="label">Date:</td><td><?= date('d-M-Y', strtotime($data['retirement_date'])) ?></td></tr>
  <tr><td class="label">Destination:</td><td><?= htmlspecialchars($data['destination'] ?? '') ?></td></tr>
  <tr><td class="label">Reason:</td><td><?= htmlspecialchars($data['reason']) ?></td></tr>
</table>

<div class="section">
  <strong>Employee Statement</strong>
  <textarea readonly><?= htmlspecialchars($data['employee_statement'] ?? '') ?></textarea>
</div>

<div class="section">
  <strong>HOD Statement / Opinion</strong>
  <textarea readonly><?= htmlspecialchars($data['hod_opinion'] ?? '') ?></textarea>
</div>

<div class="section">
  <strong>HR Department Statement / Opinion</strong>
  <textarea readonly><?= htmlspecialchars($data['hr_opinion'] ?? '') ?></textarea>
</div>

<div class="section checkbox-group">
  <strong>Final Decision by the Management:</strong><br><br>
  <label>
    <input type="checkbox" <?= $data['status'] === 'Approved' ? 'checked' : '' ?>> Accept Resignation
  </label>
  <label>
    <input type="checkbox" <?= $data['status'] === 'Rejected' ? 'checked' : '' ?>> Talk to employee to reverse decision
  </label>
</div>

<div class="signature-box">
  <div class="signature">
    <div>Adam Rakheem</div>
    <div>Director, Projects</div>
    <div class="signature-line"></div>
  </div>
  <div class="signature">
    <div>Mohamed Nazim</div>
    <div>Chairman</div>
    <div class="signature-line"></div>
  </div>
  <div class="signature">
    <div>Managing Director</div>
    <div class="signature-line"></div>
  </div>
</div>

</body>
</html>
----->

































<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<html lang="en" style="font-size: 10.0pt;">
 <head>
  <title>result</title>
  <style>
   p { margin: 0; }
   .page { margin: 10pt auto; position: relative; }
   span.position, p.paragraph { position: absolute; display: block; }
   span.position { transform-origin: left; text-align: left; white-space: nowrap; }
   span.style { white-space: nowrap; }
   td { padding: 0px; }
   a.link { color: inherit; text-decoration: inherit; }
   div.group, div.textbox, svg.graphic, img.image, div.rotation { position: absolute; }
   .noselect {
       -webkit-touch-callout: none;
       -webkit-user-select: none;
       -khtml-user-select: none;
       -moz-user-select: none;
       -ms-user-select: none;
       user-select: none;
   }
   @media print { @page { size: 595.0pt 842.0pt; margin: 0pt 0pt 0pt 0pt; } html, body { width: 595.0pt; height: 842.0pt; font-size: 10pt; } }
   @media print { .page { margin: 0pt; } }
   p.body-text { font-family: 'Times New Roman', serif; font-size: 1.00rem; color: #000000; font-weight: 700; }
   p.list-paragraph { font-family: arial, serif; font-size: 1.10rem; color: #000000; }
   p.table-paragraph { font-family: 'Times New Roman', serif; font-size: 1.10rem; color: #000000; }
  </style>
 </head>
 <body style="margin: 0; background: gainsboro;">
  <div style="position: relative; margin: auto; width: 59.50rem;">
   <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
    <img src="rcc_logo.png" class="image" style="auto; height: 43px; display: block; z-index: 10; left: 4.45rem; top: 3.88rem;"/>
    <div class="textbox" style="width: 9.32rem; height: 1.22rem; display: block; z-index: 10; left: 23.73rem; top: 62.79rem;">
     <table class="table" style="width: 9.32rem; height: 1.22rem; table-layout: fixed; z-index: 10; position: absolute; left: 0.00rem; top: 0.00rem; border-collapse: collapse;">
      <tbody>
       <tr>
        <td style="border: none; height: 0.0rem; width: 6.230rem;"/>
        <td style="border: none; height: 0.0rem; width: 3.090rem;"/>
       </tr>
       <tr>
        <td rowspan="1" colspan="1" class="cell" style="width: 6.230rem; height: 1.220rem; vertical-align: top;">
        </td>
        <td rowspan="1" colspan="1" class="cell" style="width: 3.090rem; height: 1.220rem; vertical-align: top;">
        </td>
       </tr>
      </tbody>
     </table>
    </div>
    <p class="paragraph body-text" style="width: 26.33rem; height: 2.14rem; font-size: 1.10rem; left: 30.92rem; top: 3.89rem; text-align: left; font-family: arial, serif; font-weight: 600;">
     <span id="sheet1" class="position style" style="width: 1.35rem; height: 1.40rem; font-size: 1.00rem; left: 0.00rem; top: 0.61rem; font-family: 'Poppins SemiBold', serif;">
RASHEED CARPENTRY AND CONSTRUCTIONS PVT LTD</span>
    </p>
    <p class="paragraph body-text" style="width: 53.05rem; height: 1.61rem; left: 3.70rem; top: 6.03rem; text-align: right; font-family: 'Poppins Light', serif; font-weight: 300;">
     <span class="position style" style="width: 0.59rem; height: 1.40rem; left: 40.02rem; top: -0.06rem;">RETIREMENT REQUEST FORM</span>
    </p>
    <div class="textbox" style="width: 16.67rem; height: 1.45rem; display: block; z-index: 10; left: 39.77rem; top: 11.23rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 16.33rem; height: 1.17rem; z-index: 10; font-size: 0.90rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 3.91rem; height: 1.00rem; left: 0.05rem; top: 0.17rem;"><?= htmlspecialchars($data['name']) ?></span>
     </p>
    </div>
    <div class="textbox" style="width: 4.36rem; height: 1.45rem; display: block; z-index: -10; left: 8.01rem; top: 11.23rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 4.02rem; height: 1.07rem; z-index: -10; font-size: 0.80rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 1.85rem; height: 0.92rem; left: 0.05rem; top: 0.16rem;"><?= $data['emp_no'] ?></span>
     </p>
    </div>
    <p class="paragraph body-text" style="width: 52.77rem; height: 1.15rem; left: 4.47rem; top: 11.39rem; text-align: left;">
     <span class="position style" style="width: 1.10rem; height: 1.10rem; left: 0.00rem; top: 0.05rem;">Emp no: </span>
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 32.31rem; top: 0.05rem;">Name:</span>
    </p>
    <div class="textbox" style="width: 16.67rem; height: 1.45rem; display: block; z-index: 10; left: 39.77rem; top: 13.55rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 16.33rem; height: 1.17rem; z-index: 10; font-size: 0.90rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 5.63rem; height: 1.00rem; left: 0.05rem; top: 0.17rem;"><?= htmlspecialchars($data['designation']) ?></span>
     </p>
    </div>
    <div class="textbox" style="width: 10.40rem; height: 1.45rem; display: block; z-index: -10; left: 10.26rem; top: 13.55rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 10.06rem; height: 1.07rem; z-index: -10; font-size: 0.80rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 4.42rem; height: 0.92rem; left: 0.05rem; top: 0.16rem;"><?= htmlspecialchars($data['department']) ?></span>
     </p>
    </div>
    <p class="paragraph body-text" style="width: 52.77rem; height: 1.15rem; left: 4.47rem; top: 13.71rem; text-align: left;">
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 0.00rem; top: 0.05rem;">Department:</span>
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 29.47rem; top: 0.05rem;">Designation:</span>
    </p>
    <div class="textbox" style="width: 16.67rem; height: 1.45rem; display: block; z-index: 10; left: 39.77rem; top: 15.87rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 16.33rem; height: 1.17rem; z-index: 10; font-size: 0.85rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 1.24rem; height: 0.95rem; left: 0.05rem; top: 0.22rem;"><?= date('d-M-Y', strtotime($data['retirement_date'])) ?></span>
     </p>
    </div>
    <div class="textbox" style="width: 10.40rem; height: 1.45rem; display: block; z-index: -10; left: 10.26rem; top: 15.87rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 10.06rem; height: 1.07rem; z-index: -10; font-size: 0.80rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 1.20rem; height: 0.92rem; left: 0.05rem; top: 0.16rem;"><?= date('d-M-Y', strtotime($data['date_of_join'])) ?></span>
     </p>
    </div>
    <p class="paragraph body-text" style="width: 52.77rem; height: 1.15rem; left: 4.47rem; top: 16.02rem; text-align: left;">
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 0.00rem; top: 0.05rem;">Date of Join:</span>
     <span class="position style" style="width: 1.22rem; height: 1.10rem; left: 32.78rem; top: 0.05rem;">Date:</span>
    </p>
    <div class="group" style="width: 2.35rem; height: 1.60rem; display: block; left: 18.36rem; top: 17.86rem;">
     <svg viewbox="0.000000, 0.000000, 23.550000, 14.800000" class="graphic" style="width: 2.35rem; height: 1.48rem; display: block; z-index: -10; left: 0.00rem; top: 0.13rem;">
      <path fill="#000000" fill-opacity="1.000000" d="M 0.959 0 L 0 0 L 0 14.76 L 0.959 14.76 L 0.959 0 Z" stroke="none"/>
      <path fill="#000000" fill-opacity="1.000000" d="M 23.52 0.961 L 22.56 0.961 L 22.56 13.8 L 0.96 13.8 L 0.96 14.76 L 22.56 14.76 L 23.52 14.76 L 23.52 13.8 L 23.52 0.961 Z" stroke="none"/>
      <path fill="#000000" fill-opacity="1.000000" d="M 23.52 0 L 22.78 0 L 22.78 0.96 L 23.52 0.96 L 23.52 0 Z" stroke="none"/>
     </svg>
     <svg viewbox="0.000000, 0.000000, 21.700000, 13.850000" class="graphic" style="width: 2.17rem; height: 1.39rem; display: block; z-index: -10; left: 0.06rem; top: 0.05rem;">
      <path stroke-width="0.999982" fill="none" d="M 0 13.836 L 21.691 13.836 L 21.691 -4.44089e-14 L 0 -4.44089e-14 L 0 13.836 Z" stroke="#000000" stroke-opacity="1.000000"/>
     </svg>
    </div>
    <div class="group" style="width: 2.41rem; height: 1.60rem; display: block; left: 7.90rem; top: 17.86rem;">
     <svg viewbox="0.000000, 0.000000, 23.550000, 13.800000" class="graphic" style="width: 2.35rem; height: 1.38rem; display: block; z-index: -10; left: 0.06rem; top: 0.22rem;">
      <path fill="#000000" fill-opacity="1.000000" d="M 0.959 13.03 L 0 13.03 L 0 13.799 L 0.959 13.799 L 0.959 13.03 Z" stroke="none"/>
      <path fill="#000000" fill-opacity="1.000000" d="M 23.52 0 L 22.56 0 L 22.56 13.03 L 0.96 13.03 L 0.96 13.799 L 22.56 13.799 L 23.52 13.799 L 23.52 13.03 L 23.52 0 Z" stroke="none"/>
     </svg>
     <svg viewbox="0.000000, 0.000000, 23.000000, 14.300000" class="graphic" style="width: 2.30rem; height: 1.43rem; display: block; z-index: -10; left: 0.05rem; top: 0.05rem;">
      <path stroke-width="0.999967" fill="none" d="M 0 14.272 L 23 14.272 L 23 -4.26326e-14 L 0 -4.26326e-14 L 0 14.272 Z" stroke="#000000" stroke-opacity="1.000000"/>
     </svg>
     <div class="textbox" style="width: 2.20rem; height: 1.33rem; display: block; z-index: -10; left: 0.10rem; top: 0.10rem;">
      <p class="paragraph body-text" style="width: 1.59rem; height: 1.32rem; z-index: -10; font-size: 1.15rem; left: 0.61rem; top: 0.00rem; text-align: left; font-family: 'MS Gothic', serif; font-weight: 400;">
       <span class="position style" style="width: 1.17rem; height: 1.17rem; left: 0.00rem; top: 0.06rem;">✔</span>
      </p>
     </div>
    </div>
    <svg viewbox="0.000000, 0.000000, 167.650000, 14.800000" class="graphic" style="width: 16.77rem; height: 1.48rem; display: block; z-index: 10; left: 39.72rem; top: 17.99rem;">
     <path fill="#000000" fill-opacity="1.000000" d="M 0.959 0 L 0 0 L 0 14.76 L 0.959 14.76 L 0.959 0 Z" stroke="none"/>
     <path fill="#000000" fill-opacity="1.000000" d="M 167.64 0.961 L 166.68 0.961 L 166.68 13.8 L 0.96 13.8 L 0.96 14.76 L 166.68 14.76 L 167.64 14.76 L 167.64 13.8 L 167.64 0.961 Z" stroke="none"/>
     <path fill="#000000" fill-opacity="1.000000" d="M 167.64 0 L 0.96 0 L 0.96 0.96 L 167.64 0.96 L 167.64 0 Z" stroke="none"/>
    </svg>
    <div class="textbox" style="width: 46.18rem; height: 1.60rem; display: block; z-index: 10; left: 10.26rem; top: 20.14rem; border: 0.10rem solid #000000;">
     <p class="paragraph body-text" style="width: 45.84rem; height: 1.33rem; z-index: 10; font-size: 0.95rem; left: 0.25rem; top: 0.05rem; text-align: left; font-family: Arial, serif;">
      <span class="position style" style="width: 1.69rem; height: 1.09rem; left: 0.05rem; top: 0.24rem; transform: ScaleX(0.90);">Too</span>
      
     
     </p>
    </div>
    <div class="textbox" style="width: 52.24rem; height: 12.42rem; display: block; z-index: 10; left: 4.25rem; top: 22.48rem;">
     <table class="table" style="width: 52.14rem; height: 12.36rem; table-layout: fixed; z-index: 10; position: absolute; left: 0.00rem; top: 0.05rem; border-collapse: collapse;">
      <tbody>
       <tr>
        <td style="border: none; height: 0.0rem; width: 37.895rem;"/>
        <td style="border: none; height: 0.0rem; width: 14.245rem;"/>
       </tr>
       <tr>
        <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 3.190rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
         <p class="paragraph table-paragraph" style="width: 51.85rem; height: 1.28rem; z-index: 10; left: 0.29rem; top: 0.05rem; text-align: left; font-weight: 700;">
          <span class="position style" style="width: 0.74rem; height: 1.22rem; left: -0.05rem; top: 0.06rem;">Reason</span>
         </p>
        </td>
       </tr>
       <tr>
        <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 1.810rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
         <p class="paragraph table-paragraph" style="width: 51.74rem; height: 1.40rem; z-index: 10; font-size: 1.05rem; left: 0.40rem; top: 3.24rem; text-align: left; font-family: Arial, serif; font-weight: 700;">
          <span class="position style" style="width: 0.30rem; height: 1.20rem; left: -0.05rem; top: 0.21rem;">reason explained</span>
         </p>
        </td>
       </tr>
       <tr>
        <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 1.810rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
         <p class="paragraph table-paragraph" style="width: 51.43rem; height: 1.40rem; z-index: 10; font-size: 1.05rem; left: 0.70rem; top: 5.06rem; text-align: left; font-family: Arial, serif; font-weight: 700;">
          
         </p>
        </td>
       </tr>
       <tr>
        <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 1.665rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
        </td>
       </tr>
       <tr>
        <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 37.895rem; height: 1.740rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top;">
        </td>
        <td rowspan="2" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 14.245rem; height: 3.835rem; border-top: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
         <p class="paragraph table-paragraph" style="width: 11.84rem; height: 1.24rem; z-index: 10; left: 37.90rem; top: 10.94rem; text-align: center; font-weight: 400;">
          <span class="position style" style="width: 0.61rem; height: 1.22rem; left: 4.75rem; top: 0.04rem;">Sign:</span>
         </p>
        </td>
       </tr>
       <tr>
        <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 37.895rem; height: 2.095rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top;">
         <p class="paragraph table-paragraph" style="width: 37.65rem; height: 1.90rem; z-index: 10; left: 0.29rem; top: 10.27rem; text-align: left; font-weight: 400;">
          <span class="position style" style="width: 0.80rem; height: 1.22rem; left: -0.05rem; top: 0.71rem;">Name:</span>
          <span class="position" style="width: 5.24rem; height: 1.34rem; left: 3.65rem; top: 0.47rem;">
           <span class="style" style="transform: ScaleX(1.50);"> </span>
           <span class="style" style="font-family: Arial, serif; font-size: 1.20rem; font-weight: 700;">MOTALIF</span>
          </span>
          <span class="position" style="width: 0.80rem; height: 1.22rem; left: 19.71rem; top: 0.71rem;">
           <span class="style" style="font-family: Arial, serif; font-size: 1.20rem; font-weight: 700;">	</span>
           <span class="style">Designation</span>
          </span>
          <span class="position" style="width: 7.52rem; height: 1.34rem; left: 25.51rem; top: 0.51rem;">
           <span class="style"> </span>
           <span class="style" style="font-family: Arial, serif; font-size: 1.20rem; font-weight: 700;">BARBENDER</span>
          </span>
         </p>
        </td>
       </tr>
      </tbody>
     </table>
    </div>
    <p class="paragraph body-text" style="width: 35.14rem; height: 5.57rem; left: 4.47rem; top: 17.17rem; text-align: left;">
     <span class="position style" style="width: 0.94rem; height: 1.10rem; left: 0.00rem; top: 1.00rem;">Male:</span>
     <span class="position style" style="width: 0.77rem; height: 1.10rem; left: 8.07rem; top: 1.00rem;">Hulhumale:</span>
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 29.64rem; top: 1.00rem;">Designation:</span>
     <span class="position style" style="width: 0.72rem; height: 1.10rem; left: 0.00rem; top: 3.31rem;">Reason:</span>
    </p>
    <table class="table" style="width: 52.14rem; height: 14.67rem; table-layout: fixed; z-index: 0; position: absolute; left: 4.25rem; top: 35.57rem; border-collapse: collapse;">
     <tbody>
      <tr>
       <td style="border: none; height: 0.0rem; width: 37.895rem;"/>
       <td style="border: none; height: 0.0rem; width: 14.245rem;"/>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 3.405rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 51.85rem; height: 1.28rem; left: 0.29rem; top: 0.05rem; text-align: left; font-weight: 700;">
         <span class="position style" style="width: 2.53rem; height: 1.22rem; left: -0.05rem; top: 0.06rem;">HOD Statement / Opinion</span>
        </p>
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 2.095rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 2.025rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 2.100rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 37.895rem; height: 2.095rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top;">
       </td>
       <td rowspan="2" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 14.245rem; height: 4.995rem; border-top: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 11.84rem; height: 1.24rem; left: 37.90rem; top: 13.25rem; text-align: center; font-weight: 400;">
         <span class="position style" style="width: 0.61rem; height: 1.22rem; left: 4.75rem; top: 0.04rem;">Sign:</span>
        </p>
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 37.895rem; height: 2.900rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top;">
        <p class="paragraph table-paragraph" style="width: 37.65rem; height: 1.24rem; left: 0.29rem; top: 13.25rem; text-align: left; font-weight: 400;">
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: -0.05rem; top: 0.04rem;">Name: jp</span>
         <span class="position style" style="width: 5.40rem; height: 1.22rem; left: 19.44rem; top: 0.04rem;">Designation: </span>
         <span class="position" style="width: 3.13rem; height: 1.22rem; left: 25.80rem; top: 0.04rem;">
          <span class="style" style="transform: ScaleX(1.50);"> </span>
          <span class="style">Project</span>
         </span>
         <span class="position style" style="width: 3.92rem; height: 1.22rem; left: 29.21rem; top: 0.04rem;"> Manager</span>
        </p>
       </td>
      </tr>
     </tbody>
    </table>
    <table class="table" style="width: 52.16rem; height: 13.71rem; table-layout: fixed; z-index: 0; position: absolute; left: 4.24rem; top: 50.86rem; border-collapse: collapse;">
     <tbody>
      <tr>
       <td style="border: none; height: 0.0rem; width: 52.160rem;"/>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.160rem; height: 3.465rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.15rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 51.83rem; height: 1.28rem; left: 0.30rem; top: 0.05rem; text-align: left; font-weight: 700;">
         <span class="position style" style="width: 0.86rem; height: 1.22rem; left: -0.05rem; top: 0.06rem;">HRM Statement / opinion</span>
        </p>
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.160rem; height: 2.085rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.15rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.160rem; height: 2.315rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.15rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.160rem; height: 2.170rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.15rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.160rem; height: 3.620rem; border-top: 0.10rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.15rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 51.70rem; height: 1.41rem; left: 0.43rem; top: 11.73rem; text-align: left; font-weight: 400;">
         <span class="position style" style="width: 2.95rem; height: 1.22rem; left: -0.05rem; top: 0.06rem;">Name:</span>
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: 19.31rem; top: 0.19rem;">Designation:</span>
         <span class="position style" style="width: 1.52rem; height: 1.22rem; left: 25.62rem; top: 0.19rem;">Sign:</span>
        </p>
       </td>
      </tr>
     </tbody>
    </table>
    <div class="group" style="width: 52.14rem; height: 0.10rem; display: block; left: 4.26rem; top: 65.87rem;">
     <svg viewbox="0.000000, 0.000000, 521.450000, 1.000000" class="graphic" style="width: 52.15rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
      <path fill="#000000" fill-opacity="1.000000" d="M 521.401 0 L -3.55271e-15 0 L -3.55271e-15 0.959 L 521.401 0.959 L 521.401 0 Z" stroke="none"/>
     </svg>
    </div>
    <table class="table" style="width: 52.14rem; height: 15.02rem; table-layout: fixed; z-index: 0; position: absolute; left: 4.25rem; top: 66.14rem; border-collapse: collapse;">
     <tbody>
      <tr>
       <td style="border: none; height: 0.0rem; width: 8.075rem;"/>
       <td style="border: none; height: 0.0rem; width: 44.065rem;"/>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.07rem solid#000000; width: 52.140rem; height: 7.575rem; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 51.81rem; height: 1.27rem; left: 0.33rem; top: 0.05rem; text-align: left; font-weight: 700;">
         <span class="position style" style="width: 0.67rem; height: 1.22rem; left: -0.05rem; top: 0.05rem;">Final Decision by the Management</span>
        </p>
        <div class="group" style="width: 2.36rem; height: 1.76rem; display: block; left: 3.75rem; top: 1.97rem;">
         <svg viewbox="0.000000, 0.000000, 23.600000, 17.650000" class="graphic" style="width: 2.36rem; height: 1.76rem; display: block; z-index: -10; left: -0.00rem; top: 0.00rem;">
          <path fill="#000000" fill-opacity="1.000000" d="M 23.566 0.045 L 22.638 0.045 L 22.638 0 L 22.606 0 L 22.606 0.96 L 22.606 16.654 L 1.005 16.654 L 1.005 0.96 L 22.606 0.96 L 22.606 0 L 0 0 L 0 0.96 L 0.046 0.96 L 0.046 17.61 L 0.131 17.61 L 0.131 17.614 L 23.544 17.614 L 23.544 17.077 L 23.566 17.077 L 23.566 0.045 Z" stroke="none"/>
         </svg>
        </div>
        <div class="group" style="width: 2.36rem; height: 1.70rem; display: block; left: 25.80rem; top: 1.98rem;">
         <svg viewbox="0.000000, 0.000000, 23.650000, 17.000000" class="graphic" style="width: 2.36rem; height: 1.70rem; display: block; z-index: -10; left: -0.00rem; top: 0.00rem;">
          <path fill="#000000" fill-opacity="1.000000" d="M 23.647 0.083 L 23.612 0.083 L 23.612 0 L 22.687 0 L 22.687 0.96 L 22.687 15.993 L 1.006 15.993 L 1.006 0.96 L 22.687 0.96 L 22.687 0 L 0 0 L 0 0.96 L 0.046 0.96 L 0.046 16.991 L 1.006 16.991 L 1.006 16.953 L 22.687 16.953 L 22.687 16.97 L 23.647 16.97 L 23.647 0.083 Z" stroke="none"/>
         </svg>
        </div>
        <p class="paragraph table-paragraph" style="width: 51.17rem; height: 2.10rem; left: 0.10rem; top: 1.33rem; text-align: center; font-weight: 400;">
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: 6.27rem; top: 0.88rem;">Accept Resignation</span>
         <span class="position style" style="width: 0.67rem; height: 1.22rem; left: 28.49rem; top: 0.84rem;">Talk to employee to reverse decision</span>
        </p>
        <img src="data:image/png;base64,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" class="image" style="width: 0.00rem; height: 0.00rem; display: block; z-index: 0; left: 0.18rem; top: 7.62rem;"/>
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="1" class="cell" style="border-bottom: 0.10rem solid#000000; width: 8.075rem; height: 4.590rem; border-top: 0.07rem solid#000000; border-left: 0.10rem solid#000000; vertical-align: top;">
       </td>
       <td rowspan="1" colspan="1" class="cell" style="width: 44.065rem; height: 4.590rem; border-top: 0.07rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
       </td>
      </tr>
      <tr>
       <td rowspan="1" colspan="2" class="cell" style="border-bottom: 0.10rem solid#000000; width: 52.140rem; height: 2.800rem; border-left: 0.10rem solid#000000; vertical-align: top; border-right: 0.10rem solid#000000;">
        <p class="paragraph table-paragraph" style="width: 51.85rem; height: 1.28rem; left: 0.29rem; top: 12.42rem; text-align: left; font-weight: 400;">
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: -0.05rem; top: 0.06rem;">Adam Rakheem</span>
         <span class="position style" style="width: 1.54rem; height: 1.22rem; left: 16.32rem; top: 0.06rem;">Mohamed Nazim</span>
         <span class="position style" style="width: 0.37rem; height: 1.22rem; left: 39.78rem; top: 0.06rem;">IbrahimRasheed</span>
        </p>
        <p class="paragraph table-paragraph" style="width: 51.85rem; height: 1.42rem; left: 0.29rem; top: 13.70rem; text-align: left; font-weight: 700;">
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: -0.05rem; top: 0.23rem;">Director,Projects</span>
         
         <span class="position style" style="width: 0.80rem; height: 1.22rem; left: 16.32rem; top: 0.23rem;">Chairman</span>
         
         <span class="position style" style="width: 1.04rem; height: 1.22rem; left: 39.00rem; top: 0.23rem;">Managing Director</span>
        </p>
       </td>
      </tr>
     </tbody>
    </table>
   </div>
  </div>
 </body>
</html>

