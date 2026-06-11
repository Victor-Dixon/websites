import { BLOCKED_TERRAIN } from "./world-data.js";

function inBounds(world, x, y) {
  return x >= 0 && y >= 0 && x < world.width && y < world.height;
}

function objectFootprint(object) {
  const width = object.width || 1;
  const height = object.height || 1;
  const tiles = [];

  for (let y = object.y; y < object.y + height; y += 1) {
    for (let x = object.x; x < object.x + width; x += 1) {
      tiles.push({ x, y });
    }
  }

  return tiles;
}

function tileMatches(tile, x, y) {
  return Boolean(tile && tile.x === x && tile.y === y);
}

export function getObjectAtTile(world, x, y) {
  return world.objects.find((object) => objectFootprint(object).some((tile) => tile.x === x && tile.y === y));
}

export function getInteractionObjectAtTile(world, x, y) {
  return world.objects.find((object) => {
    if (tileMatches(object.door, x, y)) return true;
    return objectFootprint(object).some((tile) => tile.x === x && tile.y === y);
  });
}

export function isTileBlocked(world, x, y, options = {}) {
  if (!inBounds(world, x, y)) return true;

  const terrainType = world.terrain[y]?.[x];
  if (BLOCKED_TERRAIN.has(terrainType)) return true;

  const blockingObject = world.objects.find((object) => {
    if (!object.blocksMovement) return false;
    if (tileMatches(object.door, x, y)) return false;
    return objectFootprint(object).some((tile) => tile.x === x && tile.y === y);
  });

  if (!blockingObject) return false;
  return options.ignoreObjectId !== blockingObject.id;
}

export function isWalkable(world, x, y) {
  return !isTileBlocked(world, x, y);
}

function keyOf(tile) {
  return `${tile.x},${tile.y}`;
}

function neighbors(world, tile) {
  return [
    { x: tile.x, y: tile.y - 1 },
    { x: tile.x + 1, y: tile.y },
    { x: tile.x, y: tile.y + 1 },
    { x: tile.x - 1, y: tile.y },
  ].filter((next) => isWalkable(world, next.x, next.y));
}

function reconstructPath(cameFrom, start, goal) {
  const path = [];
  let currentKey = keyOf(goal);
  const startKey = keyOf(start);

  while (currentKey !== startKey) {
    const [x, y] = currentKey.split(",").map(Number);
    path.unshift({ x, y });
    currentKey = cameFrom.get(currentKey);
    if (!currentKey) {
      return [];
    }
  }

  return path;
}

export function findPath(world, start, goal) {
  if (!isWalkable(world, goal.x, goal.y)) {
    return [];
  }

  if (start.x === goal.x && start.y === goal.y) {
    return [];
  }

  const frontier = [start];
  const visited = new Set([keyOf(start)]);
  const cameFrom = new Map();

  while (frontier.length) {
    const current = frontier.shift();

    if (current.x === goal.x && current.y === goal.y) {
      return reconstructPath(cameFrom, start, goal);
    }

    for (const next of neighbors(world, current)) {
      const nextKey = keyOf(next);
      if (visited.has(nextKey)) continue;

      visited.add(nextKey);
      cameFrom.set(nextKey, keyOf(current));
      frontier.push(next);
    }
  }

  return [];
}

export function adjacentWalkableTiles(world, object) {
  const footprint = objectFootprint(object);
  const candidates = [];

  for (const tile of footprint) {
    candidates.push(
      { x: tile.x, y: tile.y - 1 },
      { x: tile.x + 1, y: tile.y },
      { x: tile.x, y: tile.y + 1 },
      { x: tile.x - 1, y: tile.y },
    );
  }

  if (object.door) {
    candidates.unshift(object.door);
  }

  const unique = new Map();
  candidates.forEach((candidate) => {
    if (isWalkable(world, candidate.x, candidate.y)) {
      unique.set(keyOf(candidate), candidate);
    }
  });

  return [...unique.values()];
}

export function findPathToAdjacent(world, start, object) {
  const candidates = adjacentWalkableTiles(world, object);
  let bestPath = [];

  for (const candidate of candidates) {
    const path = findPath(world, start, candidate);
    if (!path.length && !(start.x === candidate.x && start.y === candidate.y)) continue;
    if (!bestPath.length || path.length < bestPath.length) {
      bestPath = path;
    }
  }

  return bestPath;
}
