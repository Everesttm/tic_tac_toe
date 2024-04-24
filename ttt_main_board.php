<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ttt_login.php');
    exit;
}

include 'db.php';

$initialGameState = isset($_SESSION['game_states']) ? $_SESSION['game_states'] : null;

$encodedGameState = json_encode($initialGameState);
$encodedUserId = json_encode($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tic Tac Toe Game Board</title>
    <link rel="stylesheet" href="ttt_board.css"> <!-- Make sure this path is correct -->
    <link rel="icon" type="image/png" href="https://cs.neiu.edu/sp24_haynazarov2/9.db_val_php/science.png">

</head>
<body>
    <header>
        <h1>Play Tic Tac Toe Game Here</h1>
        <div class="big">Have fun!</div>
        <!-- Logout Link -->
        <div><a href="logout.php">Logout</a></div>
    </header>

    <div class="content">
        <!-- Customization Options -->
        <aside>
            <h3>You can choose your favorite</h3>
            <div id="customization-options">
                <div>
                    <label for="boardColor">Board Background Color:</label>
                    <select id="boardColor">
                        <option value="white">White</option>
                        <option value="grey">Grey</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label for="boxShape">Box Shape:</label>
                    <select id="boxShape">
                        <option value="square">Square</option>
                        <option value="circle">Circle</option>
                    </select>
                </div>
            </div>
        </aside>

        <!-- Game Container -->
        <div id="game-container">
            <div id="board" class="board"></div>
            <button id="reset-game">New Game</button>
            <div id="game-status"></div>
        </div>
        <div id="message-box" class="hidden">
            <p id="message"></p>
            <button id="close-message">OK</button>
        </div>
        <button id="pause-game">Pause Game</button>
        <button id="resume-game">Resume Game</button>
        <button id="check-game-state">Check Game State</button>

        <!-- Score Board -->
        <aside id="score-board">X: 0 | O: 0 | Draw: 0</aside>
    </div>

    <footer>
        <div class="bottom">Enjoy The Game!</div>
    </footer>

    <!-- JavaScript to Initialize the Game State -->
    <script>
        window.initialGameState = <?php echo $encodedGameState; ?>;
        window.userId = <?php echo $encodedUserId; ?>;
    </script>
    
    
    <!-- Game Logic Script -->
    <script defer src="ttt_board_math.js"></script>

</body>
</html>