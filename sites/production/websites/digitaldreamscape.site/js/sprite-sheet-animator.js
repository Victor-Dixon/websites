const DEFAULT_DIRECTION_ROWS = {
  south: 0,
  west: 1,
  east: 2,
  north: 3,
};

const DEFAULT_ANIMATIONS = {
  idle: { frames: [0, 1, 2, 1], fps: 3 },
  walk: { frames: [0, 1, 2, 3], fps: 8 },
  attack: { frames: [1, 2, 3, 2], fps: 10 },
};

const COMMON_AI_SHEET_COLUMNS = [12, 10, 8, 6, 4];
const imageCache = new Map();

function resolveImage(src) {
  if (!src) return null;
  if (imageCache.has(src)) return imageCache.get(src);

  const image = new Image();
  const entry = {
    image,
    loaded: false,
    error: false,
    layouts: new Map(),
  };

  image.addEventListener("load", () => {
    entry.loaded = true;
  });
  image.addEventListener("error", () => {
    entry.error = true;
  });
  image.src = src;
  imageCache.set(src, entry);
  return entry;
}

function animationNameFor(player, options) {
  if (options.animation) return options.animation;
  if (player.avatar?.animation) return player.avatar.animation;
  return player.walking || player.path?.length ? "walk" : "idle";
}

function frameIndexFor(animation, frameTime) {
  const frames = animation.frames?.length ? animation.frames : DEFAULT_ANIMATIONS.idle.frames;
  const fps = animation.fps || DEFAULT_ANIMATIONS.idle.fps;
  const current = Math.floor((frameTime / 1000) * fps) % frames.length;
  return frames[current];
}

function clampFrame(frame, layout) {
  return Math.max(0, Math.min(frame, layout.columns - 1));
}

function spriteConfigFor(player) {
  const config = player.avatar?.spriteSheet;
  if (!config?.src || config.enabled === false) return null;
  return config;
}

function autoLayoutConfig(config) {
  return {
    enabled: config.autoLayout?.enabled === true || (!config.frameWidth && !config.frameHeight),
    rows: config.autoLayout?.rows || config.rowCount || Object.keys(config.rows || DEFAULT_DIRECTION_ROWS).length || 4,
    columns: config.autoLayout?.columns || config.columnCount || null,
    alphaThreshold: config.autoLayout?.alphaThreshold ?? 12,
    minGroupSize: config.autoLayout?.minGroupSize ?? 4,
    mode: config.autoLayout?.mode || "alpha-or-grid",
    animationPreset: config.autoLayout?.animationPreset || "directional-rpg",
  };
}

function occupiedGroupsFromAlpha(image, axis, options) {
  try {
    const canvas = document.createElement("canvas");
    canvas.width = image.naturalWidth || image.width;
    canvas.height = image.naturalHeight || image.height;
    const context = canvas.getContext("2d", { willReadFrequently: true });
    context.drawImage(image, 0, 0);
    const { data, width, height } = context.getImageData(0, 0, canvas.width, canvas.height);
    const outer = axis === "x" ? width : height;
    const inner = axis === "x" ? height : width;
    const minPixels = Math.max(options.minGroupSize, Math.floor(inner * .015));
    const occupied = [];

    for (let a = 0; a < outer; a += 1) {
      let count = 0;
      for (let b = 0; b < inner; b += 1) {
        const x = axis === "x" ? a : b;
        const y = axis === "x" ? b : a;
        const alpha = data[((y * width) + x) * 4 + 3];
        if (alpha > options.alphaThreshold) count += 1;
      }
      occupied.push(count >= minPixels);
    }

    const groups = [];
    let start = null;
    occupied.forEach((isOccupied, index) => {
      if (isOccupied && start === null) {
        start = index;
      } else if (!isOccupied && start !== null) {
        if (index - start >= options.minGroupSize) groups.push({ start, end: index - 1 });
        start = null;
      }
    });
    if (start !== null && occupied.length - start >= options.minGroupSize) {
      groups.push({ start, end: occupied.length - 1 });
    }

    return groups;
  } catch {
    return [];
  }
}

function inferColumns(image, rows, configColumns) {
  if (configColumns) return configColumns;

  const width = image.naturalWidth || image.width;
  const height = image.naturalHeight || image.height;
  const ratio = width / Math.max(1, height);
  const commonMatch = COMMON_AI_SHEET_COLUMNS.find((columns) => {
    const cellAspect = (width / columns) / (height / rows);
    return cellAspect >= .72 && cellAspect <= 1.28 && Math.abs((columns / rows) - ratio) < .45;
  });

  return commonMatch || Math.max(1, Math.round(ratio * rows));
}

function gridLayoutFor(image, config) {
  const auto = autoLayoutConfig(config);
  const rows = auto.rows;
  const columns = inferColumns(image, rows, auto.columns);
  const frameWidth = config.frameWidth || ((image.naturalWidth || image.width) / columns);
  const frameHeight = config.frameHeight || ((image.naturalHeight || image.height) / rows);

  return {
    type: "auto-grid",
    rows,
    columns,
    frameWidth,
    frameHeight,
  };
}

function alphaLayoutFor(image, config) {
  const auto = autoLayoutConfig(config);
  if (auto.mode === "grid") return null;

  const columns = occupiedGroupsFromAlpha(image, "x", auto);
  const rows = occupiedGroupsFromAlpha(image, "y", auto);
  if (columns.length < 2 || rows.length < 2) return null;

  return {
    type: "alpha-groups",
    rows: rows.length,
    columns: columns.length,
    columnBounds: columns,
    rowBounds: rows,
    frameWidth: Math.max(...columns.map((group) => group.end - group.start + 1)),
    frameHeight: Math.max(...rows.map((group) => group.end - group.start + 1)),
  };
}

function layoutCacheKey(config) {
  const auto = config.autoLayout || {};
  return JSON.stringify({
    frameWidth: config.frameWidth,
    frameHeight: config.frameHeight,
    rows: config.rows,
    rowCount: config.rowCount,
    columnCount: config.columnCount,
    auto,
  });
}

function resolveLayout(entry, config) {
  const key = layoutCacheKey(config);
  if (entry.layouts.has(key)) return entry.layouts.get(key);

  const auto = autoLayoutConfig(config);
  let layout = null;

  if (auto.enabled) {
    layout = alphaLayoutFor(entry.image, config);
  }

  if (!layout) {
    layout = gridLayoutFor(entry.image, config);
  }

  entry.layouts.set(key, layout);
  return layout;
}

function animationPresetFor(layout, config) {
  if (config.animations && config.autoLayout?.animationPreset !== "auto") {
    return config.animations;
  }

  const lastFrame = Math.max(0, layout.columns - 1);
  const firstFour = Array.from({ length: Math.min(4, layout.columns) }, (_, index) => index);
  const attackStart = Math.max(0, layout.columns - 4);
  const attackFrames = Array.from({ length: layout.columns - attackStart }, (_, index) => attackStart + index);
  const walkStart = layout.columns >= 8 ? 3 : 0;
  const walkEnd = layout.columns >= 8 ? Math.min(7, lastFrame) : lastFrame;
  const walkFrames = Array.from({ length: (walkEnd - walkStart) + 1 }, (_, index) => walkStart + index);

  return {
    idle: { frames: firstFour.length ? firstFour : [0], fps: config.autoLayout?.idleFps || 4 },
    walk: { frames: walkFrames.length ? walkFrames : firstFour, fps: config.autoLayout?.walkFps || 9 },
    attack: { frames: attackFrames.length ? attackFrames : [lastFrame], fps: config.autoLayout?.attackFps || 10 },
  };
}

function frameRect(layout, row, frame) {
  const safeRow = Math.max(0, Math.min(row, layout.rows - 1));
  const safeFrame = clampFrame(frame, layout);

  if (layout.type === "alpha-groups" && layout.columnBounds?.[safeFrame] && layout.rowBounds?.[safeRow]) {
    const column = layout.columnBounds[safeFrame];
    const rowBounds = layout.rowBounds[safeRow];
    return {
      sx: column.start,
      sy: rowBounds.start,
      sw: column.end - column.start + 1,
      sh: rowBounds.end - rowBounds.start + 1,
    };
  }

  return {
    sx: safeFrame * layout.frameWidth,
    sy: safeRow * layout.frameHeight,
    sw: layout.frameWidth,
    sh: layout.frameHeight,
  };
}

export function drawSpriteSheetAvatar(ctx, centerX, centerY, player, options = {}) {
  const config = spriteConfigFor(player);
  if (!config) return false;

  const entry = resolveImage(config.src);
  if (!entry?.loaded || entry.error) return false;

  const layout = resolveLayout(entry, config);
  const direction = player.direction || "south";
  const row = config.rows?.[direction] ?? DEFAULT_DIRECTION_ROWS[direction] ?? DEFAULT_DIRECTION_ROWS.south;
  const useManualAnimations = config.animations && config.autoLayout?.animationPreset !== "auto";
  const animations = {
    ...DEFAULT_ANIMATIONS,
    ...animationPresetFor(layout, config),
    ...(useManualAnimations ? config.animations : {}),
  };
  const animation = animations[animationNameFor(player, options)] || animations.idle;
  const frame = clampFrame(frameIndexFor(animation, options.frameTime || 0), layout);
  const rect = frameRect(layout, row, frame);
  const tileScale = Math.max(.85, (options.tileSize || 32) / 32);
  const scale = (config.scale || .72) * tileScale;
  const drawWidth = rect.sw * scale;
  const drawHeight = rect.sh * scale;
  const anchorX = config.anchorX ?? .5;
  const anchorY = config.anchorY ?? .82;
  const offsetX = config.offsetX || 0;
  const offsetY = config.offsetY || 0;

  ctx.save();
  ctx.imageSmoothingEnabled = config.imageSmoothing !== false;
  ctx.shadowColor = config.glowColor || "rgba(92, 244, 255, .32)";
  ctx.shadowBlur = config.glowBlur ?? 12;
  ctx.drawImage(
    entry.image,
    rect.sx,
    rect.sy,
    rect.sw,
    rect.sh,
    centerX - (drawWidth * anchorX) + offsetX,
    centerY - (drawHeight * anchorY) + offsetY,
    drawWidth,
    drawHeight,
  );
  ctx.restore();
  return true;
}

export function getSpriteSheetAnimatorStatus(player) {
  const config = spriteConfigFor(player);
  if (!config) return "disabled";
  const entry = resolveImage(config.src);
  if (entry?.error) return "error";
  if (entry?.loaded) return "ready";
  return "loading";
}
