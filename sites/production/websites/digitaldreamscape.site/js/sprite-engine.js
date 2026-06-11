/* Digital Dreamscape — slim vendored sprite engine (Grid Schema V1) */
(function (global) {
  "use strict";

  var ACTION_MAP = {
    walking: "walk",
    running: "run",
    attacking: "attack",
    idle: "idle",
    walk: "walk",
    run: "run",
    attack: "attack",
    interact: "interact",
    magic: "magic",
    hurt: "hurt"
  };

  function resolveAnimationKey(action, direction) {
    var normalized = ACTION_MAP[action] || action;
    if (normalized === "interact" || normalized === "attack" || normalized === "magic" || normalized === "hurt") {
      return normalized;
    }
    return normalized + "_" + (direction || "down");
  }

  function SpriteEngine(config) {
    this.config = config || {};
    this.atlas = null;
    this.texture = null;
    this.action = "idle";
    this.direction = "down";
    this.currentKey = "idle_down";
    this.frameIndex = 0;
    this.frameAccumulator = 0;
    this.defaultFps = this.config.defaultFps || 10;
  }

  SpriteEngine.prototype.loadAtlasWithImage = function (atlasJson, image) {
    this.atlas = atlasJson;
    this.texture = image;
    return this;
  };

  SpriteEngine.prototype.loadAtlas = function (jsonUrl, textureUrl) {
    var self = this;
    return fetch(jsonUrl)
      .then(function (res) {
        if (!res.ok) throw new Error("Failed to load atlas: " + res.status);
        return res.json();
      })
      .then(function (data) {
        var tex = textureUrl || data.asset.texture;
        var base = jsonUrl.replace(/[^/]+$/, "");
        if (tex.indexOf("/") === -1 && tex.indexOf("data:") !== 0) {
          tex = base + tex;
        }
        return new Promise(function (resolve, reject) {
          var img = new Image();
          img.onload = function () {
            self.loadAtlasWithImage(data, img);
            resolve(self);
          };
          img.onerror = function () {
            reject(new Error("Failed to load texture: " + tex));
          };
          img.src = tex;
        });
      });
  };

  SpriteEngine.prototype.setState = function (action, direction) {
    var key = resolveAnimationKey(action, direction || this.direction);
    if (!this.atlas || !this.atlas.animations[key]) return;
    this.action = action;
    this.direction = direction || this.direction;
    this.currentKey = key;
    this.frameIndex = 0;
    this.frameAccumulator = 0;
  };

  SpriteEngine.prototype.getPivot = function () {
    var p = this.atlas && this.atlas.asset && this.atlas.asset.pivot;
    return { x: p && p.x != null ? p.x : 0.5, y: p && p.y != null ? p.y : 1.0 };
  };

  SpriteEngine.prototype.update = function (dt) {
    var clip = this.atlas && this.atlas.animations[this.currentKey];
    if (!clip) return;
    var fps = clip.fps || this.defaultFps;
    this.frameAccumulator += dt;
    var frameDuration = 1 / fps;
    while (this.frameAccumulator >= frameDuration) {
      this.frameAccumulator -= frameDuration;
      var next = this.frameIndex + 1;
      if (next >= clip.length) {
        this.frameIndex = clip.loop ? 0 : clip.length - 1;
      } else {
        this.frameIndex = next;
      }
    }
  };

  /**
   * Draw current frame with feet-center pivot at anchor (tile bottom-center).
   * anchorX/Y = screen coords of tile bottom-center:
   *   anchorX = (renderX - camX) * tileSize + tileSize / 2
   *   anchorY = (renderY - camY) * tileSize + tileSize
   */
  SpriteEngine.prototype.drawFrame = function (ctx, anchorX, anchorY, scale) {
    if (!this.texture || !this.atlas) return;
    var clip = this.atlas.animations[this.currentKey];
    if (!clip) return;
    var gridW = this.atlas.asset.gridSize.w;
    var gridH = this.atlas.asset.gridSize.h;
    var sx = (clip.startFrame + this.frameIndex) * gridW;
    var sy = clip.row * gridH;
    var pivot = this.getPivot();
    var s = scale == null ? 1 : scale;
    var dw = gridW * s;
    var dh = gridH * s;
    var dx = anchorX - dw * pivot.x;
    var dy = anchorY - dh * pivot.y;
    ctx.imageSmoothingEnabled = false;
    ctx.drawImage(this.texture, sx, sy, gridW, gridH, dx, dy, dw, dh);
  };

  global.DD_SPRITE_ENGINE = {
    SpriteEngine: SpriteEngine,
    resolveAnimationKey: resolveAnimationKey
  };
})(typeof window !== "undefined" ? window : global);
