<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User is not logged in']);
    exit;
}

include "db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT board_state, score_x, score_o, draw, current_player FROM game_states WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $gameState = $result->fetch_assoc();
        echo json_encode(['success' => true, 'gameState' => $gameState]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database execute error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare error: ' . $conn->error]);
}
$conn->close();