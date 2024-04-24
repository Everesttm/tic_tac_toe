document.addEventListener('DOMContentLoaded', () => {
    let gameState = window.initialGameState || {
        board: new Array(9).fill(null),
        scores: { 'X': 0, 'O': 0 },
        currentPlayer: 'X',
        paused: false
    };

    const gameBoardElem = document.querySelector('.board');
    const resetButton = document.getElementById('reset-game');
    const pauseButton = document.getElementById('pause-game');
    const resumeButton = document.getElementById('resume-game');
    const messageBox = document.getElementById('message-box');
    const messageText = document.getElementById('message');
    const closeButton = document.getElementById('close-message');
    const boardColorSelect = document.getElementById('boardColor');
    const boxShapeSelect = document.getElementById('boxShape');
    const scoreBoard = document.getElementById('score-board');
    const checkGameStateButton = document.getElementById('check-game-state');

    function initGameBoard() {
        gameState.board.fill(null);
        updateGameBoard();
        messageBox.classList.add('hidden');
    }

    function updateGameBoard() {
        gameBoardElem.innerHTML = '';
        gameState.board.forEach((player, index) => {
            gameBoardElem.appendChild(createSquare(index, player));
        });
        updateScoreBoard();
    }

    function createSquare(index, player) {
        const square = document.createElement('div');
        square.className = `box ${boxShapeSelect.value}`;
        square.textContent = player;
        square.addEventListener('click', () => handleSquareClick(index));
        return square;
    }

    function handleSquareClick(index) {
        if (gameState.board[index] !== null || gameState.paused) {
            return;
        }
        gameState.board[index] = gameState.currentPlayer;
        checkGameStatus();
        updateGameBoard();
        saveGameState(); // Save game state after each move
    }

    function checkGameStatus() {
        if (checkForWin()) {
            showMessage(`${gameState.currentPlayer} Won the game!`);
            gameState.scores[gameState.currentPlayer]++;
            updateScoreBoard();
            initGameBoard(); // Reset the board after a win
        } else if (!gameState.board.includes(null)) {
            showMessage("Tie, use a different strategy");
            initGameBoard(); // Reset the board after a tie
        } else {
            gameState.currentPlayer = gameState.currentPlayer === 'X' ? 'O' : 'X';
        }
    }

    function checkForWin() {
        const winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6],
        ];
        return winningCombinations.some(combination => {
            return combination.every(index => gameState.board[index] === gameState.currentPlayer);
        });
    }

    function updateScoreBoard() {
        scoreBoard.textContent = `X: ${gameState.scores['X']} | O: ${gameState.scores['O']}`;
    }

    function showMessage(msg) {
        messageText.textContent = msg;
        messageBox.classList.remove('hidden');
    }

    boardColorSelect.addEventListener('change', function() {
        gameBoardElem.style.backgroundColor = this.value;
    });

    boxShapeSelect.addEventListener('change', function() {
        updateGameBoard(); // Refresh board to apply new box shapes
    });

    resetButton.addEventListener('click', initGameBoard);
    pauseButton.addEventListener('click', () => gameState.paused = true);
    resumeButton.addEventListener('click', () => gameState.paused = false);

    closeButton.addEventListener('click', () => {
        messageBox.classList.add('hidden');
        initGameBoard();
    });

    // Check current game state from the server
    checkGameStateButton.addEventListener('click', function() {
        fetch('get_game_state.php', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Current game state:', data);
            alert('Current game state retrieved successfully. Check console for details.');
        })
        .catch(error => {
            console.error('Failed to fetch game state:', error);
            alert('Failed to fetch game state: ' + error.message);
        });
    });

    initGameBoard(); // Initialize the game board on load
});

function saveGameState() {
    const stateToSave = {
        board_state:JSON.stringify(gameState.board),
        score_x: gameState.scores['X'],
        score_o: gameState.scores['O'],
        current_player: gameState.currentPlayer
    };

    console.log("Saving game state:", stateToSave);

    fetch('save_game_state.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(stateToSave)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(JSON);
            console.log('Game state saved successfully:', data);
        } catch (error) {
            console.error('Error parsing JSON:', JSON);
            alert('Error parsing JSON: Check console for details.');
        }
    })
    .catch(error => {
        console.error('Error saving game state:', error);
    });
}