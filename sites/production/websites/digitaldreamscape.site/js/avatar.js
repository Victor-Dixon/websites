export const AVATAR_LAYER_PLAN = [
  "aura",
  "cape",
  "base_body",
  "hair",
  "outfit",
  "boots",
  "weapon",
];

const DEFAULT_PALETTE = {
  skin: "#ffd2a8",
  skinShade: "#c9795a",
  hair: "#1f2c68",
  hairHighlight: "#9b5cff",
  shirt: "#4cc9ff",
  shirtShade: "#2866ff",
  trim: "#ffd166",
  pants: "#273c75",
  boots: "#111827",
  cape: "#9b5cff",
  capeShade: "#5f3dc4",
  weapon: "#f5f8ff",
  aura: "rgba(76, 201, 255, .24)",
  ink: "#071024",
};

export function getAvatarFrame(player, frameTime = 0) {
  const moving = Boolean(player.walking || player.path?.length);
  const frame = moving ? Math.floor(frameTime / 145) % 4 : Math.floor(frameTime / 520) % 2;
  return {
    direction: player.direction || "south",
    frame,
    moving,
    bob: moving ? [0, -2, 0, -1][frame] : [0, -1][frame],
    stride: moving ? [-2, 2, -1, 1][frame] : 0,
  };
}

function px(ctx, x, y, width, height, color) {
  ctx.fillStyle = color;
  ctx.fillRect(Math.round(x), Math.round(y), Math.round(width), Math.round(height));
}

function drawAura(ctx, frame, palette) {
  ctx.fillStyle = palette.aura;
  ctx.beginPath();
  ctx.ellipse(0, 10, frame.moving ? 19 : 17, 10, 0, 0, Math.PI * 2);
  ctx.fill();
}

function drawCape(ctx, frame, palette) {
  const sway = frame.moving ? frame.stride / 2 : frame.frame ? 1 : -1;
  const capeShift = frame.direction === "east" ? -4 : frame.direction === "west" ? 4 : sway;
  if (frame.direction === "south") {
    px(ctx, -9 + sway, -3, 18, 24, palette.ink);
    px(ctx, -7 + sway, -1, 14, 21, palette.cape);
    px(ctx, 3 + sway, 1, 4, 18, palette.capeShade);
    return;
  }

  px(ctx, -10 + capeShift, -6, 20, 28, palette.ink);
  px(ctx, -8 + capeShift, -4, 16, 24, palette.cape);
  px(ctx, 3 + capeShift, -2, 5, 22, palette.capeShade);
}

function drawBaseBody(ctx, frame, palette) {
  const faceOffset = frame.direction === "east" ? 2 : frame.direction === "west" ? -2 : 0;

  px(ctx, -8 + faceOffset, -24, 16, 15, palette.ink);
  px(ctx, -7 + faceOffset, -23, 14, 13, palette.skin);
  px(ctx, -6 + faceOffset, -10, 12, 12, palette.skin);
  px(ctx, -10, -8, 5, 13, palette.skin);
  px(ctx, 5, -8, 5, 13, palette.skin);

  if (frame.direction !== "north") {
    px(ctx, -5 + faceOffset, -19, 3, 1, palette.ink);
    px(ctx, 3 + faceOffset, -19, 3, 1, palette.ink);
    px(ctx, -5 + faceOffset, -17, 3, 4, palette.ink);
    px(ctx, 3 + faceOffset, -17, 3, 4, palette.ink);
    px(ctx, -4 + faceOffset, -16, 1, 2, "#f5f8ff");
    px(ctx, 4 + faceOffset, -16, 1, 2, "#f5f8ff");
    px(ctx, -2 + faceOffset, -12, 5, 1, palette.skinShade);
  }
}

function drawHair(ctx, frame, palette) {
  const side = frame.direction === "east" ? 3 : frame.direction === "west" ? -3 : 0;
  const sway = frame.frame ? 1 : 0;
  px(ctx, -10 + side, -27, 20, 6, palette.ink);
  px(ctx, -9 + side, -26, 18, 6, palette.hair);
  px(ctx, -10 + side, -21, 5, 8, palette.hair);
  px(ctx, 5 + side, -21, 5, 8, palette.hair);
  px(ctx, -2 + side + sway, -25, 8, 4, palette.hairHighlight);
  px(ctx, -6 + side - sway, -22, 5, 3, palette.hairHighlight);

  if (frame.direction === "north") {
    px(ctx, -9, -22, 18, 11, palette.hair);
    px(ctx, 1, -21, 7, 10, palette.hairHighlight);
  }
}

function drawOutfit(ctx, frame, palette) {
  px(ctx, -9, -10, 18, 15, palette.ink);
  px(ctx, -8, -9, 16, 13, palette.shirt);
  px(ctx, -8, -8, 16, 2, palette.trim);
  px(ctx, -8, 2, 7, 13, palette.pants);
  px(ctx, 1, 2, 7, 13, palette.pants);

  if (frame.direction === "east") {
    px(ctx, 4, -9, 5, 13, palette.shirtShade);
  } else if (frame.direction === "west") {
    px(ctx, -9, -9, 5, 13, palette.shirtShade);
  } else {
    px(ctx, -1, -9, 2, 13, palette.shirtShade);
  }
}

function drawBoots(ctx, frame, palette) {
  const stride = frame.stride;
  if (frame.direction === "east" || frame.direction === "west") {
    px(ctx, -7 + (stride / 2), 13, 7, 5, palette.boots);
    px(ctx, 1 - (stride / 2), 13, 7, 5, palette.boots);
    return;
  }

  px(ctx, -7, 13 + Math.max(0, -stride / 2), 7, 5, palette.boots);
  px(ctx, 1, 13 + Math.max(0, stride / 2), 7, 5, palette.boots);
}

function drawWeapon(ctx, frame, palette) {
  if (frame.direction === "west") {
    px(ctx, -17, -13, 4, 26, palette.ink);
    px(ctx, -16, -12, 2, 24, palette.weapon);
    px(ctx, -20, -15, 9, 4, palette.weapon);
  } else {
    px(ctx, 13, -13, 4, 26, palette.ink);
    px(ctx, 14, -12, 2, 24, palette.weapon);
    px(ctx, 11, -15, 9, 4, palette.weapon);
  }
}

const LAYER_DRAWERS = {
  aura: drawAura,
  cape: drawCape,
  base_body: drawBaseBody,
  hair: drawHair,
  outfit: drawOutfit,
  boots: drawBoots,
  weapon: drawWeapon,
};

export function drawLayeredAvatar(ctx, centerX, centerY, player, options = {}) {
  const frame = getAvatarFrame(player, options.frameTime || 0);
  const palette = { ...DEFAULT_PALETTE, ...(player.avatar?.palette || {}) };
  const layers = player.avatar?.layers || AVATAR_LAYER_PLAN;
  const scale = Math.max(.85, (options.tileSize || 32) / 32);

  ctx.save();
  ctx.translate(Math.round(centerX), Math.round(centerY + 4 + frame.bob));
  ctx.scale(scale, scale);
  ctx.imageSmoothingEnabled = false;
  ctx.shadowColor = "rgba(76, 201, 255, .18)";
  ctx.shadowBlur = 8;

  layers.forEach((layer) => {
    const drawLayer = LAYER_DRAWERS[layer];
    if (drawLayer) {
      drawLayer(ctx, frame, palette);
    }
  });

  ctx.restore();
}
