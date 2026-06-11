import { getInteractionObjectAtTile } from "./pathfinding.js";

const TYPE_PREFIX = {
  dialogue: "Dialogue",
  message: "World",
  portal_locked: "Locked Portal",
  shop_placeholder: "Shop",
  training_placeholder: "Training",
  encounter_placeholder: "Encounter",
};

function distance(a, b) {
  return Math.abs(a.x - b.x) + Math.abs(a.y - b.y);
}

function objectTiles(object) {
  const width = object.width || 1;
  const height = object.height || 1;
  const tiles = [];

  if (object.door) {
    tiles.push(object.door);
  }

  for (let y = object.y; y < object.y + height; y += 1) {
    for (let x = object.x; x < object.x + width; x += 1) {
      tiles.push({ x, y });
    }
  }

  return tiles;
}

export function findNearbyInteractable(world, player) {
  const standing = getInteractionObjectAtTile(world, player.x, player.y);
  if (standing?.interaction && !standing.blocksMovement) {
    return standing;
  }

  return world.objects.find((object) => {
    if (!object.interaction) return false;
    return objectTiles(object).some((tile) => distance(player, tile) <= 1);
  });
}

export function interactionText(object) {
  if (!object?.interaction) return "";
  const prefix = TYPE_PREFIX[object.interaction.type] || "Interaction";
  return `${prefix}: ${object.name} - ${object.interaction.text}`;
}

export function applyInteraction(player, object) {
  if (!object) return null;

  if ((object.type === "marker" || object.type === "portal" || object.type === "gate") && object.id) {
    const visited = new Set(player.visitedMarkers || []);
    visited.add(object.id);
    player.visitedMarkers = [...visited];
  }

  return interactionText(object);
}

export function showInteraction(messageBox, message) {
  if (!messageBox) return;
  messageBox.textContent = message || "Nothing nearby is ready to respond yet.";
}
