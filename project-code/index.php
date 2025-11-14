<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Sign In</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>💜 SparX</header>

  <div class="container">
    <h2>Welcome Back</h2>
    <form method="POST" action="signin_process.php">
      <input type="email" name="email" placeholder="Ashesi Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign In</button>
    </form>

    <p style="text-align:center; margin-top:1rem;">
      Don’t have an account? <a href="signup.php" style="color:#b084f7;">Sign Up</a>
    </p>
  </div>
</body>
</html>
