<?php
include 'db.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Fetch user's game sessions from database
$query = "SELECT * FROM game_sessions WHERE user_id = ? ORDER BY start_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);

// Calculate summary statistics
$totalGames = count($sessions);
$totalGenerations = 0;
$maxGenerations = 0;
$totalTime = 0;
$maxPopulation = 0;

foreach ($sessions as $session) {
    $totalGenerations += $session['generations'];
    $maxGenerations = max($maxGenerations, $session['generations']);
    $maxPopulation = max($maxPopulation, $session['max_population']);
    
    // Calculate time played (in minutes)
    $startTime = strtotime($session['start_time']);
    $endTime = isset($session['end_time']) ? strtotime($session['end_time']) : time();
    $sessionTime = ($endTime - $startTime) / 60; // Convert to minutes
    $totalTime += $sessionTime;
}

// Round total time to 2 decimal places
$totalTime = round($totalTime, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game of Life - Player Stats</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .stats-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .stats-header h1 {
            color: #2c3e50;
            font-size: 28px;
        }
        
        .stats-header p {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        .stats-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .stat-card {
            flex: 1 0 150px;
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            color: #3498db;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .game-history h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .game-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .game-table th, .game-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .game-table th {
            background-color: #f2f6f8;
            color: #7f8c8d;
            font-weight: 600;
        }
        
        .game-table tr:hover {
            background-color: #f5f9fa;
        }
        
        .no-games {
            text-align: center;
            padding: 30px;
            color: #95a5a6;
            font-style: italic;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .nav-button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }
        
        .nav-button:hover {
            background-color: #2980b9;
        }
        
        @media (max-width: 768px) {
            .stat-card {
                flex: 0 0 calc(50% - 20px);
            }
            
            .game-table {
                font-size: 14px;
            }
            
            .stats-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Stewie's Game of Life</h1>
        <div class="auth-buttons">
            <a href="../index.html">Play Game</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="stats-container">
        <div class="stats-header">
            <h1>Player Statistics</h1>
            <p>Hello, <?php echo htmlspecialchars($username); ?>! Here's your Game of Life journey so far.</p>
        </div>

        <div class="stats-summary">
            <div class="stat-card">
                <h3>Total Games</h3>
                <div class="stat-value"><?php echo $totalGames; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Generations</h3>
                <div class="stat-value"><?php echo $totalGenerations; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Highest Generation</h3>
                <div class="stat-value"><?php echo $maxGenerations; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Max Population</h3>
                <div class="stat-value"><?php echo $maxPopulation; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Play Time</h3>
                <div class="stat-value"><?php echo $totalTime; ?> min</div>
            </div>
        </div>

        <div class="game-history">
            <h2>Game History</h2>
            
            <?php if ($totalGames > 0): ?>
                <table class="game-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Played</th>
                            <th>Generations</th>
                            <th>Max Population</th>
                            <th>Pattern Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($session['start_time'])); ?></td>
                                <td>
                                    <?php
                                    $start = strtotime($session['start_time']);
                                    $end = isset($session['end_time']) ? strtotime($session['end_time']) : time();
                                    $minutes = round(($end - $start) / 60, 1);
                                    echo $minutes . ' min';
                                    ?>
                                </td>
                                <td><?php echo $session['generations']; ?></td>
                                <td><?php echo $session['max_population']; ?></td>
                                <td><?php echo htmlspecialchars($session['pattern_used'] ?? 'Custom'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-games">
                    <p>You haven't played any games yet. Start playing to see your stats!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-buttons">
            <a href="../index.html" class="nav-button">Play Game</a>
        </div>
    </div>
</body>
</html>