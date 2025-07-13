<?php
// Sample data – in real implementation, fetch from DB or $_POST/$_GET
$name = "MOTALIF";
$emp_id = "1187";
$designation = "BARBENDER";
$department = "PROJECTS";
$joining_date = "22-Oct-2005";
$request_date = date('d-M-Y'); // Auto-fill today
$reason = "reason explained here...";
$location = "Hulhumale";
$gender = "Male";
$hod_name = "JP";
$hod_designation = "Project Manager";
$hrm_name = "Adam Rakheem";
$hrm_position = "Director, Projects";
$chairman_name = "Mohamed Nazim";
$chairman_position = "Chairman";
$md_name = "Ibrahim Rasheed";
$md_position = "Managing Director";
?>

<body style="margin: 0; background: gainsboro;">
  <div style="position: relative; margin: auto; width: 59.50rem;">
    <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">

      <img src="rcc_logo.png" class="image" style="auto; height: 43px; display: block; z-index: 10; left: 4.45rem; top: 3.88rem;" />

      <p class="paragraph body-text" style="width: 26.33rem; height: 2.14rem; font-size: 1.10rem; left: 30.92rem; top: 3.89rem; text-align: left; font-family: arial, serif; font-weight: 600;">
        <span>RASHEED CARPENTRY AND CONSTRUCTIONS PVT LTD</span>
      </p>

      <p class="paragraph body-text" style="width: 53.05rem; height: 1.61rem; left: 3.70rem; top: 6.03rem; text-align: right; font-family: 'Poppins Light', serif; font-weight: 300;">
        <span>RETIREMENT REQUEST FORM</span>
      </p>

      <!-- ID and Name Fields -->
      <div class="textbox" style="width: 4.36rem; height: 1.45rem; left: 8.01rem; top: 11.23rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif; font-size: 0.8rem;"><?php echo $emp_id; ?></p>
      </div>
      <div class="textbox" style="width: 16.67rem; height: 1.45rem; left: 39.77rem; top: 11.23rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif;"><?php echo $name; ?></p>
      </div>

      <!-- Department and Designation -->
      <div class="textbox" style="width: 10.40rem; height: 1.45rem; left: 10.26rem; top: 13.55rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif;"><?php echo $department; ?></p>
      </div>
      <div class="textbox" style="width: 16.67rem; height: 1.45rem; left: 39.77rem; top: 13.55rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif;"><?php echo $designation; ?></p>
      </div>

      <!-- Join Date and Request Date -->
      <div class="textbox" style="width: 10.40rem; height: 1.45rem; left: 10.26rem; top: 15.87rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif;"><?php echo $joining_date; ?></p>
      </div>
      <div class="textbox" style="width: 16.67rem; height: 1.45rem; left: 39.77rem; top: 15.87rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 5px; font-family: Arial, serif;"><?php echo $request_date; ?></p>
      </div>

      <!-- Gender, Location, Designation Again -->
      <p class="paragraph body-text" style="left: 4.47rem; top: 17.17rem; position: absolute; font-family: Arial;">
        Male: <?php echo $gender; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Location: <?php echo $location; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Designation: <?php echo $designation; ?>
      </p>

      <!-- Reason Box -->
      <div class="textbox" style="width: 52.24rem; height: 5.00rem; left: 4.25rem; top: 22.48rem; border: 1px solid #000; position: absolute;">
        <p style="margin: 8px; font-family: Arial, serif;"><?php echo $reason; ?></p>
      </div>

      <!-- HOD Name/Signature -->
      <p class="paragraph table-paragraph" style="width: 37.65rem; height: 1.90rem; left: 4.25rem; top: 48.20rem; text-align: left; position: absolute;">
        Name: <strong><?php echo $hod_name; ?></strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Designation: <strong><?php echo $hod_designation; ?></strong>
      </p>

      <!-- Final Decision Section -->
      <p style="position: absolute; left: 4.25rem; top: 66.14rem; font-family: Arial; font-weight: bold;">
        Final Decision by the Management:
      </p>

      <!-- Bottom Signatories -->
      <p class="paragraph table-paragraph" style="left: 4.25rem; top: 79.0rem; position: absolute; font-family: Arial, serif;">
        <?php echo $hrm_name; ?> &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $chairman_name; ?> &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $md_name; ?><br>
        <strong><?php echo $hrm_position; ?></strong> &nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo $chairman_position; ?></strong> &nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo $md_position; ?></strong>
      </p>

    </div>
  </div>
</body>
