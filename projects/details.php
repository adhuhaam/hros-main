<?php
include '../db.php';
include '../session.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid project ID.');
}

$project_id = intval($_GET['id']);

// Fetch project details
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    die('Project not found.');
}

$project = $result->fetch_assoc();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Details</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
  <link rel="stylesheet" href="../assets/css/styles.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <style>
    /* Styling from your provided UI */
    body {
      background: #f4f5f7;
    }
    .details-container {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-top: 20px;
    }
    .carousel-container img {
      max-width: 100%;
      border-radius: 10px;
    }
    .details-heading {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .project-info p {
      margin-bottom: 10px;
    }
    .btn-primary {
      background-color: #5e72e4;
      color: #fff;
      border: none;
    }
  </style>
</head>

<body>
  <!-- Page Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="body-wrapper">
      <!-- Header -->
      <?php include '../header.php'; ?>
      <!-- End Header -->

      <div class="container-fluid">
        <div class="details-container mx-auto">
          <div class="row">
            <!-- Image Carousel -->
            <div class="col-md-6 carousel-container">
              <div id="slider" class="owl-carousel product-slider">
                <?php
                $images = json_decode($project['images'], true);
                if (!empty($images)) {
                    foreach ($images as $image) {
                        echo "<div class='item'><img src='../assets/images/projects/$image' alt='Project Image'></div>";
                    }
                } else {
                    echo "<div class='item'><img src='../assets/images/no-image.png' alt='No Image Available'></div>";
                }
                ?>
              </div>
              <div id="thumb" class="owl-carousel product-thumb mt-2">
                <?php
                if (!empty($images)) {
                    foreach ($images as $image) {
                        echo "<div class='item'><img src='../assets/images/projects/$image' alt='Project Thumbnail'></div>";
                    }
                }
                ?>
              </div>
            </div>
            <!-- Project Information -->
            <div class="col-md-6">
              <div class="details-heading"><?php echo htmlspecialchars($project['name']); ?></div>
              <div class="project-info">
                <p><strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?></p>
                <p><strong>Project Value:</strong> <?php echo number_format($project['project_value'], 2); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
                <p><strong>Start Date:</strong> <?php echo date('d-M-Y', strtotime($project['started_date'])); ?></p>
                <p><strong>End Date:</strong> <?php echo date('d-M-Y', strtotime($project['end_date'])); ?></p>
                <p><strong>Description:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
              </div>
              <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Back to Projects</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script>
    $(document).ready(function () {
      $("#slider").owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        dots: false,
        nav: false,
      });

      $("#thumb").owlCarousel({
        items: 4,
        margin: 10,
        dots: false,
        nav: true,
        navText: ['<', '>'],
        responsive: {
          0: { items: 2 },
          600: { items: 3 },
          1000: { items: 4 }
        }
      });
    });
  </script>
</body>

</html>
