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

const imageCache = new Map();

function resolveImage(src) {
  if (!src) return null;
  if (imageCache.has(src)) return imageCache.get(src);

  const image = new Image();
  const entry = {
    image,
    loaded: false,
    error: false,
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

function spriteConfigFor(player) {
  const config = player.avatar?.spriteSheet;
  if (!config?.src || config.enabled === false) return null;
  return config;
}

export function drawSpriteSheetAvatar(ctx, centerX, centerY, player, options = {}) {
  const config = spriteConfigFor(player);
  if (!config) return false;

  const entry = resolveImage(config.src);
  if (!entry?.loaded || entry.error) return false;

  const frameWidth = config.frameWidth || 64;
  const frameHeight = config.frameHeight || 64;
  const direction = player.direction || "south";
  const row = config.rows?.[direction] ?? DEFAULT_DIRECTION_ROWS[direction] ?? DEFAULT_DIRECTION_ROWS.south;
  const animations = { ...DEFAULT_ANIMATIONS, ...(config.animations || {}) };
  const animation = animations[animationNameFor(player, options)] || animations.idle;
  const frame = frameIndexFor(animation, options.frameTime || 0);
  const tileScale = Math.max(.85, (options.tileSize || 32) / 32);
  const scale = (config.scale || .72) * tileScale;
  const drawWidth = frameWidth * scale;
  const drawHeight = frameHeight * scale;
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
    frame * frameWidth,
    row * frameHeight,
    frameWidth,
    frameHeight,
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
