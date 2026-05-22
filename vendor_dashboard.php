<?php
include("connect.php");
include("functions.php");

$title = $_SESSION['u_email'];

$result = mysqli_query($con, "SELECT u_id, u_first_name, u_role FROM tbl_user WHERE u_email='$title'");

while ($row=mysqli_fetch_row($result)) {
    $id = $row[0];
    $name = $row[1];
    $user_role = $row[2];
}

if($title == NULL)
    header("location: login.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>E-Tendering | Vendor Dashboard</title>
        
            <style>
    /* Global Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f8f9fa;
    }

    /* Navigation Styles */
    .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        font-weight: 500;
        color: #495057;
        transition: color 0.3s;
    }

    .navbar-nav .nav-link:hover {
        color: #0d6efd;
    }

    /* Header Styles */
    .text-gradient {
        background: linear-gradient(to right, #0d6efd, #20c997);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .display-5 {
        font-size: 2.5rem;
        font-weight: 700;
    }

    /* Card Styles */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        padding: 1rem 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Table Styles */
    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td, .table th {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }

    /* Button Styles */
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
        
        .row {
            flex-direction: column;
        }
        
        .col-md-6 {
            width: 100%;
            margin-bottom: 1.5rem;
        }
    }

    /* Status Badges (you can add these classes to your status cells) */
    .status-submitted {
        color: #0d6efd;
    }

    .status-review {
        color: #fd7e14;
    }

    .status-accepted {
        color: #198754;
    }

    .status-rejected {
        color: #dc3545;
    }

        </style>
        <!-- Head elements remain the same -->
    </head>
    <body class="d-flex flex-column h-100 bg-light">
        <!-- Navigation updated for vendors -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
            <div class="container px-5">
                <a class="navbar-brand" href="index.html"><span class="fw-bolder text-primary">E-Tendering System</span></a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                        <li class="nav-item"><a class="nav-link" href="vendor_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="my_bids.php">My Bids</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">GET in touch</a></li>
                        <li class="nav-item"><a class="nav-link" href="contacts.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="py-5">
            <div class="container px-5 mb-5">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">Vendor Dashboard</span></h1>
                </div>
                
                <div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header">
            <h5>Open Tenders</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tender</th>
                        <th>Budget</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $results = mysqli_query($con, "SELECT t_id, t_description, t_budget, t_deadline FROM tbl_tender WHERE t_status = 1");
                        while($row = mysqli_fetch_row($results)) {
                    ?>
                    <tr>
                        <td><?= substr($row[1], 0, 30) ?>...</td>
                        <td>$<?= number_format($row[2], 2) ?></td>
                        <td><?= date('d M Y', strtotime($row[3])) ?></td>
                        <td><a href="bid.php?tender_id=<?= $row[0] ?>" class="btn btn-sm btn-primary">Bid Now</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>My Active Bids</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tender</th>
                                            <th>Bid Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $bids = mysqli_query($con, "SELECT b.b_id, t.t_description, b.b_amount, b.b_status 
                                                                      FROM tbl_bid b
                                                                      JOIN tbl_tender t ON b.b_tender_id = t.t_id
                                                                      WHERE b.b_vendor_id = '$id'");
                                            while($bid = mysqli_fetch_row($bids)) {
                                                $status = ['Submitted', 'Under Review', 'Accepted', 'Rejected'][$bid[3]-1];
                                        ?>
                                        <tr>
                                            <td><?= substr($bid[1], 0, 30) ?>...</td>
                                            <td>$<?= number_format($bid[2], 2) ?></td>
                                            <td><?= $status ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer remains the same -->
    </body>
</html>