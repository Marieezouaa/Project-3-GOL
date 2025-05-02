
const gridSize = 20;
let grid = [];
let running = false;
let timer;

const gameGrid = document.getElementById("game-grid");

function createGrid() {
  gameGrid.innerHTML = "";
  for (let i = 0; i < gridSize * gridSize; i++) {
    const cell = document.createElement("div");
    cell.classList.add("cell");
    cell.dataset.index = i;
    cell.addEventListener("click", () => toggleCell(cell));
    gameGrid.appendChild(cell);
    grid[i] = false;
  }
}

function toggleCell(cell) {
  const index = parseInt(cell.dataset.index);
  grid[index] = !grid[index];
  cell.classList.toggle("alive", grid[index]);
}

function getNeighbors(index) {
  const row = Math.floor(index / gridSize);
  const col = index % gridSize;
  let count = 0;

  for (let r = row - 1; r <= row + 1; r++) {
    for (let c = col - 1; c <= col + 1; c++) {
      if (r === row && c === col) continue;
      if (r >= 0 && r < gridSize && c >= 0 && c < gridSize) {
        if (grid[r * gridSize + c]) count++;
      }
    }
  }
  return count;
}

function nextGen() {
  const newGrid = [...grid];
  grid.forEach((alive, index) => {
    const neighbors = getNeighbors(index);
    if (alive && (neighbors < 2 || neighbors > 3)) newGrid[index] = false;
    else if (!alive && neighbors === 3) newGrid[index] = true;
  });

  grid = newGrid;
  updateGridDisplay();
}

function updateGridDisplay() {
  document.querySelectorAll(".cell").forEach((cell, index) => {
    cell.classList.toggle("alive", grid[index]);
  });
}

function startGame() {
  running = true;
  timer = setInterval(nextGen, 500);
}

function stopGame() {
  running = false;
  clearInterval(timer);
}

document.getElementById("startBtn").onclick = startGame;
document.getElementById("stopBtn").onclick = stopGame;
document.getElementById("nextBtn").onclick = () => {
  nextGen();
};
document.getElementById("gen23Btn").onclick = () => {
  for (let i = 0; i < 23; i++) nextGen();
};
document.getElementById("resetBtn").onclick = () => {
  grid.fill(false);
  updateGridDisplay();
};

createGrid();
