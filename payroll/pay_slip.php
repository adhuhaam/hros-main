<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';

// Validate GET parameters
if (!isset($_GET['emp_no']) || !isset($_GET['date']) || empty($_GET['emp_no']) || empty($_GET['date'])) {
    die("Employee number and payroll date are required.");
}

$emp_no = $_GET['emp_no'];
$date = $_GET['date']; // Expected format: YYYY-MM-01

// Fetch employee & payroll details
$query = "SELECT e.emp_no, e.name, e.department, e.designation,
                 i.basic_salary, i.service_allowance, i.island_allowance, i.attendance_allowance, 
                 i.salary_arrear_other, i.safety_allowance, i.pump_brick_batching, i.food_and_tea, 
                 i.long_term_service_allowance, i.living_allowance, i.ot, i.ot_arrears, 
                 i.phone_allowance, i.petrol_allowance, i.pension,
                 d.other_deduction, d.salary_advance, d.loan, d.pension AS pension_deduction, 
                 d.medical_deduction, d.no_pay, d.late,
                 (COALESCE(i.basic_salary, 0) + COALESCE(i.service_allowance, 0) + COALESCE(i.island_allowance, 0) + 
                 COALESCE(i.attendance_allowance, 0) + COALESCE(i.salary_arrear_other, 0) + COALESCE(i.safety_allowance, 0) + 
                 COALESCE(i.pump_brick_batching, 0) + COALESCE(i.food_and_tea, 0) + COALESCE(i.long_term_service_allowance, 0) + 
                 COALESCE(i.living_allowance, 0) + COALESCE(i.ot, 0) + COALESCE(i.ot_arrears, 0) + COALESCE(i.phone_allowance, 0) + 
                 COALESCE(i.petrol_allowance, 0) + COALESCE(i.pension, 0)) AS total_earnings,
                 (COALESCE(d.other_deduction, 0) + COALESCE(d.salary_advance, 0) + COALESCE(d.loan, 0) + COALESCE(d.pension, 0) + 
                 COALESCE(d.medical_deduction, 0) + COALESCE(d.no_pay, 0) + COALESCE(d.late, 0)) AS total_deductions,
                 ((COALESCE(i.basic_salary, 0) + COALESCE(i.service_allowance, 0) + COALESCE(i.island_allowance, 0) + 
                 COALESCE(i.attendance_allowance, 0) + COALESCE(i.salary_arrear_other, 0) + COALESCE(i.safety_allowance, 0) + 
                 COALESCE(i.pump_brick_batching, 0) + COALESCE(i.food_and_tea, 0) + COALESCE(i.long_term_service_allowance, 0) + 
                 COALESCE(i.living_allowance, 0) + COALESCE(i.ot, 0) + COALESCE(i.ot_arrears, 0) + COALESCE(i.phone_allowance, 0) + 
                 COALESCE(i.petrol_allowance, 0) + COALESCE(i.pension, 0)) - 
                 (COALESCE(d.other_deduction, 0) + COALESCE(d.salary_advance, 0) + COALESCE(d.loan, 0) + COALESCE(d.pension, 0) + 
                 COALESCE(d.medical_deduction, 0) + COALESCE(d.no_pay, 0) + COALESCE(d.late, 0))) AS net_pay
          FROM employees e
          LEFT JOIN salary_income i ON e.emp_no = i.emp_no AND DATE_FORMAT(i.date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
          LEFT JOIN salary_deductions d ON e.emp_no = d.emp_no AND DATE_FORMAT(d.date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
          WHERE e.emp_no = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $date, $date, $emp_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payroll = $result->fetch_assoc();
} else {
    die("No payroll data found for the selected employee and date.");
}

// Format Payroll Date
$payroll_date = !empty($payroll['date']) ? date('F Y', strtotime($payroll['date'])) : "N/A";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Salary Slip</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <style>
    body { font-size: 12px; margin: 0; padding: 0; background: #fff; }
    .salary-slip { width: 210mm; height: 297mm; margin: auto; padding: 10mm;  background: #fff; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table th, table td { border: 1px solid #ddd; padding: 5px; font-size: 10px; }
    table th { background-color: #f1f1f1; text-align: left; }
    .footer { text-align: center; margin-top: 10px; }
  </style>
</head>

<body>
  <div class="text-center">
    <img src="letter_head.png" width="800px">
  </div>
  <div class="salary-slip">
      
    <div class="text-center mb-3">
      <h5 class="fw-bold">SALARY SLIP</h5>
      <p>Salary slip for the month of <?php 
            $payroll_date = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : ($payroll['date'] ?? null);
        
            if ($payroll_date) {
                echo date('M-Y', strtotime($payroll_date));
            } else {
                echo "N/A"; // If no date is found, show "N/A"
            }
            ?>
        </p>
    </div>
    
    <div class="row">
      <div class="col-6">
        <p><strong>Employee No:</strong> <?php echo $payroll['emp_no']; ?></p>
        <p><strong>Employee Name:</strong> <?php echo $payroll['name']; ?></p>
        <p><strong>Department:</strong> <?php echo $payroll['department']; ?></p>
      </div>
      <div class="col-6">
        <p><strong>Designation:</strong> <?php echo $payroll['designation']; ?></p>
        <p><strong>Payroll Date:</strong> 
            <?php 
            $payroll_date = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : ($payroll['date'] ?? null);
        
            if ($payroll_date) {
                echo date('d-M-Y', strtotime($payroll_date));
            } else {
                echo "N/A"; // If no date is found, show "N/A"
            }
            ?>
        </p>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Earnings</th>
          <th>Amount</th>
          <th>Deductions</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
    <?php
    $earnings = [
        'basic_salary' => 'Basic Salary',
        'service_allowance' => 'Service Allowance',
        'island_allowance' => 'Island Allowance',
        'attendance_allowance' => 'Attendance Allowance',
        'salary_arrear_other' => 'Salary Arrears & Other',
        'safety_allowance' => 'Safety Allowance',
        'pump_brick_batching' => 'Pump/Brick/Batching',
        'food_and_tea' => 'Food and Tea',
        'long_term_service_allowance' => 'Long-Term Service Allowance',
        'living_allowance' => 'Living Allowance',
        'ot' => 'Overtime',
        'ot_arrears' => 'Overtime Arrears',
        'phone_allowance' => 'Phone Allowance',
        'petrol_allowance' => 'Petrol Allowance',
        'pension' => 'Pension'
    ];

    $deductions = [
        'other_deduction' => 'Other Deduction',
        'salary_advance' => 'Salary Advance',
        'loan' => 'Loan Deduction',
        'pension_deduction' => 'Pension Deduction',
        'medical_deduction' => 'Medical Deduction',
        'no_pay' => 'No Pay',
        'late' => 'Late Deduction'
    ];

    // Find the max number of rows needed
    $maxRows = max(count($earnings), count($deductions));
    $earningKeys = array_keys($earnings);
    $deductionKeys = array_keys($deductions);

    for ($i = 0; $i < $maxRows; $i++) {
        echo "<tr>";

        // Earnings Column
        if (isset($earningKeys[$i])) {
            $earningKey = $earningKeys[$i];
            echo "<td>{$earnings[$earningKey]}</td>";
            echo "<td>" . number_format($payroll[$earningKey] ?? 0.00, 2) . "</td>";
        } else {
            echo "<td></td><td></td>"; // Empty columns if no more earnings
        }

        // Deductions Column
        if (isset($deductionKeys[$i])) {
            $deductionKey = $deductionKeys[$i];
            echo "<td>{$deductions[$deductionKey]}</td>";
            echo "<td>" . number_format($payroll[$deductionKey] ?? 0.00, 2) . "</td>";
        } else {
            echo "<td></td><td></td>"; // Empty columns if no more deductions
        }

        echo "</tr>";
    }
    ?>
    
    <!-- Total Earnings & Deductions Row -->
    <tr class="table-primary fw-bold">
        <td>Total Earnings</td>
        <td><?php echo number_format($payroll['total_earnings'] ?? 0.00, 2); ?></td>
        <td>Total Deductions</td>
        <td><?php echo number_format($payroll['total_deductions'] ?? 0.00, 2); ?></td>
    </tr>
    
    <!-- Net Pay Row -->
    <tr class="table-success fw-bold">
        <td colspan="2">Net Salary</td>
        <td colspan="2"><?php echo number_format($payroll['net_pay'] ?? 0.00, 2); ?></td>
    </tr>
</tbody>
    </table>

    <div class="footer">
      <p>Authorized Signatory</p>
    </div>
  </div>
</body>
</html>

<script>
    window.print();
</script>