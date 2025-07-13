/// <?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//include 'db.php';

// Fetch notices
///$noticeQuery = "SELECT title, content, created_at FROM notices ORDER BY created_at DESC LIMIT 5";
///$noticesResult = $conn->query($noticeQuery);

/// Fetch holidays
///$holidaysQuery = "SELECT holiday_name, holiday_date FROM holidays ORDER BY holiday_date ASC LIMIT 5";
///$holidaysResult = $conn->query($holidaysQuery);
///?>

<!---doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Company Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="assets/images/logos/favicon.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <style>
    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: 0;
    }
    .content-overlay {
      position: relative;
      z-index: 10;
    }
  </style>
</head>
<body class="bg-blue-900 text-white min-h-screen flex flex-col justify-center items-center relative overflow-hidden">

  <div id="particles-js"></div>

  <div class="content-overlay w-full px-4 py-10 max-w-7xl">
    <div class="flex flex-col items-center">
      <img src="assets/images/logos/dark-logo.svg" class="w-60 mb-6" alt="Logo" />
      <a href="login.php" class="bg-white text-blue-800 px-6 py-2 rounded font-bold shadow hover:bg-gray-100 transition">Login</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
      <div class="bg-white text-gray-800 rounded-lg shadow p-4">
        <h2 class="text-xl font-bold text-center mb-3">Notice Board</h2>
        <?php if ($noticesResult->num_rows > 0): ?>
          <ul class="space-y-3 max-h-80 overflow-auto">
            <?php while ($notice = $noticesResult->fetch_assoc()): ?>
              <li class="bg-gray-50 p-3 rounded">
                <h3 class="font-semibold"><?= htmlspecialchars($notice['title']) ?></h3>
                <p class="text-sm"><?= htmlspecialchars($notice['content']) ?></p>
                <span class="text-xs text-gray-500">Posted on: <?= date('d-M-Y', strtotime($notice['created_at'])) ?></span>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p class="text-center">No notices available.</p>
        <?php endif; ?>
      </div>

      <div class="bg-white text-gray-800 rounded-lg shadow p-4">
        <h2 class="text-xl font-bold text-center mb-3">Upcoming Holidays</h2>
        <?php if ($holidaysResult->num_rows > 0): ?>
          <ul class="space-y-3">
            <?php while ($holiday = $holidaysResult->fetch_assoc()): ?>
              <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                <span><?= htmlspecialchars($holiday['holiday_name']) ?></span>
                <span class="text-sm bg-blue-200 text-blue-800 px-3 py-1 rounded"><?= date('d-M-Y', strtotime($holiday['holiday_date'])) ?></span>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p class="text-center">No holidays scheduled.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    particlesJS("particles-js", {
      particles: {
        number: { value: 60, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle", stroke: { width: 0, color: "#000000" } },
        opacity: { value: 0.5 },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 3 }
      },
      interactivity: {
        detect_on: "canvas",
        events: { onhover: { enable: true, mode: "grab" }, onclick: { enable: true, mode: "push" } },
        modes: {
          grab: { distance: 140, line_linked: { opacity: 1 } },
          push: { particles_nb: 4 }
        }
      },
      retina_detect: true
    });
  </script>
</body>
</html>----->

<script>
  window.location.href = "login.php";
</script>

