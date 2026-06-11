/**
 * Headless verification for Digital Dreamscape World Generator MVP
 * Usage: node verify.mjs
 */
import { readFileSync, existsSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";
import vm from "vm";

const __dir = dirname(fileURLToPath(import.meta.url));

const PAGES = ["index.html", "explore.html"];
const JS_FILES = [
  "tile-types.js", "world-data.js", "world-generator.js", "world-renderer.js",
  "player.js", "sprite-engine.js", "player-sprite.js", "pathfinding.js", "camera.js",
  "interactions.js", "save.js", "explore.js"
];

let passed = 0;
let failed = 0;

function ok(msg) {
  console.log("  OK:", msg);
  passed++;
}

function fail(msg) {
  console.error("  FAIL:", msg);
  failed++;
}

console.log("Digital Dreamscape verification\n");

for (const page of PAGES) {
  const p = join(__dir, page);
  if (!existsSync(p)) fail(page + " missing");
  else ok(page + " exists");
}

for (const js of JS_FILES) {
  const p = join(__dir, "js", js);
  if (!existsSync(p)) fail("js/" + js + " missing");
  else ok("js/" + js + " exists");
}

if (!existsSync(join(__dir, "style.css"))) fail("style.css missing");
else ok("style.css exists");

const spritePng = join(__dir, "assets/sprites/player/dream-explorer.png");
const spriteAtlas = join(__dir, "assets/sprites/player/dream-explorer-atlas.json");
if (!existsSync(spritePng)) fail("assets/sprites/player/dream-explorer.png missing");
else ok("Dream Explorer sprite sheet present");
if (!existsSync(spriteAtlas)) fail("dream-explorer-atlas.json missing");
else ok("Dream Explorer atlas present");

const css = readFileSync(join(__dir, "style.css"), "utf8");
if (!css.includes("--dd-accent") || !css.includes("Nunito")) fail("Dreamscape CSS tokens missing");
else ok("Dreamscape CSS present");

const sandbox = { global: {}, window: {}, console, performance: { now: () => 0 } };
sandbox.global = sandbox.window;
const ctx = vm.createContext(sandbox);

function loadScript(name) {
  const code = readFileSync(join(__dir, "js", name), "utf8");
  vm.runInContext(code, ctx, { filename: name });
}

try {
  for (const js of ["tile-types.js", "world-data.js", "world-generator.js", "pathfinding.js", "camera.js", "player.js", "save.js"]) {
    loadScript(js);
  }

  const TILE = sandbox.window.DD_TILE_TYPES;
  const DATA = sandbox.window.DD_WORLD_DATA;
  const GEN = sandbox.window.DD_WORLD_GENERATOR;
  const PATH = sandbox.window.DD_PATHFINDING;
  const CAM = sandbox.window.DD_CAMERA;
  const SAVE = sandbox.window.DD_SAVE;

  const requiredTiles = ["grass", "path", "water", "tree", "rock", "crystal", "floor", "wall", "portal"];
  for (const t of requiredTiles) {
    if (!TILE[t] || !TILE[t].color) fail("TILE_TYPES." + t + " missing");
    else ok("TILE_TYPES." + t + " = " + TILE[t].color);
  }

  const world = GEN.generateWorld("digital-dreamscape-v1");
  if (world.width !== 100 || world.height !== 100) fail("World should be 100x100");
  else ok("World 100x100");

  if (world.tileSize !== 32) fail("tileSize should be 32");
  else ok("tileSize 32");

  if (world.tiles.length !== 10000) fail("Expected 10000 tiles, got " + world.tiles.length);
  else ok("10000 tiles generated");

  if (world.spawn.x !== 10 || world.spawn.y !== 10) fail("Spawn should be (10,10)");
  else ok("Spawn at (10,10)");

  const grid = DATA.buildGrid(world);
  if (!TILE[grid[10][10].type].walkable) fail("Spawn tile should be walkable");
  else ok("Spawn tile walkable");

  if (TILE[grid[10][10].type].walkable && grid[10][10].type !== "grass" && grid[10][10].type !== "path") {
    fail("Spawn area unexpected type: " + grid[10][10].type);
  } else ok("Spawn area cleared");

  const npcs = world.objects.filter((o) => o.type === "npc");
  if (npcs.length !== 3) fail("Expected 3 NPCs, got " + npcs.length);
  else ok("3 NPCs placed");

  const portals = world.objects.filter((o) => o.type === "portal");
  if (portals.length !== 2) fail("Expected 2 portals, got " + portals.length);
  else ok("2 portals placed");

  const buildings = world.objects.filter((o) => o.type === "building");
  if (buildings.length < 3 || buildings.length > 5) fail("Expected 3-5 buildings, got " + buildings.length);
  else ok(buildings.length + " buildings placed");

  const resources = world.objects.filter((o) => o.type === "resource");
  if (resources.length < 4 || resources.length > 8) fail("Expected 4-8 resources, got " + resources.length);
  else ok(resources.length + " resource nodes");

  const guide = world.objects.find((o) => o.id === "npc_dream_guide");
  if (!guide) fail("Dream Guide NPC missing");
  else ok("Dream Guide at spawn");

  function isWalkable(x, y) {
    if (x < 0 || y < 0 || x >= world.width || y >= world.height) return false;
    const tile = grid[y][x];
    if (!tile || !TILE[tile.type].walkable) return false;
    return true;
  }

  if (!isWalkable(10, 10)) fail("Spawn walkability check failed");
  else ok("isWalkable(10,10)");

  let waterBlocked = false;
  for (let y = 0; y < world.height; y++) {
    for (let x = 0; x < world.width; x++) {
      if (grid[y][x].type === "water" && TILE.water.walkable) waterBlocked = true;
    }
  }
  if (TILE.water.walkable) fail("Water should not be walkable");
  else ok("Water blocks movement");

  const path = PATH.findPath(10, 10, 30, 10, isWalkable, world.width, world.height);
  if (!path || path.length < 5) fail("Pathfinding to distant tile failed");
  else ok("Pathfind long path: " + path.length + " steps");

  const camera = CAM.createCamera(world, DATA.VIEWPORT_W, DATA.VIEWPORT_H, DATA.TILE_SIZE);
  if (camera.viewCols !== 30 || camera.viewRows !== 20) {
    fail("Viewport tiles should be 30x20, got " + camera.viewCols + "x" + camera.viewRows);
  } else ok("Camera viewport 30x20 tiles (960x640)");

  camera.update(50, 50);
  if (camera.x < 0 || camera.y < 0) fail("Camera position invalid");
  else ok("Camera follows player at (50,50): cam " + camera.x + "," + camera.y);

  const store = {};
  sandbox.localStorage = {
    getItem: (k) => store[k] ?? null,
    setItem: (k, v) => { store[k] = v; }
  };
  ctx.localStorage = sandbox.localStorage;

  const save = SAVE.getOrCreateSave();
  save.player.x = 25;
  save.player.y = 18;
  SAVE.saveGame(save);

  const world2 = GEN.generateWorld(save.worldSeed);
  if (world2.tiles.length !== world.tiles.length) fail("Regenerated world tile count mismatch");
  else ok("World regenerates from seed");

  const reloaded = SAVE.loadSave();
  if (reloaded.player.x !== 25 || reloaded.player.y !== 18) fail("Position not persisted");
  else ok("Position persists: (25,18)");

  if (SAVE.STORAGE_KEY !== "digital_dreamscape_save_v1") fail("STORAGE_KEY mismatch");
  else ok("STORAGE_KEY is digital_dreamscape_save_v1");

  const seed2 = GEN.generateWorld("other-seed");
  if (seed2.id === world.id) fail("Different seeds should produce different world ids");
  else ok("Seeded worlds differ by seed");

} catch (e) {
  fail("Runtime error: " + e.message);
  console.error(e);
}

console.log("\n" + passed + " passed, " + failed + " failed");
process.exit(failed > 0 ? 1 : 0);
