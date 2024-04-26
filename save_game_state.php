<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User is not logged in']);
    exit;
}

include 'db.php'; // Include the database connection

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['board_state'], $data['score_x'], $data['score_o'], $data['draw'], $data['current_player'])) {
    http_response_code(400); // Bad request
    echo json_encode(['error' => 'Incomplete data']);
    exit;
}

// Retrieve user ID from session and data from the request
$user_id = $_SESSION['user_id'];
$board_state = $data['board_state'];
$score_x = $data['score_x'];
$score_o = $data['score_o'];
$draw = $data['draw'];
$current_player = $data['current_player'];

// SQL to insert or update the game state
$sql = "INSERT INTO game_states (user_id, board_state, score_x, score_o, draw, current_player) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE board_state = VALUES(board_state), score_x = VALUES(score_x), score_o = VALUES(score_o), draw = VALUES(draw), current_player = VALUES(current_player)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isiiis", $user_id, $board_state, $score_x, $score_o, $draw, $current_player);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Game state saved successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
