/* Planet Blue — blocky Roblox-inspired overworld sprites (canvas) */
(function (global) {
  "use strict";

  var TILE_PALETTE = {
    grass: { top: "#5BC236", side: "#3D9A1F", stud: "#7DD956" },
    path: { top: "#D4A76A", side: "#A67C3D", stud: "#E8C090" },
    water: { top: "#00A2FF", side: "#0066CC", stud: "#4DB8FF" },
    building: { top: "#B2BEC3", side: "#636E72", stud: "#DFE6E9" },
    tree: { top: "#27AE60", side: "#1E8449", stud: "#58D68D" },
    sand: { top: "#F9CA24", side: "#D4AC0D", stud: "#FDE68A" }
  };

  var ZONE_TINT = {
    safe: "rgba(0, 176, 111, 0.18)",
    contested: "rgba(255, 170, 0, 0.2)",
    overrun: "rgba(231, 76, 60, 0.22)"
  };

  function hashNoise(x, y) {
    return ((x * 374761393) ^ (y * 668265263)) >>> 0;
  }

  function drawBloxFace(ctx, x, y, w, h, topColor, sideColor) {
    ctx.fillStyle = sideColor;
    ctx.fillRect(x + 2, y + 2, w, h);
    ctx.fillStyle = topColor;
    ctx.fillRect(x, y, w, h - 2);
    ctx.fillStyle = "rgba(255,255,255,0.35)";
    ctx.fillRect(x, y, w, 2);
    ctx.fillStyle = "rgba(0,0,0,0.12)";
    ctx.fillRect(x, y + h - 4, w, 2);
  }

  function drawStud(ctx, cx, cy, color) {
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(cx, cy, 3, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = "rgba(255,255,255,0.5)";
    ctx.beginPath();
    ctx.arc(cx - 1, cy - 1, 1.2, 0, Math.PI * 2);
    ctx.fill();
  }

  function drawTile(ctx, tile, x, y, size, zoneStatus) {
    var pal = TILE_PALETTE[tile] || TILE_PALETTE.grass;
    var px = x * size;
    var py = y * size;
    var blockH = tile === "water" ? 2 : tile === "building" ? 6 : tile === "tree" ? 5 : 3;

    ctx.fillStyle = pal.side;
    ctx.fillRect(px + blockH, py + blockH, size - blockH, size - blockH);

    drawBloxFace(ctx, px, py, size - blockH, size - blockH, pal.top, pal.side);

    if (hashNoise(x, y) % 4 === 0) {
      drawStud(ctx, px + size * 0.35, py + size * 0.35, pal.stud);
    }

    if (tile === "grass" && hashNoise(x + 5, y + 3) % 5 === 0) {
      ctx.fillStyle = "#58D68D";
      ctx.fillRect(px + 8, py + 6, 4, 4);
      ctx.fillRect(px + 18, py + 14, 3, 3);
    }

    if (tile === "water") {
      ctx.fillStyle = "rgba(255,255,255,0.3)";
      ctx.fillRect(px + 4, py + 8, size - 10, 3);
      ctx.fillRect(px + 10, py + 18, size - 16, 2);
    }

    if (tile === "tree") {
      drawBloxFace(ctx, px + 10, py + 4, 12, 12, "#58D68D", "#27AE60");
      drawBloxFace(ctx, px + 12, py + 16, 8, 10, "#8B6914", "#6B4F10");
    }

    if (tile === "building") {
      drawBloxFace(ctx, px + 4, py + 2, size - 8, 18, "#DFE6E9", "#B2BEC3");
      ctx.fillStyle = "#74B9FF";
      ctx.fillRect(px + 10, py + 10, 6, 6);
      ctx.fillRect(px + 20, py + 10, 6, 6);
      ctx.fillStyle = "#FDCB6E";
      ctx.fillRect(px + 14, py + 20, 8, 6);
    }

    if (tile === "sand" && hashNoise(x + 2, y + 1) % 3 === 0) {
      drawStud(ctx, px + size * 0.65, py + size * 0.55, "#FDE68A");
    }

    if (zoneStatus && (tile === "grass" || tile === "sand" || tile === "path")) {
      ctx.fillStyle = ZONE_TINT[zoneStatus] || ZONE_TINT.contested;
      ctx.fillRect(px, py, size, size);
    }
  }

  function drawSky(ctx, width, height) {
    var grad = ctx.createLinearGradient(0, 0, 0, height);
    grad.addColorStop(0, "#6CB4EE");
    grad.addColorStop(0.55, "#B5E3FF");
    grad.addColorStop(1, "#87CEEB");
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, width, height);
  }

  function drawBloxHumanoid(ctx, px, py, size, facing, shirtColor, pantsColor, skinColor) {
    var x = px * size;
    var y = py * size;
    var cx = x + size / 2;
    var headW = 12;
    var headH = 10;
    var torsoW = 14;
    var torsoH = 10;
    var legW = 6;
    var legH = 8;
    var skin = skinColor || "#F5CBA7";
    var shirt = shirtColor || "#00A2FF";
    var pants = pantsColor || "#1B2A35";

    var headX = cx - headW / 2;
    var headY = y + 2;
    drawBloxFace(ctx, headX, headY, headW, headH, skin, shade(skin, -30));

    ctx.fillStyle = "#2D3436";
    var eyeY = headY + 4;
    if (facing === "left") {
      ctx.fillRect(headX + 2, eyeY, 2, 2);
      ctx.fillRect(headX + 6, eyeY, 2, 2);
    } else if (facing === "right") {
      ctx.fillRect(headX + 4, eyeY, 2, 2);
      ctx.fillRect(headX + 8, eyeY, 2, 2);
    } else {
      ctx.fillRect(headX + 3, eyeY, 2, 2);
      ctx.fillRect(headX + 7, eyeY, 2, 2);
    }
    ctx.fillRect(headX + 4, headY + 7, 4, 1);

    var torsoX = cx - torsoW / 2;
    var torsoY = headY + headH;
    drawBloxFace(ctx, torsoX, torsoY, torsoW, torsoH, shirt, shade(shirt, -35));

    var legY = torsoY + torsoH;
    drawBloxFace(ctx, torsoX + 1, legY, legW, legH, pants, shade(pants, -25));
    drawBloxFace(ctx, torsoX + torsoW - legW - 1, legY, legW, legH, pants, shade(pants, -25));
  }

  function shade(hex, amount) {
    var n = parseInt(hex.replace("#", ""), 16);
    var r = Math.max(0, Math.min(255, ((n >> 16) & 255) + amount));
    var g = Math.max(0, Math.min(255, ((n >> 8) & 255) + amount));
    var b = Math.max(0, Math.min(255, (n & 255) + amount));
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
  }

  function drawPlayer(ctx, px, py, size, facing, shirtColor, pantsColor) {
    var shirt = shirtColor || "#00A2FF";
    var pants = pantsColor || shade(shirt, -60);
    drawBloxHumanoid(ctx, px, py, size, facing, shirt, pants, "#F5CBA7");
  }

  function drawNpc(ctx, px, py, size, color, label, opts) {
    opts = opts || {};
    var shirt = opts.shirt || color || "#00B06F";
    var pants = opts.pants || "#1B2A35";
    drawBloxHumanoid(ctx, px, py, size, "down", shirt, pants, "#F5CBA7");

    if (label) {
      ctx.font = "bold 9px Nunito, sans-serif";
      ctx.fillStyle = "#2D3436";
      ctx.strokeStyle = "#FFFFFF";
      ctx.lineWidth = 3;
      ctx.textAlign = "center";
      ctx.strokeText(label, px * size + size / 2, py * size - 3);
      ctx.fillText(label, px * size + size / 2, py * size - 3);
    }
  }

  function drawMarker(ctx, px, py, size, kind, pulse) {
    var cx = px * size + size / 2;
    var cy = py * size + size / 2;
    var r = pulse ? 11 + Math.sin(pulse) * 2 : 11;

    if (kind === "mission") {
      ctx.fillStyle = "#FFAA00";
      ctx.beginPath();
      ctx.arc(cx, cy, r, 0, Math.PI * 2);
      ctx.fill();
      ctx.strokeStyle = "#2D3436";
      ctx.lineWidth = 2;
      ctx.stroke();
      ctx.fillStyle = "#FFFFFF";
      ctx.font = "bold 12px Nunito, sans-serif";
      ctx.textAlign = "center";
      ctx.fillText("!", cx, cy + 4);
    } else if (kind === "hub") {
      drawBloxFace(ctx, cx - 8, cy - 14, 16, 16, "#FF6B6B", "#CC4444");
      ctx.fillStyle = "#FFFFFF";
      ctx.fillRect(cx - 4, cy - 8, 8, 6);
      ctx.fillStyle = "#74B9FF";
      ctx.fillRect(cx - 3, cy - 7, 2, 4);
      ctx.fillRect(cx + 1, cy - 7, 2, 4);
    } else if (kind === "locked") {
      ctx.fillStyle = "rgba(178, 190, 195, 0.7)";
      ctx.beginPath();
      ctx.arc(cx, cy, r, 0, Math.PI * 2);
      ctx.fill();
      ctx.strokeStyle = "#636E72";
      ctx.lineWidth = 2;
      ctx.stroke();
      ctx.fillStyle = "#636E72";
      ctx.font = "bold 11px Nunito, sans-serif";
      ctx.textAlign = "center";
      ctx.fillText("?", cx, cy + 4);
    }
  }

  function raceColor(raceId) {
    var map = {
      human: "#00A2FF",
      robot: "#B2BEC3",
      celestial: "#A29BFE",
      infernal: "#FF6B6B",
      beastborn: "#00B06F",
      beastmen: "#55E6C1",
      stoneborn: "#BDC3C7",
      shadowborn: "#6C5CE7",
      aquatic: "#0984E3",
      spirit: "#81ECEC",
      ancient: "#FDCB6E"
    };
    return map[raceId] || "#00A2FF";
  }

  function racePants(raceId) {
    var map = {
      human: "#1B2A35",
      robot: "#2D3436",
      celestial: "#4834D4",
      infernal: "#2C0A0A",
      beastborn: "#145A32",
      beastmen: "#0B5345",
      stoneborn: "#4A4A4A",
      shadowborn: "#2C1654",
      aquatic: "#0A3D62",
      spirit: "#1A5276",
      ancient: "#7D6608"
    };
    return map[raceId] || "#1B2A35";
  }

  global.PLANET_BLUE_SPRITES = {
    TILE_PALETTE: TILE_PALETTE,
    TILE_COLORS: TILE_PALETTE,
    drawTile: drawTile,
    drawSky: drawSky,
    drawPlayer: drawPlayer,
    drawNpc: drawNpc,
    drawMarker: drawMarker,
    raceColor: raceColor,
    racePants: racePants
  };
})(typeof window !== "undefined" ? window : global);
