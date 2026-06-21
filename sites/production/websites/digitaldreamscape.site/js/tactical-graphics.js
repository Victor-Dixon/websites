import { isWalkable } from "./pathfinding.js";

const MOVE_RANGE = 5;
const DANGER_RANGE = 3;

function keyOf(tile) {
  return `${tile.x},${tile.y}`;
}

function inBounds(world, x, y) {
  return x >= 0 && y >= 0 && x < world.width && y < world.height;
}

function cardinalNeighbors(tile) {
  return [
    { x: tile.x, y: tile.y - 1 },
    { x: tile.x + 1, y: tile.y },
    { x: tile.x, y: tile.y + 1 },
    { x: tile.x - 1, y: tile.y },
  ];
}

function diamondTiles(world, origin, range, walkableOnly = true) {
  const tiles = [];

  for (let y = origin.y - range; y <= origin.y + range; y += 1) {
    for (let x = origin.x - range; x <= origin.x + range; x += 1) {
      if (!inBounds(world, x, y)) continue;
      if (Math.abs(origin.x - x) + Math.abs(origin.y - y) > range) continue;
      if (walkableOnly && !isWalkable(world, x, y)) continue;
      tiles.push({ x, y });
    }
  }

  return tiles;
}

function movementTiles(world, player) {
  const start = { x: player.x, y: player.y };
  const frontier = [{ ...start, cost: 0 }];
  const visited = new Map([[keyOf(start), 0]]);

  while (frontier.length) {
    const current = frontier.shift();
    if (current.cost >= MOVE_RANGE) continue;

    cardinalNeighbors(current).forEach((next) => {
      if (!isWalkable(world, next.x, next.y)) return;
      const nextCost = current.cost + 1;
      const key = keyOf(next);
      if (visited.has(key) && visited.get(key) <= nextCost) return;
      visited.set(key, nextCost);
      frontier.push({ ...next, cost: nextCost });
    });
  }

  return [...visited.keys()]
    .map((key) => {
      const [x, y] = key.split(",").map(Number);
      return { x, y };
    })
    .filter((tile) => tile.x !== player.x || tile.y !== player.y);
}

function dangerTiles(world) {
  const dangerAnchors = world.objects.filter((object) => object.type === "gate");
  const unique = new Map();

  dangerAnchors.forEach((anchor) => {
    diamondTiles(world, anchor, DANGER_RANGE).forEach((tile) => {
      unique.set(keyOf(tile), tile);
    });
  });

  return [...unique.values()];
}

function objectiveTiles(world) {
  const objectives = world.objects.filter((object) => {
    return Boolean(object.interaction) && ["quest", "portal_locked", "training_placeholder", "encounter_placeholder"].includes(object.interaction.type);
  });
  const unique = new Map();

  objectives.forEach((object) => {
    const tile = object.door || { x: object.x, y: object.y };
    if (inBounds(world, tile.x, tile.y)) {
      unique.set(keyOf(tile), tile);
    }
  });

  return [...unique.values()];
}

function battlePreview(world, player) {
  const defender = world.objects.find((object) => object.type === "gate") || world.objects.find((object) => object.interaction);

  return {
    attacker: player.name || "Explorer",
    defender: defender?.name || "Training Echo",
    damage: "12-18",
    hit: "82%",
  };
}

export function createTacticalGraphicsState(world, player) {
  const maxHp = player.combat?.maxHp || 28;
  const currentHp = Math.min(maxHp, player.combat?.hp || maxHp);

  return {
    movementTiles: movementTiles(world, player),
    dangerTiles: dangerTiles(world),
    objectiveTiles: objectiveTiles(world),
    selectedUnit: {
      name: player.name || "Explorer",
      className: player.combat?.className || "Dreamblade Cadet",
      hp: currentHp,
      maxHp,
    },
    preview: battlePreview(world, player),
  };
}
