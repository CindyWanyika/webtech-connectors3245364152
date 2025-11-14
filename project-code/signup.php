<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SparX | Sign Up</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>💜 SparX</header>

  <div class="container">
    <h2>Create Account</h2>
    <form method="POST" action="signup.php">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Ashesi Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <select name="gender" required>
        <option value="">Select Gender</option>
        <option>Female</option>
        <option>Male</option>
        <option>Other</option>
      </select>
      <button type="submit">Sign Up</button>
    </form>

    <p style="text-align:center; margin-top:1rem;">
      Already have an account? <a href="index.php" style="color:#b084f7;">Sign In</a>
    </p>
  </div>
</body>
</html>
