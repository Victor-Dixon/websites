/* Digital Dreamscape — explore game loop */
(function () {
  "use strict";

  if (typeof document === "undefined") return;

  var TILE = window.DD_TILE_TYPES;
  var DATA = window.DD_WORLD_DATA;
  var GEN = window.DD_WORLD_GENERATOR;
  var SAVE = window.DD_SAVE;
  var PATH = window.DD_PATHFINDING;
  var CAM = window.DD_CAMERA;
  var PLAYER = window.DD_PLAYER;
  var RENDER = window.DD_RENDERER;
  var INTERACT = window.DD_INTERACTIONS;

  var canvas = document.getElementById("explore-canvas");
  if (!canvas) return;

  var ctx = canvas.getContext("2d");
  var hudEl = document.getElementById("explore-hud");
  var greetingEl = document.getElementById("explore-greeting");

  var save = SAVE.getOrCreateSave();
  var world = GEN.generateWorld(save.worldSeed);
  var grid = DATA.buildGrid(world);
  var player = PLAYER.createPlayer(save.player.x, save.player.y);
  var camera = CAM.createCamera(world, DATA.VIEWPORT_W, DATA.VIEWPORT_H, DATA.TILE_SIZE);
  var pathPreview = [];
  var pulseT = 0;
  var rafId = 0;
  var lastTickTs = 0;

  canvas.width = DATA.VIEWPORT_W;
  canvas.height = DATA.VIEWPORT_H;

  INTERACT.init(
    {
      overlay: document.getElementById("interact-overlay"),
      title: document.getElementById("interact-title"),
      body: document.getElementById("interact-body"),
      actions: document.getElementById("interact-actions"),
      closeBtn: document.getElementById("btn-close-overlay")
    },
    function () {}
  );

  if (greetingEl) {
    greetingEl.textContent = "Dreamfield — " + world.name;
  }

  function isWalkable(x, y) {
    if (x < 0 || y < 0 || x >= world.width || y >= world.height) return false;
    var tile = grid[y][x];
    if (!tile || !TILE[tile.type].walkable) return false;
    var i;
    for (i = 0; i < world.objects.length; i++) {
      var o = world.objects[i];
      if (o.blocksMovement && o.x === x && o.y === y) return false;
    }
    return true;
  }

  function persistPosition() {
    save.player.x = player.x;
    save.player.y = player.y;
    SAVE.saveGame(save);
  }

  function updateHud() {
    if (!hudEl) return;
    hudEl.innerHTML =
      "<p><strong>" + save.player.name + "</strong> · Lv " + save.player.level + "</p>" +
      "<p class=\"muted\">Dream Shards: " + save.player.currency + " · XP: " + save.player.xp + "</p>" +
      "<p class=\"muted\">Pos " + player.x + "," + player.y + " · Seed: " + world.seed + "</p>";
  }

  function onStepComplete() {
    persistPosition();
    INTERACT.checkTileInteraction(world, player, save, SAVE);
    updateHud();
    PLAYER.stepQueue(player, isWalkable);
  }

  function canvasToTile(clientX, clientY) {
    var rect = canvas.getBoundingClientRect();
    var scaleX = canvas.width / rect.width;
    var scaleY = canvas.height / rect.height;
    var lx = (clientX - rect.left) * scaleX;
    var ly = (clientY - rect.top) * scaleY;
    return {
      x: Math.floor(lx / DATA.TILE_SIZE) + camera.x,
      y: Math.floor(ly / DATA.TILE_SIZE) + camera.y
    };
  }

  function onCanvasClick(e) {
    if (player.moving) return;
    var tile = canvasToTile(e.clientX, e.clientY);
    if (tile.x < 0 || tile.y < 0 || tile.x >= world.width || tile.y >= world.height) return;

    var adjacent = INTERACT.findAdjacentInteractable(world, player.x, player.y, tile.x, tile.y);
    if (adjacent && tile.x === adjacent.x && tile.y === adjacent.y) {
      var pathToObj = PATH.findPath(player.x, player.y, tile.x, tile.y, isWalkable, world.width, world.height);
      if (pathToObj && pathToObj.length) {
        pathPreview = pathToObj;
        PLAYER.queuePath(player, pathToObj);
        PLAYER.stepQueue(player, isWalkable);
      } else if (Math.abs(player.x - tile.x) + Math.abs(player.y - tile.y) <= 1) {
        INTERACT.triggerInteraction(adjacent, save, SAVE);
      }
      return;
    }

    if (!isWalkable(tile.x, tile.y)) return;

    var path = PATH.findPath(player.x, player.y, tile.x, tile.y, isWalkable, world.width, world.height);
    if (path) {
      pathPreview = path;
      PLAYER.queuePath(player, path);
      PLAYER.stepQueue(player, isWalkable);
    }
  }

  function tick(now) {
    pulseT = now / 400;
    if (!lastTickTs) lastTickTs = now;
    if (window.DD_PLAYER_SPRITE) {
      DD_PLAYER_SPRITE.tick(now, player, false);
    }
    var finished = PLAYER.updateAnimation(player, now);
    if (finished) onStepComplete();
    if (!player.moving && !player.moveQueue.length) pathPreview = [];
    camera.update(player.renderX, player.renderY);
    RENDER.render(ctx, world, grid, camera, player, world.objects, pathPreview, pulseT);
    rafId = requestAnimationFrame(tick);
  }

  canvas.addEventListener("click", onCanvasClick);
  window.addEventListener("resize", function () {
    canvas.width = DATA.VIEWPORT_W;
    canvas.height = DATA.VIEWPORT_H;
  });

  if (!isWalkable(player.x, player.y)) {
    player.x = world.spawn.x;
    player.y = world.spawn.y;
    player.renderX = player.x;
    player.renderY = player.y;
    persistPosition();
  }

  updateHud();
  var boot = function () {
    rafId = requestAnimationFrame(tick);
  };
  if (window.DD_PLAYER_SPRITE) {
    DD_PLAYER_SPRITE.init().then(boot).catch(function (err) {
      console.warn("Dream Explorer sprite failed to load, using placeholder:", err);
      boot();
    });
  } else {
    boot();
  }
})();
