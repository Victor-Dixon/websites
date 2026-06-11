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
  skin: "#d8a16f",
  skinShade: "#a9694b",
  hair: "#17203a",
  shirt: "#62d9ff",
  shirtShade: "#2866ff",
  pants: "#273c75",
  boots: "#111827",
  cape: "#8d74ff",
  weapon: "#d9e5f5",
  aura: "rgba(98, 217, 255, .22)",
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
  ctx.ellipse(0, 9, frame.moving ? 17 : 15, 9, 0, 0, Math.PI * 2);
  ctx.fill();
}

function drawCape(ctx, frame, palette) {
  const capeShift = frame.direction === "east" ? -3 : frame.direction === "west" ? 3 : 0;
  if (frame.direction === "south") {
    px(ctx, -7, -1, 14, 20, palette.cape);
    return;
  }

  px(ctx, -8 + capeShift, -4, 16, 24, palette.cape);
}

function drawBaseBody(ctx, frame, palette) {
  const faceOffset = frame.direction === "east" ? 2 : frame.direction === "west" ? -2 : 0;

  px(ctx, -6 + faceOffset, -22, 12, 12, palette.skin);
  px(ctx, -5 + faceOffset, -10, 10, 11, palette.skin);
  px(ctx, -8, -7, 4, 12, palette.skin);
  px(ctx, 4, -7, 4, 12, palette.skin);

  if (frame.direction !== "north") {
    px(ctx, -4 + faceOffset, -17, 2, 2, "#050914");
    px(ctx, 3 + faceOffset, -17, 2, 2, "#050914");
    px(ctx, -2 + faceOffset, -13, 5, 1, palette.skinShade);
  }
}

function drawHair(ctx, frame, palette) {
  const side = frame.direction === "east" ? 2 : frame.direction === "west" ? -2 : 0;
  px(ctx, -7 + side, -24, 14, 5, palette.hair);
  px(ctx, -7 + side, -20, 3, 6, palette.hair);
  px(ctx, 4 + side, -20, 3, 6, palette.hair);

  if (frame.direction === "north") {
    px(ctx, -7, -20, 14, 9, palette.hair);
  }
}

function drawOutfit(ctx, frame, palette) {
  px(ctx, -7, -9, 14, 13, palette.shirt);
  px(ctx, -7, 2, 6, 12, palette.pants);
  px(ctx, 1, 2, 6, 12, palette.pants);

  if (frame.direction === "east") {
    px(ctx, 4, -9, 4, 13, palette.shirtShade);
  } else if (frame.direction === "west") {
    px(ctx, -8, -9, 4, 13, palette.shirtShade);
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
  if (!frame.moving) return;

  if (frame.direction === "west") {
    px(ctx, -13, -9, 3, 20, palette.weapon);
  } else {
    px(ctx, 10, -9, 3, 20, palette.weapon);
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

  layers.forEach((layer) => {
    const drawLayer = LAYER_DRAWERS[layer];
    if (drawLayer) {
      drawLayer(ctx, frame, palette);
    }
  });

  ctx.restore();
}
