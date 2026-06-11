export const TERRAIN = {
  GRASS: "grass",
  PATH: "path",
  PLAIN: "plain",
  WATER: "water",
  WALL: "wall",
  TREE: "tree",
  ROCK: "rock",
};

const WIDTH = 60;
const HEIGHT = 60;

function createTerrain() {
  const terrain = Array.from({ length: HEIGHT }, () => Array(WIDTH).fill(TERRAIN.GRASS));

  const paintRect = (x1, y1, x2, y2, type) => {
    for (let y = Math.max(0, y1); y <= Math.min(HEIGHT - 1, y2); y += 1) {
      for (let x = Math.max(0, x1); x <= Math.min(WIDTH - 1, x2); x += 1) {
        terrain[y][x] = type;
      }
    }
  };

  const paintEllipse = (cx, cy, rx, ry, type) => {
    for (let y = Math.max(0, cy - ry); y <= Math.min(HEIGHT - 1, cy + ry); y += 1) {
      for (let x = Math.max(0, cx - rx); x <= Math.min(WIDTH - 1, cx + rx); x += 1) {
        const dx = (x - cx) / rx;
        const dy = (y - cy) / ry;
        if ((dx * dx) + (dy * dy) <= 1) {
          terrain[y][x] = type;
        }
      }
    }
  };

  const paintPath = (points) => {
    for (let i = 0; i < points.length - 1; i += 1) {
      const from = points[i];
      const to = points[i + 1];
      const minX = Math.min(from.x, to.x);
      const maxX = Math.max(from.x, to.x);
      const minY = Math.min(from.y, to.y);
      const maxY = Math.max(from.y, to.y);
      paintRect(minX - 1, minY - 1, maxX + 1, maxY + 1, TERRAIN.PATH);
    }
  };

  paintPath([
    { x: 8, y: 8 },
    { x: 15, y: 12 },
    { x: 25, y: 20 },
    { x: 42, y: 24 },
    { x: 49, y: 37 },
    { x: 54, y: 50 },
  ]);
  paintPath([
    { x: 8, y: 8 },
    { x: 7, y: 35 },
    { x: 18, y: 44 },
    { x: 35, y: 40 },
    { x: 54, y: 50 },
  ]);
  paintPath([
    { x: 15, y: 20 },
    { x: 22, y: 13 },
    { x: 39, y: 15 },
    { x: 46, y: 32 },
  ]);

  paintEllipse(31, 30, 7, 5, TERRAIN.WATER);
  paintEllipse(4, 51, 5, 6, TERRAIN.WATER);
  paintRect(27, 0, 29, 13, TERRAIN.WATER);
  paintRect(0, 25, 8, 27, TERRAIN.WATER);

  paintRect(0, 0, WIDTH - 1, 0, TERRAIN.WALL);
  paintRect(0, HEIGHT - 1, WIDTH - 1, HEIGHT - 1, TERRAIN.WALL);
  paintRect(0, 0, 0, HEIGHT - 1, TERRAIN.WALL);
  paintRect(WIDTH - 1, 0, WIDTH - 1, HEIGHT - 1, TERRAIN.WALL);

  [
    [4, 5], [5, 5], [5, 6], [13, 5], [14, 6], [21, 7], [22, 7],
    [33, 5], [34, 6], [44, 8], [45, 8], [52, 12], [53, 13],
    [3, 17], [4, 18], [9, 21], [10, 22], [18, 28], [19, 29],
    [40, 29], [41, 29], [50, 27], [51, 28], [14, 36], [15, 37],
    [27, 43], [28, 44], [44, 47], [45, 48], [51, 55],
  ].forEach(([x, y]) => {
    terrain[y][x] = TERRAIN.TREE;
  });

  [
    [11, 31], [12, 31], [23, 25], [24, 25], [36, 21], [37, 22],
    [6, 40], [21, 50], [22, 50], [47, 18], [48, 18], [55, 41],
  ].forEach(([x, y]) => {
    terrain[y][x] = TERRAIN.ROCK;
  });

  // Keep key destinations and building doors reachable after decorative terrain passes.
  [
    [8, 8], [10, 9], [15, 12], [15, 20], [18, 14], [22, 13],
    [25, 20], [39, 15], [42, 24], [49, 37], [35, 40], [54, 50],
  ].forEach(([x, y]) => {
    terrain[y][x] = TERRAIN.PATH;
  });

  return terrain;
}

export const WORLD = {
  id: "digital_dreamscape_starting_region",
  name: "Starting Region",
  width: WIDTH,
  height: HEIGHT,
  tileSize: 32,
  spawn: { x: 8, y: 8 },
  terrain: createTerrain(),
  objects: [
    {
      id: "guide_001",
      type: "npc",
      name: "Guide",
      x: 10,
      y: 9,
      blocksMovement: true,
      interaction: {
        type: "dialogue",
        text: "Welcome to Digital Dreamscape. Click a walkable tile and your explorer will walk there.",
      },
    },
    {
      id: "cartographer_001",
      type: "npc",
      name: "Cartographer",
      x: 18,
      y: 14,
      blocksMovement: true,
      interaction: {
        type: "dialogue",
        text: "The region is bigger than this viewport. Follow the paths to find doors, portals, and resource nodes.",
      },
    },
    {
      id: "keeper_001",
      type: "npc",
      name: "Gate Keeper",
      x: 35,
      y: 40,
      blocksMovement: true,
      interaction: {
        type: "dialogue",
        text: "The encounter gate is only a marker for now. Combat will arrive in a later phase.",
      },
    },
    {
      id: "dream_archive",
      type: "building",
      name: "Dream Archive",
      x: 12,
      y: 16,
      width: 6,
      height: 5,
      blocksMovement: true,
      door: { x: 15, y: 20 },
      interaction: {
        type: "message",
        text: "The Dream Archive doors shimmer. Interior rooms will unlock in a future build.",
      },
    },
    {
      id: "silver_observatory",
      type: "building",
      name: "Silver Observatory",
      x: 36,
      y: 10,
      width: 7,
      height: 6,
      blocksMovement: true,
      door: { x: 39, y: 15 },
      interaction: {
        type: "message",
        text: "A silver lens tracks the sky. World event systems will connect here later.",
      },
    },
    {
      id: "waypoint_hall",
      type: "building",
      name: "Waypoint Hall",
      x: 46,
      y: 32,
      width: 6,
      height: 6,
      blocksMovement: true,
      door: { x: 49, y: 37 },
      interaction: {
        type: "portal_locked",
        text: "Waypoint Hall recognizes you, but long-range travel is still locked.",
      },
    },
    {
      id: "glowleaf_grove",
      type: "resource",
      name: "Glowleaf Grove",
      x: 22,
      y: 13,
      blocksMovement: false,
      interaction: {
        type: "message",
        text: "Glowleaf fronds pulse softly. Gathering and skill XP will come in Phase 2.",
      },
    },
    {
      id: "moonstone_outcrop",
      type: "resource",
      name: "Moonstone Outcrop",
      x: 7,
      y: 35,
      blocksMovement: false,
      interaction: {
        type: "message",
        text: "A moonstone seam catches the light. Mining will be added after the walking loop is proven.",
      },
    },
    {
      id: "training_marker_001",
      type: "marker",
      name: "Training Marker",
      x: 15,
      y: 12,
      blocksMovement: false,
      interaction: {
        type: "training_placeholder",
        text: "Training systems will unlock here later.",
      },
    },
    {
      id: "portal_001",
      type: "portal",
      name: "Blue Portal",
      x: 25,
      y: 20,
      blocksMovement: false,
      interaction: {
        type: "portal_locked",
        text: "This blue portal hums, but it is not active yet.",
      },
    },
    {
      id: "portal_002",
      type: "portal",
      name: "North Star Marker",
      x: 54,
      y: 50,
      blocksMovement: false,
      interaction: {
        type: "message",
        text: "The marker records your visit. Shared world progression will use markers like this later.",
      },
    },
    {
      id: "encounter_gate_001",
      type: "gate",
      name: "Encounter Gate",
      x: 42,
      y: 24,
      blocksMovement: false,
      interaction: {
        type: "encounter_placeholder",
        text: "An encounter gate stands here, but combat is not active in this MVP.",
      },
    },
    {
      id: "shop_kiosk_001",
      type: "marker",
      name: "Market Kiosk",
      x: 29,
      y: 18,
      blocksMovement: false,
      interaction: {
        type: "shop_placeholder",
        text: "The market kiosk is closed. Shops and currency sinks will come later.",
      },
    },
  ],
};

export const BLOCKED_TERRAIN = new Set([
  TERRAIN.WATER,
  TERRAIN.WALL,
  TERRAIN.TREE,
  TERRAIN.ROCK,
]);
