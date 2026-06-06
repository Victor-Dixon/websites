/* xThunder Storm Siege — build structures, fight waves */

const COLS = 24;
const ROWS = 13;
const TILE = 40;
const OFFSET_X = 0;
const OFFSET_Y = (540 - ROWS * TILE) / 2;

const CORE = { x: 3, y: Math.floor(ROWS / 2), hp: 100, maxHp: 100 };

const BUILD = {
  wall:     { cost: 10,  hp: 90,  color: "#5a6478", label: "Wall" },
  turret:   { cost: 50,  hp: 60,  color: "#ffd34d", range: 5, dmg: 14, rate: 900, label: "Bolt Tower" },
  barracks: { cost: 75,  hp: 80,  color: "#7c5cff", label: "Storm Hall" },
  trap:     { cost: 25,  hp: 1,   color: "#4dd4ff", dmg: 45, label: "Shock Trap" },
};

const ENEMY_TYPES = {
  shade:  { hp: 30,  speed: 55,  dmg: 6,  reward: 8,  color: "#8b6cc1", r: 12 },
  brute:  { hp: 70,  speed: 38,  dmg: 12, reward: 14, color: "#c45c8a", r: 15 },
  wraith: { hp: 22,  speed: 88,  dmg: 5,  reward: 10, color: "#5ce0ff", r: 10 },
};

const BOSS_TYPES = {
  storm_titan: {
    hp: 420, speed: 26, dmg: 22, reward: 100, color: "#ff4466", r: 28, name: "Storm Titan",
  },
  thunder_lich: {
    hp: 720, speed: 20, dmg: 38, reward: 180, color: "#ff1133", r: 34, name: "Thunder Lich",
  },
};

const BOSS_INTERVAL = 5;
const MAX_WAVES = 10;
const START_GOLD = 150;
const MAX_TRAPS_PER_ROUND = 10;

const canvas = document.getElementById("game-canvas");
const ctx = canvas.getContext("2d");

const state = {
  phase: "build",
  wave: 0,
  gold: START_GOLD,
  grid: [],
  enemies: [],
  allies: [],
  projectiles: [],
  effects: [],
  selectedTool: "wall",
  spawnQueue: [],
  spawnTimer: 0,
  fightActive: false,
  hover: null,
  coreHp: CORE.maxHp,
  bossPrep: false,
  trapsPlacedThisRound: 0,
};

function normalizeGold() {
  state.gold = Math.max(0, Math.floor(Number(state.gold) || 0));
  return state.gold;
}

function canAfford(cost) {
  return normalizeGold() >= cost;
}

function spendGold(cost) {
  if (!canAfford(cost)) return false;
  state.gold = Math.max(0, state.gold - cost);
  return true;
}

function addGold(amount) {
  state.gold = Math.max(0, normalizeGold() + amount);
}

function isBossWave(wave) {
  return wave > 0 && wave % BOSS_INTERVAL === 0;
}

function nextWaveNumber() {
  return state.wave + 1;
}

function getBossKey(wave) {
  return wave >= 10 ? "thunder_lich" : "storm_titan";
}

function initGrid() {
  state.grid = Array.from({ length: ROWS }, () => Array(COLS).fill(null));
}

function tileAt(px, py) {
  const x = Math.floor((px - OFFSET_X) / TILE);
  const y = Math.floor((py - OFFSET_Y) / TILE);
  if (x < 0 || y < 0 || x >= COLS || y >= ROWS) return null;
  return { x, y };
}

function isCoreTile(x, y) {
  return x === CORE.x && y === CORE.y;
}

function isBlocked(x, y) {
  if (x < 0 || y < 0 || x >= COLS || y >= ROWS) return true;
  if (isCoreTile(x, y)) return true;
  const cell = state.grid[y][x];
  return cell && (cell.type === "wall" || cell.type === "turret" || cell.type === "barracks");
}

function countArmedTraps() {
  let total = 0;
  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      if (state.grid[y][x]?.type === "trap") total += 1;
    }
  }
  return total;
}

function isTrapLimitReached() {
  return state.trapsPlacedThisRound >= MAX_TRAPS_PER_ROUND
    || countArmedTraps() >= MAX_TRAPS_PER_ROUND;
}

function trapLimitHudCount() {
  return Math.max(countArmedTraps(), state.trapsPlacedThisRound);
}


function canPlace(type, x, y) {
  const def = BUILD[type];
  if (!def || isCoreTile(x, y)) return false;
  if (state.grid[y][x]) return false;
  if (!canAfford(def.cost)) return false;
  if (type === "trap" && isTrapLimitReached()) return false;
  return true;
}

function placeStructure(type, x, y) {
  if (!canPlace(type, x, y)) return false;
  const def = BUILD[type];
  if (!spendGold(def.cost)) return false;
  state.grid[y][x] = {
    type,
    x,
    y,
    hp: def.hp,
    maxHp: def.hp,
    cooldown: 0,
    spent: def.cost,
  };
  if (type === "trap") {
    state.trapsPlacedThisRound += 1;
  }
  addFeed("system", `Built ${def.label} at (${x},${y}).`);
  if (type === "trap" && isTrapLimitReached()) {
    addFeed("system", `Shock trap limit reached (${MAX_TRAPS_PER_ROUND}/${MAX_TRAPS_PER_ROUND}) for this round.`);
  }
  updateHUD();
  return true;
}

function removeStructure(x, y) {
  const cell = state.grid[y][x];
  if (!cell) return false;
  const refund = Math.floor((cell.spent || BUILD[cell.type]?.cost || 0) * 0.5);
  addGold(refund);
  state.grid[y][x] = null;
  addFeed("system", `Demolished ${BUILD[cell.type].label}. Refund ⚡${refund}.`);
  updateHUD();
  return true;
}

function bfsPath(sx, sy) {
  const key = (x, y) => `${x},${y}`;
  const goal = { x: CORE.x, y: CORE.y };
  const queue = [{ x: sx, y: sy, path: [] }];
  const seen = new Set([key(sx, sy)]);

  while (queue.length) {
    const node = queue.shift();
    if (node.x === goal.x && node.y === goal.y) return node.path;

    const dirs = [
      [1, 0], [-1, 0], [0, 1], [0, -1],
    ];
    for (const [dx, dy] of dirs) {
      const nx = node.x + dx;
      const ny = node.y + dy;
      const k = key(nx, ny);
      if (seen.has(k)) continue;
      if (nx < 0 || ny < 0 || nx >= COLS || ny >= ROWS) continue;
      if (isBlocked(nx, ny) && !(nx === goal.x && ny === goal.y)) continue;
      seen.add(k);
      queue.push({ x: nx, y: ny, path: [...node.path, { x: nx, y: ny }] });
    }
  }
  return null;
}

function spawnEnemy(type, row, opts = {}) {
  const def = ENEMY_TYPES[type];
  const scale = 1 + state.wave * 0.12;
  const path = bfsPath(COLS - 1, row) || [];
  state.enemies.push({
    type,
    boss: false,
    name: null,
    x: (COLS - 1) * TILE + TILE / 2,
    y: row * TILE + TILE / 2 + OFFSET_Y,
    hp: def.hp * scale,
    maxHp: def.hp * scale,
    speed: def.speed,
    dmg: def.dmg,
    reward: def.reward,
    color: def.color,
    r: def.r,
    path,
    pathIdx: 0,
    attackCd: 0,
    tx: COLS - 1,
    ty: row,
    ...opts,
  });
}

function spawnBoss(wave) {
  const key = getBossKey(wave);
  const def = BOSS_TYPES[key];
  const row = Math.floor(ROWS / 2);
  const scale = 1 + (wave / BOSS_INTERVAL) * 0.35;
  const path = bfsPath(COLS - 1, row) || [];
  state.enemies.push({
    type: key,
    boss: true,
    name: def.name,
    x: (COLS - 1) * TILE + TILE / 2,
    y: row * TILE + TILE / 2 + OFFSET_Y,
    hp: def.hp * scale,
    maxHp: def.hp * scale,
    speed: def.speed,
    dmg: def.dmg,
    reward: def.reward,
    color: def.color,
    r: def.r,
    path,
    pathIdx: 0,
    attackCd: 0,
    tx: COLS - 1,
    ty: row,
  });
  addFeed("combat", `☠ BOSS: ${def.name} has entered the storm!`);
  state.effects.push({
    kind: "burst",
    x: (COLS - 1) * TILE + TILE / 2,
    y: row * TILE + TILE / 2 + OFFSET_Y,
    life: 800,
    color: def.color,
  });
}

function buildSpawnQueue() {
  state.spawnQueue = [];
  const bossWave = isBossWave(state.wave);

  if (bossWave) {
    state.spawnQueue.push({ kind: "boss", delay: 1200 });
    const adds = 2 + Math.floor(state.wave / BOSS_INTERVAL);
    for (let i = 0; i < adds; i++) {
      const type = i % 2 === 0 ? "brute" : "wraith";
      state.spawnQueue.push({ kind: "enemy", type, row: 1 + (i % (ROWS - 2)), delay: 1800 + i * 600 });
    }
    return;
  }

  const count = 4 + state.wave * 2;
  for (let i = 0; i < count; i++) {
    let type = "shade";
    if (state.wave > 2 && i % 4 === 0) type = "brute";
    if (state.wave > 4 && i % 5 === 0) type = "wraith";
    if (state.wave > 7 && i % 3 === 0) type = "brute";
    const row = 1 + (i % (ROWS - 2));
    state.spawnQueue.push({ kind: "enemy", type, row, delay: i * 420 });
  }
}

function spawnWave() {
  state.wave += 1;
  state.phase = "fight";
  state.fightActive = true;
  state.bossPrep = false;
  state.trapsPlacedThisRound = 0;
  buildSpawnQueue();
  state.spawnTimer = 0;

  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      const cell = state.grid[y][x];
      if (cell?.type === "barracks" && cell.hp > 0) {
        spawnKnight(x, y);
      }
    }
  }

  if (isBossWave(state.wave)) {
    const bossName = BOSS_TYPES[getBossKey(state.wave)].name;
    addFeed("combat", `⚡ BOSS WAVE ${state.wave} — ${bossName} approaches!`);
  } else {
    addFeed("combat", `⚡ WAVE ${state.wave} — shadows incoming!`);
  }
  updateHUD();
}

function spawnKnight(bx, by) {
  state.allies.push({
    x: bx * TILE + TILE / 2,
    y: by * TILE + TILE / 2 + OFFSET_Y,
    hp: 50 + state.wave * 5,
    maxHp: 50 + state.wave * 5,
    dmg: 10 + state.wave,
    speed: 70,
    r: 11,
    attackCd: 0,
    homeX: bx,
    homeY: by,
  });
  addFeed("combat", "Storm knight deployed from hall!");
}

function startFight() {
  if (state.phase === "fight" && state.fightActive) return;
  if (state.wave >= MAX_WAVES && state.phase === "build") {
    showOverlay("Victory!", "Ten waves broken. The thunder holds!", restart);
    return;
  }
  spawnWave();
}

function enterBossPrep(nextWave) {
  state.bossPrep = true;
  const bossName = BOSS_TYPES[getBossKey(nextWave)].name;
  addFeed("system", `— PAUSE — Boss wave ${nextWave} next: ${bossName}.`);
  addFeed("system", "Organize your defenses, then hit Face the Boss when ready.");
}

function endWave() {
  state.fightActive = false;
  state.phase = "build";
  const bonus = 20 + state.wave * 15;
  addGold(bonus);
  addFeed("system", `Wave ${state.wave} cleared! Bonus ⚡${bonus}.`);
  updateHUD();

  if (state.wave >= MAX_WAVES) {
    state.phase = "won";
    showOverlay("Victory!", `You survived ${MAX_WAVES} waves. The storm is yours!`, restart, "Play Again");
    return;
  }

  const upcoming = nextWaveNumber();
  if (isBossWave(upcoming)) {
    enterBossPrep(upcoming);
    updateHUD();
  }
}

function dist(a, b) {
  return Math.hypot(a.x - b.x, a.y - b.y);
}

function findTarget(turret, rangePx) {
  let best = null;
  let bestD = Infinity;
  for (const e of state.enemies) {
    const d = dist(
      { x: turret.x * TILE + TILE / 2, y: turret.y * TILE + TILE / 2 + OFFSET_Y },
      e
    );
    if (d <= rangePx && d < bestD) {
      best = e;
      bestD = d;
    }
  }
  return best;
}

function fireBolt(tx, ty, target) {
  state.projectiles.push({
    x: tx * TILE + TILE / 2,
    y: ty * TILE + TILE / 2 + OFFSET_Y,
    tx: target.x,
    ty: target.y,
    target,
    dmg: BUILD.turret.dmg,
    speed: 520,
    life: 600,
  });
}

function damageEnemy(enemy, amount) {
  enemy.hp -= amount;
  state.effects.push({ kind: "hit", x: enemy.x, y: enemy.y, life: 200 });
  if (enemy.hp <= 0) {
    addGold(enemy.reward);
    if (enemy.boss) {
      addFeed("combat", `☠ ${enemy.name} defeated! +⚡${enemy.reward}`);
    } else {
      addFeed("combat", `Shadow fell! +⚡${enemy.reward}`);
    }
    state.enemies = state.enemies.filter((e) => e !== enemy);
    state.effects.push({ kind: "burst", x: enemy.x, y: enemy.y, life: 350, color: enemy.color });
    updateHUD();
  }
}

function damageCore(amount) {
  state.coreHp = Math.max(0, state.coreHp - amount);
  state.effects.push({
    kind: "burst",
    x: CORE.x * TILE + TILE / 2,
    y: CORE.y * TILE + TILE / 2 + OFFSET_Y,
    life: 400,
    color: "#ffd34d",
  });
  addFeed("combat", `Thunder Core hit! ${Math.ceil(state.coreHp)}% remaining.`);
  updateHUD();
  if (state.coreHp <= 0) {
    state.phase = "lost";
    state.fightActive = false;
    showOverlay(
      "Defeat",
      `The core has fallen on wave ${state.wave}. Rebuild and try again.`,
      restart,
      "Restart Game"
    );
  }
}

function triggerTrap(x, y, enemy) {
  const cell = state.grid[y][x];
  if (!cell || cell.type !== "trap") return;
  damageEnemy(enemy, BUILD.trap.dmg);
  state.grid[y][x] = null;
  state.effects.push({
    kind: "burst",
    x: x * TILE + TILE / 2,
    y: y * TILE + TILE / 2 + OFFSET_Y,
    life: 500,
    color: "#4dd4ff",
  });
  addFeed("combat", "Shock trap detonated!");
  updateHUD();
}

function updateStructures(dt) {
  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      const cell = state.grid[y][x];
      if (!cell || cell.type !== "turret" || cell.hp <= 0) continue;
      cell.cooldown = Math.max(0, cell.cooldown - dt);
      if (cell.cooldown > 0) continue;
      const target = findTarget(cell, BUILD.turret.range * TILE);
      if (target) {
        fireBolt(x, y, target);
        cell.cooldown = BUILD.turret.rate;
      }
    }
  }
}

function updateProjectiles(dt) {
  const step = dt / 1000;
  state.projectiles = state.projectiles.filter((p) => {
    p.life -= dt;
    const dx = p.tx - p.x;
    const dy = p.ty - p.y;
    const len = Math.hypot(dx, dy) || 1;
    const vx = (dx / len) * p.speed * step;
    const vy = (dy / len) * p.speed * step;
    p.x += vx;
    p.y += vy;
    if (Math.hypot(p.tx - p.x, p.ty - p.y) < 14) {
      if (p.target && state.enemies.includes(p.target)) {
        damageEnemy(p.target, p.dmg);
      }
      state.effects.push({ kind: "spark", x: p.x, y: p.y, life: 180 });
      return false;
    }
    return p.life > 0;
  });
}

function moveEnemy(e, dt) {
  const step = e.speed * (dt / 1000);
  if (e.path && e.pathIdx < e.path.length) {
    const next = e.path[e.pathIdx];
    const tx = next.x * TILE + TILE / 2;
    const ty = next.y * TILE + TILE / 2 + OFFSET_Y;
    const dx = tx - e.x;
    const dy = ty - e.y;
    const len = Math.hypot(dx, dy) || 1;
    if (len < step) {
      e.x = tx;
      e.y = ty;
      e.tx = next.x;
      e.ty = next.y;
      e.pathIdx += 1;
      const cell = state.grid[next.y]?.[next.x];
      if (cell?.type === "trap") triggerTrap(next.x, next.y, e);
    } else {
      e.x += (dx / len) * step;
      e.y += (dy / len) * step;
    }
    return;
  }

  const coreX = CORE.x * TILE + TILE / 2;
  const coreY = CORE.y * TILE + TILE / 2 + OFFSET_Y;
  const dx = coreX - e.x;
  const dy = coreY - e.y;
  if (Math.hypot(dx, dy) < TILE * 0.45) {
    e.attackCd -= dt;
    if (e.attackCd <= 0) {
      damageCore(e.dmg);
      e.attackCd = 900;
    }
    return;
  }
  const len = Math.hypot(dx, dy) || 1;
  e.x += (dx / len) * step;
  e.y += (dy / len) * step;
}

function updateEnemies(dt) {
  for (const e of [...state.enemies]) {
    moveEnemy(e, dt);
  }
}

function updateAllies(dt) {
  for (const a of state.allies) {
    let target = null;
    let bestD = Infinity;
    for (const e of state.enemies) {
      const d = dist(a, e);
      if (d < bestD) {
        bestD = d;
        target = e;
      }
    }
    if (!target) continue;

    if (bestD > TILE * 0.9) {
      const step = a.speed * (dt / 1000);
      const dx = target.x - a.x;
      const dy = target.y - a.y;
      const len = Math.hypot(dx, dy) || 1;
      a.x += (dx / len) * step;
      a.y += (dy / len) * step;
    } else {
      a.attackCd -= dt;
      if (a.attackCd <= 0) {
        damageEnemy(target, a.dmg);
        a.attackCd = 650;
        state.effects.push({ kind: "slash", x: target.x, y: target.y, life: 120 });
      }
    }
  }
  state.allies = state.allies.filter((a) => a.hp > 0);
}

function updateSpawns(dt) {
  if (!state.fightActive) return;
  state.spawnTimer += dt;
  state.spawnQueue = state.spawnQueue.filter((s) => {
    if (state.spawnTimer >= s.delay) {
      if (s.kind === "boss") spawnBoss(state.wave);
      else spawnEnemy(s.type, s.row);
      return false;
    }
    return true;
  });
  if (state.spawnQueue.length === 0 && state.enemies.length === 0) {
    endWave();
  }
}

function updateEffects(dt) {
  state.effects = state.effects.filter((fx) => {
    fx.life -= dt;
    return fx.life > 0;
  });
}

function drawGrid() {
  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      const px = OFFSET_X + x * TILE;
      const py = OFFSET_Y + y * TILE;
      const alt = (x + y) % 2 === 0;
      ctx.fillStyle = alt ? "rgba(255,255,255,.03)" : "rgba(0,0,0,.15)";
      ctx.fillRect(px, py, TILE, TILE);
      ctx.strokeStyle = "rgba(255,255,255,.04)";
      ctx.strokeRect(px, py, TILE, TILE);
    }
  }
}

function drawCore() {
  const px = OFFSET_X + CORE.x * TILE;
  const py = OFFSET_Y + CORE.y * TILE;
  const pulse = 0.5 + Math.sin(Date.now() / 300) * 0.15;
  const grad = ctx.createRadialGradient(px + TILE / 2, py + TILE / 2, 4, px + TILE / 2, py + TILE / 2, TILE * 0.7);
  grad.addColorStop(0, `rgba(255,211,77,${0.9 * pulse})`);
  grad.addColorStop(1, "rgba(124,92,255,0.2)");
  ctx.fillStyle = grad;
  ctx.beginPath();
  ctx.arc(px + TILE / 2, py + TILE / 2, TILE * 0.38, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = "#ffd34d";
  ctx.lineWidth = 2;
  ctx.stroke();

  const hpPct = state.coreHp / CORE.maxHp;
  ctx.fillStyle = "rgba(0,0,0,.5)";
  ctx.fillRect(px, py - 8, TILE, 5);
  ctx.fillStyle = hpPct > 0.35 ? "#5cff9a" : "#ff5c7a";
  ctx.fillRect(px, py - 8, TILE * hpPct, 5);
}

function drawStructure(cell) {
  const px = OFFSET_X + cell.x * TILE;
  const py = OFFSET_Y + cell.y * TILE;
  const def = BUILD[cell.type];
  const pad = 4;

  if (cell.type === "wall") {
    ctx.fillStyle = def.color;
    ctx.fillRect(px + pad, py + pad, TILE - pad * 2, TILE - pad * 2);
    ctx.strokeStyle = "#8892a8";
    ctx.strokeRect(px + pad, py + pad, TILE - pad * 2, TILE - pad * 2);
  } else if (cell.type === "turret") {
    ctx.fillStyle = "#2a2540";
    ctx.fillRect(px + pad, py + pad, TILE - pad * 2, TILE - pad * 2);
    ctx.fillStyle = def.color;
    ctx.beginPath();
    ctx.moveTo(px + TILE / 2, py + 8);
    ctx.lineTo(px + TILE - 8, py + TILE - 8);
    ctx.lineTo(px + 8, py + TILE - 8);
    ctx.closePath();
    ctx.fill();
  } else if (cell.type === "barracks") {
    ctx.fillStyle = def.color;
    ctx.fillRect(px + 6, py + 10, TILE - 12, TILE - 14);
    ctx.fillStyle = "#a894ff";
    ctx.fillRect(px + TILE / 2 - 5, py + 6, 10, 8);
  } else if (cell.type === "trap") {
    ctx.strokeStyle = def.color;
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.arc(px + TILE / 2, py + TILE / 2, TILE * 0.28, 0, Math.PI * 2);
    ctx.stroke();
    ctx.fillStyle = "rgba(77,212,255,.25)";
    ctx.fill();
  }

  if (cell.maxHp > 1 && cell.hp < cell.maxHp) {
    const pct = cell.hp / cell.maxHp;
    ctx.fillStyle = "rgba(0,0,0,.45)";
    ctx.fillRect(px + 2, py + TILE - 6, TILE - 4, 4);
    ctx.fillStyle = "#5cff9a";
    ctx.fillRect(px + 2, py + TILE - 6, (TILE - 4) * pct, 4);
  }
}

function drawEntity(ent, isEnemy) {
  const radius = ent.r || 12;

  if (ent.boss) {
    const pulse = 0.6 + Math.sin(Date.now() / 200) * 0.2;
    ctx.strokeStyle = `rgba(255,68,102,${pulse})`;
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.arc(ent.x, ent.y, radius + 6, 0, Math.PI * 2);
    ctx.stroke();
  }

  ctx.fillStyle = ent.color || (isEnemy ? "#8b6cc1" : "#7c5cff");
  ctx.beginPath();
  ctx.arc(ent.x, ent.y, radius, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = ent.boss ? "#ffb3c4" : "rgba(255,255,255,.35)";
  ctx.lineWidth = ent.boss ? 2.5 : 1.5;
  ctx.stroke();

  if (ent.boss && ent.name) {
    ctx.font = "bold 11px Inter, sans-serif";
    ctx.textAlign = "center";
    ctx.fillStyle = "#ff8899";
    ctx.fillText(ent.name, ent.x, ent.y - radius - 14);
  }

  if (ent.maxHp) {
    const barW = ent.boss ? 48 : 28;
    const pct = ent.hp / ent.maxHp;
    ctx.fillStyle = "rgba(0,0,0,.5)";
    ctx.fillRect(ent.x - barW / 2, ent.y - radius - 10, barW, 5);
    ctx.fillStyle = isEnemy ? "#ff5c7a" : "#5cff9a";
    ctx.fillRect(ent.x - barW / 2, ent.y - radius - 10, barW * pct, 5);
  }
}

function drawProjectiles() {
  for (const p of state.projectiles) {
    ctx.strokeStyle = "#ffd34d";
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.moveTo(p.x - 6, p.y);
    ctx.lineTo(p.x + 6, p.y);
    ctx.stroke();
    ctx.fillStyle = "#fff8d0";
    ctx.beginPath();
    ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
    ctx.fill();
  }
}

function drawEffects() {
  for (const fx of state.effects) {
    const alpha = Math.min(1, fx.life / 200);
    if (fx.kind === "burst") {
      ctx.strokeStyle = fx.color || "#4dd4ff";
      ctx.globalAlpha = alpha;
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.arc(fx.x, fx.y, (1 - alpha) * 28 + 8, 0, Math.PI * 2);
      ctx.stroke();
      ctx.globalAlpha = 1;
    } else if (fx.kind === "spark") {
      ctx.fillStyle = `rgba(255,211,77,${alpha})`;
      for (let i = 0; i < 5; i++) {
        const a = (i / 5) * Math.PI * 2;
        ctx.fillRect(fx.x + Math.cos(a) * 10, fx.y + Math.sin(a) * 10, 3, 3);
      }
    } else if (fx.kind === "slash") {
      ctx.strokeStyle = `rgba(92,255,154,${alpha})`;
      ctx.lineWidth = 3;
      ctx.beginPath();
      ctx.moveTo(fx.x - 12, fx.y - 8);
      ctx.lineTo(fx.x + 12, fx.y + 8);
      ctx.stroke();
    }
  }
}

function drawBossPrepBanner() {
  if (!state.bossPrep || state.phase !== "build") return;
  const next = nextWaveNumber();
  const bossName = BOSS_TYPES[getBossKey(next)].name;
  ctx.fillStyle = "rgba(255,17,51,.18)";
  ctx.fillRect(OFFSET_X, OFFSET_Y, COLS * TILE, ROWS * TILE);
  ctx.font = "bold 22px Inter, sans-serif";
  ctx.textAlign = "center";
  ctx.fillStyle = "#ff8899";
  ctx.fillText(`BOSS PREP — Wave ${next}`, canvas.width / 2, OFFSET_Y + 36);
  ctx.font = "14px Inter, sans-serif";
  ctx.fillStyle = "#ffd34d";
  ctx.fillText(`${bossName} incoming. Organize, then Face the Boss!`, canvas.width / 2, OFFSET_Y + 58);
}

function drawHover() {
  if (!state.hover || state.phase !== "build") return;
  const { x, y } = state.hover;
  const px = OFFSET_X + x * TILE;
  const py = OFFSET_Y + y * TILE;
  const ok = state.selectedTool === "remove"
    ? !!state.grid[y][x]
    : canPlace(state.selectedTool, x, y);
  ctx.fillStyle = ok ? "rgba(77,212,255,.22)" : "rgba(255,92,122,.22)";
  ctx.fillRect(px, py, TILE, TILE);
  ctx.strokeStyle = ok ? "#4dd4ff" : "#ff5c7a";
  ctx.lineWidth = 2;
  ctx.strokeRect(px + 1, py + 1, TILE - 2, TILE - 2);

  if (state.selectedTool === "turret") {
    const range = BUILD.turret.range * TILE;
    ctx.strokeStyle = "rgba(255,211,77,.2)";
    ctx.beginPath();
    ctx.arc(px + TILE / 2, py + TILE / 2, range, 0, Math.PI * 2);
    ctx.stroke();
  }
}

function drawSpawnZone() {
  ctx.fillStyle = "rgba(255,92,122,.08)";
  ctx.fillRect(OFFSET_X + (COLS - 1) * TILE, OFFSET_Y, TILE, ROWS * TILE);
}

function render() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawGrid();
  drawSpawnZone();
  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      if (state.grid[y][x]) drawStructure(state.grid[y][x]);
    }
  }
  drawCore();
  for (const a of state.allies) drawEntity(a, false);
  for (const e of state.enemies) drawEntity(e, true);
  drawProjectiles();
  drawEffects();
  drawBossPrepBanner();
  drawHover();
}

let last = performance.now();
function loop(now) {
  const dt = Math.min(50, now - last);
  last = now;
  if (state.phase === "fight") {
    updateSpawns(dt);
    updateStructures(dt);
    updateProjectiles(dt);
    updateEnemies(dt);
    updateAllies(dt);
  }
  updateEffects(dt);
  render();
  requestAnimationFrame(loop);
}

function updateHUD() {
  document.getElementById("stat-gold").textContent = `⚡ ${normalizeGold()}`;
  const trapStat = document.getElementById("stat-traps");
  if (trapStat) {
    trapStat.textContent = `Traps ${trapLimitHudCount()} / ${MAX_TRAPS_PER_ROUND}`;
  }
  const waveLabel = state.bossPrep
    ? `Wave ${state.wave} → BOSS ${nextWaveNumber()}`
    : `Wave ${state.wave}${state.fightActive ? " — FIGHT" : ""}`;
  document.getElementById("stat-wave").textContent = waveLabel;
  document.getElementById("stat-hp").textContent = `Core ${Math.ceil(state.coreHp)}%`;

  const phaseEl = document.getElementById("phase-label");
  const fightBtn = document.getElementById("btn-fight");
  if (state.phase === "fight" && state.fightActive) {
    phaseEl.textContent = isBossWave(state.wave) ? "BOSS FIGHT!" : "FIGHT!";
    phaseEl.classList.remove("boss-prep");
    phaseEl.classList.add("fighting");
    fightBtn.disabled = true;
    fightBtn.textContent = "Fighting…";
  } else if (state.phase === "lost") {
    phaseEl.textContent = "DEFEAT";
    phaseEl.classList.remove("fighting");
    fightBtn.disabled = true;
    fightBtn.textContent = "Restart to continue";
  } else if (state.phase === "won") {
    phaseEl.textContent = "VICTORY";
    fightBtn.disabled = true;
  } else if (state.bossPrep) {
    phaseEl.textContent = "BOSS PREP";
    phaseEl.classList.add("boss-prep");
    phaseEl.classList.remove("fighting");
    fightBtn.disabled = false;
    fightBtn.textContent = `Face the Boss! (Wave ${nextWaveNumber()})`;
  } else {
    phaseEl.textContent = "BUILD PHASE";
    phaseEl.classList.remove("boss-prep");
    phaseEl.classList.remove("fighting");
    fightBtn.disabled = false;
    fightBtn.textContent = isBossWave(nextWaveNumber())
      ? `Face the Boss! (Wave ${nextWaveNumber()})`
      : "Strike — Fight!";
  }

  document.querySelectorAll(".tool[data-tool]").forEach((btn) => {
    const tool = btn.getAttribute("data-tool");
    if (tool === "remove") return;
    const cost = BUILD[tool]?.cost ?? 0;
    const trapCapped = tool === "trap" && isTrapLimitReached();
    btn.disabled = (state.phase === "fight" && state.fightActive) || trapCapped;
    btn.querySelector(".tool-cost").textContent = trapCapped
      ? `${MAX_TRAPS_PER_ROUND}/${MAX_TRAPS_PER_ROUND}`
      : !canAfford(cost) ? `${cost} ✗` : `${cost}`;
  });
}

const feedEl = document.getElementById("storm-feed");
const feedMessages = [];

function addFeed(kind, text, who) {
  feedMessages.push({ kind, text, who, at: Date.now() });
  while (feedMessages.length > 60) feedMessages.shift();
  renderFeed();
}

function renderFeed() {
  if (!feedEl) return;
  feedEl.innerHTML = feedMessages
    .map((m) => {
      const who = m.who ? `<span class="who">${escapeHtml(m.who)}:</span>` : "";
      return `<div class="feed-line ${m.kind}">${who}${escapeHtml(m.text)}</div>`;
    })
    .join("");
  feedEl.scrollTop = feedEl.scrollHeight;
}

function escapeHtml(t) {
  return String(t)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function showOverlay(title, text, action, btnLabel = "Play Again") {
  const overlay = document.getElementById("overlay");
  document.getElementById("overlay-title").textContent = title;
  document.getElementById("overlay-text").textContent = text;
  const btn = document.getElementById("overlay-btn");
  btn.textContent = btnLabel;
  overlay.hidden = false;
  btn.onclick = () => {
    overlay.hidden = true;
    action();
  };
}

function restart() {
  initGrid();
  state.phase = "build";
  state.wave = 0;
  state.gold = Math.max(0, START_GOLD);
  state.enemies = [];
  state.allies = [];
  state.projectiles = [];
  state.effects = [];
  state.spawnQueue = [];
  state.fightActive = false;
  state.bossPrep = false;
  state.trapsPlacedThisRound = 0;
  state.coreHp = CORE.maxHp;
  document.getElementById("overlay").hidden = true;
  feedMessages.length = 0;
  addFeed("system", "New storm siege. Build your defenses!");
  addFeed("system", "Boss waves arrive every 5 rounds — prep time before each boss.");
  updateHUD();
}

function clearBuild() {
  if (state.phase === "fight" && state.fightActive) return;
  for (let y = 0; y < ROWS; y++) {
    for (let x = 0; x < COLS; x++) {
      state.grid[y][x] = null;
    }
  }
  addFeed("system", "Map cleared.");
  updateHUD();
}

function getCanvasPos(evt) {
  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width / rect.width;
  const scaleY = canvas.height / rect.height;
  const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
  const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;
  return {
    x: (clientX - rect.left) * scaleX,
    y: (clientY - rect.top) * scaleY,
  };
}

function onPointerMove(evt) {
  const pos = getCanvasPos(evt);
  state.hover = tileAt(pos.x, pos.y);
}

function onPointerDown(evt) {
  if (state.phase === "won") return;
  if (state.phase === "lost") return;
  if (state.phase === "fight" && state.fightActive) return;
  const pos = getCanvasPos(evt);
  const tile = tileAt(pos.x, pos.y);
  if (!tile) return;
  if (state.selectedTool === "remove") {
    removeStructure(tile.x, tile.y);
  } else {
    placeStructure(state.selectedTool, tile.x, tile.y);
  }
}

canvas.addEventListener("mousemove", onPointerMove);
canvas.addEventListener("mouseleave", () => { state.hover = null; });
canvas.addEventListener("mousedown", onPointerDown);
canvas.addEventListener("touchstart", (e) => {
  e.preventDefault();
  onPointerDown(e);
}, { passive: false });
canvas.addEventListener("touchmove", (e) => {
  e.preventDefault();
  onPointerMove(e);
}, { passive: false });

document.querySelectorAll(".tool[data-tool]").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".tool[data-tool]").forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");
    state.selectedTool = btn.getAttribute("data-tool");
  });
});

document.getElementById("btn-fight")?.addEventListener("click", startFight);
document.getElementById("btn-clear-build")?.addEventListener("click", clearBuild);
document.getElementById("btn-restart")?.addEventListener("click", restart);

document.getElementById("btn-help")?.addEventListener("click", () => {
  document.getElementById("help-dialog").showModal();
});
document.getElementById("help-close")?.addEventListener("click", () => {
  document.getElementById("help-dialog").close();
});

document.getElementById("feed-form")?.addEventListener("submit", (e) => {
  e.preventDefault();
  const name = document.getElementById("feed-name")?.value?.trim() || "Player";
  const msg = document.getElementById("feed-msg")?.value?.trim();
  if (!msg) return;
  addFeed("player", msg, name.slice(0, 16));
  document.getElementById("feed-msg").value = "";
});

const year = document.getElementById("year");
if (year) year.textContent = new Date().getFullYear();

initGrid();
addFeed("system", "Welcome to Storm Siege. Build walls & towers, then hit Strike!");
addFeed("system", "Protect the Thunder Core on the left. Survive 10 waves.");
addFeed("system", "Boss fights at waves 5 & 10 — you get prep time to organize first.");
updateHUD();
requestAnimationFrame(loop);
