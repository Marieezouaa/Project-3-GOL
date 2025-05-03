// Conway's Game of Life - JavaScript Logic
// Main variables
const GRID_SIZE = 20; // Default grid size
let grid = [];
let nextGrid = [];
let isRunning = false;
let intervalId = null;
let generation = 0;
let gridElement = document.getElementById('game-grid');

// Initialize the game on document load
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const nextBtn = document.getElementById('nextBtn');
    const gen23Btn = document.getElementById('gen23Btn');
    const resetBtn = document.getElementById('resetBtn');
    
    // Initialize the grid
    createGrid();
    
    // Add event listeners for the buttons
    startBtn.addEventListener('click', startGame);
    stopBtn.addEventListener('click', stopGame);
    nextBtn.addEventListener('click', nextGeneration);
    gen23Btn.addEventListener('click', generate23);
    resetBtn.addEventListener('click', resetGame);
});

// Create the initial grid
function createGrid() {
    gridElement = document.getElementById('game-grid');
    gridElement.innerHTML = '';
    
    // Create the initial arrays
    grid = [];
    nextGrid = [];
    
    // Style the grid
    gridElement.style.display = 'grid';
    gridElement.style.gridTemplateColumns = `repeat(${GRID_SIZE}, 1fr)`;
    
    // Create the cells
    for (let y = 0; y < GRID_SIZE; y++) {
        grid[y] = [];
        nextGrid[y] = [];
        
        for (let x = 0; x < GRID_SIZE; x++) {
            const cell = document.createElement('div');
            cell.classList.add('cell');
            cell.dataset.x = x;
            cell.dataset.y = y;
            
            // Initialize as dead
            grid[y][x] = 0;
            nextGrid[y][x] = 0;
            
            // Add click event to toggle cell state
            cell.addEventListener('click', function() {
                toggleCell(x, y);
            });
            
            gridElement.appendChild(cell);
        }
    }
    
    // Reset generation counter
    generation = 0;
    updateGenerationDisplay();
}

// Toggle cell state (alive/dead) when clicked
function toggleCell(x, y) {
    // Toggle the state in the grid array
    grid[y][x] = grid[y][x] ? 0 : 1;
    
    // Update the cell appearance
    updateCellDisplay(x, y);
}

// Update the visual appearance of a cell
function updateCellDisplay(x, y) {
    const cell = gridElement.querySelector(`[data-x="${x}"][data-y="${y}"]`);
    if (grid[y][x]) {
        cell.classList.add('alive');
    } else {
        cell.classList.remove('alive');
    }
}

// Start the game (continuous generation)
function startGame() {
    if (!isRunning) {
        isRunning = true;
        intervalId = setInterval(nextGeneration, 200); // Update every 200ms
    }
}

// Stop the game
function stopGame() {
    if (isRunning) {
        isRunning = false;
        clearInterval(intervalId);
    }
}

// Process one generation
function nextGeneration() {
    // Apply rules to determine the next generation
    for (let y = 0; y < GRID_SIZE; y++) {
        for (let x = 0; x < GRID_SIZE; x++) {
            // Count live neighbors
            const neighbors = countNeighbors(x, y);
            
            // Apply Conway's Game of Life rules
            if (grid[y][x]) {
                // Rule 1 & 2: Any live cell with fewer than 2 or more than 3 live neighbors dies
                if (neighbors < 2 || neighbors > 3) {
                    nextGrid[y][x] = 0;
                } else {
                    // Rule 3: Any live cell with 2 or 3 live neighbors lives on
                    nextGrid[y][x] = 1;
                }
            } else {
                // Rule 4: Any dead cell with exactly 3 live neighbors becomes alive
                if (neighbors === 3) {
                    nextGrid[y][x] = 1;
                } else {
                    nextGrid[y][x] = 0;
                }
            }
        }
    }
    
    // Update the grid and visuals
    updateGrid();
    
    // Increment generation count
    generation++;
    updateGenerationDisplay();
}

// Count living neighbors around a cell
function countNeighbors(x, y) {
    let count = 0;
    
    // Check all 8 surrounding cells
    for (let dy = -1; dy <= 1; dy++) {
        for (let dx = -1; dx <= 1; dx++) {
            // Skip the cell itself
            if (dx === 0 && dy === 0) continue;
            
            // Handle grid edges (wrap around)
            const nx = (x + dx + GRID_SIZE) % GRID_SIZE;
            const ny = (y + dy + GRID_SIZE) % GRID_SIZE;
            
            // Count if the neighbor is alive
            count += grid[ny][nx];
        }
    }
    
    return count;
}

// Update the grid after calculating the next generation
function updateGrid() {
    // Copy nextGrid to grid and update display
    for (let y = 0; y < GRID_SIZE; y++) {
        for (let x = 0; x < GRID_SIZE; x++) {
            grid[y][x] = nextGrid[y][x];
            updateCellDisplay(x, y);
        }
    }
}

// Reset the game
function resetGame() {
    stopGame();
    
    // Clear all cells
    for (let y = 0; y < GRID_SIZE; y++) {
        for (let x = 0; x < GRID_SIZE; x++) {
            grid[y][x] = 0;
            nextGrid[y][x] = 0;
            updateCellDisplay(x, y);
        }
    }
    
    // Reset generation counter
    generation = 0;
    updateGenerationDisplay();
}

// Run 23 generations quickly
function generate23() {
    stopGame();
    for (let i = 0; i < 23; i++) {
        nextGeneration();
    }
}

// Update the generation display
function updateGenerationDisplay() {
    const genDisplay = document.createElement('div');
    genDisplay.id = 'generationDisplay';
    genDisplay.textContent = `Generation: ${generation}`;
    
    // Remove any existing display
    const oldDisplay = document.getElementById('generationDisplay');
    if (oldDisplay) {
        oldDisplay.remove();
    }
    
    // Add the new display above the grid
    const controls = document.querySelector('.controls');
    controls.parentNode.insertBefore(genDisplay, controls);
}

// Pattern definitions for the required patterns
const patterns = {
    // Still life patterns
    block: [
        [1, 1],
        [1, 1]
    ],
    beehive: [
        [0, 1, 1, 0],
        [1, 0, 0, 1],
        [0, 1, 1, 0]
    ],
    boat: [
        [1, 1, 0],
        [1, 0, 1],
        [0, 1, 0]
    ],
    
    // Oscillator patterns
    blinker: [
        [0, 0, 0],
        [1, 1, 1],
        [0, 0, 0]
    ],
    beacon: [
        [1, 1, 0, 0],
        [1, 1, 0, 0],
        [0, 0, 1, 1],
        [0, 0, 1, 1]
    ]
};

// Create a dropdown for pattern selection
document.addEventListener('DOMContentLoaded', function() {
    // Create the pattern selector
    const controlsDiv = document.querySelector('.controls');
    
    const patternSelector = document.createElement('select');
    patternSelector.id = 'patternSelector';
    
    // Add options
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '🧬 Load Pattern';
    patternSelector.appendChild(defaultOption);
    
    // Add patterns from our pattern object
    for (const patternName in patterns) {
        const option = document.createElement('option');
        option.value = patternName;
        option.textContent = patternName.charAt(0).toUpperCase() + patternName.slice(1);
        patternSelector.appendChild(option);
    }
    
    // Add event listener
    patternSelector.addEventListener('change', function() {
        if (this.value) {
            loadPattern(this.value);
            // Reset selection
            this.value = '';
        }
    });
    
    // Add to controls
    controlsDiv.appendChild(patternSelector);
});

// Load a predefined pattern at the center of the grid
function loadPattern(patternName) {
    // Stop the game if it's running
    stopGame();
    
    // Get the pattern
    const pattern = patterns[patternName];
    if (!pattern) return;
    
    // Calculate center position
    const centerX = Math.floor(GRID_SIZE / 2) - Math.floor(pattern[0].length / 2);
    const centerY = Math.floor(GRID_SIZE / 2) - Math.floor(pattern.length / 2);
    
    // Clear the grid first
    resetGame();
    
    // Place the pattern
    for (let y = 0; y < pattern.length; y++) {
        for (let x = 0; x < pattern[y].length; x++) {
            if (pattern[y][x]) {
                grid[centerY + y][centerX + x] = 1;
                updateCellDisplay(centerX + x, centerY + y);
            }
        }
    }
}
