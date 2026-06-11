/* Digital Dreamscape — canvas world renderer */
(function (global) {
  "use strict";

  var TILE = global.DD_TILE_TYPES;
  var TILE_SIZE = global.DD_WORLD_DATA.TILE_SIZE;

  function drawTile(ctx, type, screenX, screenY, pulseT) {
    var def = TILE[type] || TILE.grass;
    var px = screenX * TILE_SIZE;
    var py = screenY * TILE_SIZE;

    ctx.fillStyle = def.color;
    ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);

    if (type === "grass") {
      ctx.fillStyle = "rgba(255,255,255,0.06)";
      ctx.fillRect(px + 4, py + 6, 6, 4);
    } else if (type === "path") {
      ctx.strokeStyle = "rgba(0,0,0,0.08)";
      ctx.strokeRect(px + 0.5, py + 0.5, TILE_SIZE - 1, TILE_SIZE - 1);
    } else if (type === "water") {
      ctx.fillStyle = "rgba(255,255,255,0.15)";
      ctx.fillRect(px + 6, py + 8 + Math.sin(pulseT + screenX) * 2, 12, 3);
    } else if (type === "tree") {
      ctx.fillStyle = "#1a5c38";
      ctx.beginPath();
      ctx.arc(px + 16, py + 14, 11, 0, Math.PI * 2);
      ctx.fill();
      ctx.fillStyle = "#3d2817";
      ctx.fillRect(px + 14, py + 20, 4, 10);
    } else if (type === "rock") {
      ctx.fillStyle = "#6b6b75";
      ctx.beginPath();
      ctx.moveTo(px + 8, py + 24);
      ctx.lineTo(px + 14, py + 10);
      ctx.lineTo(px + 24, py + 12);
      ctx.lineTo(px + 26, py + 24);
      ctx.closePath();
      ctx.fill();
    } else if (type === "crystal") {
      ctx.fillStyle = "#00e5ff";
      ctx.globalAlpha = 0.7 + Math.sin(pulseT * 2 + screenX) * 0.2;
      ctx.beginPath();
      ctx.moveTo(px + 16, py + 6);
      ctx.lineTo(px + 24, py + 22);
      ctx.lineTo(px + 16, py + 28);
      ctx.lineTo(px + 8, py + 22);
      ctx.closePath();
      ctx.fill();
      ctx.globalAlpha = 1;
    } else if (type === "wall") {
      ctx.fillStyle = "#1e3550";
      ctx.fillRect(px + 2, py + 2, TILE_SIZE - 4, TILE_SIZE - 4);
    } else if (type === "floor") {
      ctx.strokeStyle = "rgba(100,140,180,0.3)";
      ctx.strokeRect(px + 4, py + 4, TILE_SIZE - 8, TILE_SIZE - 8);
    } else if (type === "portal") {
      ctx.fillStyle = "#9b5de5";
      ctx.beginPath();
      ctx.arc(px + 16, py + 16, 12, 0, Math.PI * 2);
      ctx.fill();
      ctx.strokeStyle = "#00e5ff";
      ctx.lineWidth = 2;
      ctx.globalAlpha = 0.5 + Math.sin(pulseT * 3) * 0.3;
      ctx.stroke();
      ctx.globalAlpha = 1;
      ctx.lineWidth = 1;
    }
  }

  function drawLabel(ctx, text, wx, wy, camera) {
    var sx = (wx - camera.x) * TILE_SIZE;
    var sy = (wy - camera.y) * TILE_SIZE - 6;
    ctx.font = "bold 10px Nunito, sans-serif";
    ctx.textAlign = "center";
    ctx.fillStyle = "rgba(10,20,40,0.75)";
    ctx.fillText(text, sx + TILE_SIZE / 2 + 1, sy + 1);
    ctx.fillStyle = "#e8f4ff";
    ctx.fillText(text, sx + TILE_SIZE / 2, sy);
  }

  function drawNpc(ctx, obj, camera, pulseT) {
    var sx = (obj.x - camera.x) * TILE_SIZE;
    var sy = (obj.y - camera.y) * TILE_SIZE;
    ctx.fillStyle = "#4cc9f0";
    ctx.beginPath();
    ctx.arc(sx + 16, sy + 18, 10, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = "#1a3a5c";
    ctx.fillRect(sx + 10, sy + 22, 12, 10);
    drawLabel(ctx, obj.name, obj.x, obj.y, camera);
  }

  function drawResource(ctx, obj, camera, pulseT) {
    var sx = (obj.x - camera.x) * TILE_SIZE;
    var sy = (obj.y - camera.y) * TILE_SIZE;
    var colors = {
      dream_crystal: "#00e5ff",
      blue_flower: "#4a9eff",
      memory_stone: "#8a8a94",
      glow_tree: "#5ce08a"
    };
    ctx.fillStyle = colors[obj.subtype] || "#00e5ff";
    ctx.globalAlpha = 0.8 + Math.sin(pulseT + obj.x) * 0.15;
    ctx.beginPath();
    ctx.arc(sx + 16, sy + 20, 7, 0, Math.PI * 2);
    ctx.fill();
    ctx.globalAlpha = 1;
  }

  function drawBuildingLabel(ctx, obj, camera) {
    drawLabel(ctx, obj.name, obj.x + Math.floor(obj.width / 2), obj.y - 0.5, camera);
  }

  function drawPlayer(ctx, player, camera, pulseT) {
    var tileSize = TILE_SIZE;
    if (global.DD_PLAYER_SPRITE && global.DD_PLAYER_SPRITE.draw(ctx, player, camera, tileSize)) {
      return;
    }
    // Fallback placeholder if sprite not loaded yet
    var sx = (player.renderX - camera.x) * tileSize;
    var sy = (player.renderY - camera.y) * tileSize;
    ctx.fillStyle = "rgba(0,0,0,0.2)";
    ctx.beginPath();
    ctx.ellipse(sx + 16, sy + 28, 9, 4, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = "#2ec4b6";
    ctx.beginPath();
    ctx.arc(sx + 16, sy + 16, 11, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = "#0a2540";
    var fx = 0;
    var fy = 0;
    if (player.facing === "left") fx = -4;
    if (player.facing === "right") fx = 4;
    if (player.facing === "up") fy = -4;
    if (player.facing === "down") fy = 4;
    ctx.beginPath();
    ctx.arc(sx + 16 + fx, sy + 14 + fy, 3, 0, Math.PI * 2);
    ctx.fill();
  }

  function drawPathPreview(ctx, path, player, camera) {
    if (!path || !path.length) return;
    ctx.fillStyle = "rgba(0, 229, 255, 0.35)";
    var i;
    for (i = 0; i < path.length; i++) {
      var pt = path[i];
      var sx = (pt.x - camera.x) * TILE_SIZE + 10;
      var sy = (pt.y - camera.y) * TILE_SIZE + 10;
      ctx.fillRect(sx, sy, 12, 12);
    }
  }

  function render(ctx, world, grid, camera, player, objects, pathPreview, pulseT) {
    var vx, vy;
    var wx, wy;
    var viewCols = camera.viewCols;
    var viewRows = camera.viewRows;

    ctx.clearRect(0, 0, viewCols * TILE_SIZE, viewRows * TILE_SIZE);

    var grad = ctx.createLinearGradient(0, 0, 0, viewRows * TILE_SIZE);
    grad.addColorStop(0, "#87ceeb");
    grad.addColorStop(1, "#c8e6f5");
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, viewCols * TILE_SIZE, viewRows * TILE_SIZE);

    for (vy = 0; vy < viewRows; vy++) {
      for (vx = 0; vx < viewCols; vx++) {
        wx = camera.x + vx;
        wy = camera.y + vy;
        if (wx >= world.width || wy >= world.height) continue;
        var tile = grid[wy][wx];
        if (tile) drawTile(ctx, tile.type, vx, vy, pulseT);
      }
    }

    drawPathPreview(ctx, pathPreview, player, camera);

    objects.forEach(function (obj) {
      if (obj.x < camera.x - 1 || obj.y < camera.y - 1) return;
      if (obj.x > camera.x + viewCols || obj.y > camera.y + viewRows) return;
      if (obj.type === "building") drawBuildingLabel(ctx, obj, camera);
      else if (obj.type === "npc") drawNpc(ctx, obj, camera, pulseT);
      else if (obj.type === "resource") drawResource(ctx, obj, camera, pulseT);
      else if (obj.type === "portal" || obj.type === "marker") drawLabel(ctx, obj.name, obj.x, obj.y, camera);
    });

    drawPlayer(ctx, player, camera, pulseT);
  }

  global.DD_RENDERER = {
    render: render,
    drawTile: drawTile,
    drawPlayer: drawPlayer
  };
})(typeof window !== "undefined" ? window : global);
