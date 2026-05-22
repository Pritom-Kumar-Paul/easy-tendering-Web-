<?php
$host = 'localhost';
$db = 'contact_db';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass, $db);

// Create replies table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS `message_replies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT NOT NULL,
    `admin_id` INT DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`)
)";

mysqli_query($conn, $createTable);

// Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $contact_id = $_POST['contact_id'];
    $subject = $_POST['subject'];
    $message = $_POST['reply_message'];
    
    $stmt = mysqli_prepare($conn, "INSERT INTO message_replies (contact_id, subject, message) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $contact_id, $subject, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        $reply_status = "Reply saved successfully";
    } else {
        $reply_status = "Failed to save reply: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Previous head content remains the same -->
    <style>
      
    /* Base Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
    }
    
    /* Table Container */
    .table-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Header */
    .table-header h1 {
        color: #2c3e50;
        margin: 0 0 20px 0;
        font-size: 28px;
        text-align: center;
        position: relative;
        padding-bottom: 10px;
    }
    
    .table-header h1::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(to right, #3498db, #2ecc71);
    }
    
    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 14px;
    }
    
    th, td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    
    th {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }
    
    tr:nth-child(even) {
        background-color: #f8fafc;
    }
    
    tr:hover {
        background-color: #f1f8fe;
        transition: background-color 0.2s ease;
    }
    
    /* Buttons */
    .reply-btn {
        background: linear-gradient(to right, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .reply-btn:hover {
        background: linear-gradient(to right, #2980b9, #3498db);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .view-replies {
        background: linear-gradient(to right, #f39c12, #e67e22);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .view-replies:hover {
        background: linear-gradient(to right, #e67e22, #f39c12);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* Modal Styles */
    .reply-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(3px);
    }
    
    .reply-content {
        background-color: white;
        margin: 8% auto;
        padding: 25px;
        border-radius: 8px;
        width: 50%;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        animation: modalFadeIn 0.3s ease-out;
    }
    
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        transition: color 0.2s;
    }
    
    .close:hover {
        color: #555;
        cursor: pointer;
    }
    
    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
    }
    
    .form-group textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    .submit-btn {
        background: linear-gradient(to right, #2ecc71, #27ae60);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .submit-btn:hover {
        background: linear-gradient(to right, #27ae60, #2ecc71);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Replies Section */
    .replies-container {
        display: none;
        padding: 20px;
        background-color: #f8fafc;
        border-radius: 6px;
        margin-top: 10px;
        border-left: 4px solid #3498db;
    }
    
    .reply-item {
        padding: 15px;
        margin-bottom: 15px;
        background-color: white;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-left: 3px solid #3498db;
    }
    
    .reply-header {
        color: #2c3e50;
        font-size: 16px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .reply-date {
        color: #7f8c8d;
        font-size: 12px;
    }
    
    .reply-item p {
        margin: 8px 0 0 0;
        color: #34495e;
    }
    
    /* Status Messages */
    .status-message {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
        .reply-content {
            width: 70%;
        }
    }
    
    @media (max-width: 768px) {
        .table-container {
            padding: 15px;
        }
        
        th, td {
            padding: 10px 12px;
        }
        
        .reply-content {
            width: 85%;
            margin: 15% auto;
        }
    }
    
    @media (max-width: 576px) {
        .reply-content {
            width: 95%;
            padding: 15px;
        }
        
        .form-group textarea {
            min-height: 120px;
        }
        
        .reply-btn, .view-replies {
            padding: 6px 12px;
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
            width: 100%;
        }
        
        .view-replies {
            margin-left: 0;
            margin-top: 5px;
        }
    }
</style>
    </style>
</head>
<body>
    <div class="table-container">
        <div class="table-header">
            <h1>Contact Messages</h1>
        </div>
        
        <?php if (isset($reply_status)): ?>
            <div class="status-message <?php echo strpos($reply_status, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($reply_status); ?>
            </div>
        <?php endif; ?>
        
        <table id="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM message_replies WHERE contact_id = c.id) as reply_count
                       FROM `contacts` c";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>".htmlspecialchars($row['id'])."</td>
                            <td>".htmlspecialchars($row['name'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                            <td>".htmlspecialchars($row['phone'])."</td>
                            <td>".htmlspecialchars($row['message'])."</td>
                            <td>
                                <button class='reply-btn' onclick=\"openReplyModal('".htmlspecialchars($row['id'])."', '".urlencode($row['message'])."')\">
                                    Reply
                                </button>
                                <button class='view-replies' onclick=\"toggleReplies('".htmlspecialchars($row['id'])."')\">
                                    Replies (".$row['reply_count'].")
                                </button>
                            </td>
                        </tr>
                        <tr id='replies-".htmlspecialchars($row['id'])."' class='replies-container'>
                            <td colspan='6'>";
                        
                        // Display replies for this message
                        $reply_sql = "SELECT * FROM message_replies WHERE contact_id = ".$row['id']." ORDER BY created_at DESC";
                        $reply_result = mysqli_query($conn, $reply_sql);
                        
                        if (mysqli_num_rows($reply_result) > 0) {
                            while($reply = mysqli_fetch_assoc($reply_result)) {
                                echo "<div class='reply-item'>
                                    <div class='reply-header'>".htmlspecialchars($reply['subject'])."</div>
                                    <div class='reply-date'>".date('M j, Y g:i a', strtotime($reply['created_at']))."</div>
                                    <div>".nl2br(htmlspecialchars($reply['message']))."</div>
                                </div>";
                            }
                        } else {
                            echo "<div class='reply-item'>No replies yet</div>";
                        }
                        
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Reply Modal -->
    <div id="replyModal" class="reply-modal">
        <div class="reply-content">
            <span class="close" onclick="closeReplyModal()">&times;</span>
            <h2>Reply to Message</h2>
            <form method="POST" action="">
                <input type="hidden" id="contact_id" name="contact_id">
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" value="Re: Your inquiry" required>
                </div>
                <div class="form-group">
                    <label for="original_message">Original Message:</label>
                    <textarea id="original_message" readonly></textarea>
                </div>
                <div class="form-group">
                    <label for="reply_message">Your Reply:</label>
                    <textarea id="reply_message" name="reply_message" required></textarea>
                </div>
                <button type="submit" name="reply" class="submit-btn">Save Reply</button>
            </form>
        </div>
    </div>

    <script>
        function openReplyModal(contactId, originalMessage) {
            document.getElementById('replyModal').style.display = 'block';
            document.getElementById('contact_id').value = contactId;
            document.getElementById('original_message').value = decodeURIComponent(originalMessage);
        }
        
        function closeReplyModal() {
            document.getElementById('replyModal').style.display = 'none';
        }
        
        function toggleReplies(contactId) {
            const container = document.getElementById('replies-' + contactId);
            container.style.display = container.style.display === 'none' ? 'table-row' : 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('replyModal');
            if (event.target == modal) {
                closeReplyModal();
            }
        }
    </script>
</body>
</html>