<?php
function sendMessage($con, $sender_id, $receiver_id, $subject, $message, $tender_id = null) {
    $query = "INSERT INTO tbl_messages (sender_id, receiver_id, tender_id, subject, message) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iiiss", $sender_id, $receiver_id, $tender_id, $subject, $message);
    return mysqli_stmt_execute($stmt);
}

function getMessages($con, $user_id, $is_sent = false) {
    $field = $is_sent ? "sender_id" : "receiver_id";
    $query = "SELECT m.*, 
              u1.u_first_name as sender_name, u1.u_last_name as sender_last_name,
              u2.u_first_name as receiver_name, u2.u_last_name as receiver_last_name,
              t.t_description as tender_title
              FROM tbl_messages m
              LEFT JOIN tbl_user u1 ON m.sender_id = u1.u_id
              LEFT JOIN tbl_user u2 ON m.receiver_id = u2.u_id
              LEFT JOIN tbl_tender t ON m.tender_id = t.t_id
              WHERE m.$field = ?
              ORDER BY m.created_at DESC";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getUnreadCount($con, $user_id) {
    $query = "SELECT COUNT(*) as count FROM tbl_messages 
              WHERE receiver_id = ? AND is_read = 0";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

function markAsRead($con, $message_id) {
    $query = "UPDATE tbl_messages SET is_read = 1 WHERE msg_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    return mysqli_stmt_execute($stmt);
}
?>