import fs from "node:fs";
import path from "node:path";

const root = process.cwd();
const siteRoot = path.join(root, "sites/production/websites/digitaldreamscape.site");
const files = {
  html: path.join(siteRoot, "explore.html"),
  css: path.join(siteRoot, "style.css"),
  renderer: path.join(siteRoot, "js/world-renderer.js"),
  tactical: path.join(siteRoot, "js/tactical-graphics.js"),
  terrainAtlas: path.join(siteRoot, "js/terrain-atlas.js"),
  terrainManifest: path.join(siteRoot, "assets/terrain/terrain-atlas-manifest.json"),
  spriteAnimator: path.join(siteRoot, "js/sprite-sheet-animator.js"),
  spriteSheet: path.join(siteRoot, "assets/sprites/dreamblade-cadet-spritesheet.svg"),
  explore: path.join(siteRoot, "js/explore.js"),
  avatar: path.join(siteRoot, "js/avatar.js"),
  camera: path.join(siteRoot, "js/camera.js"),
  player: path.join(siteRoot, "js/player.js"),
  task: path.join(root, "runtime/tasks/digital_dreamscape_anime_tactical_graphics_001.yaml"),
};

const terrainAssetFiles = [
  "assets/terrain/tiles/grass-tile.svg",
  "assets/terrain/tiles/moss-grass-tile.svg",
  "assets/terrain/tiles/stone-path-tile.svg",
  "assets/terrain/tiles/dirt-path-tile.svg",
  "assets/terrain/tiles/cracked-stone-tile.svg",
  "assets/terrain/tiles/ruin-tile.svg",
  "assets/terrain/tiles/water-tile.svg",
  "assets/terrain/props/stone-ruin-wall.svg",
  "assets/terrain/props/pine-tree.svg",
  "assets/terrain/props/crystal-encounter-gate.svg",
  "assets/terrain/props/waypoint-pedestal.svg",
].map((relativePath) => path.join(siteRoot, relativePath));

function read(file) {
  return fs.readFileSync(file, "utf8");
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function includesAll(content, needles, label) {
  needles.forEach((needle) => {
    assert(content.includes(needle), `${label} is missing ${needle}`);
  });
}

const html = read(files.html);
const css = read(files.css);
const renderer = read(files.renderer);
const tactical = read(files.tactical);
const terrainAtlas = read(files.terrainAtlas);
const terrainManifest = read(files.terrainManifest);
const spriteAnimator = read(files.spriteAnimator);
const spriteSheet = read(files.spriteSheet);
const explore = read(files.explore);
const avatar = read(files.avatar);
const camera = read(files.camera);
const player = read(files.player);
const task = read(files.task);

includesAll(css, [
  "--dd-bg-night",
  "--dd-blue-move",
  "--dd-red-danger",
  "--dd-gold-objective",
  "--dd-magic-violet",
  "--dd-panel-dark",
  "--dd-panel-glass",
  "--dd-hp-green",
  "--dd-outline-ink",
  "--dd-neon-cyan",
  "--dd-neon-magenta",
  "--dd-deep-space",
  "--dd-holo-line",
], "style tokens");

includesAll(css, [
  "@media (max-width: 760px)",
  "@media (max-width: 430px)",
  "position: relative",
  "visual-composition-bounded-canvas",
  "height: clamp(270px, 46svh, 430px)",
  "height: clamp(238px, 42svh, 360px)",
  "env(safe-area-inset-bottom)",
], "mobile tactical layout");

includesAll(css, [
  ".combat-hud",
  "min-height: 6.1rem",
  ".unit-status-card",
  "min-height: 4.75rem",
  ".battle-command",
  ".combat-preview-panel",
  "min-height: 8.25rem",
], "visible non-zero HUD panels");

includesAll(html, [
  'id="combat-hud"',
  'id="combat-preview-panel"',
  'id="unit-status-card"',
  'class="battle-command battle-command--attack"',
  'class="battle-command"',
  'class="battle-command battle-command--skill"',
], "combat HUD markup");

includesAll(renderer, [
  "TACTICAL_TILE_STYLES",
  "movementTiles",
  "dangerTiles",
  "objectiveTiles",
  "drawTacticalOverlay",
  "drawAtlasTerrainTile",
  "drawAtlasProp",
  "drawAtlasTileFinish",
  "collectVisibleRenderables",
  "sortRenderablesByY",
  "tileNoise",
  "shadowBlur",
  "FUTURE_ISO",
  "drawFuturisticIsoFacet",
  "mobileScale",
  "roundRect",
], "canvas tactical overlay");

includesAll(tactical, [
  "createTacticalGraphicsState",
  "movementTiles",
  "dangerTiles",
  "objectiveTiles",
  "battlePreview",
  "inBattle",
  "mode: inBattle ? \"battle\" : \"exploration\"",
  "movementTiles: inBattle ? movementTiles(world, player) : []",
  "dangerTiles: inBattle ? dangerTiles(world) : []",
], "tactical graphics state");

includesAll(terrainAtlas, [
  "TERRAIN_ATLAS_MANIFEST_URL",
  "drawAtlasTerrainTile",
  "drawAtlasProp",
  "getTerrainAtlasStatus",
  "loadTerrainAtlasManifest",
  "preloadTerrainAtlas",
], "terrain atlas loader");

includesAll(terrainManifest, [
  "digital_dreamscape_premium_terrain_atlas_v1",
  "grass-tile.svg",
  "moss-grass-tile.svg",
  "stone-path-tile.svg",
  "dirt-path-tile.svg",
  "cracked-stone-tile.svg",
  "ruin-tile.svg",
  "water-tile.svg",
  "stone-ruin-wall.svg",
  "pine-tree.svg",
  "crystal-encounter-gate.svg",
  "waypoint-pedestal.svg",
], "terrain atlas manifest");

terrainAssetFiles.forEach((file) => {
  assert(fs.existsSync(file), `required terrain asset missing: ${path.relative(root, file)}`);
  includesAll(read(file), ["<svg"], `terrain asset ${path.basename(file)}`);
});

includesAll(spriteAnimator, [
  "drawSpriteSheetAvatar",
  "getSpriteSheetAnimatorStatus",
  "DEFAULT_DIRECTION_ROWS",
  "frameWidth",
  "frameHeight",
], "sprite sheet animator");

includesAll(spriteSheet, [
  "<svg",
  "dreamblade",
  "viewBox=\"0 0 256 256\"",
  "href=\"#south\"",
  "href=\"#north\"",
], "sample sprite sheet asset");

includesAll(explore, [
  "createTacticalGraphicsState",
  "updateCombatHud",
  "preview-attacker",
  "preview-defender",
  "setTransform",
  "spriteSheetStatus",
  "terrainAtlasStatus",
  "visualCompositionStatus",
  "explorationRangeHidden",
  "actionButtonCount",
  "combatHudHeight",
  "unitStatusHeight",
  "battlePreviewHeight",
], "explore tactical wiring");

includesAll(camera, [
  "pixelRatio",
  "devicePixelRatio",
  "backingWidth",
], "high-DPI tactical canvas");

includesAll(avatar, [
  "drawSpriteSheetAvatar",
  "hairHighlight",
  "trim",
  "shadowBlur",
], "toon avatar pass");

includesAll(player, [
  "sprite_sheet_with_toon_fallback",
  "dreamblade-cadet-spritesheet.svg",
  "frameWidth",
  "animations",
  "scale: .96",
  "Dreamblade Cadet",
  "combat",
  "maxHp",
], "player combat metadata");

includesAll(task, [
  "digital_dreamscape_anime_tactical_graphics_001",
  "blue movement grid",
  "red danger grid",
  "bottom combat HUD",
], "runtime task");

const restrictedNames = [
  ["Fire", " Emblem"].join(""),
  ["Nin", "tendo"].join(""),
  ["Eng", "age"].join(""),
  ["Ale", "ar"].join(""),
];

Object.entries(files).forEach(([label, file]) => {
  const content = read(file);
  restrictedNames.forEach((name) => {
    assert(!content.includes(name), `${label} contains restricted reference name: ${name}`);
  });
});

terrainAssetFiles.forEach((file) => {
  const content = read(file);
  restrictedNames.forEach((name) => {
    assert(!content.includes(name), `${path.basename(file)} contains restricted reference name: ${name}`);
  });
});

console.log("Digital Dreamscape anime tactical graphics smoke check passed.");
