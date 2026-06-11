/* Digital Dreamscape — Dream Explorer player sprite loader */
(function (global) {
  "use strict";

  var engine = null;
  var ready = false;
  var loadPromise = null;
  var lastTs = 0;
  var lastAction = "";
  var lastFacing = "";

  var ATLAS_URL = "./assets/sprites/player/dream-explorer-atlas.json";
  var TEXTURE_URL = "./assets/sprites/player/dream-explorer.png";
  var DRAW_SCALE = 0.75;

  function init() {
    if (loadPromise) return loadPromise;
    if (!global.DD_SPRITE_ENGINE) {
      loadPromise = Promise.reject(new Error("DD_SPRITE_ENGINE missing"));
      return loadPromise;
    }
    var SpriteEngine = global.DD_SPRITE_ENGINE.SpriteEngine;
    engine = new SpriteEngine({ defaultFps: 10 });
    loadPromise = engine.loadAtlas(ATLAS_URL, TEXTURE_URL).then(function () {
      ready = true;
      engine.setState("idle", "down");
      lastTs = performance.now();
      return engine;
    });
    return loadPromise;
  }

  function syncPlayerState(player, running) {
    if (!engine || !ready) return;
    var action = player.moving ? (running ? "running" : "walking") : "idle";
    if (action === lastAction && player.facing === lastFacing) return;
    lastAction = action;
    lastFacing = player.facing;
    engine.setState(action, player.facing);
  }

  function tick(now, player, running) {
    if (!engine || !ready) return;
    if (!lastTs) lastTs = now;
    var dt = Math.min(0.1, (now - lastTs) / 1000);
    lastTs = now;
    syncPlayerState(player, running);
    engine.update(dt);
  }

  function draw(ctx, player, camera, tileSize) {
    if (!engine || !ready) return false;
    var sx = (player.renderX - camera.x) * tileSize;
    var sy = (player.renderY - camera.y) * tileSize;
    var anchorX = sx + tileSize / 2;
    var anchorY = sy + tileSize;
    ctx.fillStyle = "rgba(0,0,0,0.2)";
    ctx.beginPath();
    ctx.ellipse(anchorX, anchorY - 2, 8, 3, 0, 0, Math.PI * 2);
    ctx.fill();
    engine.drawFrame(ctx, anchorX, anchorY, DRAW_SCALE);
    return true;
  }

  global.DD_PLAYER_SPRITE = {
    init: init,
    tick: tick,
    draw: draw,
    isReady: function () {
      return ready;
    },
    getEngine: function () {
      return engine;
    }
  };
})(typeof window !== "undefined" ? window : global);
