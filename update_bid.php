<?php
session_start();
include("connect.php");
include("functions.php");

// Check if user is logged in
if(!isset($_SESSION['u_email'])) {
    header("location: login.php");
    exit();
}

// Get user info
$title = $_SESSION['u_email'];
$result = mysqli_query($con, "SELECT u_id, u_first_name FROM tbl_user WHERE u_email='".mysqli_real_escape_string($con, $title)."'");
$row = mysqli_fetch_assoc($result);
$vendor_id = $row['u_id'];
$name = $row['u_first_name'];

// Get bid ID from URL
$bid_id = isset($_GET['bid_id']) ? intval($_GET['bid_id']) : 0;

// Fetch bid details
$bid_query = "SELECT b.*, t.t_description, t.t_budget, t.t_deadline 
              FROM tbl_bid b
              JOIN tbl_tender t ON b.b_tender_id = t.t_id
              WHERE b.b_id = ? AND b.b_vendor_id = ?";
$stmt = mysqli_prepare($con, $bid_query);
mysqli_stmt_bind_param($stmt, "ii", $bid_id, $vendor_id);
mysqli_stmt_execute($stmt);
$bid = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$bid) {
    header("location: my_bids.php?error=Invalid bid");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_bid'])) {
    $amount = floatval($_POST['amount']);
    $proposal = mysqli_real_escape_string($con, $_POST['proposal']);
    
    // Validate amount
    if($amount <= 0) {
        $error = "Bid amount must be greater than 0";
    } else {
        $update_query = "UPDATE tbl_bid SET b_amount = ?, b_proposal = ? WHERE b_id = ? AND b_vendor_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "dsii", $amount, $proposal, $bid_id, $vendor_id);
        
        if(mysqli_stmt_execute($stmt)) {
            header("location: my_bids.php?update_success=1");
            exit();
        } else {
            $error = "Failed to update bid. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Bid - E-Tendering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: rgba(67, 97, 238, 0.1);
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .tender-info {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .tender-info p {
            margin-bottom: 0.5rem;
        }
        
        .tender-info strong {
            color: var(--primary);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        @media (max-width: 768px) {
            .card {
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><span class="text-gradient">Update Your Bid</span></h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="tender-info">
                            <h5 class="mb-3">Tender Details</h5>
                            <p><strong>Title:</strong> <?= htmlspecialchars($bid['t_description']) ?></p>
                            <p><strong>Budget:</strong> $<?= number_format($bid['t_budget'], 2) ?></p>
                            <p><strong>Deadline:</strong> <?= date('M j, Y', strtotime($bid['t_deadline'])) ?></p>
                        </div>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Bid Amount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                       value="<?= htmlspecialchars($bid['b_amount']) ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="proposal" class="form-label">Proposal Details</label>
                                <textarea class="form-control" id="proposal" name="proposal" rows="6" required><?= 
                                    htmlspecialchars($bid['b_proposal']) ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="my_bids.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Back to My Bids
                                </a>
                                <button type="submit" name="update_bid" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Update Bid
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validate amount before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('amount').value);
            if(amount <= 0) {
                e.preventDefault();
                alert('Bid amount must be greater than 0');
            }
        });
    </script>
</body>
</html>