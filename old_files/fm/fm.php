<?php

include '../session.php';

// Function to calculate food money correctly including today
function calculateFoodMoney($start_date) {
    $foodMoneyPerMonth = 2000; // Monthly food money in MVR
    $currentDate = new DateTime($start_date);
    $lastDayOfMonth = (clone $currentDate)->modify('last day of this month');

    // Get the total days in the month
    $daysInMonth = (int)$lastDayOfMonth->format('t');

    // Calculate remaining days including today
    $daysRemaining = (int)$currentDate->diff($lastDayOfMonth)->format('%a') + 1;

    // Calculate food money: (2000 / total days in the month) * remaining days
    $foodMoney = ($foodMoneyPerMonth / $daysInMonth) * $daysRemaining;

    return round($foodMoney, 2); // Rounded to two decimal places
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'];

    // Validate input date
    if (DateTime::createFromFormat('Y-m-d', $startDate)) {
        $foodMoney = calculateFoodMoney($startDate);
        $successMessage = "Food money from $startDate (including today) to the end of the month is MVR $foodMoney.";
    } else {
        $errorMessage = "Invalid date format. Please enter a valid date in 'YYYY-MM-DD' format.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Food Money Calculator</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>
        <div class="body-wrapper">
            <?php include '../header.php'; ?>

            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Calculate Food Money</h5>
                        <?php if (isset($successMessage)): ?>
                            <div class="alert alert-success"><?php echo $successMessage; ?></div>
                        <?php elseif (isset($errorMessage)): ?>
                            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <form method="POST" class="shadow p-4 bg-light rounded">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" required class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Calculate</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>

</html>
