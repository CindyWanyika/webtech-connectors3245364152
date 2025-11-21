<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    
    // Check if request already exists
    $check_stmt = $conn->prepare("SELECT id FROM connection_requests WHERE sender_id = ? AND receiver_id = ?");
    $check_stmt->bind_param("ii", $sender_id, $receiver_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        // Send new request
        $insert_stmt = $conn->prepare("INSERT INTO connection_requests (sender_id, receiver_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $sender_id, $receiver_id);
        $insert_stmt->execute();
        
        $_SESSION['request_sent'] = "Connection request sent!";
    } else {
        $_SESSION['request_error'] = "Request already sent!";
    }
}

header("Location: home.php");
exit();
?>