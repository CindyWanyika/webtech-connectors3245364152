<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$selected_id = (int) ($_GET['user_id'] ?? 0);

function getMessages($conn, $u1, $u2) : array {
    $sql = "
        SELECT m.*, u.name AS sender_name 
        FROM messages m
        JOIN datingAppUsers u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
        OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $u1, $u2, $u2, $u1);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$messages = getMessages($conn, $user_id, $selected_id);

foreach ($messages as $msg) {
    $is_sender = $msg['sender_id'] == $user_id;
    $msg_class = $is_sender ? 'sent' : 'received';

    echo "<div class='bubble {$msg_class}'>
            <p>" . htmlspecialchars($msg['message']) . "</p>
            <small>" . date('h:i', strtotime($msg['created_at'])) . "</small>
          </div>";
}
?>