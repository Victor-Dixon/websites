/* Digital Dreamscape — shared constants and helpers */
(function (global) {
  "use strict";

  var VIEWPORT_W = 960;
  var VIEWPORT_H = 640;
  var TILE_SIZE = 32;
  var STEP_MS = 140;
  var DEFAULT_SEED = "digital-dreamscape-v1";

  function hashString(str) {
    var h = 2166136261;
    for (var i = 0; i < str.length; i++) {
      h ^= str.charCodeAt(i);
      h = Math.imul(h, 16777619);
    }
    return h >>> 0;
  }

  function mulberry32(seed) {
    return function () {
      seed |= 0;
      seed = (seed + 0x6d2b79f5) | 0;
      var t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
      t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
      return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
  }

  function createRng(seedStr) {
    return mulberry32(hashString(seedStr));
  }

  function idx(x, y, cols) {
    return y * cols + x;
  }

  function buildGrid(world) {
    var grid = [];
    var y;
    for (y = 0; y < world.height; y++) {
      grid.push(new Array(world.width));
    }
    world.tiles.forEach(function (t) {
      grid[t.y][t.x] = t;
    });
    return grid;
  }

  function tileAtGrid(grid, x, y) {
    if (!grid || y < 0 || x < 0 || y >= grid.length || x >= grid[0].length) return null;
    return grid[y][x];
  }

  function objectAt(world, x, y) {
    for (var i = 0; i < world.objects.length; i++) {
      var o = world.objects[i];
      if (o.x === x && o.y === y) return o;
    }
    return null;
  }

  function buildingAt(world, x, y) {
    for (var i = 0; i < world.objects.length; i++) {
      var o = world.objects[i];
      if (o.type !== "building") continue;
      if (x >= o.x && x < o.x + o.width && y >= o.y && y < o.y + o.height) return o;
    }
    return null;
  }

  global.DD_WORLD_DATA = {
    VIEWPORT_W: VIEWPORT_W,
    VIEWPORT_H: VIEWPORT_H,
    TILE_SIZE: TILE_SIZE,
    STEP_MS: STEP_MS,
    DEFAULT_SEED: DEFAULT_SEED,
    hashString: hashString,
    createRng: createRng,
    idx: idx,
    buildGrid: buildGrid,
    tileAtGrid: tileAtGrid,
    objectAt: objectAt,
    buildingAt: buildingAt
  };
})(typeof window !== "undefined" ? window : global);
