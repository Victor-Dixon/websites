import { WORLD } from "./world-data.js";
import { createCamera, followPlayer, resizeCamera, screenToWorldTile } from "./camera.js";
import { findPath, findPathToAdjacent, getInteractionObjectAtTile, isWalkable } from "./pathfinding.js";
import { createPlayerState, defaultPlayerState, directionToVector, setPlayerPath, stepPlayer } from "./player.js";
import { formatSaveStatus, loadSave, savePlayerState } from "./save.js";
import { applyInteraction, findNearbyInteractable, showInteraction } from "./interactions.js";
import { renderWorld } from "./world-renderer.js";

const STEP_DELAY_MS = 145;

const canvas = document.getElementById("world-canvas");
const ctx = canvas.getContext("2d");
const interactionBox = document.getElementById("interaction-box");
const nameEl = document.getElementById("player-name");
const coordinatesEl = document.getElementById("player-coordinates");
const statusEl = document.getElementById("player-status");
const saveStatusEl = document.getElementById("save-status");

const loadedPlayer = loadSave({
  ...defaultPlayerState,
  x: WORLD.spawn.x,
  y: WORLD.spawn.y,
});
const player = createPlayerState(loadedPlayer);
const camera = createCamera(canvas, WORLD);
const renderState = {
  destination: null,
  targetObject: null,
};

let lastStepAt = 0;

function updateHud(statusText = null) {
  nameEl.textContent = player.name;
  coordinatesEl.textContent = `${player.x}, ${player.y}`;
  statusEl.textContent = statusText || (player.walking ? `Walking (${player.path.length} steps)` : "Ready");
}

function updateSaveStatus(savedPayload = null) {
  if (savedPayload?.savedAt) {
    saveStatusEl.textContent = formatSaveStatus(savedPayload.savedAt);
    return;
  }

  saveStatusEl.textContent = "Loaded from this browser";
}

function persistPosition() {
  const savedPayload = savePlayerState(player);
  updateSaveStatus(savedPayload);
}

function finishMovement() {
  renderState.destination = null;
  persistPosition();
  updateHud("Ready");

  const nearby = renderState.targetObject || findNearbyInteractable(WORLD, player);
  renderState.targetObject = null;

  if (nearby) {
    showInteraction(interactionBox, applyInteraction(player, nearby));
    persistPosition();
  }
}

function beginPath(path, destination, targetObject = null) {
  if (!path.length) {
    const nearby = targetObject || findNearbyInteractable(WORLD, player);
    if (nearby) {
      showInteraction(interactionBox, applyInteraction(player, nearby));
      persistPosition();
      updateHud("Ready");
      return;
    }

    showInteraction(interactionBox, "No path found. Try another walkable tile.");
    updateHud("Blocked");
    return;
  }

  setPlayerPath(player, path);
  renderState.destination = destination;
  renderState.targetObject = targetObject;
  showInteraction(interactionBox, `Walking to ${destination.x}, ${destination.y}...`);
  updateHud();
}

function requestMoveTo(tile) {
  const clickedObject = getInteractionObjectAtTile(WORLD, tile.x, tile.y);
  const start = { x: player.x, y: player.y };

  if (clickedObject?.blocksMovement && !isWalkable(WORLD, tile.x, tile.y)) {
    const path = findPathToAdjacent(WORLD, start, clickedObject);
    const destination = path[path.length - 1] || start;
    beginPath(path, destination, clickedObject);
    return;
  }

  if (!isWalkable(WORLD, tile.x, tile.y)) {
    showInteraction(interactionBox, "That tile is blocked by terrain. Choose a clear path, plain, or doorway.");
    updateHud("Blocked");
    return;
  }

  const path = findPath(WORLD, start, tile);
  beginPath(path, tile, clickedObject || null);
}

function requestKeyboardMove(direction) {
  const vector = directionToVector(direction);
  const goal = { x: player.x + vector.x, y: player.y + vector.y };

  player.direction = direction;
  if (!isWalkable(WORLD, goal.x, goal.y)) {
    showInteraction(interactionBox, "Blocked. Try stepping around the obstacle.");
    updateHud("Blocked");
    return;
  }

  beginPath([goal], goal, getInteractionObjectAtTile(WORLD, goal.x, goal.y) || null);
}

function resize() {
  resizeCamera(camera, canvas, WORLD);
  followPlayer(camera, player, WORLD);
}

function tick(timestamp) {
  if (player.walking && timestamp - lastStepAt >= STEP_DELAY_MS) {
    const moved = stepPlayer(player);
    lastStepAt = timestamp;
    followPlayer(camera, player, WORLD);
    updateHud();

    if (moved && !player.walking) {
      finishMovement();
    }
  }

  renderWorld(ctx, WORLD, camera, player, renderState);
  window.requestAnimationFrame(tick);
}

canvas.addEventListener("click", (event) => {
  const tile = screenToWorldTile(camera, WORLD, event.clientX, event.clientY, canvas);
  requestMoveTo(tile);
});

window.addEventListener("keydown", (event) => {
  const keyMap = {
    ArrowUp: "north",
    w: "north",
    W: "north",
    ArrowDown: "south",
    s: "south",
    S: "south",
    ArrowLeft: "west",
    a: "west",
    A: "west",
    ArrowRight: "east",
    d: "east",
    D: "east",
  };

  const direction = keyMap[event.key];
  if (!direction) return;

  event.preventDefault();
  if (!player.walking) {
    requestKeyboardMove(direction);
  }
});

window.addEventListener("resize", resize);

if (!isWalkable(WORLD, player.x, player.y)) {
  player.x = WORLD.spawn.x;
  player.y = WORLD.spawn.y;
}

resize();
followPlayer(camera, player, WORLD);
updateHud();
updateSaveStatus();
showInteraction(interactionBox, "Click a walkable tile to move. NPCs, doors, resources, portals, and gates respond when you reach them.");
window.requestAnimationFrame(tick);

window.digitalDreamscapeDebug = {
  world: WORLD,
  player,
  camera,
  requestMoveTo,
  isWalkable: (x, y) => isWalkable(WORLD, x, y),
};
