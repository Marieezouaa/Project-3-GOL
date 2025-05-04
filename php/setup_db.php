<?php
// This script creates the necessary database tables for the Game of Life project
// Run this once to set up your database structure

include 'db.php';

// Create game_sessions table if it doesn't exist
$game_sessions_table = "
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    generations INT DEFAULT 0,
    max_population INT DEFAULT 0,
    pattern_used VARCHAR(50) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($game_sessions_table) === TRUE) {
    echo "Table game_sessions created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Check if users table exists, and add any missing columns if needed
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "Users table exists, checking for required columns<br>";
    
    // Check if is_admin column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    echo "is_admin exists<br>s";
    if ($result->num_rows == 0) {
        // Add is_admin column
        if ($conn->query("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0") === TRUE) {
            echo "Column is_admin added to users table<br>";
        } else {
            echo "Error adding is_admin column: " . $conn->error . "<br>";
        }
    }

    $conn->query("UPDATE users SET is_admin = 1 WHERE username = 'admin'");
    echo "Admin status set for 'admin' user<br>";
} else {
    echo "Users table doesn't exist. Please make sure it's created first.<br>";
}

$conn->close();
echo "Database setup completed!";
?>