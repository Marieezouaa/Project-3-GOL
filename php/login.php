<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      $_SESSION["user_id"] = $id;
      $_SESSION["username"] = $username;
      
      // ✅ Redirect to game page after login
      header("Location: ../index.html");
      exit();
    }
  }

  $error = "Invalid username or password.";
}
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
      <p>"Alright you undead bastards, time to send you back to hell! AHHHHH!!!!!" - Stewie Griffin</p>

      <?php if (isset($error)): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
      <?php endif; ?>

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
