<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Sign Up</title>
  <script src="app.js" defer></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>💜 SparX</header>

<div class="container">
  <h2>Create Account</h2>

  <?php 
  if (isset($_SESSION['register_error'])) {
      echo "<div class='alert alert-error'>".$_SESSION['register_error']."</div>";
      unset($_SESSION['register_error']);
  }
  ?>

  <form method="POST" action="login_register.php">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Ashesi Email" required>
    <input type="password" id="password" name="password" placeholder="Password" required>
    <div id="password-strength">
      <span id="strength-text">Strength: Weak</span>
      <div id="strength-bar"></div>
    </div>
    <input type="text" name="username" placeholder="Nickname" required>

    <select name="gender" required>
      <option value="">Select Gender</option>
      <option value="Female">Female</option>
      <option value="Male">Male</option>
      <option value="Other">Other</option>
    </select>

    <select name="role" required>
      <option value="">Select Role</option>
      <option value="user">User</option>
      <option value="admin">Admin</option>
    </select>

    <button type="submit">Sign Up</button>
  </form>

  <p style="text-align:center; margin-top:1rem;">
    Already have an account? <a href="login.php" style="color:#b084f7;">Sign In</a>
  </p>
</div>

</body>
</html>