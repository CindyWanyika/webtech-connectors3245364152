<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Login</title>
  <link rel="stylesheet" href="style.css">
  <script src="app.js" defer></script>
</head>
<body>

<header>💜 SparX</header>

<div class="container">
    <h2>Welcome Back!</h2>

    <?php 
    if (isset($_SESSION['register_success'])) {
        echo "<div class='alert alert-success'>".$_SESSION['register_success']."</div>";
        unset($_SESSION['register_success']);
    }
    if (isset($_SESSION['login_error'])) {
        echo "<div class='alert alert-error'>".$_SESSION['login_error']."</div>";
        unset($_SESSION['login_error']);
    }
    ?>

    <form method="POST" action="login_register.php">
        <input type="email" name="email" class="input-field" placeholder="Email" required>
        <input type="password" name="password" id="login-password" class="input-field" placeholder="Password" required>
        <button type="button" id="toggle-password" class="toggle-btn">Show</button>

        <button type="submit" class="btn-main">Login</button>

        <p style="text-align:center; margin-top:1rem;">
            Don't have an account? <a href="signup.php">Sign up</a>
        </p>
    </form>
</div>

</body>
</html>