import fs from "node:fs";
import path from "node:path";

const root = process.cwd();
const siteRoot = path.join(root, "sites/production/websites/digitaldreamscape.site");
const files = {
  html: path.join(siteRoot, "explore.html"),
  css: path.join(siteRoot, "style.css"),
  renderer: path.join(siteRoot, "js/world-renderer.js"),
  tactical: path.join(siteRoot, "js/tactical-graphics.js"),
  spriteAnimator: path.join(siteRoot, "js/sprite-sheet-animator.js"),
  spriteSheet: path.join(siteRoot, "assets/sprites/dreamblade-cadet-spritesheet.svg"),
  explore: path.join(siteRoot, "js/explore.js"),
  avatar: path.join(siteRoot, "js/avatar.js"),
  camera: path.join(siteRoot, "js/camera.js"),
  player: path.join(siteRoot, "js/player.js"),
  task: path.join(root, "runtime/tasks/digital_dreamscape_anime_tactical_graphics_001.yaml"),
};

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
  "height: 58vh",
], "mobile tactical layout");

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
  "tileNoise",
  "shadowBlur",
  "FUTURE_ISO",
  "drawFuturisticIsoFacet",
], "canvas tactical overlay");

includesAll(tactical, [
  "createTacticalGraphicsState",
  "movementTiles",
  "dangerTiles",
  "objectiveTiles",
  "battlePreview",
], "tactical graphics state");

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

console.log("Digital Dreamscape anime tactical graphics smoke check passed.");
