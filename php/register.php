<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
  $email = $_POST["email"];

  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $email, $password);

  if ($stmt->execute()) {
    header("Location: login.php");
    exit();
  } else {
    $error = "Registration failed.";
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
        <p style="color: red;"><?php echo $error; ?></p>
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
    </div>
    <div class="auth-image" style="background-image: url('../assets/alive.png');"></div>
  </div>
</body>
</html>
