<?php
session_start();
include("connect.php");

// Redirect if not logged in
if (!isset($_SESSION['u_email'])) {
    header("location: login.php");
    exit();
}

if (!isset($_GET['tender_id'])) {
    echo "No tender selected.";
    exit();
}

$tender_id = intval($_GET['tender_id']);

// Get bids for this tender
$bids = mysqli_query($con, "SELECT * FROM tbl_bid WHERE b_tender_id = $tender_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bids for Tender <?php echo $tender_id; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f6;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #2c3e50;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .bid-list {
            list-style-type: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .bid-item {
            background: #f8f9fa;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
            transition: all 0.3s ease;
        }
        
        .bid-item:hover {
            background: #e9f5ff;
            transform: translateX(5px);
        }
        
        .bid-amount {
            font-weight: bold;
            color: #27ae60;
        }
        
        .no-bids {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border: 1px solid #3498db;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #3498db;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bids for Tender ID: <?php echo $tender_id; ?></h2>
        
        <?php if (mysqli_num_rows($bids) > 0) { ?>
            <ul class="bid-list">
                <?php while ($bid = mysqli_fetch_assoc($bids)) { ?>
                    <li class="bid-item">
                        <strong>Bidder:</strong> <?php echo htmlspecialchars($bid['b_bidder_name']); ?> 
                        - <span class="bid-amount">Amount: $<?php echo number_format($bid['b_amount'], 2); ?></span>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <div class="no-bids">No bids found for this tender.</div>
        <?php } ?>
        
        <a href="javascript:history.back()" class="back-link">← Back to Previous Page</a>
    </div>
</body>
</html>