<?php
session_start();
include("connect.php");
include("function.php");

if(!isset($_SESSION['u_email'])) {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['u_id'];
$unread_count = getUnreadCount($con, $user_id);

// Mark message as read if viewing
if(isset($_GET['view'])) {
    markAsRead($con, $_GET['view']);
}

// Handle message sending
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $tender_id = isset($_POST['tender_id']) ? $_POST['tender_id'] : null;
    
    if(sendMessage($con, $user_id, $receiver_id, $subject, $message, $tender_id)) {
        $success = "Message sent successfully!";
    } else {
        $error = "Failed to send message. Please try again.";
    }
}

$inbox_messages = getMessages($con, $user_id);
$sent_messages = getMessages($con, $user_id, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System - E-Tendering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        .messaging-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .message-sidebar {
            border-right: 1px solid #eee;
            height: 80vh;
            overflow-y: auto;
        }
        
        .message-content {
            height: 80vh;
            overflow-y: auto;
        }
        
        .message-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .message-item:hover {
            background-color: #f8f9fa;
        }
        
        .message-item.unread {
            background-color: rgba(67, 97, 238, 0.05);
            font-weight: 500;
        }
        
        .message-item.active {
            background-color: rgba(67, 97, 238, 0.1);
        }
        
        .message-sender {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .message-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .message-preview {
            font-size: 0.9rem;
            color: #495057;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .badge-unread {
            background-color: var(--accent-color);
            color: white;
        }
        
        .compose-form {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .message-attachment {
            position: relative;
            padding: 10px;
            background-color: #f1f3f5;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            .message-sidebar {
                height: auto;
                max-height: 300px;
            }
            
            .message-content {
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-gradient d-inline">Messaging Center</h2>
                <span class="badge bg-primary ms-2"><?= $unread_count ?> unread</span>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="fas fa-plus me-2"></i>New Message
                </button>
            </div>
        </div>
        
        <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="messaging-container shadow">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-md-4 col-lg-3 message-sidebar">
                    <ul class="nav nav-tabs" id="messageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab">
                                Inbox <span class="badge badge-unread ms-1"><?= $unread_count ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">Sent</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="inbox" role="tabpanel">
                            <?php if(mysqli_num_rows($inbox_messages) > 0): ?>
                                <?php while($message = mysqli_fetch_assoc($inbox_messages)): ?>
                                    <a href="messaging.php?view=<?= $message['msg_id'] ?>" class="text-decoration-none">
                                        <div class="message-item <?= !$message['is_read'] ? 'unread' : '' ?> <?= isset($_GET['view']) && $_GET['view'] == $message['msg_id'] ? 'active' : '' ?>">
                                            <div class="d-flex justify-content-between">
                                                <span class="message-sender"><?= htmlspecialchars($message['sender_name'] . ' ' . htmlspecialchars($message['sender_last_name'])) ?></span>
                                                <span class="message-time"><?= date('M j, g:i a', strtotime($message['created_at'])) ?></span>
                                            </div>
                                            <h6 class="mb-1"><?= htmlspecialchars($message['subject']) ?></h6>
                                            <p class="message-preview mb-0"><?= htmlspecialchars(substr($message['message'], 0, 60)) ?>...</p>
                                            <?php if($message['tender_id']): ?>
                                                <small class="text-muted">Re: Tender #<?= $message['tender_id'] ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Your inbox is empty</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-pane fade" id="sent" role="tabpanel">
                            <?php if(mysqli_num_rows($sent_messages) > 0): ?>
                                <?php while($message = mysqli_fetch_assoc($sent_messages)): ?>
                                    <a href="messaging.php?view=<?= $message['msg_id'] ?>" class="text-decoration-none">
                                        <div class="message-item <?= isset($_GET['view']) && $_GET['view'] == $message['msg_id'] ? 'active' : '' ?>">
                                            <div class="d-flex justify-content-between">
<span class="message-sender">To: <?= htmlspecialchars($message['receiver_name'] . ' ' . htmlspecialchars($message['receiver_last_name'])) ?></span>
                                                <span class="message-time"><?= date('M j, g:i a', strtotime($message['created_at'])) ?></span>
                                            </div>
                                            <h6 class="mb-1"><?= htmlspecialchars($message['subject']) ?></h6>
                                            <p class="message-preview mb-0"><?= htmlspecialchars(substr($message['message'], 0, 60)) ?>...</p>
                                            <?php if($message['tender_id']): ?>
                                                <small class="text-muted">Re: Tender #<?= $message['tender_id'] ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No sent messages</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Message Content -->
                <div class="col-md-8 col-lg-9 message-content p-4">
                    <?php if(isset($_GET['view'])): ?>
                        <?php 
                            $message_id = $_GET['view'];
                            $query = "SELECT m.*, 
                                     u1.u_first_name as sender_name, u1.u_last_name as sender_last_name, u1.u_email as sender_email,
                                     u2.u_first_name as receiver_name, u2.u_last_name as receiver_last_name, u2.u_email as receiver_email,
                                     t.t_description as tender_title
                                     FROM tbl_messages m
                                     LEFT JOIN tbl_user u1 ON m.sender_id = u1.u_id
                                     LEFT JOIN tbl_user u2 ON m.receiver_id = u2.u_id
                                     LEFT JOIN tbl_tender t ON m.tender_id = t.t_id
                                     WHERE m.msg_id = ?";
                            $stmt = mysqli_prepare($con, $query);
                            mysqli_stmt_bind_param($stmt, "i", $message_id);
                            mysqli_stmt_execute($stmt);
                            $message = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                        ?>
                        
                        <?php if($message): ?>
                            <div class="mb-4">
                                <h4><?= htmlspecialchars($message['subject']) ?></h4>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong>From:</strong> 
                                        <?= htmlspecialchars($message['sender_name'] . ' ' . $message['sender_last_name']) ?>
                                        &lt;<?= htmlspecialchars($message['sender_email']) ?>&gt;
                                    </div>
                                    <div class="text-muted"><?= date('F j, Y \a\t g:i a', strtotime($message['created_at'])) ?></div>
                                </div>
                                <div class="mb-3">
                                    <strong>To:</strong> 
                                    <?= htmlspecialchars($message['receiver_name'] . ' ' . $message['receiver_last_name']) ?>
                                    &lt;<?= htmlspecialchars($message['receiver_email']) ?>&gt;
                                </div>
                                <?php if($message['tender_id']): ?>
                                    <div class="mb-3">
                                        <strong>Related Tender:</strong> 
                                        <a href="tender_details.php?id=<?= $message['tender_id'] ?>"><?= htmlspecialchars($message['tender_title']) ?></a>
                                    </div>
                                <?php endif; ?>
                                <hr>
                                <div class="message-body py-3">
                                    <?= nl2br(htmlspecialchars($message['message'])) ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="messaging.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to messages
                                </a>
                                <div>
                                    <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#replyModal">
                                        <i class="fas fa-reply me-2"></i>Reply
                                    </button>
                                    <button class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Message not found</p>
                                <a href="messaging.php" class="btn btn-primary">Back to messages</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Select a message to read</h4>
                            <p class="text-muted">Or compose a new one</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                                <i class="fas fa-plus me-2"></i>New Message
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Compose Modal -->
    <div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="messaging.php">
                    <div class="modal-body">
                        <div class="compose-form">
                            <div class="mb-3">
                                <label class="form-label">To</label>
                                <select class="form-select" name="receiver_id" required>
                                    <option value="">Select recipient</option>
                                    <?php
                                        $users = mysqli_query($con, "SELECT u_id, u_first_name, u_last_name FROM tbl_user WHERE u_id != $user_id ORDER BY u_first_name");
                                        while($user = mysqli_fetch_assoc($users)) {
                                            echo '<option value="'.$user['u_id'].'">'.htmlspecialchars($user['u_first_name']).' '.htmlspecialchars($user['u_last_name']).'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Related Tender (optional)</label>
                                <select class="form-select" name="tender_id">
                                    <option value="">Not related to any tender</option>
                                    <?php
                                        $tenders = mysqli_query($con, "SELECT t_id, t_description FROM tbl_tender ORDER BY t_id DESC");
                                        while($tender = mysqli_fetch_assoc($tenders)) {
                                            echo '<option value="'.$tender['t_id'].'">Tender #'.$tender['t_id'].': '.htmlspecialchars(substr($tender['t_description'], 0, 50)).'...</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" name="message" rows="8" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Attachment (optional)</label>
                                <input type="file" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reply Modal -->
    <?php if(isset($_GET['view']) && $message): ?>
    <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reply Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="messaging.php">
                    <input type="hidden" name="receiver_id" value="<?= $message['sender_id'] ?>">
                    <div class="modal-body">
                        <div class="compose-form">
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject" value="Re: <?= htmlspecialchars($message['subject']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Related Tender (optional)</label>
                                <input type="text" class="form-control" value="<?= $message['tender_id'] ? 'Tender #'.$message['tender_id'] : 'None' ?>" readonly>
                                <input type="hidden" name="tender_id" value="<?= $message['tender_id'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" name="message" rows="8" required>On <?= date('M j, Y', strtotime($message['created_at'])) ?>, <?= htmlspecialchars($message['sender_name']) ?> wrote:
&gt; <?= str_replace("\n", "\n&gt; ", htmlspecialchars($message['message'])) ?>


</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_message" class="btn btn-primary">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus the message textarea when composing/replying
        document.addEventListener('DOMContentLoaded', function() {
            const composeModal = document.getElementById('composeModal');
            const replyModal = document.getElementById('replyModal');
            
            if(composeModal) {
                composeModal.addEventListener('shown.bs.modal', function() {
                    const textarea = composeModal.querySelector('textarea[name="message"]');
                    textarea.focus();
                });
            }
            
            if(replyModal) {
                replyModal.addEventListener('shown.bs.modal', function() {
                    const textarea = replyModal.querySelector('textarea[name="message"]');
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                });
            }
        });
    </script>
</body>
</html>