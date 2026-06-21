import { drawLayeredAvatar } from "./avatar.js";

const TERRAIN_STYLES = {
  grass: { fill: "#2f8d52", accent: "#3cae64" },
  path: { fill: "#9a8257", accent: "#b79a68" },
  plain: { fill: "#5b9f69", accent: "#78ba7c" },
  water: { fill: "#175d9e", accent: "#2180cb" },
  wall: { fill: "#20293f", accent: "#3b4663" },
  tree: { fill: "#174f35", accent: "#2a7a4c" },
  rock: { fill: "#4d5870", accent: "#6d7891" },
};

const OBJECT_STYLES = {
  npc: "#ffd166",
  building: "#5d6f95",
  resource: "#76f0aa",
  portal: "#62d9ff",
  marker: "#d9e5f5",
  gate: "#ff7a8a",
};

const TACTICAL_TILE_STYLES = {
  movement: {
    fill: "rgba(76, 201, 255, .22)",
    stroke: "rgba(76, 201, 255, .72)",
    glow: "rgba(76, 201, 255, .28)",
  },
  danger: {
    fill: "rgba(255, 77, 109, .2)",
    stroke: "rgba(255, 77, 109, .78)",
    glow: "rgba(255, 77, 109, .24)",
  },
  objective: {
    fill: "rgba(255, 209, 102, .24)",
    stroke: "rgba(255, 209, 102, .82)",
    glow: "rgba(255, 209, 102, .28)",
  },
};

function tileToScreen(camera, world, tile) {
  return {
    x: (tile.x * world.tileSize) - camera.x,
    y: (tile.y * world.tileSize) - camera.y,
  };
}

function drawTerrainTile(ctx, x, y, size, terrainType) {
  const style = TERRAIN_STYLES[terrainType] || TERRAIN_STYLES.grass;
  ctx.fillStyle = style.fill;
  ctx.fillRect(x, y, size, size);
  ctx.fillStyle = style.accent;
  ctx.globalAlpha = .18;
  ctx.fillRect(x + 3, y + 3, size - 6, size - 6);
  ctx.globalAlpha = 1;

  ctx.fillStyle = "rgba(255, 255, 255, .08)";
  ctx.fillRect(x + 3, y + 3, size - 6, 2);
  ctx.fillStyle = "rgba(5, 9, 20, .16)";
  ctx.fillRect(x + 3, y + size - 5, size - 6, 2);
}

function drawLabel(ctx, text, x, y) {
  ctx.font = "12px ui-sans-serif, system-ui, sans-serif";
  const metrics = ctx.measureText(text);
  const width = metrics.width + 12;
  ctx.fillStyle = "rgba(5, 9, 20, .82)";
  ctx.fillRect(x - (width / 2), y - 24, width, 18);
  ctx.fillStyle = "#f5f8ff";
  ctx.textAlign = "center";
  ctx.fillText(text, x, y - 10);
}

function drawBuilding(ctx, camera, world, object) {
  const size = world.tileSize;
  const screen = tileToScreen(camera, world, object);
  const width = object.width * size;
  const height = object.height * size;

  ctx.fillStyle = OBJECT_STYLES.building;
  ctx.fillRect(screen.x + 2, screen.y + 2, width - 4, height - 4);
  ctx.strokeStyle = "rgba(255, 255, 255, .28)";
  ctx.lineWidth = 2;
  ctx.strokeRect(screen.x + 3, screen.y + 3, width - 6, height - 6);

  if (object.door) {
    const door = tileToScreen(camera, world, object.door);
    ctx.fillStyle = "#171f35";
    ctx.fillRect(door.x + 8, door.y + 8, size - 16, size - 6);
    ctx.fillStyle = "#62d9ff";
    ctx.fillRect(door.x + 13, door.y + 13, 6, 6);
  }

  drawLabel(ctx, object.name, screen.x + (width / 2), screen.y + 2);
}

function drawObject(ctx, camera, world, object) {
  if (object.type === "building") {
    drawBuilding(ctx, camera, world, object);
    return;
  }

  const size = world.tileSize;
  const screen = tileToScreen(camera, world, object);
  const centerX = screen.x + (size / 2);
  const centerY = screen.y + (size / 2);
  const color = OBJECT_STYLES[object.type] || OBJECT_STYLES.marker;

  ctx.fillStyle = color;
  ctx.strokeStyle = "rgba(5, 9, 20, .65)";
  ctx.lineWidth = 3;

  if (object.type === "npc") {
    ctx.beginPath();
    ctx.arc(centerX, centerY - 3, 9, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    ctx.fillRect(centerX - 7, centerY + 5, 14, 12);
  } else if (object.type === "resource") {
    ctx.beginPath();
    ctx.moveTo(centerX, screen.y + 5);
    ctx.lineTo(screen.x + size - 5, centerY + 8);
    ctx.lineTo(screen.x + 6, centerY + 10);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();
  } else if (object.type === "portal") {
    ctx.beginPath();
    ctx.ellipse(centerX, centerY, 10, 15, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    ctx.strokeStyle = "rgba(255, 255, 255, .72)";
    ctx.stroke();
  } else if (object.type === "gate") {
    ctx.fillRect(screen.x + 6, screen.y + 6, size - 12, size - 12);
    ctx.strokeRect(screen.x + 6, screen.y + 6, size - 12, size - 12);
  } else {
    ctx.beginPath();
    ctx.arc(centerX, centerY, 10, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
  }

  drawLabel(ctx, object.name, centerX, screen.y + 1);
}

function drawPlayer(ctx, camera, world, player, renderState = {}) {
  const size = world.tileSize;
  const screen = tileToScreen(camera, world, player);
  const centerX = screen.x + (size / 2);
  const centerY = screen.y + (size / 2);

  ctx.fillStyle = "rgba(98, 217, 255, .18)";
  ctx.beginPath();
  ctx.arc(centerX, centerY, 17, 0, Math.PI * 2);
  ctx.fill();

  drawLayeredAvatar(ctx, centerX, centerY, player, {
    frameTime: renderState.frameTime || 0,
    tileSize: size,
  });
}

function drawTacticalTile(ctx, camera, world, tile, style) {
  const size = world.tileSize;
  const screen = tileToScreen(camera, world, tile);
  if (
    screen.x > camera.width
    || screen.y > camera.height
    || screen.x + size < 0
    || screen.y + size < 0
  ) {
    return;
  }

  ctx.save();
  ctx.fillStyle = style.glow;
  ctx.fillRect(screen.x + 5, screen.y + 5, size - 10, size - 10);
  ctx.fillStyle = style.fill;
  ctx.fillRect(screen.x + 7, screen.y + 7, size - 14, size - 14);
  ctx.strokeStyle = style.stroke;
  ctx.lineWidth = 2;
  ctx.strokeRect(screen.x + 6, screen.y + 6, size - 12, size - 12);
  ctx.fillStyle = "rgba(255, 255, 255, .22)";
  ctx.fillRect(screen.x + 9, screen.y + 9, size - 18, 2);
  ctx.restore();
}

function drawTacticalOverlay(ctx, camera, world, tacticalState = {}) {
  // Anime tactical graphics pass: these overlays are data-driven by js/tactical-graphics.js.
  (tacticalState.movementTiles || []).forEach((tile) => {
    drawTacticalTile(ctx, camera, world, tile, TACTICAL_TILE_STYLES.movement);
  });

  (tacticalState.dangerTiles || []).forEach((tile) => {
    drawTacticalTile(ctx, camera, world, tile, TACTICAL_TILE_STYLES.danger);
  });

  (tacticalState.objectiveTiles || []).forEach((tile) => {
    drawTacticalTile(ctx, camera, world, tile, TACTICAL_TILE_STYLES.objective);
  });
}

function drawPath(ctx, camera, world, path, destination) {
  const size = world.tileSize;

  ctx.fillStyle = "rgba(98, 217, 255, .26)";
  path.forEach((tile) => {
    const screen = tileToScreen(camera, world, tile);
    ctx.fillRect(screen.x + 9, screen.y + 9, size - 18, size - 18);
  });

  if (destination) {
    const screen = tileToScreen(camera, world, destination);
    ctx.strokeStyle = "#62d9ff";
    ctx.lineWidth = 3;
    ctx.strokeRect(screen.x + 4, screen.y + 4, size - 8, size - 8);
  }
}

export function renderWorld(ctx, world, camera, player, renderState = {}) {
  const size = world.tileSize;
  const startX = Math.max(0, Math.floor(camera.x / size));
  const startY = Math.max(0, Math.floor(camera.y / size));
  const endX = Math.min(world.width - 1, Math.ceil((camera.x + camera.width) / size));
  const endY = Math.min(world.height - 1, Math.ceil((camera.y + camera.height) / size));

  ctx.clearRect(0, 0, camera.width, camera.height);
  ctx.fillStyle = "#050914";
  ctx.fillRect(0, 0, camera.width, camera.height);

  for (let y = startY; y <= endY; y += 1) {
    for (let x = startX; x <= endX; x += 1) {
      const screenX = (x * size) - camera.x;
      const screenY = (y * size) - camera.y;
      drawTerrainTile(ctx, screenX, screenY, size, world.terrain[y]?.[x]);
      ctx.strokeStyle = "rgba(76, 201, 255, .16)";
      ctx.lineWidth = 1;
      ctx.strokeRect(screenX, screenY, size, size);
    }
  }

  drawTacticalOverlay(ctx, camera, world, renderState.tactical);
  drawPath(ctx, camera, world, player.path, renderState.destination);

  world.objects
    .filter((object) => {
      const objectWidth = (object.width || 1) * size;
      const objectHeight = (object.height || 1) * size;
      const screen = tileToScreen(camera, world, object);
      return screen.x + objectWidth >= -64
        && screen.y + objectHeight >= -64
        && screen.x <= camera.width + 64
        && screen.y <= camera.height + 64;
    })
    .forEach((object) => drawObject(ctx, camera, world, object));

  drawPlayer(ctx, camera, world, player, renderState);

  ctx.fillStyle = "rgba(5, 9, 20, .76)";
  ctx.fillRect(12, 12, 172, 30);
  ctx.fillStyle = "#d9e5f5";
  ctx.font = "13px ui-sans-serif, system-ui, sans-serif";
  ctx.textAlign = "left";
  ctx.fillText(`${world.name} (${world.width}x${world.height})`, 22, 32);
}
