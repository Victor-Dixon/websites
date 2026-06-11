/* Planet Blue — walkable overworld exploration */
(function (global) {
  "use strict";

  var COLS = 20;
  var ROWS = 15;
  var TILE_SIZE = 32;
  var STEP_MS = 140;

  var TILE = {
    G: "grass",
    P: "path",
    W: "water",
    B: "building",
    T: "tree",
    S: "sand"
  };

  var WALKABLE = { grass: 1, path: 1, sand: 1 };

  var MAP_LAYOUT = [
    "TTTTTTTTTTTTTTTTTTTT",
    "TTTTGGGGPPGGGGGGTTTT",
    "TTTGGGGGBBGGGGGGTTTT",
    "TTGGGGGPPPPGGGGGGTTT",
    "TGGGGGGGPPGGGGGGGGTT",
    "GGGGGGGGPPGGGGGGGGGT",
    "GGGGWWWGGPPGGGGGGGGT",
    "GGGWWWWWGGPPGGGGGGGT",
    "GGGGWWWGGGPPGGGGGGGT",
    "GGGGGGGGGGPPPPGGGGGT",
    "GGGGGGGGGGGPPBGGGGGT",
    "GGGPPPPPPPPPPPGGGGGT",
    "GGGBBGPPGGGGPPGGGGGT",
    "GGGPPPPGGGGPPPGGGGGT",
    "GGGPPPBGGGGPPBGGGGGT"
  ];

  var ZONE_REGIONS = {
    sky_spire: { x1: 3, y1: 0, x2: 12, y2: 4, zoneId: "sky_spire" },
    deep_caverns: { x1: 10, y1: 8, x2: 18, y2: 11, zoneId: "deep_caverns" },
    landing_bay: { x1: 2, y1: 12, x2: 11, y2: 14, zoneId: "landing_bay" }
  };

  var INTERACTABLES = [
    { id: "hub", kind: "hub", x: 10, y: 11, label: "Colony Hub", href: "character.html" },
    { id: "sign", kind: "hub", x: 4, y: 12, label: "Adventurer Sign", href: "character.html" },
    { id: "map_board", kind: "hub", x: 6, y: 12, label: "Mission Board", href: "map.html" },
    { id: "first_landing", kind: "mission", x: 7, y: 14, label: "First Landing", missionId: "first_landing", zoneId: "landing_bay" },
    { id: "deep_caverns", kind: "mission", x: 14, y: 10, label: "Deep Caverns", missionId: "deep_caverns", zoneId: "deep_caverns" },
    { id: "sky_spire", kind: "mission", x: 8, y: 2, label: "Sky Spire", missionId: "sky_spire", zoneId: "sky_spire" }
  ];

  var NPCS = [
    { id: "colonist_a", x: 9, y: 12, shirt: "#00B06F", pants: "#1B2A35", label: "Scout", path: [[9, 12], [11, 12], [11, 11], [9, 11], [9, 12]] },
    { id: "colonist_b", x: 15, y: 11, shirt: "#FFAA00", pants: "#2D3436", label: "Trader", path: [[15, 11], [17, 11], [17, 12], [15, 12], [15, 11]] }
  ];

  global.PLANET_BLUE_OVERWORLD = {
    COLS: COLS,
    ROWS: ROWS,
    TILE_SIZE: TILE_SIZE,
    MAP_LAYOUT: MAP_LAYOUT,
    TILE: TILE,
    WALKABLE: WALKABLE,
    ZONE_REGIONS: ZONE_REGIONS,
    INTERACTABLES: INTERACTABLES,
    NPCS: NPCS,
    tileAt: tileAt,
    isWalkable: isWalkable,
    zoneAt: zoneAt,
    interactableAt: interactableAt,
    clampPosition: clampPosition
  };

  function tileAt(x, y) {
    if (x < 0 || y < 0 || x >= COLS || y >= ROWS) return "tree";
    var ch = MAP_LAYOUT[y][x];
    return TILE[ch] || "grass";
  }

  function isWalkable(x, y) {
    return !!WALKABLE[tileAt(x, y)];
  }

  function zoneAt(x, y) {
    var keys = Object.keys(ZONE_REGIONS);
    for (var i = 0; i < keys.length; i++) {
      var r = ZONE_REGIONS[keys[i]];
      if (x >= r.x1 && x <= r.x2 && y >= r.y1 && y <= r.y2) return r.zoneId;
    }
    return null;
  }

  function interactableAt(x, y) {
    for (var i = 0; i < INTERACTABLES.length; i++) {
      var it = INTERACTABLES[i];
      if (it.x === x && it.y === y) return it;
    }
    return null;
  }

  function clampPosition(x, y) {
    return {
      x: Math.max(0, Math.min(COLS - 1, x)),
      y: Math.max(0, Math.min(ROWS - 1, y))
    };
  }
})(typeof window !== "undefined" ? window : global);

(function () {
  "use strict";

  if (typeof document === "undefined") return;

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;
  var WORLD = window.PLANET_BLUE_WORLD;
  var PATH = window.PLANET_BLUE_PATH;
  var SPRITES = window.PLANET_BLUE_SPRITES;
  var OW = window.PLANET_BLUE_OVERWORLD;

  var save = SAVE.getOrCreateSave();
  WORLD.ensureWorldSystems(save);
  SAVE.syncMissionUnlocks(save);
  SAVE.saveGame(save);

  if (!save.profileCreated) {
    window.location.href = "character.html";
    return;
  }

  var canvas = document.getElementById("overworld-canvas");
  if (!canvas) return;

  var ctx = canvas.getContext("2d");
  var hudEl = document.getElementById("overworld-hud");
  var dialogueEl = document.getElementById("world-dialogue");
  var greetingEl = document.getElementById("world-greeting");
  var overlayEl = document.getElementById("interact-overlay");
  var overlayTitle = document.getElementById("interact-title");
  var overlayBody = document.getElementById("interact-body");
  var overlayActions = document.getElementById("interact-actions");

  var pos = WORLD.ensureOverworld(save);
  var player = {
    x: pos.x,
    y: pos.y,
    facing: "down",
    moving: false,
    renderX: pos.x,
    renderY: pos.y
  };

  var moveQueue = [];
  var keysDown = {};
  var keyRepeatLock = false;
  var animStart = 0;
  var animFrom = { x: player.x, y: player.y };
  var animTo = { x: player.x, y: player.y };
  var pulseT = 0;
  var pendingInteract = null;
  var npcStates = OW.NPCS.map(function (npc) {
    return { def: npc, pathIdx: 0, x: npc.x, y: npc.y, renderX: npc.x, renderY: npc.y, waitUntil: 0 };
  });

  var viewCols = 13;
  var viewRows = 10;
  var camera = { x: 0, y: 0 };

  function resizeCanvas() {
    var wrap = canvas.parentElement;
    var maxW = wrap.clientWidth - 8;
    var maxH = Math.min(window.innerHeight * 0.62, 480);
    var scale = Math.floor(Math.min(maxW / (viewCols * OW.TILE_SIZE), maxH / (viewRows * OW.TILE_SIZE)));
    scale = Math.max(2, scale);
    canvas.width = viewCols * OW.TILE_SIZE * scale;
    canvas.height = viewRows * OW.TILE_SIZE * scale;
    canvas.dataset.scale = String(scale);
  }

  function scale() {
    return Number(canvas.dataset.scale) || 2;
  }

  function updateCamera() {
    camera.x = Math.max(0, Math.min(OW.COLS - viewCols, Math.round(player.renderX - viewCols / 2)));
    camera.y = Math.max(0, Math.min(OW.ROWS - viewRows, Math.round(player.renderY - viewRows / 2)));
  }

  function zoneStatusAt(x, y) {
    var zoneId = OW.zoneAt(x, y);
    if (!zoneId || !save.world.zones[zoneId]) return null;
    return WORLD.zoneStatus(save.world.zones[zoneId].safety);
  }

  function missionStatus(missionId) {
    return save.missions[missionId] || "locked";
  }

  function canEngageMission(missionId) {
    var status = missionStatus(missionId);
    if (status === "completed") return false;
    if (status !== "unlocked") return false;
    return WORLD.missionAllowedByMorality(save, missionId);
  }

  function persistPosition() {
    WORLD.setOverworldPosition(save, player.x, player.y);
    SAVE.saveGame(save);
  }

  function setFacing(dx, dy) {
    if (dx < 0) player.facing = "left";
    else if (dx > 0) player.facing = "right";
    else if (dy < 0) player.facing = "up";
    else if (dy > 0) player.facing = "down";
  }

  function beginStep(tx, ty) {
    if (player.moving) return false;
    if (!OW.isWalkable(tx, ty)) return false;

    animFrom = { x: player.x, y: player.y };
    animTo = { x: tx, y: ty };
    setFacing(tx - player.x, ty - player.y);
    player.moving = true;
    animStart = performance.now();
    return true;
  }

  function finishStep() {
    player.x = animTo.x;
    player.y = animTo.y;
    player.renderX = player.x;
    player.renderY = player.y;
    player.moving = false;
    persistPosition();
    checkInteraction();
    updateHud();
  }

  function queuePath(path) {
    if (!path || !path.length) return;
    moveQueue = path.slice();
    if (!player.moving) stepQueue();
  }

  function stepQueue() {
    if (!moveQueue.length || player.moving) return;
    var next = moveQueue.shift();
    if (next.x === player.x && next.y === player.y) {
      stepQueue();
      return;
    }
    if (!beginStep(next.x, next.y)) {
      moveQueue = [];
    }
  }

  function tryKeyboardMove(dx, dy) {
    if (player.moving) return;
    moveQueue = [];
    var tx = player.x + dx;
    var ty = player.y + dy;
    if (!OW.isWalkable(tx, ty)) return;
    beginStep(tx, ty);
  }

  function checkInteraction() {
    var it = OW.interactableAt(player.x, player.y);
    if (!it) return;

    if (it.kind === "hub") {
      showInteract(it.label, "Visit " + it.label + "?", [
        { label: "Enter", primary: true, action: function () { window.location.href = it.href; } },
        { label: "Stay", action: closeOverlay }
      ]);
      return;
    }

    if (it.kind === "mission") {
      var status = missionStatus(it.missionId);
      var m = DATA.MISSIONS[it.missionId];
      if (status === "completed") {
        showInteract(m.name, "This zone has been secured. The colonists hold the line.", [
          { label: "OK", action: closeOverlay }
        ]);
        return;
      }
      if (status === "locked") {
        showInteract(m.name, "The path is blocked. Complete earlier missions first.", [
          { label: "OK", action: closeOverlay }
        ]);
        return;
      }
      if (!WORLD.missionAllowedByMorality(save, it.missionId)) {
        showInteract(m.name, "Your alignment bars this mission for now.", [
          { label: "OK", action: closeOverlay }
        ]);
        return;
      }
      showInteract(m.name, m.desc + "\n\nEnter battle?", [
        { label: "Engage", primary: true, action: function () { window.location.href = "battle.html?mission=" + it.missionId; } },
        { label: "Not yet", action: closeOverlay }
      ]);
    }
  }

  function showInteract(title, body, actions) {
    overlayTitle.textContent = title;
    overlayBody.textContent = body;
    overlayActions.innerHTML = "";
    actions.forEach(function (act) {
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn" + (act.primary ? " primary" : "");
      btn.textContent = act.label;
      btn.addEventListener("click", act.action);
      overlayActions.appendChild(btn);
    });
    overlayEl.classList.remove("hidden");
  }

  function closeOverlay() {
    overlayEl.classList.add("hidden");
  }

  function canvasToTile(clientX, clientY) {
    var rect = canvas.getBoundingClientRect();
    var sc = scale();
    var lx = (clientX - rect.left) / sc;
    var ly = (clientY - rect.top) / sc;
    return {
      x: Math.floor(lx / OW.TILE_SIZE) + camera.x,
      y: Math.floor(ly / OW.TILE_SIZE) + camera.y
    };
  }

  function onCanvasClick(e) {
    if (player.moving) return;
    var tile = canvasToTile(e.clientX, e.clientY);
    if (tile.x < 0 || tile.y < 0 || tile.x >= OW.COLS || tile.y >= OW.ROWS) return;

    var it = OW.interactableAt(tile.x, tile.y);
    if (it && Math.abs(it.x - player.x) + Math.abs(it.y - player.y) <= 1) {
      player.x = tile.x;
      player.y = tile.y;
      player.renderX = player.x;
      player.renderY = player.y;
      persistPosition();
      checkInteraction();
      return;
    }

    var path = PATH.findPath(player.x, player.y, tile.x, tile.y, OW.isWalkable, OW.COLS, OW.ROWS);
    if (path) queuePath(path);
  }

  function updateHud() {
    var ch = save.character;
    var zoneId = OW.zoneAt(player.x, player.y);
    var zoneLine = "Wilderness";
    if (zoneId && save.world.zones[zoneId]) {
      var z = save.world.zones[zoneId];
      zoneLine = z.name + " — " + WORLD.zoneStatusLabel(WORLD.zoneStatus(z.safety));
    }
    hudEl.innerHTML =
      "<p><strong>" + ch.name + "</strong> · Lv " + ch.level + "</p>" +
      "<p class=\"muted\">" + zoneLine + "</p>" +
      "<p class=\"muted\">Pos " + player.x + "," + player.y + "</p>";
  }

  function updateNpcs(now) {
    npcStates.forEach(function (ns) {
      if (now < ns.waitUntil) return;
      var path = ns.def.path;
      var nextIdx = (ns.pathIdx + 1) % path.length;
      var target = path[nextIdx];
      if (ns.x === target[0] && ns.y === target[1]) {
        ns.pathIdx = nextIdx;
        ns.waitUntil = now + 1200 + Math.random() * 800;
        return;
      }
      var dx = target[0] - ns.x;
      var dy = target[1] - ns.y;
      if (Math.abs(dx) > Math.abs(dy)) ns.x += dx > 0 ? 1 : -1;
      else ns.y += dy > 0 ? 1 : -1;
      ns.renderX = ns.x;
      ns.renderY = ns.y;
    });
  }

  function render(now) {
    pulseT = now / 400;
    var sc = scale();
    ctx.save();
    ctx.scale(sc, sc);
    ctx.clearRect(0, 0, viewCols * OW.TILE_SIZE, viewRows * OW.TILE_SIZE);
    SPRITES.drawSky(ctx, viewCols * OW.TILE_SIZE, viewRows * OW.TILE_SIZE);

    for (var vy = 0; vy < viewRows; vy++) {
      for (var vx = 0; vx < viewCols; vx++) {
        var wx = camera.x + vx;
        var wy = camera.y + vy;
        if (wx >= OW.COLS || wy >= OW.ROWS) continue;
        var tile = OW.tileAt(wx, wy);
        SPRITES.drawTile(ctx, tile, vx, vy, OW.TILE_SIZE, zoneStatusAt(wx, wy));
      }
    }

    OW.INTERACTABLES.forEach(function (it) {
      if (it.x < camera.x || it.y < camera.y || it.x >= camera.x + viewCols || it.y >= camera.y + viewRows) return;
      var sx = it.x - camera.x;
      var sy = it.y - camera.y;
      if (it.kind === "mission") {
        var st = missionStatus(it.missionId);
        if (st === "locked") SPRITES.drawMarker(ctx, sx, sy, OW.TILE_SIZE, "locked", pulseT);
        else if (st === "unlocked") SPRITES.drawMarker(ctx, sx, sy, OW.TILE_SIZE, "mission", pulseT);
        else SPRITES.drawMarker(ctx, sx, sy, OW.TILE_SIZE, "hub", pulseT);
      } else {
        SPRITES.drawMarker(ctx, sx, sy, OW.TILE_SIZE, "hub", pulseT);
      }
    });

    npcStates.forEach(function (ns) {
      if (ns.renderX < camera.x || ns.renderY < camera.y || ns.renderX >= camera.x + viewCols || ns.renderY >= camera.y + viewRows) return;
      SPRITES.drawNpc(ctx, ns.renderX - camera.x, ns.renderY - camera.y, OW.TILE_SIZE, ns.def.shirt, ns.def.label, {
        shirt: ns.def.shirt,
        pants: ns.def.pants
      });
    });

    var prx = player.renderX - camera.x;
    var pry = player.renderY - camera.y;
    SPRITES.drawPlayer(ctx, prx, pry, OW.TILE_SIZE, player.facing, SPRITES.raceColor(save.character.race), SPRITES.racePants(save.character.race));

    ctx.restore();
  }

  function tick(now) {
    updateNpcs(now);

    if (player.moving) {
      var t = Math.min(1, (now - animStart) / STEP_MS);
      player.renderX = animFrom.x + (animTo.x - animFrom.x) * t;
      player.renderY = animFrom.y + (animTo.y - animFrom.y) * t;
      updateCamera();
      if (t >= 1) {
        finishStep();
        stepQueue();
      }
    }

    render(now);
    requestAnimationFrame(tick);
  }

  function onKeyDown(e) {
    if (overlayEl && !overlayEl.classList.contains("hidden")) {
      if (e.key === "Escape") closeOverlay();
      return;
    }
    var k = e.key.toLowerCase();
    if (["arrowup", "arrowdown", "arrowleft", "arrowright", "w", "a", "s", "d"].indexOf(k) === -1) return;
    e.preventDefault();
    if (keysDown[k]) return;
    keysDown[k] = true;
    if (k === "arrowup" || k === "w") tryKeyboardMove(0, -1);
    else if (k === "arrowdown" || k === "s") tryKeyboardMove(0, 1);
    else if (k === "arrowleft" || k === "a") tryKeyboardMove(-1, 0);
    else if (k === "arrowright" || k === "d") tryKeyboardMove(1, 0);
  }

  function onKeyUp(e) {
    keysDown[e.key.toLowerCase()] = false;
  }

  if (dialogueEl) {
    dialogueEl.textContent = WORLD.getMoralityDialogue(save, "map_greeting");
  }
  if (greetingEl) {
    greetingEl.textContent = "Explore — " + save.character.name;
  }

  document.getElementById("btn-close-overlay").addEventListener("click", closeOverlay);
  canvas.addEventListener("click", onCanvasClick);
  window.addEventListener("keydown", onKeyDown);
  window.addEventListener("keyup", onKeyUp);
  window.addEventListener("resize", resizeCanvas);

  if (!OW.isWalkable(player.x, player.y)) {
    var spawn = WORLD.DEFAULT_OVERWORLD;
    player.x = spawn.x;
    player.y = spawn.y;
    player.renderX = spawn.x;
    player.renderY = spawn.y;
    persistPosition();
  }

  resizeCanvas();
  updateCamera();
  updateHud();
  requestAnimationFrame(tick);
})();
