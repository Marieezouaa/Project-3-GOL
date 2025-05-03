<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

// Check if this is a POST request with JSON data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the JSON data from the request
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Validate required fields
    if (isset($data['generations']) && isset($data['population'])) {
        $user_id = $_SESSION["user_id"];
        $generations = intval($data['generations']);
        $population = intval($data['population']);
        $pattern = isset($data['pattern']) ? $data['pattern'] : null;
        
        // Insert or update game session
        if (isset($data['session_id']) && !empty($data['session_id'])) {
            // Update existing session
            $session_id = intval($data['session_id']);
            $stmt = $conn->prepare("UPDATE game_sessions SET generations = ?, max_population = ?, pattern_used = ?, end_time = NOW() WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iisii", $generations, $population, $pattern, $session_id, $user_id);
        } else {
            // Create new session
            $stmt = $conn->prepare("INSERT INTO game_sessions (user_id, generations, max_population, pattern_used) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $user_id, $generations, $population, $pattern);
        }
        
        if ($stmt->execute()) {
            // Return the session ID for future updates
            $session_id = isset($data['session_id']) ? $data['session_id'] : $conn->insert_id;
            echo json_encode([
                "success" => true, 
                "message" => "Game data saved successfully",
                "session_id" => $session_id
            ]);
        } else {
            http_response_code(500); // Internal server error
            echo json_encode(["success" => false, "message" => "Error saving game data"]);
        }
    } else {
        http_response_code(400); // Bad request
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
    }
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed"]);
}
?>