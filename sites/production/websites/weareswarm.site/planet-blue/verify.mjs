/**
 * Headless verification for Planet Blue MVP (run with Node 18+)
 * Usage: node verify.mjs
 */
import { readFileSync, existsSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";
import { createRequire } from "module";
import vm from "vm";

const __dir = dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);

const PAGES = ["index.html", "map.html", "battle.html", "character.html"];
const JS_FILES = [
  "data.js", "save.js", "pathfinding.js", "combat.js",
  "abilities.js", "ai.js", "grid.js", "battle.js", "map.js", "character.js"
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

console.log("Planet Blue verification\n");

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

/* Load game modules in sandbox */
const sandbox = { global: {}, window: {}, console };
sandbox.global = sandbox.window;
const ctx = vm.createContext(sandbox);

function loadScript(name) {
  const code = readFileSync(join(__dir, "js", name), "utf8");
  vm.runInContext(code, ctx, { filename: name });
}

try {
  loadScript("data.js");
  loadScript("save.js");
  loadScript("pathfinding.js");
  loadScript("combat.js");
  loadScript("abilities.js");
  loadScript("ai.js");

  const DATA = sandbox.window.PLANET_BLUE_DATA;
  const SAVE = sandbox.window.PLANET_BLUE_SAVE;
  const PATH = sandbox.window.PLANET_BLUE_PATH;
  const COMBAT = sandbox.window.PLANET_BLUE_COMBAT;
  const AI = sandbox.window.PLANET_BLUE_AI;

  if (!DATA.RACES.human) fail("Human race missing");
  else ok("Human race defined");

  if (!DATA.CLASSES.fire) fail("Fire class missing");
  else ok("Fire class defined");

  const stats = DATA.computeStats("human", "fire");
  if (stats.hp < 10 || stats.atk < 1) fail("Invalid computed stats");
  else ok("Stats compute: HP=" + stats.hp + " ATK=" + stats.atk);

  /* Mock localStorage */
  const store = {};
  sandbox.localStorage = {
    getItem: (k) => store[k] ?? null,
    setItem: (k, v) => { store[k] = v; }
  };
  ctx.localStorage = sandbox.localStorage;

  const save = SAVE.resetSave();
  save.profileCreated = true;
  save.character = { ...DATA.DEFAULT_CHARACTER };
  SAVE.saveGame(save);

  const mission = DATA.MISSIONS.first_landing;
  SAVE.completeMission("first_landing", mission.rewards);
  const after = SAVE.loadSave();

  if (after.missions.first_landing !== "completed") fail("Mission not completed");
  else ok("Mission first_landing completed");

  if (after.character.xp !== 50) fail("XP should be 50, got " + after.character.xp);
  else ok("XP reward 50");

  if (after.character.currency !== 25) fail("Currency should be 25");
  else ok("Currency reward 25");

  /* Pathfinding smoke test */
  const unit = { x: 1, y: 4, move: 3 };
  const terrain = [];
  for (let y = 0; y < 6; y++) {
    for (let x = 0; x < 8; x++) {
      const ch = mission.mapLayout[y][x];
      terrain.push(ch === "G" ? DATA.TERRAIN.GRASS : DATA.TERRAIN.ROCK);
    }
  }
  const units = [{ ...unit, id: "p", team: "player", hp: 30 }];
  const reachable = PATH.reachableTiles(unit, terrain, units, 8, 6, DATA.TERRAIN.GRASS);
  if (reachable.length < 3) fail("BFS reachable too small: " + reachable.length);
  else ok("BFS reachable tiles: " + reachable.length);

  /* Combat smoke test */
  const atk = { atk: 14, def: 0 };
  const def = { hp: 20, def: 2 };
  const res = COMBAT.resolveAttack(atk, def);
  if (res.damage < 1) fail("Combat damage invalid");
  else ok("Combat damage: " + res.damage);

  /* AI smoke test */
  const enemy = { id: "e0", team: "enemy", x: 6, y: 1, hp: 16, atk: 8, move: 3, range: 1, label: "Scout" };
  const player = { id: "p0", team: "player", x: 1, y: 4, hp: 30, atk: 14, def: 2, move: 3, range: 2, label: "Hero" };
  const battleState = { terrain, units: [player, enemy], phase: "enemy" };
  const aiRes = AI.enemyTurn(enemy, battleState, 8, 6, DATA.TERRAIN.GRASS, null);
  if (!aiRes) fail("AI returned null");
  else ok("Enemy AI turn executed (moved=" + aiRes.moved + ", attacked=" + aiRes.attacked + ")");

} catch (e) {
  fail("Runtime error: " + e.message);
  console.error(e);
}

console.log("\n" + passed + " passed, " + failed + " failed");
process.exit(failed > 0 ? 1 : 0);
