<?php
include("connect.php");
include("functions.php");

// Check if user is logged in
if(!isset($_SESSION['u_email'])) {
    header("location: login.php");
}

// Get bid ID from URL
if(!isset($_GET['bid_id'])) {
    header("location: vendor_dashboard.php");
}

$bid_id = mysqli_real_escape_string($con, $_GET['bid_id']);

// Get bid details
$bid_query = "SELECT b.*, t.t_description, t.t_deadline 
              FROM tbl_bid b
              JOIN tbl_tender t ON b.b_tender_id = t.t_id
              WHERE b.b_id = '$bid_id' AND b.b_vendor_id = '{$_SESSION['u_id']}'";
$bid_result = mysqli_query($con, $bid_query);

if(mysqli_num_rows($bid_result) == 0) {
    header("location: vendor_dashboard.php");
}

$bid = mysqli_fetch_assoc($bid_result);

// Check if bid can still be edited (status is Submitted and deadline hasn't passed)
if($bid['b_status'] != 1 || strtotime($bid['t_deadline']) < time()) {
    header("location: vendor_dashboard.php");
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = mysqli_real_escape_string($con, $_POST['amount']);
    $proposal = mysqli_real_escape_string($con, $_POST['proposal']);
    
    $update_query = "UPDATE tbl_bid SET b_amount = '$amount', b_proposal = '$proposal' WHERE b_id = '$bid_id'";
    
    if(mysqli_query($con, $update_query)) {
        $_SESSION['success_msg'] = "Bid updated successfully!";
        header("location: vendor_dashboard.php");
    } else {
        $error_msg = "Error updating bid: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bid - E-Tendering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Bid for: <?= substr($bid['t_description'], 0, 50) ?>...</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error_msg)): ?>
                            <div class="alert alert-danger"><?= $error_msg ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Bid Amount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                       value="<?= $bid['b_amount'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="proposal" class="form-label">Proposal Details</label>
                                <textarea class="form-control" id="proposal" name="proposal" rows="5" required><?= $bid['b_proposal'] ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Bid</button>
                                <a href="vendor_dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>