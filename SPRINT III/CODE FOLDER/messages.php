<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch connected users (accepted connections)
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.profile_picture 
    FROM datingAppUsers u 
    WHERE u.id IN (
        SELECT sender_id FROM connection_requests 
        WHERE receiver_id = ? AND status = 'accepted'
        UNION
        SELECT receiver_id FROM connection_requests 
        WHERE sender_id = ? AND status = 'accepted'
    )
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$connected_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    // Check if users are connected
    $check_stmt = $conn->prepare("
        SELECT id FROM connection_requests 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
        AND status = 'accepted'
    ");
    $check_stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0 && !empty($message)) {
        $insert_stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iis", $user_id, $receiver_id, $message);
        $insert_stmt->execute();
    }
    
    header("Location: messages.php?user_id=" . $receiver_id);
    exit();
}

// Fetch messages with selected user
$selected_user = null;
$messages = [];
if (isset($_GET['user_id'])) {
    $selected_user_id = $_GET['user_id'];
    
    // Get selected user info
    $user_stmt = $conn->prepare("SELECT id, name, profile_picture FROM datingAppUsers WHERE id = ?");
    $user_stmt->bind_param("i", $selected_user_id);
    $user_stmt->execute();
    $selected_user = $user_stmt->get_result()->fetch_assoc();
    
    // Get messages between current user and selected user
    $msg_stmt = $conn->prepare("
        SELECT m.*, u.name as sender_name 
        FROM messages m 
        JOIN datingAppUsers u ON m.sender_id = u.id 
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
        OR (m.sender_id = ? AND m.receiver_id = ?) 
        ORDER BY m.created_at ASC
    ");
    $msg_stmt->bind_param("iiii", $user_id, $selected_user_id, $selected_user_id, $user_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Mark messages as read
    $update_stmt = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE receiver_id = ? AND sender_id = ?");
    $update_stmt->bind_param("ii", $user_id, $selected_user_id);
    $update_stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Messages</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <header>💌 Messages</header>

  <div class="chat-container">
    <!-- Connected Users List -->
    <div class="chat-list">
      <h3 style="padding: 1rem; margin: 0; border-bottom: 1px solid #eee;">Connections</h3>
      <?php if (empty($connected_users)): ?>
        <div style="padding: 1rem; text-align: center; color: #666;">
          <p>No connections yet</p>
          <p><small>Accept connection requests to start messaging</small></p>
        </div>
      <?php else: ?>
        <?php foreach ($connected_users as $user): ?>
          <a href="messages.php?user_id=<?php echo $user['id']; ?>" 
             class="chat-list-item <?php echo ($selected_user && $selected_user['id'] == $user['id']) ? 'active' : ''; ?>">
            <img src="assets/profiles/<?php echo htmlspecialchars($user['profile_picture'] ?? 'lotus.png'); ?>" 
                 alt="<?php echo htmlspecialchars($user['name']); ?>">
            <div>
              <h4><?php echo htmlspecialchars($user['name']); ?></h4>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
      <?php if ($selected_user): ?>
        <div class="chat-header">
          <img src="assets/profiles/<?php echo htmlspecialchars($selected_user['profile_picture'] ?? 'lotus.png'); ?>" 
               alt="<?php echo htmlspecialchars($selected_user['name']); ?>">
          <h3><?php echo htmlspecialchars($selected_user['name']); ?></h3>
        </div>
        
        <div class="chat-messages" id="chat-messages">
          <?php foreach ($messages as $message): ?>
            <div class="bubble <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
              <p><?php echo htmlspecialchars($message['message']); ?></p>
              <small><?php echo date('h:i A', strtotime($message['created_at'])); ?></small>
            </div>
          <?php endforeach; ?>
        </div>
        
        <form method="POST" class="chat-input">
          <input type="hidden" name="receiver_id" value="<?php echo $selected_user['id']; ?>">
          <input type="text" name="message" placeholder="Type a message..." required>
          <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
      <?php else: ?>
        <div class="no-chat-selected">
          <i class="fa-regular fa-message" style="font-size: 3rem; color: #b084f7; margin-bottom: 1rem;"></i>
          <h3>Select a connection to start chatting</h3>
          <p>Choose someone from your connections list to begin messaging</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="navbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="messages.php"><i class="fa-regular fa-message"></i> Messages</a>
    <a href="notifications.html"><i class="fa-regular fa-bell"></i> Notifications</a>
    <a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <script>
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
  </script>
</body>
</html>