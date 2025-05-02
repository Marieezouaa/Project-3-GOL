<?php
include 'db.php';
session_start();
// handle login logic here (if needed)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="../css/auth.css" />
</head>
<body>
  <div class="auth-container">
    <div class="auth-form">
      <h2>Login to Game of Life</h2>
      <p>Welcome back! Please login to continue.</p>

      <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        
        <div class="auth-options">
          <label><input type="checkbox" /> Remember</label>
          <a href="#">Forgot Password?</a>
        </div>

        <button type="submit">LOGIN</button>
        <div class="switch">
          Don't have an account? <a href="register.php">Register</a>
        </div>
      </form>
    </div>
    <div class="auth-image" style="background-image: url('../assets/dead-Peter.png');"></div>
  </div>
</body>
</html>
