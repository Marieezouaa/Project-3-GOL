<?php
include 'db.php';
session_start();

// checks login and admin right
if (!isset($_SESSION["admin_status"]) || !$_SESSION["admin_status"]) {
    header("Location: login.php");
    exit();
}

// Get the user ID from url
$userId = $_GET['id'] ?? null;

if (!$userId) {
    die("No user selected!");
}

// Gets details for database
$query = "SELECT username, is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    // edit and update the user details
    $updateQuery = "UPDATE users SET username = ?, is_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $username, $isAdmin, $userId);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <div class="auth-buttons">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="profile-edit-container">
        <h2>Edit User</h2>
        
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label for="is_admin">Admin Status:</label>
            <input type="checkbox" id="is_admin" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>

            <button type="submit">Update</button>
        </form>

        <p><a href="admin.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
