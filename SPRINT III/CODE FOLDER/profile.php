<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM datingAppUsers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    
    // Handle profile picture upload
    $profile_picture = $user['profile_picture']; // Keep existing picture by default
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_picture']['type'];
        $file_size = $_FILES['profile_picture']['size'];
        
        // Check file type and size (max 5MB)
        if (in_array($file_type, $allowed_types) && $file_size < 5000000) {
            $upload_dir = 'assets/profiles/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                // Delete old profile picture if it exists and is not the default
                if ($user['profile_picture'] && $user['profile_picture'] !== 'lotus.png') {
                    $old_file_path = $upload_dir . $user['profile_picture'];
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
                $profile_picture = $filename;
            }
        }
    }
    
    // Update user in database
    $update_stmt = $conn->prepare("UPDATE datingAppUsers SET name = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $name, $bio, $profile_picture, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['profile_success'] = "Profile updated successfully!";
        // Update session data
        $_SESSION['user_name'] = $name;
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['profile_error'] = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Profile</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <header>My SparX Profile</header>

  <section class="profile-page">
    <?php 
    if (isset($_SESSION['profile_success'])) {
        echo "<div class='alert alert-success' style='margin-bottom: 1rem;'>".$_SESSION['profile_success']."</div>";
        unset($_SESSION['profile_success']);
    }
    if (isset($_SESSION['profile_error'])) {
        echo "<div class='alert alert-error' style='margin-bottom: 1rem;'>".$_SESSION['profile_error']."</div>";
        unset($_SESSION['profile_error']);
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
      <!-- Profile Picture Section -->
      <div class="profile-picture-container">
        <?php
        $profile_img_path = 'assets/profiles/' . ($user['profile_picture'] ?? 'lotus.png');
        $default_img_path = 'assets/lotus.png';
        
        // Check if profile image exists, otherwise use default
        if (isset($user['profile_picture']) && $user['profile_picture'] !== 'lotus.png' && file_exists($profile_img_path)) {
            $img_src = $profile_img_path;
        } else {
            $img_src = $default_img_path;
        }
        ?>
        <img src="<?php echo $img_src; ?>" 
             alt="Profile Picture" 
             id="profile-preview"
             class="profile-image"
             onerror="this.src='assets/lotus.png'">
        
        <div class="picture-upload">
          <input type="file" 
                 id="profile_picture" 
                 name="profile_picture" 
                 accept="image/*" 
                 style="display: none;">
          <button type="button" class="change-photo-btn" onclick="document.getElementById('profile_picture').click()">
            <i class="fa-solid fa-camera"></i> Change Photo
          </button>
        </div>
      </div>

      <!-- Form Fields -->
      <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Full Name">
      <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled placeholder="Email">
      <textarea name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
      <button type="submit" class="save-btn">Save Changes</button>
    </form>
  </section>

  <div class="navbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="messages.php"><i class="fa-regular fa-message"></i> Messages</a>
    <a href="notifications.html"><i class="fa-regular fa-bell"></i> Notifications</a>
    <a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <script>
    // Preview profile picture before upload
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (5MB max)
            if (file.size > 5000000) {
                alert('File too large. Please choose an image under 5MB.');
                this.value = '';
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, GIF, or WebP).');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Simple form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.querySelector('input[name="name"]').value.trim();
        
        if (!name) {
            e.preventDefault();
            alert('Please enter your name.');
            return;
        }
    });
  </script>
</body>
</html>