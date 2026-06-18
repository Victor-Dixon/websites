import { createFactionReputation } from "./factions.js";

export const defaultPlayerState = {
  id: "player",
  name: "Explorer",
  x: 8,
  y: 8,
  direction: "south",
  avatar: {
    style: "pseudo_3d_layered_2d",
    layers: ["aura", "cape", "base_body", "hair", "outfit", "boots", "weapon"],
  },
  walking: false,
  path: [],
  level: 1,
  xp: 0,
  currency: 0,
  visitedMarkers: [],
  stats: {
    knowledge: 0,
    discipline: 0,
    creativity: 0,
    leadership: 0,
    connection: 0,
    purpose: 0,
  },
  reputation: createFactionReputation(),
  quests: {},
  lastLoginDate: null,
};

export const DIRECTIONS = {
  north: { x: 0, y: -1 },
  south: { x: 0, y: 1 },
  west: { x: -1, y: 0 },
  east: { x: 1, y: 0 },
};

function directionFromStep(from, to) {
  if (to.x > from.x) return "east";
  if (to.x < from.x) return "west";
  if (to.y < from.y) return "north";
  return "south";
}

export function createPlayerState(savedState = {}) {
  return {
    ...defaultPlayerState,
    ...savedState,
    stats: { ...defaultPlayerState.stats, ...(savedState.stats || {}) },
    reputation: { ...defaultPlayerState.reputation, ...(savedState.reputation || {}) },
    quests: { ...(savedState.quests || {}) },
    walking: false,
    path: [],
  };
}

export function setPlayerPath(player, path) {
  player.path = Array.isArray(path) ? path.slice() : [];
  player.walking = player.path.length > 0;
}

export function clearPlayerPath(player) {
  player.path = [];
  player.walking = false;
}

export function stepPlayer(player) {
  if (!player.path.length) {
    player.walking = false;
    return false;
  }

  const next = player.path.shift();
  player.direction = directionFromStep(player, next);
  player.x = next.x;
  player.y = next.y;
  player.walking = player.path.length > 0;
  return true;
}

export function directionToVector(direction) {
  return DIRECTIONS[direction] || DIRECTIONS.south;
}
