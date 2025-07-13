<?php
include '../db.php';
include '../session.php';

// Pagination settings
$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search Query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$whereClause = "WHERE 1 ";

if (!empty($search)) {
    $whereClause .= "AND (e.emp_no LIKE '%$search%' OR e.ticket_status LIKE '%$search%' OR d.destination_name LIKE '%$search%')";
}

if (!empty($from_date) && !empty($to_date)) {
    $whereClause .= " AND (e.departure_date BETWEEN '$from_date' AND '$to_date')";
}

// Fetch total number of records for pagination
$total_records_query = "SELECT COUNT(*) AS total FROM employee_tickets e 
    LEFT JOIN employee_tickets_destination d ON e.destination = d.destination_name 
    $whereClause";
$total_result = $conn->query($total_records_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Total pages calculation
$total_pages = ceil($total_records / $limit);

// Fetch Status Counts
$status_counts = [
    'total' => 0,
    'pending' => 0,
    'reservation_sent' => 0,
    'ticket_received' => 0,
    'departed' => 0,
    'completed' => 0
];

$status_query = "SELECT ticket_status, COUNT(*) AS count FROM employee_tickets GROUP BY ticket_status";
$status_result = $conn->query($status_query);

while ($row = $status_result->fetch_assoc()) {
    $status_key = strtolower(str_replace(' ', '_', $row['ticket_status']));
    $status_counts[$status_key] = $row['count'];
}

// Set total count
$status_counts['total'] = array_sum($status_counts);

// Fetch Paginated Data
$query = "
    SELECT e.*, 
           emp.name AS employee_name,  
           p.online_price, p.agency_price,  
           d.destination_name,
           l.start_date AS leave_start_date,
           CASE 
               WHEN l.emp_no IS NOT NULL THEN 'Yes!' 
               ELSE '' 
           END AS has_leave
    FROM employee_tickets e
    LEFT JOIN employees emp ON e.emp_no = emp.emp_no  
    LEFT JOIN employee_tickets_destination d ON e.destination = d.destination_name
    LEFT JOIN employee_tickets_price p ON e.price_id = p.id  
    LEFT JOIN leave_records l ON e.emp_no = l.emp_no
    $whereClause
    GROUP BY e.id
    ORDER BY e.created_at DESC
    LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Tickets</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <?php include '../sidebar.php'; ?>
    <div class="body-wrapper">
            <?php include '../header.php'; ?>
        <div class="container-fluid" style="max-width:100%;">
            
            <!-- Count Cards -->
            <div class="row">
                <div class="col-md-2">
                    <div class="card bg-light text-white p-3">
                        <h5>Total Tickets</h5>
                        <h3><?php echo $status_counts['total']; ?></h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white p-3">
                        <h5>Pending</h5>
                        <h3><?php echo $status_counts['pending']; ?></h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white p-3">
                        <h5>Reservation Sent</h5>
                        <h3><?php echo $status_counts['reservation_sent']; ?></h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white p-3">
                        <h5>Ticket Received</h5>
                        <h3><?php echo $status_counts['ticket_received']; ?></h3>
                    </div>
                </div>
           
                <div class="col-md-2">
                    <div class="card bg-info text-white p-3">
                        <h5>Departed</h5>
                        <h3><?php echo $status_counts['departed']; ?></h3>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="card bg-success text-white p-3">
                        <h5>Completed</h5>
                        <h3><?php echo $status_counts['completed']; ?></h3>
                    </div>
                </div>
            </div>




            <form method="GET" class="mb-3">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="Search by Employee No, Name, or Status">
                    </div>
                    
                    <!-- Start Date -->
                    <div class="col-md-2">
                        <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" class="form-control">
                    </div>
            
                    <!-- End Date -->
                    <div class="col-md-2">
                        <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" class="form-control">
                    </div>
            
                    <!-- Filter & Reset Buttons -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="index.php" class="btn btn-secondary w-100 mt-2">Reset</a>
                    </div>
                </div>
            </form>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Employee Tickets</h5>
                    
                    <!-- Search Bar
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="Search by Employee No, Name, or Status">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="index.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </form> -->

                    <a href="add_ticket.php" class="btn btn-primary mb-3">Add New Ticket</a>

                    <!-- Tickets Table -->
                  <table class="table table-bordered">
    <thead>
        <tr>
            <!--th>#</th-->
            <th>Emp No</th>
            <th>Name</th>
            <th>For Leave</th>
            <th>Ticket Type</th>
            <th>Destination</th>
            <th>Departure</th>
            <th>Online Price</th> <!-- New Column -->
            <th>Agency Price</th> <!-- New Column -->
            <th>Status</th>
            <th>Actions</th>
            <th>Ticket Price</th> 
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <!--td><?php echo $row['id']; ?></td-->
                <td><?php echo $row['emp_no']; ?></td>
                <td><?php echo $row['employee_name']; ?></td>
                <td><?php echo $row['has_leave']; ?></td>
                <td><?php echo $row['ticket_type']; ?></td>
                <td><?php echo $row['destination']; ?></td>
                <td><?php echo date("d-M-Y", strtotime($row['departure_date'])); ?></td>
                <td><?php echo (!empty($row['online_price'])) ? "$" . number_format($row['online_price'], 2) : 'N/A'; ?></td> <!-- Online Price -->
                <td><?php echo (!empty($row['agency_price'])) ? "$" . number_format($row['agency_price'], 2) : 'N/A'; ?></td> <!-- Agency Price -->
                <td>
                    <span class="badge 
                        <?php 
                            switch ($row['ticket_status']) {
                                case 'Pending': echo 'bg-danger'; break;
                                case 'Rejected': echo 'bg-danger'; break;
                                case 'Reservation Sent': echo 'bg-warning'; break;
                                case 'Ticket Received': echo 'bg-success'; break;
                                case 'Departed': echo 'bg-info'; break;
                                case 'Completed': echo 'bg-success'; break;
                                default: echo 'bg-warning'; 
                            }
                        ?>">
                        <?php echo $row['ticket_status']; ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($row['ticket_file'])): ?>
                        <a href="<?php echo $row['ticket_file']; ?>" target="_blank" class="btn btn-sm btn-secondary"><i class="fa-solid fa-plane"></i></a>
                    <?php else: ?>
                        <span class="text-muted">No Ticket</span>
                    <?php endif; ?>
               
                    <?php if (!empty($row['invoice_file'])): ?>
                        <a href="<?php echo $row['invoice_file']; ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa-solid fa-file-invoice-dollar"></i></a>
                    <?php else: ?>
                        <span class="text-muted">No Invoice</span>
                    <?php endif; ?>
                    -
                    <a href="edit_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="delete_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('yageen?');"><i class="fa-solid fa-trash-can"></i></a>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $row['id']; ?>">
                        Add $
                    </button>
                </td>
            </tr>


           
                <!-- ADD Price Modal Popup -->
                <div class="modal fade" id="detailsModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel<?php echo $row['id']; ?>">Ticket Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Destination:</strong> <?php echo $row['destination']; ?></p>
                                <p><strong>Leave Start Date:</strong> 
                                    <?php echo (!empty($row['leave_start_date'])) ? date("d-M-Y", strtotime($row['leave_start_date'])) : 'N/A'; ?>
                                </p>
                
                                <!-- Price Form -->
                                <form method="POST" action="update_price.php">
                                    <input type="hidden" name="destination_id" value="<?php echo $row['destination']; ?>">
                                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Online Price:</strong></label>
                                        <input type="number" step="0.01" name="online_price" value="<?php echo $row['online_price']; ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Agency Price:</strong></label>
                                        <input type="number" step="0.01" name="agency_price" value="<?php echo $row['agency_price']; ?>" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Save Prices</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>




        <?php endwhile; ?>
    </tbody>
</table>
           <nav>
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
            
                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
            
                    <!-- Next Page -->
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>




                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
