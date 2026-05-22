<?php
include('connect.php');
include('functions.php'); // Assuming you have a functions file
session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$invoice_number = '';
$amount_due = 0;
$order_id = 0;
$error = '';

// Process order details
if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    $user_id = (int)$_SESSION['user_id'];
    
    // Verify order exists and belongs to user
    $stmt = $con->prepare("SELECT * FROM `user_orders` WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = "Order not found or access denied";
    } else {
        $row_fetch = $result->fetch_assoc();
        $invoice_number = htmlspecialchars($row_fetch['invoice_number']);
        $amount_due = (float)$row_fetch['amount_due'];
    }
    $stmt->close();
}

// Process payment confirmation
if (isset($_POST['confirm_payment'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Security token validation failed";
    } else {
        // Get form data with validation
        $invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : '';
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $payment_mode = isset($_POST['payment_mode']) ? $con->real_escape_string($_POST['payment_mode']) : '';
        
        // Validate payment amount
        if (abs($amount - $amount_due) > 0.01) {
            $error = "Payment amount does not match order total";
        } else {
            // Start transaction
            $con->begin_transaction();
            
            try {
                // Verify order exists again (prevent race condition)
                $check_order = $con->prepare("SELECT order_id FROM user_orders WHERE order_id = ?");
                $check_order->bind_param("i", $order_id);
                $check_order->execute();
                $check_order->store_result();
                
                if($check_order->num_rows === 0) {
                    throw new Exception("The order you're trying to pay for doesn't exist");
                }
                $check_order->close();
                
                // Insert payment
                $stmt = $con->prepare("INSERT INTO `user_payments` (order_id, invoice_number, amount, payment_mode) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isds", $order_id, $invoice_number, $amount, $payment_mode);
                $stmt->execute();
                
                // Update order status
                $stmt = $con->prepare("UPDATE `user_orders` SET order_status = 'Complete' WHERE order_id = ?");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                
                $con->commit();
                
                $_SESSION['success_message'] = "Payment completed successfully";
                header("Location: profile.php?my_orders");
                exit();
            } catch (Exception $e) {
                $con->rollback();
                $error = "Payment processing failed: " . $e->getMessage();
                
                // Log the error
                error_log("Payment Error: " . $e->getMessage() . " for order $order_id by user " . $_SESSION['user_id']);
            }
        }
    }
}

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .payment-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .input-group-text {
            min-width: 80px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="payment-container">
            <h1 class="text-center mb-4">Confirm Payment</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="" method="post" onsubmit="return validatePayment()">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="mb-3">
                    <label for="invoice_number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                           value="<?php echo htmlspecialchars($invoice_number); ?>" readonly>
                </div>
                
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="amount" name="amount" 
                               value="<?php echo htmlspecialchars(number_format($amount_due, 2)); ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="payment_mode" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_mode" name="payment_mode" required>
                        <option value="">Select Payment Mode</option>
                        <option value="UPI" <?php echo (isset($_POST['payment_mode']) && $_POST['payment_mode'] === 'UPI') ? 'selected' : ''; ?>>UPI</option>
                        <option value="Netbanking" <?php echo (isset($_POST['payment_mode']) && $_POST['payment_mode'] === 'Netbanking') ? 'selected' : ''; ?>>Netbanking</option>
                        <option value="Paypal" <?php echo (isset($_POST['payment_mode']) && $_POST['payment_mode'] === 'Paypal') ? 'selected' : ''; ?>>Paypal</option>
                        <option value="Cash on Delivery" <?php echo (isset($_POST['payment_mode']) && $_POST['payment_mode'] === 'Cash on Delivery') ? 'selected' : ''; ?>>Cash on Delivery</option>
                        <option value="Payoffline" <?php echo (isset($_POST['payment_mode']) && $_POST['payment_mode'] === 'Payoffline') ? 'selected' : ''; ?>>Payoffline</option>
                    </select>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="confirm_payment">
                        Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Client-side validation -->
    <script>
        function validatePayment() {
            const paymentMode = document.getElementById('payment_mode').value;
            if (!paymentMode) {
                alert('Please select a payment method');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>