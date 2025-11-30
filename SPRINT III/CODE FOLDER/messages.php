<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

function getConnectedUsers($conn, $user_id) : array {
    $sql = "
        SELECT u.id, u.name, u.profile_picture 
        FROM datingAppUsers u 
        WHERE u.id IN (
            SELECT sender_id FROM connection_requests 
            WHERE receiver_id = ? AND status = 'accepted'
            UNION
            SELECT receiver_id FROM connection_requests 
            WHERE sender_id = ? AND status = 'accepted'
        )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function usersAreConnected($conn, $u1, $u2) : bool {
    $sql = "
        SELECT id FROM connection_requests 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND status = 'accepted'
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $u1, $u2, $u2, $u1);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function sendMessage($conn, $sender_id, $receiver_id, $message) : bool {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    return $stmt->execute();
}

function getUserById($conn, $id) : array {
    $stmt = $conn->prepare("SELECT id, name, profile_picture FROM datingAppUsers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

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

function markAsRead($conn, $receiver_id, $sender_id) : bool {
    $sql = "UPDATE messages SET is_read = TRUE 
            WHERE receiver_id = ? AND sender_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed (markAsRead): " . $conn->error);
    }

    $stmt->bind_param("ii", $receiver_id, $sender_id);
    return $stmt->execute();
}

function loadMessages($conn, $user_id, $selected_id) : void {
  $messages = getMessages($conn, $user_id, $selected_id);
  foreach ($messages as $msg) {
    $is_sender = $msg['sender_id'] == $user_id;
    $msg_class = $is_sender ? 'sent' : 'received';
    echo "<div class='bubble {$msg_class}'>
            <p>" . htmlspecialchars($msg['message']) . "</p>
            <small> " . date('h:i', strtotime($msg['created_at'])) . "</small>
          </div>";
  }
}

// Load connected users
$connected_users = getConnectedUsers($conn, $user_id);

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $receiver = (int) $_POST['receiver_id'];
    $message  = trim($_POST['message']);

    if (!empty($message) && usersAreConnected($conn, $user_id, $receiver)) {
        sendMessage($conn, $user_id, $receiver, $message);
    }

    header("Location: messages.php?user_id=" . $receiver);
    exit();
}

// Load selected chat + messages
$selected_user = null;
$messages = [];

if (isset($_GET['user_id'])) {
    $selected_id = (int) $_GET['user_id'];

    $selected_user = getUserById($conn, $selected_id);
    $messages = getMessages($conn, $user_id, $selected_id);

    // Mark unread as read
    markAsRead($conn, $user_id, $selected_id);
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
          <?php loadMessages($conn, $user_id, $selected_user['id']); ?>
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

  <div class="navbar" style="position: sticky;">
    <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="messages.php"><i class="fa-regular fa-message"></i> Messages</a>
    <a href="notifications.php"><i class="fa-regular fa-bell"></i> Notifications</a>
    <a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <script>
    const chatMessages = document.getElementById('chat-messages');

    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    <?php if ($selected_user): ?>
      function refreshMessages() {
          fetch("load_messages.php?user_id=<?php echo $selected_user['id']; ?>")
              .then(result => result.text())
              .then(html => {
                  chatMessages.innerHTML = html;
                  scrollToBottom();
              });
      }

      scrollToBottom();

      // Refresh every 5 seconds
      setInterval(refreshMessages, 5000);
    <?php endif; ?>
  </script>
</body>
</html>