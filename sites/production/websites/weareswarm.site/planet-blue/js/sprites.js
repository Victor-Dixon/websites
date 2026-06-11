/* Planet Blue — overworld pixel sprites (canvas) */
(function (global) {
  "use strict";

  var TILE_COLORS = {
    grass: ["#2a4018", "#3a5a28", "#4a7038"],
    path: ["#4a3d28", "#5a4a32", "#6b5a3e"],
    water: ["#1a3848", "#2a4858", "#3a5868"],
    building: ["#2b2114", "#3d3220", "#4a3d28"],
    tree: ["#1a3010", "#2a4818", "#3a6020"],
    sand: ["#5a4a28", "#6b5a32", "#7a6a3a"]
  };

  var ZONE_TINT = {
    safe: "rgba(136, 204, 68, 0.12)",
    contested: "rgba(201, 162, 39, 0.14)",
    overrun: "rgba(170, 68, 68, 0.18)"
  };

  function hashNoise(x, y) {
    return ((x * 374761393) ^ (y * 668265263)) >>> 0;
  }

  function drawTile(ctx, tile, x, y, size, zoneStatus) {
    var colors = TILE_COLORS[tile] || TILE_COLORS.grass;
    var n = hashNoise(x, y) % colors.length;
    ctx.fillStyle = colors[n];
    ctx.fillRect(x * size, y * size, size, size);

    if (tile === "grass" || tile === "sand") {
      ctx.fillStyle = colors[(n + 1) % colors.length];
      if (hashNoise(x + 3, y + 7) % 3 === 0) {
        ctx.fillRect(x * size + 4, y * size + 4, 3, 3);
      }
    }

    if (tile === "water") {
      ctx.fillStyle = "rgba(120, 180, 220, 0.25)";
      ctx.fillRect(x * size + 2, y * size + 6, size - 4, 2);
    }

    if (tile === "tree") {
      ctx.fillStyle = "#1a3010";
      ctx.fillRect(x * size + 10, y * size + 18, 12, 10);
      ctx.fillStyle = "#3a6020";
      ctx.fillRect(x * size + 6, y * size + 4, 20, 16);
    }

    if (tile === "building") {
      ctx.fillStyle = "#1a1208";
      ctx.fillRect(x * size + 4, y * size + 14, size - 8, 14);
      ctx.fillStyle = "#c9a227";
      ctx.fillRect(x * size + 10, y * size + 20, 6, 8);
      ctx.fillRect(x * size + 20, y * size + 20, 6, 8);
    }

    if (zoneStatus && (tile === "grass" || tile === "sand" || tile === "path")) {
      ctx.fillStyle = ZONE_TINT[zoneStatus] || ZONE_TINT.contested;
      ctx.fillRect(x * size, y * size, size, size);
    }
  }

  function drawPlayer(ctx, px, py, size, facing, raceColor) {
    var x = px * size;
    var y = py * size;
    var pad = 6;

    ctx.fillStyle = raceColor || "#3366cc";
    ctx.fillRect(x + pad, y + pad + 4, size - pad * 2, size - pad * 2 - 4);

    ctx.fillStyle = "#ffcc99";
    ctx.fillRect(x + pad + 4, y + pad, size - pad * 2 - 8, 10);

    ctx.fillStyle = "#1a1408";
    if (facing === "left") {
      ctx.fillRect(x + pad + 4, y + pad + 2, 3, 3);
      ctx.fillRect(x + pad + 10, y + pad + 2, 3, 3);
    } else if (facing === "right") {
      ctx.fillRect(x + pad + 6, y + pad + 2, 3, 3);
      ctx.fillRect(x + pad + 12, y + pad + 2, 3, 3);
    } else {
      ctx.fillRect(x + pad + 5, y + pad + 2, 3, 3);
      ctx.fillRect(x + pad + 11, y + pad + 2, 3, 3);
    }

    ctx.fillStyle = "#ffffcc";
    ctx.fillRect(x + pad + 8, y + pad + 8, 4, 2);
  }

  function drawNpc(ctx, px, py, size, color, label) {
    var x = px * size;
    var y = py * size;
    var pad = 7;

    ctx.fillStyle = color || "#88cc44";
    ctx.fillRect(x + pad, y + pad + 2, size - pad * 2, size - pad * 2 - 2);
    ctx.fillStyle = "#ffcc99";
    ctx.fillRect(x + pad + 3, y + pad, size - pad * 2 - 6, 8);

    if (label) {
      ctx.font = "bold 8px Courier New, monospace";
      ctx.fillStyle = "#ffffcc";
      ctx.textAlign = "center";
      ctx.fillText(label, x + size / 2, y - 2);
    }
  }

  function drawMarker(ctx, px, py, size, kind, pulse) {
    var cx = px * size + size / 2;
    var cy = py * size + size / 2;
    var r = pulse ? 10 + Math.sin(pulse) * 2 : 10;

    if (kind === "mission") {
      ctx.strokeStyle = "#ffd966";
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.arc(cx, cy, r, 0, Math.PI * 2);
      ctx.stroke();
      ctx.fillStyle = "rgba(201, 162, 39, 0.35)";
      ctx.fill();
      ctx.fillStyle = "#ffd966";
      ctx.font = "bold 10px Georgia, serif";
      ctx.textAlign = "center";
      ctx.fillText("!", cx, cy + 4);
    } else if (kind === "hub") {
      ctx.fillStyle = "#c9a227";
      ctx.fillRect(cx - 6, cy - 10, 12, 14);
      ctx.fillStyle = "#3d3220";
      ctx.fillRect(cx - 3, cy - 4, 6, 8);
    } else if (kind === "locked") {
      ctx.fillStyle = "rgba(80, 80, 80, 0.5)";
      ctx.beginPath();
      ctx.arc(cx, cy, r, 0, Math.PI * 2);
      ctx.fill();
      ctx.fillStyle = "#888";
      ctx.font = "bold 10px Georgia, serif";
      ctx.textAlign = "center";
      ctx.fillText("?", cx, cy + 4);
    }
  }

  function raceColor(raceId) {
    var map = {
      human: "#3366cc",
      robot: "#778899",
      celestial: "#aa88ff",
      infernal: "#cc4422",
      beastborn: "#44aa44",
      beastmen: "#66aa33",
      stoneborn: "#888866",
      shadowborn: "#554466",
      aquatic: "#2288aa",
      spirit: "#88aacc",
      ancient: "#aa8844"
    };
    return map[raceId] || "#3366cc";
  }

  global.PLANET_BLUE_SPRITES = {
    TILE_COLORS: TILE_COLORS,
    drawTile: drawTile,
    drawPlayer: drawPlayer,
    drawNpc: drawNpc,
    drawMarker: drawMarker,
    raceColor: raceColor
  };
})(typeof window !== "undefined" ? window : global);
