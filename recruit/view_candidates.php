<?php

include '../session.php';


$host = 'localhost';
$username = 'rccmgvfd_recruit_user';
$password = 'Ompl@65482*';
$dbname = 'rccmgvfd_recruit';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT candidates.*, jobs.title AS job_title, designations.rcc_designation 
        FROM candidates 
        LEFT JOIN jobs ON candidates.job_id = jobs.id
        LEFT JOIN designations ON candidates.designation_id = designations.id
        WHERE candidates.name LIKE '%$search%' OR candidates.passport_number LIKE '%$search%'
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total records for pagination
$total = $conn->query("SELECT COUNT(*) AS count FROM candidates WHERE name LIKE '%$search%'")->fetch_assoc()['count'];
$total_pages = ceil($total / $limit);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Candidates</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
<div class="page-wrapper" id="main-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Uploaded Candidates</h5>

                    <form method="GET" class="mb-3">
                        <input type="text" name="search" placeholder="Search candidates..." value="<?php echo htmlspecialchars($search); ?>" class="form-control" />
                    </form>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Passport No</th>
                                <th>Job Title</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Uploaded Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['passport_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['rcc_designation']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($row['status'] == 'Hired') ? 'success' : (($row['status'] == 'Rejected') ? 'danger' : 'warning'); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d-M-Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

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

<?php $conn->close(); ?>
