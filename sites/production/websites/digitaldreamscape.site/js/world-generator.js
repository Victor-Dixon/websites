/* Digital Dreamscape — seeded procedural world generator */
(function (global) {
  "use strict";

  var TILE = global.DD_TILE_TYPES;
  var DATA = global.DD_WORLD_DATA;

  function setTile(grid, x, y, type) {
    if (y < 0 || x < 0 || y >= grid.length || x >= grid[0].length) return;
    grid[y][x] = type;
  }

  function getTile(grid, x, y) {
    if (y < 0 || x < 0 || y >= grid.length || x >= grid[0].length) return "grass";
    return grid[y][x] || "grass";
  }

  function distSq(ax, ay, bx, by) {
    var dx = ax - bx;
    var dy = ay - by;
    return dx * dx + dy * dy;
  }

  function clearCircle(grid, cx, cy, radius, type) {
    var r2 = radius * radius;
    var y, x;
    for (y = cy - radius; y <= cy + radius; y++) {
      for (x = cx - radius; x <= cx + radius; x++) {
        if (distSq(x, y, cx, cy) <= r2) setTile(grid, x, y, type);
      }
    }
  }

  function carveHV(grid, x1, y1, x2, y2) {
    var x, y;
    var minX = Math.min(x1, x2);
    var maxX = Math.max(x1, x2);
    var minY = Math.min(y1, y2);
    var maxY = Math.max(y1, y2);
    for (x = minX; x <= maxX; x++) setTile(grid, x, y1, "path");
    for (y = minY; y <= maxY; y++) setTile(grid, x2, y, "path");
  }

  function carveVH(grid, x1, y1, x2, y2) {
    var x, y;
    var minX = Math.min(x1, x2);
    var maxX = Math.max(x1, x2);
    var minY = Math.min(y1, y2);
    var maxY = Math.max(y1, y2);
    for (y = minY; y <= maxY; y++) setTile(grid, x1, y, "path");
    for (x = minX; x <= maxX; x++) setTile(grid, x, y2, "path");
  }

  function carvePath(grid, x1, y1, x2, y2, rng) {
    if (rng() < 0.5) carveHV(grid, x1, y1, x2, y2);
    else carveVH(grid, x1, y1, x2, y2);
  }

  function canPlaceCluster(grid, cx, cy, radius) {
    var y, x;
    for (y = cy - radius; y <= cy + radius; y++) {
      for (x = cx - radius; x <= cx + radius; x++) {
        var t = getTile(grid, x, y);
        if (t === "path" || t === "floor" || t === "wall" || t === "portal") return false;
      }
    }
    return true;
  }

  function placeCluster(grid, cx, cy, type, count, spread, rng) {
    var placed = 0;
    var attempts = 0;
    while (placed < count && attempts < count * 20) {
      attempts++;
      var ox = Math.floor(cx + (rng() - 0.5) * spread * 2);
      var oy = Math.floor(cy + (rng() - 0.5) * spread * 2);
      if (getTile(grid, ox, oy) !== "grass") continue;
      setTile(grid, ox, oy, type);
      placed++;
    }
  }

  function placeWaterPool(grid, cx, cy, w, h, rng) {
    var x, y;
    for (y = cy; y < cy + h; y++) {
      for (x = cx; x < cx + w; x++) {
        if (rng() > 0.15) setTile(grid, x, y, "water");
      }
    }
  }

  function placeBuilding(grid, objects, bx, by, bw, bh, name, doorSide, interaction) {
    var x, y;
    var doorX = bx + Math.floor(bw / 2);
    var doorY = by + bh - 1;
    if (doorSide === "north") {
      doorX = bx + Math.floor(bw / 2);
      doorY = by;
    } else if (doorSide === "west") {
      doorX = bx;
      doorY = by + Math.floor(bh / 2);
    } else if (doorSide === "east") {
      doorX = bx + bw - 1;
      doorY = by + Math.floor(bh / 2);
    }

    for (y = by; y < by + bh; y++) {
      for (x = bx; x < bx + bw; x++) {
        var edge = x === bx || x === bx + bw - 1 || y === by || y === by + bh - 1;
        if (x === doorX && y === doorY) setTile(grid, x, y, "path");
        else if (edge) setTile(grid, x, y, "wall");
        else setTile(grid, x, y, "floor");
      }
    }

    var id = "building_" + bx + "_" + by;
    objects.push({
      id: id,
      type: "building",
      name: name,
      x: bx,
      y: by,
      width: bw,
      height: bh,
      door: { x: doorX, y: doorY },
      blocksMovement: false,
      interaction: interaction
    });
    return { id: id, doorX: doorX, doorY: doorY };
  }

  function gridToTiles(grid) {
    var tiles = [];
    var y, x;
    for (y = 0; y < grid.length; y++) {
      for (x = 0; x < grid[0].length; x++) {
        var type = grid[y][x];
        tiles.push({
          x: x,
          y: y,
          type: type,
          walkable: TILE[type].walkable
        });
      }
    }
    return tiles;
  }

  function generateWorld(seed) {
    if (!seed) seed = DATA.DEFAULT_SEED;
    var rng = DATA.createRng(seed);
    var width = 100;
    var height = 100;
    var tileSize = 32;
    var spawn = { x: 10, y: 10 };

    var grid = [];
    var y, x;
    for (y = 0; y < height; y++) {
      grid.push(new Array(width).fill("grass"));
    }

    clearCircle(grid, spawn.x, spawn.y, 6, "grass");
    setTile(grid, spawn.x, spawn.y, "path");
    setTile(grid, spawn.x + 1, spawn.y, "path");
    setTile(grid, spawn.x, spawn.y + 1, "path");

    var training = {
      x: 22 + Math.floor(rng() * 8),
      y: 8 + Math.floor(rng() * 6)
    };
    var firstBuilding = {
      x: 38 + Math.floor(rng() * 10),
      y: 20 + Math.floor(rng() * 10)
    };
    var firstPortal = {
      x: 55 + Math.floor(rng() * 12),
      y: 35 + Math.floor(rng() * 12)
    };
    var resourceArea = {
      x: 68 + Math.floor(rng() * 15),
      y: 62 + Math.floor(rng() * 15)
    };

    carvePath(grid, spawn.x, spawn.y, training.x, training.y, rng);
    carvePath(grid, training.x, training.y, firstBuilding.x, firstBuilding.y, rng);
    carvePath(grid, firstBuilding.x, firstBuilding.y, firstPortal.x, firstPortal.y, rng);
    carvePath(grid, firstPortal.x, firstPortal.y, resourceArea.x, resourceArea.y, rng);

    var poolCount = 3 + Math.floor(rng() * 3);
    var p;
    for (p = 0; p < poolCount; p++) {
      var px = 15 + Math.floor(rng() * (width - 30));
      var py = 15 + Math.floor(rng() * (height - 30));
      if (canPlaceCluster(grid, px, py, 4)) {
        placeWaterPool(grid, px, py, 3 + Math.floor(rng() * 4), 2 + Math.floor(rng() * 3), rng);
      }
    }

    var treeSpots = 4 + Math.floor(rng() * 3);
    for (p = 0; p < treeSpots; p++) {
      var tx = 10 + Math.floor(rng() * (width - 20));
      var ty = 10 + Math.floor(rng() * (height - 20));
      if (canPlaceCluster(grid, tx, ty, 5)) {
        placeCluster(grid, tx, ty, "tree", 8 + Math.floor(rng() * 8), 5, rng);
      }
    }

    var rockSpots = 3 + Math.floor(rng() * 2);
    for (p = 0; p < rockSpots; p++) {
      var rx = 10 + Math.floor(rng() * (width - 20));
      var ry = 10 + Math.floor(rng() * (height - 20));
      if (canPlaceCluster(grid, rx, ry, 4)) {
        placeCluster(grid, rx, ry, "rock", 5 + Math.floor(rng() * 5), 4, rng);
      }
    }

    var crystalSpots = 2 + Math.floor(rng() * 2);
    for (p = 0; p < crystalSpots; p++) {
      var cx = 10 + Math.floor(rng() * (width - 20));
      var cy = 10 + Math.floor(rng() * (height - 20));
      if (canPlaceCluster(grid, cx, cy, 4)) {
        placeCluster(grid, cx, cy, "crystal", 4 + Math.floor(rng() * 4), 3, rng);
      }
    }

    var objects = [];
    var buildingCount = 3 + Math.floor(rng() * 3);
    var buildingNames = [
      "Archive Hut",
      "Dream Guide Station",
      "Memory Observatory",
      "Crystal Workshop",
      "Echo Library"
    ];
    var buildingPositions = [
      { x: firstBuilding.x - 2, y: firstBuilding.y - 2, w: 6, h: 5 },
      { x: spawn.x + 14, y: spawn.y + 8, w: 5, h: 4 },
      { x: training.x + 10, y: training.y + 6, w: 5, h: 5 },
      { x: resourceArea.x - 4, y: resourceArea.y - 5, w: 6, h: 4 },
      { x: firstPortal.x + 8, y: firstPortal.y - 3, w: 5, h: 5 }
    ];

    for (p = 0; p < buildingCount && p < buildingPositions.length; p++) {
      var bp = buildingPositions[p];
      placeBuilding(
        grid,
        objects,
        bp.x,
        bp.y,
        bp.w,
        bp.h,
        buildingNames[p],
        p % 2 === 0 ? "south" : "north",
        {
          type: p === 0 ? "shop_placeholder" : "message",
          text: p === 0
            ? "The Archive Hut hums with stored memories. Trading opens in a future update."
            : "You enter " + buildingNames[p] + ". Soft blue light fills the room."
        }
      );
    }

    objects.push({
      id: "training_marker",
      type: "marker",
      name: "Training Grounds",
      x: training.x,
      y: training.y,
      blocksMovement: false,
      sprite: null,
      interaction: {
        type: "training_placeholder",
        text: "Dream training begins here. Combat tutorials unlock in a future phase."
      }
    });

    objects.push({
      id: "npc_dream_guide",
      type: "npc",
      name: "Dream Guide",
      x: spawn.x,
      y: spawn.y + 2,
      blocksMovement: false,
      sprite: "assets/sprites/npc/dream-guide.png",
      interaction: {
        type: "dialogue",
        text: "Welcome, Dream Explorer. Walk the Dreamfield paths, visit the Archive Hut, and gather memories when the world is ready."
      }
    });

    objects.push({
      id: "npc_archive_keeper",
      type: "npc",
      name: "Archive Keeper",
      x: firstBuilding.x + 1,
      y: firstBuilding.y + 3,
      blocksMovement: false,
      sprite: "assets/sprites/npc/archive-keeper.png",
      interaction: {
        type: "dialogue",
        text: "Every step you take writes a new line in the Dreamscape archive. Return often — the world remembers."
      }
    });

    objects.push({
      id: "npc_field_scout",
      type: "npc",
      name: "Field Scout",
      x: resourceArea.x - 2,
      y: resourceArea.y,
      blocksMovement: false,
      sprite: "assets/sprites/npc/field-scout.png",
      interaction: {
        type: "dialogue",
        text: "Resources shimmer ahead — dream crystals, blue flowers, memory stones. Gathering unlocks soon."
      }
    });

    setTile(grid, firstPortal.x, firstPortal.y, "portal");
    objects.push({
      id: "portal_dreamgate",
      type: "portal",
      name: "Dreamgate Portal",
      x: firstPortal.x,
      y: firstPortal.y,
      blocksMovement: false,
      sprite: null,
      interaction: {
        type: "portal_locked",
        text: "This portal is dormant."
      }
    });

    var portal2 = {
      x: 30 + Math.floor(rng() * 50),
      y: 40 + Math.floor(rng() * 40)
    };
    if (getTile(grid, portal2.x, portal2.y) === "grass") {
      setTile(grid, portal2.x, portal2.y, "portal");
      carvePath(grid, firstPortal.x, firstPortal.y, portal2.x, portal2.y, rng);
    }
    objects.push({
      id: "portal_echo",
      type: "portal",
      name: "Echo Portal",
      x: portal2.x,
      y: portal2.y,
      blocksMovement: false,
      sprite: null,
      interaction: {
        type: "portal_locked",
        text: "This portal is dormant."
      }
    });

    var resourceTypes = [
      { type: "dream_crystal", name: "Dream Crystal" },
      { type: "blue_flower", name: "Blue Flower" },
      { type: "memory_stone", name: "Memory Stone" },
      { type: "glow_tree", name: "Glow Tree" }
    ];
    var resourceCount = 4 + Math.floor(rng() * 5);
    for (p = 0; p < resourceCount; p++) {
      var rt = resourceTypes[p % resourceTypes.length];
      var rpx = resourceArea.x + Math.floor(rng() * 12) - 6;
      var rpy = resourceArea.y + Math.floor(rng() * 12) - 6;
      if (getTile(grid, rpx, rpy) === "grass" || getTile(grid, rpx, rpy) === "path") {
        objects.push({
          id: "resource_" + p,
          type: "resource",
          subtype: rt.type,
          name: rt.name,
          x: rpx,
          y: rpy,
          blocksMovement: false,
          sprite: null,
          interaction: {
            type: "resource_placeholder",
            text: "Gathering will unlock later."
          }
        });
      }
    }

    return {
      id: "world_" + DATA.hashString(seed).toString(16),
      name: "Generated Dreamfield",
      seed: seed,
      width: width,
      height: height,
      tileSize: tileSize,
      spawn: spawn,
      tiles: gridToTiles(grid),
      objects: objects,
      landmarks: {
        training: training,
        firstBuilding: firstBuilding,
        firstPortal: firstPortal,
        resourceArea: resourceArea
      }
    };
  }

  global.DD_WORLD_GENERATOR = {
    generateWorld: generateWorld
  };
})(typeof window !== "undefined" ? window : global);
