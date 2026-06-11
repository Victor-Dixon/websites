/**
 * Procedural demo sprite sheet conforming to Grid Schema V1 (64×64 cells, 4 cols × 13 rows).
 */

const COLS = 4;
const ROWS = 13;
const CELL = 64;

const ROW_LABELS = [
  'idle',
  'walk↑', 'walk↓', 'walk←', 'walk→',
  'run↑', 'run↓', 'run←', 'run→',
  'interact', 'attack', 'magic', 'hurt',
];

const DIR_COLORS = {
  up: '#4ecdc4',
  down: '#ffe66d',
  left: '#ff6b6b',
  right: '#95e1d3',
};

const ACTION_TINT = {
  idle: 1,
  walk: 0.85,
  run: 0.7,
  interact: 0.9,
  attack: 0.75,
  magic: 0.8,
  hurt: 0.65,
};

/**
 * Draw a simple blocky character facing a direction with a frame offset for walk/run.
 */
function drawCharacter(ctx, x, y, size, direction, frameIndex, rowKind) {
  const cx = x + size / 2;
  const cy = y + size / 2;
  const bob = rowKind.startsWith('walk') || rowKind.startsWith('run')
    ? Math.sin((frameIndex / 4) * Math.PI * 2) * (rowKind.startsWith('run') ? 4 : 2)
    : 0;
  const scale = rowKind.startsWith('run') ? 1.05 : 1;

  ctx.save();
  ctx.translate(cx, cy + bob);
  ctx.scale(scale, scale);

  const bodyColor = direction === 'up' ? DIR_COLORS.up
    : direction === 'down' ? DIR_COLORS.down
    : direction === 'left' ? DIR_COLORS.left
    : DIR_COLORS.right;

  const legOffset = (frameIndex % 2 === 0) ? 3 : -3;
  const armSwing = Math.sin((frameIndex / 4) * Math.PI * 2) * 6;

  // Shadow
  ctx.fillStyle = 'rgba(0,0,0,0.25)';
  ctx.beginPath();
  ctx.ellipse(0, size * 0.32, size * 0.22, size * 0.06, 0, 0, Math.PI * 2);
  ctx.fill();

  // Legs
  ctx.fillStyle = '#2d3436';
  if (direction === 'left' || direction === 'right') {
    ctx.fillRect(-10 + legOffset, 8, 8, 14);
    ctx.fillRect(2 - legOffset, 8, 8, 14);
  } else {
    ctx.fillRect(-10, 8 + (direction === 'up' ? -legOffset : legOffset), 8, 14);
    ctx.fillRect(2, 8 + (direction === 'up' ? legOffset : -legOffset), 8, 14);
  }

  // Body
  ctx.fillStyle = bodyColor;
  ctx.fillRect(-14, -6, 28, 18);
  ctx.strokeStyle = '#1a1a2e';
  ctx.lineWidth = 2;
  ctx.strokeRect(-14, -6, 28, 18);

  // Head
  ctx.fillStyle = '#f5cba7';
  ctx.fillRect(-10, -22, 20, 16);
  ctx.strokeRect(-10, -22, 20, 16);

  // Eyes by direction
  ctx.fillStyle = '#2d3436';
  if (direction === 'up') {
    ctx.fillRect(-6, -18, 3, 3);
    ctx.fillRect(3, -18, 3, 3);
  } else if (direction === 'down') {
    ctx.fillRect(-6, -14, 3, 3);
    ctx.fillRect(3, -14, 3, 3);
    ctx.fillRect(-3, -10, 6, 2);
  } else if (direction === 'left') {
    ctx.fillRect(-8, -16, 3, 3);
  } else {
    ctx.fillRect(5, -16, 3, 3);
  }

  // Arms
  ctx.fillStyle = bodyColor;
  if (rowKind === 'attack') {
    ctx.fillRect(direction === 'left' ? -28 : 12, -4, 16, 6);
  } else if (rowKind === 'magic') {
    ctx.fillStyle = '#a29bfe';
    ctx.beginPath();
    ctx.arc(direction === 'left' ? -20 : 20, -8, 8, 0, Math.PI * 2);
    ctx.fill();
  } else if (rowKind === 'hurt') {
    ctx.globalAlpha = 0.7;
    ctx.rotate(0.15);
    ctx.fillRect(-18, -2 + armSwing * 0.2, 8, 6);
    ctx.fillRect(10, -2 - armSwing * 0.2, 8, 6);
  } else {
    ctx.fillRect(-20, -2 + armSwing, 8, 6);
    ctx.fillRect(12, -2 - armSwing, 8, 6);
  }

  // Frame number badge
  ctx.globalAlpha = 1;
  ctx.fillStyle = 'rgba(0,0,0,0.55)';
  ctx.fillRect(-size / 2 + 4, -size / 2 + 4, 22, 14);
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 10px monospace';
  ctx.textAlign = 'left';
  ctx.fillText(String(frameIndex), -size / 2 + 7, -size / 2 + 14);

  ctx.restore();
}

function rowDirection(rowIndex) {
  if (rowIndex === 0) return ['up', 'down', 'left', 'right'];
  if (rowIndex >= 1 && rowIndex <= 4) return ['up', 'down', 'left', 'right'][rowIndex - 1];
  if (rowIndex >= 5 && rowIndex <= 8) return ['up', 'down', 'left', 'right'][rowIndex - 5];
  return 'down';
}

function rowKind(rowIndex) {
  return ROW_LABELS[rowIndex] ?? 'idle';
}

/**
 * Generate demo sprite sheet as HTMLImageElement (loads from canvas data URL).
 * @returns {Promise<HTMLImageElement>}
 */
export function generateDemoSpriteSheet() {
  const canvas = document.createElement('canvas');
  canvas.width = COLS * CELL;
  canvas.height = ROWS * CELL;
  const ctx = canvas.getContext('2d');

  for (let row = 0; row < ROWS; row++) {
    for (let col = 0; col < COLS; col++) {
      const x = col * CELL;
      const y = row * CELL;
      const label = ROW_LABELS[row];
      const dirSpec = rowDirection(row);
      const direction = Array.isArray(dirSpec) ? dirSpec[col] : dirSpec;

      let bg = '#1e272e';
      if (row === 0) bg = '#2f3640';
      else if (row >= 1 && row <= 4) bg = '#353b48';
      else if (row >= 5 && row <= 8) bg = '#2c2c54';
      else bg = '#222f3e';

      const tint = row <= 8
        ? (row === 0 ? ACTION_TINT.idle : row <= 4 ? ACTION_TINT.walk : ACTION_TINT.run)
        : ACTION_TINT[['interact', 'attack', 'magic', 'hurt'][row - 9]] ?? 1;

      ctx.fillStyle = bg;
      ctx.fillRect(x, y, CELL, CELL);
      ctx.strokeStyle = `rgba(255,255,255,${0.08 * tint})`;
      ctx.strokeRect(x + 0.5, y + 0.5, CELL - 1, CELL - 1);

      const frameCount = row === 0 ? 1 : (row >= 9 ? 2 : 4);
      if (col >= frameCount && row !== 0) continue;
      if (row === 0 && col >= 4) continue;

      const frameIndex = row === 0 ? 0 : col;
      drawCharacter(ctx, x, y, CELL, direction, frameIndex, label);

      ctx.fillStyle = 'rgba(255,255,255,0.35)';
      ctx.font = '9px monospace';
      ctx.textAlign = 'right';
      ctx.fillText(`r${row}c${col}`, x + CELL - 6, y + CELL - 6);
    }
  }

  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = reject;
    img.src = canvas.toDataURL('image/png');
  });
}

export const DEMO_GRID = { w: CELL, h: CELL, cols: COLS, rows: ROWS };
