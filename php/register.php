<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $email, $password);

  if ($stmt->execute()) {
    // ✅ Redirect to game after successful registration
    $_SESSION["user_id"] = $conn->insert_id;
    $_SESSION["username"] = $username;
    header("Location: ../index.html");
    exit();
  } else {
    $error = "Registration failed. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register</title>
  <link rel="stylesheet" href="../css/auth.css" />
</head>
<body>
  <div class="auth-container">
    <div class="auth-form">
      <h2>Create Your Account</h2>
      <p>"Would you like to meet him? Would you like to meet Bitch Stewie?" - Stewie Griffin</p>

      <?php if (isset($error)): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />

        <button type="submit">REGISTER</button>
        <div class="switch">
          Already have an account? <a href="login.php">Login</a>
        </div>
      </form>
      
      <div style="margin-top: 15px; text-align: center;">
        <a href="../index.html">Return to Game</a>
      </div>
    </div>
    <div class="auth-image" style="background-image: url('../assets/alive.png');"></div>
  </div>
</body>
</html>