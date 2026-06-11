/**
 * Maps Grid Schema V1 rows to animation metadata JSON.
 * @see README.md for the full grid layout.
 */

export const GRID_SCHEMA_V1 = {
  version: '1.0',
  rows: {
    idle: { row: 0, directions: ['up', 'down', 'left', 'right'], frames: 4, loop: true, fps: 8 },
    walk: { baseRow: 1, directions: ['up', 'down', 'left', 'right'], frames: 4, loop: true, fps: 10 },
    run: { baseRow: 5, directions: ['up', 'down', 'left', 'right'], frames: 4, loop: true, fps: 12 },
    interact: { row: 9, frames: 2, loop: false, fps: 10 },
    attack: { row: 10, frames: 2, loop: false, fps: 10 },
    magic: { row: 11, frames: 2, loop: false, fps: 10 },
    hurt: { row: 12, frames: 2, loop: false, fps: 10 },
  },
};

const DEFAULT_FPS = {
  idle: 8,
  walk: 10,
  run: 12,
  interact: 10,
  attack: 10,
  magic: 10,
  hurt: 10,
};

/**
 * Build animations object from Grid Schema V1.
 * Idle row 0: columns 0–3 are the four directions (one pose each, length 1).
 * Walk rows 1–4 and run rows 5–8: one row per direction, 4 frames each.
 *
 * @param {object} options
 * @param {number} [options.fps] - Override default fps for all clips
 * @param {Record<string, number>} [options.fpsByAction] - Per-action fps overrides
 * @returns {Record<string, { row: number, startFrame: number, length: number, loop: boolean, fps: number }>}
 */
export function buildAnimationsFromGrid(options = {}) {
  const globalFps = options.fps;
  const fpsByAction = options.fpsByAction || {};
  const animations = {};

  const idleFps = globalFps ?? fpsByAction.idle ?? DEFAULT_FPS.idle;
  for (let i = 0; i < 4; i++) {
    const dir = GRID_SCHEMA_V1.rows.idle.directions[i];
    animations[`idle_${dir}`] = {
      row: 0,
      startFrame: i,
      length: 1,
      loop: true,
      fps: idleFps,
    };
  }

  for (const [action, baseKey] of [
    ['walk', 'walk'],
    ['run', 'run'],
  ]) {
    const schema = GRID_SCHEMA_V1.rows[action];
    const fps = globalFps ?? fpsByAction[action] ?? DEFAULT_FPS[action];
    schema.directions.forEach((dir, i) => {
      animations[`${action}_${dir}`] = {
        row: schema.baseRow + i,
        startFrame: 0,
        length: schema.frames,
        loop: schema.loop,
        fps,
      };
    });
  }

  for (const key of ['interact', 'attack', 'magic', 'hurt']) {
    const schema = GRID_SCHEMA_V1.rows[key];
    const fps = globalFps ?? fpsByAction[key] ?? DEFAULT_FPS[key];
    animations[key] = {
      row: schema.row,
      startFrame: 0,
      length: schema.frames,
      loop: schema.loop,
      fps,
    };
  }

  return animations;
}

/**
 * Build a complete atlas JSON document.
 *
 * @param {object} asset
 * @param {string} asset.name
 * @param {string} asset.texture - URL or data URL
 * @param {number} [asset.gridW=64]
 * @param {number} [asset.gridH=64]
 * @param {string} [asset.version='1.0']
 * @param {object} [buildOptions] - Passed to buildAnimationsFromGrid
 */
export function buildAtlasJson(asset, buildOptions = {}) {
  const gridW = asset.gridW ?? asset.gridSize?.w ?? 64;
  const gridH = asset.gridH ?? asset.gridSize?.h ?? 64;
  const pivot = asset.pivot ?? { x: 0.5, y: 1.0 };
  return {
    asset: {
      name: asset.name,
      version: asset.version ?? '1.0',
      texture: asset.texture,
      gridSize: { w: gridW, h: gridH },
      pivot,
    },
    animations: buildAnimationsFromGrid(buildOptions),
  };
}

/**
 * Resolve animation key from action + direction.
 * @param {string} action - idle | walking | walk | running | run | attacking | attack | ...
 * @param {string} [direction] - up | down | left | right
 */
export function resolveAnimationKey(action, direction = 'down') {
  const normalized = normalizeAction(action);
  if (['interact', 'attack', 'magic', 'hurt'].includes(normalized)) {
    return normalized;
  }
  const dir = direction || 'down';
  return `${normalized}_${dir}`;
}

function normalizeAction(action) {
  const map = {
    walking: 'walk',
    running: 'run',
    attacking: 'attack',
    idle: 'idle',
    walk: 'walk',
    run: 'run',
    attack: 'attack',
    interact: 'interact',
    magic: 'magic',
    hurt: 'hurt',
  };
  return map[action] ?? action;
}
