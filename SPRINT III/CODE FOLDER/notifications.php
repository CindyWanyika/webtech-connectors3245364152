<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle POST actions (accept/decline)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {

    $request_id = (int)$_POST['request_id'];
    $action = $_POST['action'];

    // Fetch request details
    $stmt = $conn->prepare("SELECT sender_id, receiver_id FROM connection_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($sender_id, $receiver_id);
    $request = null;
    if ($stmt->fetch()) {
        $request = ['sender_id' => $sender_id, 'receiver_id' => $receiver_id];
    }
    $stmt->close();

    if ($request && $request['receiver_id'] == $user_id) {

        if ($action === 'accept') {
            // Mark as accepted
            $update = $conn->prepare("UPDATE connection_requests SET status = 'accepted' WHERE id = ?");
            $update->bind_param("i", $request_id);
            $update->execute();
            $update->close();

            // Add notification for the sender
            $notif = $conn->prepare("
                INSERT INTO notifications (user_id, sender_id, type, reference_id)
                VALUES (?, ?, 'request_accepted', ?)
            ");
            $notif->bind_param("iii", $request['sender_id'], $user_id, $request_id);
            $notif->execute();
            $notif->close();

        } elseif ($action === 'decline') {
            // Mark as declined
            $update = $conn->prepare("UPDATE connection_requests SET status = 'declined' WHERE id = ?");
            $update->bind_param("i", $request_id);
            $update->execute();
            $update->close();
        }
    }

    header("Location: notifications.php");
    exit();
}

// Fetch pending requests
$pending_requests = [];
$pending_stmt = $conn->prepare("
    SELECT cr.id, u.name, u.bio, u.profile_picture
    FROM connection_requests cr
    JOIN datingAppUsers u ON cr.sender_id = u.id
    WHERE cr.receiver_id = ? AND cr.status = 'pending'
    ORDER BY cr.created_at DESC
");
$pending_stmt->bind_param("i", $user_id);
$pending_stmt->execute();
$pending_stmt->bind_result($cr_id, $cr_name, $cr_bio, $cr_pic);

while ($pending_stmt->fetch()) {
    $pending_requests[] = [
        'id' => $cr_id,
        'name' => $cr_name,
        'bio' => $cr_bio,
        'profile_picture' => $cr_pic
    ];
}
$pending_stmt->close();

// Fetch notifications
$notifications = [];
$notif_stmt = $conn->prepare("
    SELECT n.id, n.sender_id, n.type, n.reference_id, n.created_at,
           u.name AS sender_name, u.profile_picture
    FROM notifications n
    LEFT JOIN datingAppUsers u ON n.sender_id = u.id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_stmt->bind_result($nid, $sender_id, $type, $ref_id, $created_at, $sender_name, $profile_picture);

while ($notif_stmt->fetch()) {
    $notifications[] = [
        'id' => $nid,
        'sender_id' => $sender_id,
        'type' => $type,
        'reference_id' => $ref_id,
        'created_at' => $created_at,
        'sender_name' => $sender_name,
        'profile_picture' => $profile_picture
    ];
}
$notif_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SparX | Notifications</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>

<header>Your Connections</header>


<section class="requests-section" id="requests">
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
                        
                     
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="accept-btn">
                                <i class="fa-solid fa-check"></i> Accept
                            </button>
                        </form>

                        
                        <form method="POST" style="display:inline;">
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

<section class="notifications">
    <h2>Recent Activity</h2>

    <?php if (empty($notifications)): ?>
        <p class="no-activity">No recent notifications</p>

    <?php else: ?>
        <?php foreach ($notifications as $note): ?>
        
        <div class="notification-card">
            <img src="assets/profiles/<?php echo htmlspecialchars($note['profile_picture'] ?? 'lotus.png'); ?>">

            <div class="notification-text">
                <h4><?php echo htmlspecialchars($note['sender_name'] ?? 'Unknown'); ?></h4>

                <?php if ($note['type'] === 'request'): ?>
                    <p>sent you a connection request</p>
                    <a href="#requests" class="btn-small">View</a>

                <?php elseif ($note['type'] === 'request_accepted'): ?>
                    <p>accepted your connection</p>
                    <a href="messages.php?user=<?php echo $note['sender_id']; ?>" class="btn-small">Message</a>
                <?php endif; ?>

                <small class="timestamp">
                    <?php echo date("M d, Y • h:i A", strtotime($note['created_at'])); ?>
                </small>
            </div>
        </div>

        <?php endforeach; ?>
    <?php endif; ?>
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
