import { drawLayeredAvatar } from "./avatar.js";

const TERRAIN_STYLES = {
  grass: { fill: "#37a35d", accent: "#8ee489", shadow: "#1e6c43" },
  path: { fill: "#c09a5a", accent: "#ffd990", shadow: "#7a5a35" },
  plain: { fill: "#6dc978", accent: "#b8f38d", shadow: "#438a55" },
  water: { fill: "#1e78c8", accent: "#65d5ff", shadow: "#0e4c8a" },
  wall: { fill: "#293858", accent: "#6f82b7", shadow: "#151d34" },
  tree: { fill: "#20864d", accent: "#65cf72", shadow: "#114b32" },
  rock: { fill: "#71809a", accent: "#c7d1e6", shadow: "#3e4a60" },
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

function tileNoise(x, y) {
  const value = Math.sin((x * 127.1) + (y * 311.7)) * 43758.5453;
  return value - Math.floor(value);
}

function tileToScreen(camera, world, tile) {
  return {
    x: (tile.x * world.tileSize) - camera.x,
    y: (tile.y * world.tileSize) - camera.y,
  };
}

function drawTerrainTile(ctx, x, y, size, terrainType, tileX = 0, tileY = 0, frameTime = 0) {
  const style = TERRAIN_STYLES[terrainType] || TERRAIN_STYLES.grass;
  const inset = 2;

  ctx.fillStyle = "rgba(0, 0, 0, .16)";
  ctx.fillRect(x + 2, y + 4, size - 1, size - 1);

  ctx.fillStyle = style.fill;
  ctx.fillRect(x, y, size, size);
  ctx.fillStyle = style.shadow;
  ctx.globalAlpha = .28;
  ctx.fillRect(x, y + size - 7, size, 7);
  ctx.globalAlpha = 1;

  ctx.fillStyle = style.accent;
  ctx.globalAlpha = .22;
  ctx.fillRect(x + inset + 1, y + inset + 1, size - ((inset + 1) * 2), size - ((inset + 1) * 2));
  ctx.globalAlpha = 1;

  ctx.fillStyle = "rgba(255, 255, 255, .16)";
  ctx.fillRect(x + 3, y + 3, size - 6, 2);

  const noise = tileNoise(tileX, tileY);
  if (terrainType === "grass" || terrainType === "plain") {
    ctx.strokeStyle = noise > .5 ? "rgba(255, 255, 255, .18)" : "rgba(13, 75, 44, .34)";
    ctx.lineWidth = 1;
    for (let i = 0; i < 2; i += 1) {
      const bladeX = x + 8 + ((tileX * 7 + tileY * 11 + i * 9) % (size - 14));
      const bladeY = y + 11 + ((tileY * 5 + i * 8) % (size - 18));
      ctx.beginPath();
      ctx.moveTo(bladeX, bladeY + 5);
      ctx.lineTo(bladeX + 3, bladeY);
      ctx.stroke();
    }
  } else if (terrainType === "path") {
    ctx.fillStyle = "rgba(255, 247, 214, .22)";
    ctx.fillRect(x + 8 + ((tileX * 3) % 8), y + 9, 7, 3);
    ctx.fillRect(x + 17, y + 20 + ((tileY * 5) % 5), 8, 3);
  } else if (terrainType === "water") {
    const shimmer = Math.sin((frameTime / 380) + tileX + tileY) * 2;
    ctx.strokeStyle = "rgba(215, 248, 255, .42)";
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(x + 5, y + 11 + shimmer);
    ctx.lineTo(x + 14, y + 9 - shimmer);
    ctx.lineTo(x + 24, y + 12 + shimmer);
    ctx.stroke();
  } else if (terrainType === "tree") {
    ctx.fillStyle = "rgba(255, 255, 255, .2)";
    ctx.beginPath();
    ctx.arc(x + 10, y + 10, 4, 0, Math.PI * 2);
    ctx.fill();
  } else if (terrainType === "rock") {
    ctx.fillStyle = "rgba(255, 255, 255, .18)";
    ctx.fillRect(x + 9, y + 8, 9, 3);
    ctx.fillStyle = "rgba(0, 0, 0, .18)";
    ctx.fillRect(x + 12, y + 19, 10, 4);
  }
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

  ctx.fillStyle = "rgba(0, 0, 0, .24)";
  ctx.fillRect(screen.x + 7, screen.y + 10, width - 5, height - 2);

  ctx.fillStyle = OBJECT_STYLES.building;
  ctx.fillRect(screen.x + 2, screen.y + 2, width - 4, height - 4);
  ctx.fillStyle = "rgba(255, 209, 102, .5)";
  ctx.fillRect(screen.x + 8, screen.y + 8, width - 16, 7);
  ctx.fillStyle = "rgba(7, 16, 36, .22)";
  ctx.fillRect(screen.x + 8, screen.y + height - 16, width - 16, 8);
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

  ctx.fillStyle = "rgba(76, 201, 255, .42)";
  for (let wx = screen.x + 14; wx < screen.x + width - 14; wx += 24) {
    ctx.fillRect(wx, screen.y + 23, 8, 10);
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

  ctx.save();
  if (object.type === "portal" || object.type === "gate" || object.type === "resource") {
    ctx.shadowColor = color;
    ctx.shadowBlur = 18;
  }

  ctx.fillStyle = "rgba(0, 0, 0, .22)";
  ctx.beginPath();
  ctx.ellipse(centerX, centerY + 11, 13, 5, 0, 0, Math.PI * 2);
  ctx.fill();

  ctx.fillStyle = color;
  ctx.strokeStyle = "rgba(5, 9, 20, .65)";
  ctx.lineWidth = 3;

  if (object.type === "npc") {
    ctx.fillStyle = "#071024";
    ctx.beginPath();
    ctx.arc(centerX, centerY - 3, 11, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(centerX, centerY - 3, 9, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    ctx.fillStyle = "#4cc9ff";
    ctx.fillRect(centerX - 8, centerY + 5, 16, 12);
    ctx.fillStyle = "#071024";
    ctx.fillRect(centerX - 4, centerY - 5, 2, 3);
    ctx.fillRect(centerX + 3, centerY - 5, 2, 3);
  } else if (object.type === "resource") {
    ctx.fillStyle = "rgba(118, 240, 170, .28)";
    ctx.beginPath();
    ctx.arc(centerX, centerY, 16, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.moveTo(centerX, screen.y + 5);
    ctx.lineTo(screen.x + size - 5, centerY + 8);
    ctx.lineTo(screen.x + 6, centerY + 10);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();
  } else if (object.type === "portal") {
    ctx.fillStyle = "rgba(76, 201, 255, .24)";
    ctx.beginPath();
    ctx.ellipse(centerX, centerY, 16, 21, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.ellipse(centerX, centerY, 10, 15, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    ctx.strokeStyle = "rgba(255, 255, 255, .72)";
    ctx.stroke();
  } else if (object.type === "gate") {
    ctx.fillStyle = "rgba(255, 77, 109, .24)";
    ctx.fillRect(screen.x + 3, screen.y + 3, size - 6, size - 6);
    ctx.fillStyle = color;
    ctx.fillRect(screen.x + 7, screen.y + 7, size - 14, size - 14);
    ctx.strokeRect(screen.x + 7, screen.y + 7, size - 14, size - 14);
    ctx.strokeStyle = "rgba(255, 255, 255, .72)";
    ctx.beginPath();
    ctx.moveTo(screen.x + 10, screen.y + 10);
    ctx.lineTo(screen.x + size - 10, screen.y + size - 10);
    ctx.moveTo(screen.x + size - 10, screen.y + 10);
    ctx.lineTo(screen.x + 10, screen.y + size - 10);
    ctx.stroke();
  } else {
    ctx.beginPath();
    ctx.arc(centerX, centerY, 10, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
  }

  ctx.restore();
  drawLabel(ctx, object.name, centerX, screen.y + 1);
}

function drawPlayer(ctx, camera, world, player, renderState = {}) {
  const size = world.tileSize;
  const screen = tileToScreen(camera, world, player);
  const centerX = screen.x + (size / 2);
  const centerY = screen.y + (size / 2);

  ctx.fillStyle = "rgba(98, 217, 255, .18)";
  ctx.beginPath();
  ctx.ellipse(centerX, centerY + 11, 18, 7, 0, 0, Math.PI * 2);
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
  ctx.strokeStyle = "rgba(255, 255, 255, .36)";
  ctx.beginPath();
  ctx.moveTo(screen.x + (size / 2), screen.y + 8);
  ctx.lineTo(screen.x + size - 8, screen.y + (size / 2));
  ctx.lineTo(screen.x + (size / 2), screen.y + size - 8);
  ctx.lineTo(screen.x + 8, screen.y + (size / 2));
  ctx.closePath();
  ctx.stroke();
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
      drawTerrainTile(ctx, screenX, screenY, size, world.terrain[y]?.[x], x, y, renderState.frameTime || 0);
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
