<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle connection actions (accept/decline)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];
        $action = $_POST['action'];
        
        if ($action === 'accept') {
            $get_sender = $conn->prepare("SELECT sender_id FROM connection_requests WHERE id = ? AND receiver_id = ?");
            $get_sender->bind_param("ii", $request_id, $user_id);
            $get_sender->execute();
            $get_sender->bind_result($sender_id);
            $get_sender->fetch();
            $get_sender->close();

            $stmt = $conn->prepare("UPDATE connection_requests SET status = 'accepted' WHERE id = ? AND receiver_id = ?");
            $stmt->bind_param("ii", $request_id, $user_id);
            $stmt->execute();

            $notif = $conn->prepare("
                INSERT INTO notifications (user_id, sender_id, type, reference_id, created_at)
                VALUES (?, ?, 'request_accepted', ?, NOW())
            ");
            $notif->bind_param("iii", $sender_id, $user_id, $request_id);
            $notif->execute();
            $notif->close();
        } elseif ($action === 'decline') {
            $stmt = $conn->prepare("UPDATE connection_requests SET status = 'declined' WHERE id = ? AND receiver_id = ?");
            $stmt->bind_param("ii", $request_id, $user_id);
            $stmt->execute();
        }
        
        header("Location: home.php");
        exit();
    }
}

// Fetch pending connection requests for current user
$stmt = $conn->prepare("
    SELECT cr.id, cr.sender_id, u.name, u.username, u.bio, u.profile_picture 
    FROM connection_requests cr 
    JOIN datingAppUsers u ON cr.sender_id = u.id 
    WHERE cr.receiver_id = ? AND cr.status = 'pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch suggested users (users not connected and not pending)
$suggested_stmt = $conn->prepare("
    SELECT u.id, u.name, u.username, u.bio, u.profile_picture 
    FROM datingAppUsers u 
    WHERE u.id != ? 
    AND u.id NOT IN (
        SELECT sender_id FROM connection_requests WHERE receiver_id = ? 
        UNION 
        SELECT receiver_id FROM connection_requests WHERE sender_id = ?
    )
    LIMIT 6
");
$suggested_stmt->bind_param("iii", $user_id, $user_id, $user_id);
$suggested_stmt->execute();
$suggested_users = $suggested_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Home</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <header>✨ Welcome to SparX — Find your campus vibe ✨</header>
  
  <div style="padding: 1rem; text-align: center;">
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
  </div>

  <!-- Pending Connection Requests -->
  <section class="requests-section">
    <h2>Connection Requests</h2>
    <?php if (empty($pending_requests)): ?>
      <div class="no-requests">
        <p>No pending connection requests</p>
      </div>
    <?php else: ?>
      <div class="requests-feed">
        <?php foreach ($pending_requests as $request): ?>
        <div class="request-card">
          <img src="assets/profiles/<?php echo htmlspecialchars($request['profile_picture'] ?? 'lotus.png'); ?>" 
               alt="<?php echo htmlspecialchars($request['name']); ?>">
          <div class="request-info">
            <h3><?php echo htmlspecialchars($request['name']); ?></h3>
            <p><?php echo htmlspecialchars($request['bio'] ?? 'No bio yet...'); ?></p>
            <div class="request-actions">
              <form method="POST" style="display: inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="accept-btn">
                  <i class="fa-solid fa-check"></i> Accept
                </button>
              </form>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                <input type="hidden" name="action" value="decline">
                <button type="submit" class="decline-btn">
                  <i class="fa-solid fa-xmark"></i> Decline
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Suggested Users to Connect With -->
  <section class="suggestions-section">
    <h2>Suggested Connections</h2>
    <div class="suggestions-feed">
      <?php foreach ($suggested_users as $user): ?>
      <div class="suggestion-card">
        <img src="assets/profiles/<?php echo htmlspecialchars($user['profile_picture'] ?? 'lotus.png'); ?>" 
             alt="<?php echo htmlspecialchars($user['name']); ?>">
        <div class="suggestion-info">
          <h3><?php echo htmlspecialchars($user['name']); ?></h3>
          <p><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet...'); ?></p>
          <form method="POST" action="send_request.php">
            <input type="hidden" name="receiver_id" value="<?php echo $user['id']; ?>">
            <button type="submit" class="connect-btn">
              <i class="fa-solid fa-user-plus"></i> Connect
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="navbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="messages.php"><i class="fa-regular fa-message"></i> Messages</a>
    <a href="notifications.php"><i class="fa-regular fa-bell"></i> Notifications</a>
    <a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>
</body>
</html>