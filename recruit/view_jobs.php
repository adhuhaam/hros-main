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

$sql = "SELECT jobs.*, designations.rcc_designation 
        FROM jobs 
        LEFT JOIN designations ON jobs.designation_id = designations.id
        WHERE jobs.title LIKE '%$search%' OR designations.rcc_designation LIKE '%$search%'
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total records for pagination
$total = $conn->query("SELECT COUNT(*) AS count FROM jobs WHERE title LIKE '%$search%'")->fetch_assoc()['count'];
$total_pages = ceil($total / $limit);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Jobs</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
<div class="page-wrapper" id="main-wrapper">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Job Vacancies</h5>

                    <form method="GET" class="mb-3">
                        <input type="text" name="search" placeholder="Search jobs..." value="<?php echo htmlspecialchars($search); ?>" class="form-control" />
                    </form>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Designation</th>
                                <th>Positions</th>
                                <th>Salary Range</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['rcc_designation']); ?></td>
                                    <td><?php echo htmlspecialchars($row['number_of_positions']); ?></td>
                                    <td><?php echo htmlspecialchars($row['proposed_salary_range']); ?></td>
                                    <td><?php echo ucfirst($row['status']); ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($row['posted_date'])); ?></td>
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
