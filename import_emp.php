<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Define the formatDate function if not already defined
if (!function_exists('formatDate')) {
    function formatDate($date) {
        $timestamp = strtotime($date);
        return $timestamp ? date('Y-m-d', $timestamp) : null; // Convert to `Y-m-d`
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileExtension === 'csv') {
            // Open the CSV file
            if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                // Read the header row
                $header = fgetcsv($handle);
                $expectedColumns = 28; // Number of columns in the employees table

                // Validate the header column count
                if (count($header) !== $expectedColumns) {
                    die("Error: The CSV file has " . count($header) . " columns, but $expectedColumns columns are expected.");
                }

                // Prepare the SQL query
                $sql = "INSERT INTO employees (
                            emp_no, name, designation, xpat_designation, xpat_join_date,
                            department, nationality, passport_nic_no, passport_nic_no_expires, dob,
                            wp_no, pp_sticker, medical_status, date_of_join, contact_number,
                            emergency_contact_number, emergency_contact_name, employment_status, work_site,
                            religion, insurance_endo_no, insurance_provider, recruiting_agency,
                            location_id, permanent_address, basic_salary, salary_currency, termination_date
                        ) VALUES (
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?
                        )";

                $stmt = $conn->prepare($sql);
                $rowNumber = 1; // Track row numbers for error reporting

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rowNumber++;

                    // Skip rows with incorrect column count
                    if (count($data) !== $expectedColumns) {
                        echo "Skipping row $rowNumber: Column count mismatch (" . count($data) . " columns found).<br>";
                        continue;
                    }

                    // Format date fields
                    $data[4] = formatDate($data[4]); // xpat_join_date
                    $data[8] = formatDate($data[8]); // passport_nic_no_expires
                    $data[9] = formatDate($data[9]); // dob
                    $data[13] = formatDate($data[13]); // date_of_join
                    $data[27] = !empty($data[27]) ? formatDate($data[27]) : null; // termination_date

                    // Handle null values for empty fields
                    $data = array_map(function ($value) {
                        return $value === '' ? null : $value;
                    }, $data);

                    // Bind parameters
                    $stmt->bind_param(
                        "ssssssssssssssssssssssssssss",
                        $data[0], $data[1], $data[2], $data[3], $data[4],
                        $data[5], $data[6], $data[7], $data[8], $data[9],
                        $data[10], $data[11], $data[12], $data[13], $data[14],
                        $data[15], $data[16], $data[17], $data[18], $data[19],
                        $data[20], $data[21], $data[22], $data[23], $data[24],
                        $data[25], $data[26], $data[27]
                    );

                    // Execute query
                    if (!$stmt->execute()) {
                        echo "Error inserting row $rowNumber: " . $stmt->error . "<br>";
                        continue;
                    }
                }

                fclose($handle);
                echo "Employees imported successfully!";
            } else {
                echo "Error opening the CSV file.";
            }
        } else {
            echo "Please upload a valid CSV file.";
        }
    } else {
        echo "Error uploading the file.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Employees</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>
        <div class="body-wrapper">
            <div class="container mt-5">
                <h1 class="text-center">Import Employees</h1>
                <div class="text-end mb-3">
                    <a href="sample_employees.csv" class="btn btn-info">Download Sample CSV</a>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Upload CSV File</label>
                        <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import Employees</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/app.min.js"></script>
</body>

</html>
