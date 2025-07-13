<?php
// Simple test version of admin dashboard
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Mock data for testing
$activeCount = 150;
$deadCount = 5;
$missingCount = 3;
$resignedCount = 12;
$retiredCount = 8;
$terminatedCount = 7;
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin Dashboard - Test</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard - Test Version</h1>
    
    <!-- Employment Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-blue-600 mb-1">Active</h5>
            <h4 class="text-3xl font-bold text-blue-600"><?php echo $activeCount; ?></h4>
          </div>
          <div class="text-blue-600">
            <i class="fa-solid fa-user-check fa-2x"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-red-600 mb-1">DEAD</h5>
            <h4 class="text-3xl font-bold text-red-600"><?php echo $deadCount; ?></h4>
          </div>
          <div class="text-red-600">
            <i class="fa-solid fa-user-times fa-2x"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-yellow-600 mb-1">MISSING</h5>
            <h4 class="text-3xl font-bold text-yellow-600"><?php echo $missingCount; ?></h4>
          </div>
          <div class="text-yellow-600">
            <i class="fa-solid fa-user-slash fa-2x"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-gray-600 mb-1">RESIGNED</h5>
            <h4 class="text-3xl font-bold text-gray-600"><?php echo $resignedCount; ?></h4>
          </div>
          <div class="text-gray-600">
            <i class="fa-solid fa-user-minus fa-2x"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-cyan-600 mb-1">RETIRED</h5>
            <h4 class="text-3xl font-bold text-cyan-600"><?php echo $retiredCount; ?></h4>
          </div>
          <div class="text-cyan-600">
            <i class="fa-solid fa-user-clock fa-2x"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
          <div>
            <h5 class="text-lg font-semibold text-red-600 mb-1">TERMINATED</h5>
            <h4 class="text-3xl font-bold text-red-600"><?php echo $terminatedCount; ?></h4>
          </div>
          <div class="text-red-600">
            <i class="fa-solid fa-user-xmark fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">Test Status</h2>
      <p class="text-green-600">✅ Dashboard is working! This is a test version without database dependencies.</p>
      <p class="text-gray-600 mt-2">Session user ID: <?php echo $_SESSION['user_id'] ?? 'Not set'; ?></p>
      <p class="text-gray-600">Session role: <?php echo $_SESSION['role'] ?? 'Not set'; ?></p>
    </div>
  </div>
</body>
</html>