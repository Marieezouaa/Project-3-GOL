<?php
include 'db.php';
session_start();

// Check admin
if (!isset($_SESSION["admin_status"]) || !$_SESSION["admin_status"]) {
    header("Location: login.php");
    exit();
}

// All Users
$query = "SELECT id, username, is_admin FROM users";
$usersResult = $conn->query($query);

// ALl Game Sessions
$gameSessionsQuery = "SELECT gs.id, u.username, gs.start_time, gs.end_time, gs.generations, gs.max_population, gs.pattern_used 
                       FROM game_sessions gs
                       JOIN users u ON gs.user_id = u.id
                       ORDER BY gs.start_time DESC";
$gameSessionsResult = $conn->query($gameSessionsQuery);

// Deletes
if (isset($_GET['delete_user'])) {
    $userIdToDelete = $_GET['delete_user'];
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userIdToDelete);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Edits
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_user'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
    
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <div class="auth-buttons">
            <a href="login.php">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Manage Users</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Admin Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while ($user = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                            <a href="admin.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php 
                endwhile; 
                ?>
            </tbody>
        </table>

        <h2>Game Sessions</h2>
        <table class="game-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Generations</th>
                    <th>Max Population</th>
                    <th>Pattern Used</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($session = $gameSessionsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session['username']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($session['start_time'])); ?></td>
                        <td><?php echo $session['generations']; ?></td>
                        <td><?php echo $session['max_population']; ?></td>
                        <td><?php echo htmlspecialchars($session['pattern_used'] ?? 'Custom'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
