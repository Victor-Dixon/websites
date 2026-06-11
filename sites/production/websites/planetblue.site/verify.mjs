/**
 * Headless verification for Planet Blue (run with Node 18+)
 * Usage: node verify.mjs
 */
import { readFileSync, existsSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";

const __dir = dirname(fileURLToPath(import.meta.url));

const PAGES = ["index.html", "world.html", "map.html", "battle.html", "character.html"];
const JS_FILES = [
  "data.js", "world.js", "save.js", "pathfinding.js", "combat.js",
  "abilities.js", "ai.js", "grid.js", "battle.js", "map.js", "character.js",
  "sprites.js", "overworld.js"
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

const css = readFileSync(join(__dir, "style.css"), "utf8");
if (!css.includes("--gold") || !css.includes("Georgia")) fail("OSRS-style CSS tokens missing");
else ok("OSRS-style CSS present");

/* Load game modules in sandbox */
import vm from "vm";

const sandbox = { global: {}, window: {}, console };
sandbox.global = sandbox.window;
const ctx = vm.createContext(sandbox);

function loadScript(name) {
  const code = readFileSync(join(__dir, "js", name), "utf8");
  vm.runInContext(code, ctx, { filename: name });
}

try {
  loadScript("data.js");
  loadScript("world.js");
  loadScript("save.js");
  loadScript("pathfinding.js");
  loadScript("combat.js");
  loadScript("abilities.js");
  loadScript("ai.js");

  const DATA = sandbox.window.PLANET_BLUE_DATA;
  const SAVE = sandbox.window.PLANET_BLUE_SAVE;
  const WORLD = sandbox.window.PLANET_BLUE_WORLD;
  const PATH = sandbox.window.PLANET_BLUE_PATH;
  const COMBAT = sandbox.window.PLANET_BLUE_COMBAT;
  const AI = sandbox.window.PLANET_BLUE_AI;

  if (!DATA.RACES.human) fail("Human race missing");
  else ok("Human race defined");

  if (!DATA.ZONES.landing_bay) fail("Zone landing_bay missing");
  else ok("Zone landing_bay defined");

  if (!DATA.MORAL_CHOICES.first_landing_pre) fail("Moral choice hooks missing");
  else ok("Moral choice hooks defined");

  if (!DATA.CLASSES.fire) fail("Fire class missing");
  else ok("Fire class defined");

  if (!DATA.CLASSES.assassin) fail("Assassin class missing from data.js");
  else ok("Assassin class defined");

  if (!DATA.CLASSES.mage) fail("Mage class missing from data.js");
  else ok("Mage class defined");

  if (!DATA.RACES.beastmen) fail("Beastmen race missing from data.js");
  else ok("Beastmen race defined");

  const chrisPath = join(__dir, "data", "classes", "chris_classes.json");
  if (!existsSync(chrisPath)) fail("data/classes/chris_classes.json missing");
  else {
    const chrisClasses = JSON.parse(readFileSync(chrisPath, "utf8"));
    if (!Array.isArray(chrisClasses) || chrisClasses.length !== 2) {
      fail("chris_classes.json must be an array of 2 classes");
    } else {
      ok("chris_classes.json parses");
    }
  }

  const beastmenPath = join(__dir, "data", "races", "beastmen.json");
  if (!existsSync(beastmenPath)) fail("data/races/beastmen.json missing");
  else {
    const beastmen = JSON.parse(readFileSync(beastmenPath, "utf8"));
    if (beastmen.id !== "beastmen") fail("beastmen.json incomplete");
    else ok("beastmen.json parses");
  }

  const stats = DATA.computeStats("human", "fire");
  if (stats.hp < 10 || stats.atk < 1) fail("Invalid computed stats");
  else ok("Stats compute: HP=" + stats.hp + " ATK=" + stats.atk);

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

  const loaded = SAVE.loadSave();
  if (loaded.version !== 2) fail("Save version should be 2, got " + loaded.version);
  else ok("Save version 2");

  if (!loaded.world || !loaded.world.zones.landing_bay) fail("World state not initialized");
  else ok("World state initialized");

  if (!loaded.morality || loaded.morality.alignment !== "neutral") fail("Morality not initialized");
  else ok("Morality axis initialized");

  if (!loaded.nemesis || !Array.isArray(loaded.nemesis.registry)) fail("Nemesis registry missing");
  else ok("Nemesis registry initialized");

  WORLD.applyMoralityDelta(loaded, 20, "Test good act", "landing_bay", "test_good");
  if (loaded.morality.score !== 20) fail("Morality delta failed");
  else ok("Morality delta +20");

  if (loaded.morality.alignment !== "neutral") fail("Alignment should stay neutral at +20");
  else ok("Alignment neutral at +20");

  const beforeSafety = loaded.world.zones.landing_bay.safety;
  WORLD.applyMissionOutcome(loaded, "first_landing", "win");
  if (loaded.world.zones.landing_bay.safety <= beforeSafety) fail("Zone safety should rise on win");
  else ok("Zone influence updates on victory");

  WORLD.applyMissionOutcome(loaded, "first_landing", "lose");
  if (loaded.world.zones.landing_bay.threat < 45) fail("Zone threat should rise on defeat");
  else ok("Zone influence updates on defeat");

  const nem = WORLD.registerNemesis(loaded, { type: "scout_drone", nemesisKills: 1 }, "landing_bay", "basic_attack");
  if (!nem.displayName || loaded.nemesis.registry.length !== 1) fail("Nemesis registration failed");
  else ok("Nemesis registered: " + nem.displayName);

  if (WORLD.zoneStatus(70) !== "safe") fail("Zone status safe threshold wrong");
  else ok("Zone status thresholds");

  const mission = DATA.MISSIONS.first_landing;
  SAVE.completeMission("first_landing", mission.rewards);
  const after = SAVE.loadSave();

  if (after.missions.first_landing !== "completed") fail("Mission not completed");
  else ok("Mission first_landing completed");

  if (after.missions.deep_caverns !== "unlocked") fail("deep_caverns should unlock after first_landing");
  else ok("Mission deep_caverns unlocked after first_landing");

  if (after.missions.sky_spire !== "locked") fail("sky_spire should stay locked until deep_caverns");
  else ok("Mission sky_spire still locked");

  if (after.character.xp !== 50) fail("XP should be 50, got " + after.character.xp);
  else ok("XP reward 50");

  const freshTab = SAVE.loadSave();
  if (freshTab.missions.first_landing !== "completed" || freshTab.missions.deep_caverns !== "unlocked") {
    fail("Save persistence lost on reload");
  } else ok("Save persists across reload (new tab)");

  if (SAVE.STORAGE_KEY !== "planet-blue-save") fail("STORAGE_KEY mismatch");
  else ok("STORAGE_KEY is planet-blue-save");

  store[SAVE.STORAGE_KEY] = JSON.stringify({
    version: 2,
    profileCreated: true,
    character: { ...DATA.DEFAULT_CHARACTER, xp: 50, currency: 25 },
    missions: { first_landing: "completed", deep_caverns: "locked", sky_spire: "locked" },
    world: after.world,
    morality: after.morality,
    nemesis: after.nemesis,
    quests: after.quests
  });
  const repaired = SAVE.loadSave();
  if (repaired.missions.deep_caverns !== "unlocked") fail("migrate should repair stale unlock chain");
  else ok("Stale save unlock chain repaired on load");

  SAVE.completeMission("deep_caverns", DATA.MISSIONS.deep_caverns.rewards);
  const chain = SAVE.loadSave();
  if (chain.missions.deep_caverns !== "completed" || chain.missions.sky_spire !== "unlocked") {
    fail("sky_spire should unlock after deep_caverns");
  } else ok("Mission sky_spire unlocked after deep_caverns");

  const unit = { x: 1, y: 4, move: 3 };
  const terrain = [];
  for (let y = 0; y < 6; y++) {
    for (let x = 0; x < 8; x++) {
      const ch = mission.mapLayout[y][x];
      terrain.push(ch === "G" ? DATA.TERRAIN.GRASS : DATA.TERRAIN.ROCK);
    }
  }
  const reachable = PATH.reachableTiles(unit, terrain, [{ ...unit, id: "p", team: "player", hp: 30 }], 8, 6, DATA.TERRAIN.GRASS);
  if (reachable.length < 3) fail("BFS reachable too small: " + reachable.length);
  else ok("BFS reachable tiles: " + reachable.length);

  const res = COMBAT.resolveAttack({ atk: 14, def: 0 }, { hp: 20, def: 2 });
  if (res.damage < 1) fail("Combat damage invalid");
  else ok("Combat damage: " + res.damage);

  const enemy = { id: "e0", team: "enemy", x: 6, y: 1, hp: 16, atk: 8, move: 3, range: 1, label: "Scout" };
  const player = { id: "p0", team: "player", x: 1, y: 4, hp: 30, atk: 14, def: 2, move: 3, range: 2, label: "Hero" };
  const aiRes = AI.enemyTurn(enemy, { terrain, units: [player, enemy], phase: "enemy" }, 8, 6, DATA.TERRAIN.GRASS, null);
  if (!aiRes) fail("AI returned null");
  else ok("Enemy AI turn executed");

  loadScript("sprites.js");
  loadScript("overworld.js");
  const OW = sandbox.window.PLANET_BLUE_OVERWORLD;
  const SPRITES = sandbox.window.PLANET_BLUE_SPRITES;

  if (!OW || OW.COLS !== 20 || OW.ROWS !== 15) fail("Overworld dimensions invalid");
  else ok("Overworld map 20x15");

  if (!OW.isWalkable(10, 11)) fail("Hub spawn tile should be walkable");
  else ok("Hub spawn tile walkable");

  if (OW.isWalkable(0, 0)) fail("Tree border should block movement");
  else ok("Tree border blocks movement");

  const path = PATH.findPath(10, 11, 7, 14, OW.isWalkable, OW.COLS, OW.ROWS);
  if (!path || path.length < 3) fail("Overworld path to First Landing too short");
  else ok("Pathfind to First Landing: " + path.length + " steps");

  WORLD.setOverworldPosition(loaded, 12, 13);
  if (loaded.world.overworld.x !== 12 || loaded.world.overworld.y !== 13) fail("Overworld position not saved");
  else ok("Overworld position persists in save");

  SAVE.saveGame(loaded);
  const reloaded = SAVE.loadSave();
  if (reloaded.world.overworld.x !== 12) fail("Overworld position lost on reload");
  else ok("Overworld position survives reload");

  const landing = OW.interactableAt(7, 14);
  if (!landing || landing.missionId !== "first_landing") fail("First Landing interactable missing");
  else ok("First Landing mission trigger at (7,14)");

  if (!SPRITES || typeof SPRITES.drawPlayer !== "function") fail("Sprite renderer missing");
  else ok("Sprite renderer defined");

} catch (e) {
  fail("Runtime error: " + e.message);
  console.error(e);
}

console.log("\n" + passed + " passed, " + failed + " failed");
process.exit(failed > 0 ? 1 : 0);
