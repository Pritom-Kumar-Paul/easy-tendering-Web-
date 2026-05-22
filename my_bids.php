<?php
session_start();
include("connect.php");
include("functions.php");

// Check if user is logged in
if(!isset($_SESSION['u_email'])) {
    header("location: login.php");
    exit();
}

$title = $_SESSION['u_email'];

$result = mysqli_query($con, "SELECT u_id, u_first_name, u_role FROM tbl_user WHERE u_email='".mysqli_real_escape_string($con, $title)."'");
$row = mysqli_fetch_assoc($result);
$id = $row['u_id'];
$name = $row['u_first_name'];
$user_role = $row['u_role'];

// Handle delete action
if(isset($_GET['delete_bid'])) {
    $bid_id = $_GET['delete_bid'];
    $delete_query = "DELETE FROM tbl_bid WHERE b_id = ? AND b_vendor_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "ii", $bid_id, $id);
    
    if(mysqli_stmt_execute($stmt)) {
        $success = "Bid deleted successfully";
    } else {
        $error = "Failed to delete bid";
    }
}

// Handle status messages
if(isset($_GET['update_success'])) {
    $success = "Bid updated successfully";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>E-Tendering | My Bids</title>
    <style>
       
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --success-color: #4cc9f0;
        --warning-color: #f8961e;
        --danger-color: #f72585;
        --edit-color: #ffc107;
        --delete-color: #dc3545;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #2b2d42;
    }
    
    .navbar {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 15px 0;
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color) !important;
        display: flex;
        align-items: center;
    }
    
    .navbar-brand i {
        margin-right: 10px;
        color: var(--accent-color);
    }
    
    .nav-link {
        font-weight: 500;
        color: #495057 !important;
        padding: 8px 15px;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .nav-link:hover, .nav-link.active {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color) !important;
    }
    
    .nav-link i {
        margin-right: 8px;
    }
    
    .text-gradient {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 30px;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 600;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 15px;
        background-color: #f8f9fa;
    }
    
    .table tbody tr {
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
        display: inline-block;
    }
    
    .status-submitted {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }
    
    .status-review {
        background-color: rgba(248, 150, 30, 0.1);
        color: var(--warning-color);
    }
    
    .status-accepted {
        background-color: rgba(76, 201, 240, 0.1);
        color: var(--success-color);
    }
    
    .status-rejected {
        background-color: rgba(247, 37, 133, 0.1);
        color: var(--danger-color);
    }
    
    .action-btns {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        border: none;
    }
    
    .action-btn i {
        margin-right: 5px;
    }
    
    .btn-outline-primary {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        background-color: transparent;
    }
    
    .btn-outline-primary:hover {
        background-color: rgba(67, 97, 238, 0.1);
    }
    
    .btn-edit {
        background-color: var(--edit-color);
        color: #212529;
    }
    
    .btn-edit:hover {
        background-color: #e0a800;
        color: #212529;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .btn-delete {
        background-color: var(--delete-color);
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .empty-state {
        padding: 30px;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px 20px;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .modal-header {
        border-bottom: none;
        padding: 20px 20px 10px;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        border-top: none;
        padding: 15px 20px;
    }
    
    .confirmation-text {
        font-size: 1.1rem;
        margin-bottom: 20px;
        color: #495057;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 15px;
        }
        
        .table td, .table th {
            padding: 12px 10px;
        }
        
        .action-btns {
            flex-direction: column;
            gap: 5px;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1.3rem;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .empty-state {
            padding: 20px;
        }
    }
</style>
    </style>
</head>
<body class="d-flex flex-column h-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="vendor_dashboard.php">
                <i class="fas fa-gavel"></i>
                <span>E-Tendering</span>
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="vendor_dashboard.php">
                            <i class="fas fa-home me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_bids.php">
                            <i class="fas fa-list me-1"></i>
                            My Bids
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container px-4 px-lg-5 mb-5">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bolder mb-3"><span class="text-gradient d-inline">My Bids</span></h1>
                <p class="lead">View and manage all your submitted bids</p>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Submitted Bids</h5>
                        <span class="badge bg-primary"><?= mysqli_num_rows(mysqli_query($con, "SELECT * FROM tbl_bid WHERE b_vendor_id = $id")) ?> bids</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tender ID</th>
                                    <th>Tender Title</th>
                                    <th>Bid Amount</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $bids = mysqli_query($con, "SELECT b.b_id, b.b_tender_id, t.t_description, b.b_amount, b.b_submission_date, b.b_status, t.t_status as tender_status 
                                                              FROM tbl_bid b
                                                              JOIN tbl_tender t ON b.b_tender_id = t.t_id
                                                              WHERE b.b_vendor_id = $id
                                                              ORDER BY b.b_submission_date DESC");
                                    
                                    if(mysqli_num_rows($bids) > 0) {
                                        while($bid = mysqli_fetch_assoc($bids)) {
                                            $status_class = '';
                                            $status_text = '';
                                            
                                            switch($bid['b_status']) {
                                                case 1: $status_class = 'submitted'; $status_text = 'Submitted'; break;
                                                case 2: $status_class = 'review'; $status_text = 'Under Review'; break;
                                                case 3: $status_class = 'accepted'; $status_text = 'Accepted'; break;
                                                case 4: $status_class = 'rejected'; $status_text = 'Rejected'; break;
                                                default: $status_class = 'submitted'; $status_text = 'Submitted';
                                            }
                                            
                                            // Check if bid can be modified (only if tender is still open and bid is not accepted/rejected)
                                            $can_modify = ($bid['tender_status'] == 1 && $bid['b_status'] <= 2);
                                ?>
                                <tr>
                                    <td>#<?= $bid['b_tender_id'] ?></td>
                                    <td><?= htmlspecialchars(substr($bid['t_description'], 0, 30)) ?>...</td>
                                    <td>$<?= number_format($bid['b_amount'], 2) ?></td>
                                    <td><?= date('M j, Y', strtotime($bid['b_submission_date'])) ?></td>
                                    <td><span class="status-badge status-<?= $status_class ?>"><?= $status_text ?></span></td>
                                  <td>
    <div class="action-btns">
        <?php if($can_modify): ?>
               <a href="update_bid.php?bid_id=<?= $bid['b_id'] ?>" ">
                                                    <i class="fas fa-edit me-1"></i>Edit
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal<?= $bid['b_id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="">
                    <div class="">
                      
                      </div>
                        <div class="modal-footer">
                            <a href="my_bids.php?delete_bid=<?= $bid['b_id'] ?>" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</td>
                                </tr>
                                <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5>No bids submitted yet</h5>
                                                <p>You haven\'t placed any bids. Find tenders to bid on from the dashboard.</p>
                                                <a href="vendor_dashboard.php" class="btn btn-primary mt-3">Browse Tenders</a>
                                            </div>
                                        </td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>