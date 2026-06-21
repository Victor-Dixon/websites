import { createFactionReputation } from "./factions.js";

export const defaultPlayerState = {
  id: "player",
  name: "Explorer",
  x: 8,
  y: 8,
  direction: "south",
  avatar: {
    style: "sprite_sheet_with_toon_fallback",
    layers: ["aura", "cape", "base_body", "hair", "outfit", "boots", "weapon"],
    spriteSheet: {
      enabled: true,
      src: "./assets/sprites/dreamblade-cadet-spritesheet.svg",
      autoLayout: {
        enabled: true,
        rows: 4,
        mode: "grid",
        animationPreset: "auto",
      },
      scale: .96,
      anchorX: .5,
      anchorY: .86,
      imageSmoothing: true,
      glowColor: "rgba(92, 244, 255, .35)",
      glowBlur: 10,
      rows: {
        south: 0,
        west: 1,
        east: 2,
        north: 3,
      },
      animations: {
        idle: { frames: [0, 1, 2, 1], fps: 4 },
        walk: { frames: [0, 1, 2, 3], fps: 9 },
        attack: { frames: [1, 2, 3, 2], fps: 10 },
      },
    },
  },
  combat: {
    className: "Dreamblade Cadet",
    hp: 28,
    maxHp: 28,
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
    avatar: {
      ...defaultPlayerState.avatar,
      ...(savedState.avatar || {}),
      spriteSheet: {
        ...defaultPlayerState.avatar.spriteSheet,
        ...((savedState.avatar || {}).spriteSheet || {}),
      },
    },
    stats: { ...defaultPlayerState.stats, ...(savedState.stats || {}) },
    reputation: { ...defaultPlayerState.reputation, ...(savedState.reputation || {}) },
    combat: { ...defaultPlayerState.combat, ...(savedState.combat || {}) },
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
