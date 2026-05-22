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

$result = mysqli_query($con, "SELECT u_id, u_first_name FROM tbl_user WHERE u_email='".mysqli_real_escape_string($con, $title)."'");
$row = mysqli_fetch_assoc($result);
$id = $row['u_id'];
$name = $row['u_first_name'];

$error = "";

if(isset($_GET['evaluation_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            Bid evaluated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

if(isset($_GET['evaluation_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
            Error evaluating bid. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
    <!-- [HEAD SECTION REMAINS THE SAME] -->
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
        --info-color: #4895ef;
        --text-color: #2b2d42;
        --bg-color: #f8f9fa;
        --card-bg: #ffffff;
        --border-radius: 12px;
        --box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        --transition: all 0.3s ease;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-color);
        line-height: 1.6;
        min-height: 100vh;
    }
    
    /* Navbar Styles */
    .navbar {
        background-color: var(--card-bg);
        box-shadow: var(--box-shadow);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 1020;
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color) !important;
        display: flex;
        align-items: center;
        transition: var(--transition);
    }
    
    .navbar-brand:hover {
        transform: translateY(-2px);
    }
    
    .navbar-brand i {
        margin-right: 0.5rem;
        color: var(--accent-color);
        font-size: 1.8rem;
    }
    
    .nav-link {
        font-weight: 500;
        padding: 0.5rem 1.2rem;
        transition: var(--transition);
        color: var(--text-color);
        border-radius: var(--border-radius);
        margin: 0 0.2rem;
        position: relative;
    }
    
    .nav-link:hover, .nav-link.active {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color) !important;
        transform: translateY(-2px);
    }
    
    .nav-link i {
        margin-right: 0.5rem;
        font-size: 1.1rem;
    }
    
    .nav-link.active:after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background-color: var(--primary-color);
        border-radius: 50%;
    }
    
    /* Text Gradient */
    .text-gradient {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* Main Content */
    section {
        padding: 3rem 0;
        position: relative;
    }
    
    .page-title {
        position: relative;
        margin-bottom: 3rem;
        text-align: center;
    }
    
    .page-title h1 {
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 2.5rem;
    }
    
    .page-title p {
        color: #6c757d;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .page-title:after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        border-radius: 2px;
    }
    
    /* Table Styles */
    .table-container {
        background-color: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        padding: 0;
        border: none;
        transition: var(--transition);
    }
    
    .table-container:hover {
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }
    
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }
    
    .table th {
        font-weight: 600;
        padding: 1.2rem 1.5rem;
        vertical-align: middle;
        white-space: nowrap;
        border: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .table td {
        padding: 1.2rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0,0,0,0.05);
        border-bottom: none;
    }
    
    .table tbody tr {
        transition: var(--transition);
        background-color: var(--card-bg);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        z-index: 1;
        position: relative;
    }
    
    /* Status Badges */
    .status {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
        transition: var(--transition);
    }
    
    .status-open {
        background-color: rgba(76, 201, 240, 0.1);
        color: var(--success-color);
    }
    
    .status-closed {
        background-color: rgba(247, 37, 133, 0.1);
        color: var(--danger-color);
    }
    
    .status-awarded {
        background-color: rgba(248, 150, 30, 0.1);
        color: var(--warning-color);
    }
    
    .status-completed {
        background-color: rgba(72, 149, 239, 0.1);
        color: var(--info-color);
    }
    
    /* Type Badges */
    .badge-type {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
        margin-right: 0.5rem;
        margin-bottom: 0.3rem;
        transition: var(--transition);
    }
    
    .badge-type:hover {
        transform: translateY(-2px);
    }
    
    .construction {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }
    
    .supply {
        background-color: rgba(76, 201, 240, 0.1);
        color: var(--success-color);
    }
    
    .service {
        background-color: rgba(72, 149, 239, 0.1);
        color: var(--info-color);
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        margin: 0.2rem;
        white-space: nowrap;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        border: none;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .action-btn i {
        margin-right: 0.3rem;
        font-size: 0.8rem;
    }
    
    .btn-primary.action-btn {
        background-color: var(--primary-color);
    }
    
    .btn-primary.action-btn:hover {
        background-color: var(--secondary-color);
    }
    
    .btn-outline-secondary.action-btn {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-secondary.action-btn:hover {
        background-color: rgba(67, 97, 238, 0.1);
    }
    
    /* Responsive Table */
    .table-responsive {
        overflow-x: auto;
        border-radius: var(--border-radius);
    }
    
    /* Evaluation Status Badges */
    .badge-pending {
        background-color: rgba(248, 150, 30, 0.1);
        color: var(--warning-color);
    }
    
    .badge-evaluated {
        background-color: rgba(76, 201, 240, 0.1);
        color: var(--success-color);
    }
    
    /* Empty State */
    .empty-state {
        padding: 3rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #dee2e6;
    }
    
    /* Modern Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-color);
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .table tbody tr {
        animation: fadeIn 0.3s ease forwards;
        opacity: 0;
    }
    
    .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
    .table tbody tr:nth-child(2) { animation-delay: 0.2s; }
    .table tbody tr:nth-child(3) { animation-delay: 0.3s; }
    .table tbody tr:nth-child(4) { animation-delay: 0.4s; }
    .table tbody tr:nth-child(5) { animation-delay: 0.5s; }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .table td, .table th {
            padding: 0.9rem;
            font-size: 0.85rem;
        }
        
        .action-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            margin: 0.15rem;
        }
        
        .navbar-nav {
            margin-top: 1rem;
        }
        
        .nav-link {
            padding: 0.5rem;
            margin: 0.1rem 0;
        }
        
        .page-title h1 {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        section {
            padding: 2rem 0;
        }
        
        .page-title h1 {
            font-size: 1.8rem;
        }
        
        .page-title p {
            font-size: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem;
        }
        
        .badge-type {
            margin-right: 0.3rem;
            margin-bottom: 0.2rem;
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }
    }
    
    @media (max-width: 576px) {
        .page-title h1 {
            font-size: 1.5rem;
        }
        
        .navbar-brand {
            font-size: 1.2rem;
        }
        
        .navbar-brand i {
            font-size: 1.4rem;
        }
        
        .action-btn {
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
        }
        
        .status, .badge-type {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>
    <body class="d-flex flex-column h-100">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.html">
                    <i class="fas fa-gavel"></i>
                    <span>E-Tendering</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="Evaluator_deshbord.php">
                                <i class="fas fa-list"></i>
                                Tenders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacts.php">
                                <i class="fas fa-clipboard-check"></i>
                                contacts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="py-5">
            <div class="container px-4 px-lg-5 mb-5">
                <div class="text-center mb-5 page-title">
                    <h1 class="display-5 fw-bolder mb-3"><span class="text-gradient d-inline">Evaluator Dashboard</span></h1>
                    <p class="lead">Welcome back, <?= htmlspecialchars($name) ?>. Manage tender evaluations</p>
                </div>
                
                <div class="table-container shadow">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tender Type</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Budget</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Deadline</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Bids</th>
                                    <th scope="col">Actions</th>                            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $results = mysqli_query($con, "SELECT t.*, 
                                        (SELECT COUNT(*) FROM tbl_bid WHERE b_tender_id = t.t_id) as bid_count
                                        FROM tbl_tender t 
                                        WHERE t.t_status = 2"); // Only show closed tenders ready for evaluation
                                    
                                    if(mysqli_num_rows($results) > 0) {
                                        $counter = 1;
                                        while($row = mysqli_fetch_assoc($results)) {
                                ?>
                                <tr>
                                    <th scope="row"><?= $counter ?></th>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            <?php
                                                if($row['t_type_construction'] == 1) echo "<span class='badge-type construction'><i class='fas fa-building me-1'></i>Construction</span>";
                                                if($row['t_type_supply'] == 1) echo "<span class='badge-type supply'><i class='fas fa-truck me-1'></i>Supply</span>";
                                                if($row['t_type_service'] == 1) echo "<span class='badge-type service'><i class='fas fa-concierge-bell me-1'></i>Service</span>";
                                            ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['t_description']) ?></td>
                                    <td>$<?= number_format($row['t_budget'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['t_location']) ?></td>
                                    <td><?= ($row['t_deadline'] != '0000-00-00') ? date('M d, Y', strtotime($row['t_deadline'])) : 'Not set' ?></td>
                                    <td>
                                        <span class="status status-<?php 
                                            if($row['t_status'] == '1') echo 'open';
                                            else if($row['t_status'] == '2') echo 'closed';
                                            else if($row['t_status'] == '3') echo 'awarded';
                                        ?>">
                                            <i class="fas fa-<?php 
                                                if($row['t_status'] == '1') echo 'lock-open';
                                                else if($row['t_status'] == '2') echo 'lock';
                                                else if($row['t_status'] == '3') echo 'award';
                                            ?> me-1"></i>
                                            <?php 
                                                if($row['t_status'] == '1') echo 'Open';
                                                else if($row['t_status'] == '2') echo 'Closed';
                                                else if($row['t_status'] == '3') echo 'Awarded';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark">
                                            <?= $row['bid_count'] ?> bids
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            <a href="evaluate_bids.php?tender_id=<?= $row['t_id'] ?>" class="btn btn-primary action-btn">
                                                <i class="fas fa-clipboard-check"></i>
                                                Evaluate
                                            </a>
                                            <a href="view_bids.php?tender_id=<?= $row['t_id'] ?>" class="btn btn-outline-secondary action-btn">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                        $counter++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center py-4">No tenders available for evaluation</td></tr>';
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>