import { resolveAnimationKey } from './atlas-builder.js';

const DEFAULT_CONFIG = {
  background: '#0d1117',
  scale: 2,
  defaultFps: 10,
  targetRenderFps: 60,
};

/**
 * Lightweight canvas sprite engine with decoupled render vs animation timing.
 */
export class SpriteEngine {
  /**
   * @param {HTMLCanvasElement} canvas
   * @param {object} [config]
   */
  constructor(canvas, config = {}) {
    if (!canvas || !(canvas instanceof HTMLCanvasElement)) {
      throw new TypeError('SpriteEngine requires an HTMLCanvasElement');
    }
    this.canvas = canvas;
    this.ctx = canvas.getContext('2d');
    this.config = { ...DEFAULT_CONFIG, ...config };

    this.atlas = null;
    this.texture = null;
    this.textureUrl = null;

    this.action = 'idle';
    this.direction = 'down';
    this.currentKey = 'idle_down';
    this.frameIndex = 0;
    this.frameAccumulator = 0;

    this.oneShot = null;
    this.oneShotCallback = null;
    this.preOneShotKey = null;

    this._running = false;
    this._lastTs = 0;
    this._rafId = null;
    this._frameCount = 0;
    this._lastFpsTs = 0;
    this._renderFps = 0;

    this._onStateChange = null;
  }

  /** @param {(info: object) => void} fn */
  onStateChange(fn) {
    this._onStateChange = fn;
  }

  /**
   * Load atlas metadata and texture.
   * @param {string|object} jsonUrlOrObject
   * @param {string} [textureUrl] - Override texture URL from JSON
   */
  async loadAtlas(jsonUrlOrObject, textureUrl) {
    let data;
    if (typeof jsonUrlOrObject === 'string') {
      const res = await fetch(jsonUrlOrObject);
      if (!res.ok) throw new Error(`Failed to load atlas JSON: ${res.status}`);
      data = await res.json();
    } else {
      data = jsonUrlOrObject;
    }

    this.atlas = data;
    const texUrl = textureUrl ?? data.asset?.texture;
    if (!texUrl) throw new Error('Atlas missing texture URL');

    if (texUrl === this.textureUrl && this.texture) {
      return this;
    }

    this.textureUrl = texUrl;
    this.texture = await this._loadImage(texUrl);
    return this;
  }

  /**
   * Use a pre-loaded image (e.g. procedural demo sheet).
   * @param {object} atlasJson
   * @param {HTMLImageElement|HTMLCanvasElement} image
   */
  loadAtlasWithImage(atlasJson, image) {
    this.atlas = atlasJson;
    this.texture = image;
    this.textureUrl = atlasJson.asset?.texture ?? 'inline';
    return this;
  }

  /**
   * @param {string} action - idle | walking | walk | running | run
   * @param {string} direction - up | down | left | right
   */
  setState(action, direction = this.direction) {
    const key = resolveAnimationKey(action, direction);
    if (!this.atlas?.animations?.[key]) {
      console.warn(`SpriteEngine: unknown animation "${key}"`);
      return;
    }
    this.action = action;
    this.direction = direction;
    this.oneShot = null;
    this.currentKey = key;
    this.frameIndex = 0;
    this.frameAccumulator = 0;
    this._emitState();
  }

  /**
   * Play a one-shot animation (attack, interact, magic, hurt).
   * @param {string} animationKey
   * @param {() => void} [onComplete]
   */
  play(animationKey, onComplete) {
    const key = resolveAnimationKey(animationKey);
    const clip = this.atlas?.animations?.[key];
    if (!clip) {
      console.warn(`SpriteEngine: unknown one-shot "${key}"`);
      return;
    }
    this.preOneShotKey = this.currentKey;
    this.oneShot = key;
    this.currentKey = key;
    this.frameIndex = 0;
    this.frameAccumulator = 0;
    this.oneShotCallback = onComplete ?? null;
    this._emitState();
  }

  start() {
    if (this._running) return;
    this._running = true;
    this._lastTs = performance.now();
    this._lastFpsTs = this._lastTs;
    this._frameCount = 0;
    this._tick = this._tick.bind(this);
    this._rafId = requestAnimationFrame(this._tick);
  }

  stop() {
    this._running = false;
    if (this._rafId != null) {
      cancelAnimationFrame(this._rafId);
      this._rafId = null;
    }
  }

  destroy() {
    this.stop();
    this.texture = null;
    this.atlas = null;
  }

  getDebugInfo() {
    const clip = this.atlas?.animations?.[this.currentKey];
    return {
      action: this.action,
      direction: this.direction,
      animationKey: this.currentKey,
      frameIndex: this.frameIndex,
      clipLength: clip?.length ?? 0,
      clipFps: clip?.fps ?? this.config.defaultFps,
      renderFps: this._renderFps,
      oneShot: this.oneShot,
    };
  }

  /** @returns {{ x: number, y: number }} Normalized pivot (default feet-center). */
  getPivot() {
    const p = this.atlas?.asset?.pivot;
    return { x: p?.x ?? 0.5, y: p?.y ?? 1.0 };
  }

  /**
   * Advance animation by delta seconds (for external game loops).
   * @param {number} dt - Seconds since last update
   */
  update(dt) {
    this._advanceAnimation(dt);
  }

  /**
   * Draw current frame onto an external canvas context (world/tile maps).
   * Pivot (0.5, 1.0) aligns feet to anchorX/anchorY (tile bottom-center).
   *
   * @param {CanvasRenderingContext2D} ctx
   * @param {number} anchorX - Screen X for pivot point (e.g. tile center)
   * @param {number} anchorY - Screen Y for pivot point (e.g. tile bottom)
   * @param {number} [scale=1] - Draw scale relative to grid cell size
   */
  drawFrame(ctx, anchorX, anchorY, scale = 1) {
    const { texture, atlas } = this;
    if (!texture || !atlas) return;

    const clip = atlas.animations[this.currentKey];
    if (!clip) return;

    const gridW = atlas.asset.gridSize.w;
    const gridH = atlas.asset.gridSize.h;
    const sx = (clip.startFrame + this.frameIndex) * gridW;
    const sy = clip.row * gridH;
    const pivot = this.getPivot();
    const dw = gridW * scale;
    const dh = gridH * scale;
    const dx = anchorX - dw * pivot.x;
    const dy = anchorY - dh * pivot.y;

    ctx.imageSmoothingEnabled = false;
    ctx.drawImage(texture, sx, sy, gridW, gridH, dx, dy, dw, dh);
  }

  _emitState() {
    if (this._onStateChange) {
      this._onStateChange(this.getDebugInfo());
    }
  }

  _loadImage(url) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.onload = () => resolve(img);
      img.onerror = () => reject(new Error(`Failed to load texture: ${url}`));
      img.src = url;
    });
  }

  _tick(ts) {
    if (!this._running) return;
    const dt = Math.min((ts - this._lastTs) / 1000, 0.1);
    this._lastTs = ts;

    this._frameCount++;
    if (ts - this._lastFpsTs >= 1000) {
      this._renderFps = this._frameCount;
      this._frameCount = 0;
      this._lastFpsTs = ts;
    }

    this._advanceAnimation(dt);
    this._render();
    this._rafId = requestAnimationFrame(this._tick);
  }

  _advanceAnimation(dt) {
    const clip = this.atlas?.animations?.[this.currentKey];
    if (!clip) return;

    const fps = clip.fps ?? this.config.defaultFps;
    this.frameAccumulator += dt;
    const frameDuration = 1 / fps;

    while (this.frameAccumulator >= frameDuration) {
      this.frameAccumulator -= frameDuration;
      const next = this.frameIndex + 1;

      if (next >= clip.length) {
        if (clip.loop) {
          this.frameIndex = 0;
        } else if (this.oneShot) {
          this.frameIndex = clip.length - 1;
          this._finishOneShot();
          break;
        } else {
          this.frameIndex = clip.length - 1;
        }
      } else {
        this.frameIndex = next;
      }
    }
  }

  _finishOneShot() {
    const cb = this.oneShotCallback;
    const restore = this.preOneShotKey;
    this.oneShot = null;
    this.oneShotCallback = null;
    this.preOneShotKey = null;

    if (restore && this.atlas?.animations?.[restore]) {
      this.currentKey = restore;
      this.frameIndex = 0;
      this.frameAccumulator = 0;
    }
    this._emitState();
    if (cb) cb();
  }

  _render() {
    const { ctx, canvas, texture, atlas } = this;
    if (!texture || !atlas) return;

    const clip = atlas.animations[this.currentKey];
    if (!clip) return;

    const gridW = atlas.asset.gridSize.w;
    const gridH = atlas.asset.gridSize.h;
    const sx = (clip.startFrame + this.frameIndex) * gridW;
    const sy = clip.row * gridH;
    const scale = this.config.scale;

    canvas.width = gridW * scale;
    canvas.height = gridH * scale;

    ctx.imageSmoothingEnabled = false;
    ctx.fillStyle = this.config.background;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(texture, sx, sy, gridW, gridH, 0, 0, gridW * scale, gridH * scale);
  }
}
