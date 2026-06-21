export const TERRAIN_ATLAS_MANIFEST_URL = "./assets/terrain/terrain-atlas-manifest.json";

const FALLBACK_MANIFEST = {
  id: "digital_dreamscape_premium_terrain_atlas_v1_fallback",
  tileSize: 64,
  tiles: {
    grass: "./assets/terrain/tiles/grass-tile.svg",
    moss: "./assets/terrain/tiles/moss-grass-tile.svg",
    path: "./assets/terrain/tiles/stone-path-tile.svg",
    dirtPath: "./assets/terrain/tiles/dirt-path-tile.svg",
    stone: "./assets/terrain/tiles/cracked-stone-tile.svg",
    ruin: "./assets/terrain/tiles/ruin-tile.svg",
    water: "./assets/terrain/tiles/water-tile.svg",
  },
  props: {
    stoneRuinWall: "./assets/terrain/props/stone-ruin-wall.svg",
    pineTree: "./assets/terrain/props/pine-tree.svg",
    crystalEncounterGate: "./assets/terrain/props/crystal-encounter-gate.svg",
    waypointPedestal: "./assets/terrain/props/waypoint-pedestal.svg",
  },
  terrainMap: {
    grass: "grass",
    plain: "moss",
    path: "path",
    water: "water",
    wall: "ruin",
    tree: "moss",
    rock: "ruin",
  },
  propMap: {
    building: "stoneRuinWall",
    tree: "pineTree",
    wall: "stoneRuinWall",
    rock: "stoneRuinWall",
    gate: "crystalEncounterGate",
    portal: "waypointPedestal",
    marker: "waypointPedestal",
    resource: "crystalEncounterGate",
  },
};

const imageCache = new Map();
const atlasState = {
  manifestStatus: "loading",
  manifest: FALLBACK_MANIFEST,
  requested: 0,
  loaded: 0,
  failed: 0,
};

function assetUrl(src) {
  return src;
}

function imageFor(src) {
  if (!src) return null;
  const resolved = assetUrl(src);
  if (imageCache.has(resolved)) return imageCache.get(resolved);

  const image = new Image();
  const entry = {
    image,
    loaded: false,
    error: false,
    src: resolved,
  };

  atlasState.requested += 1;
  image.addEventListener("load", () => {
    entry.loaded = true;
    atlasState.loaded += 1;
  });
  image.addEventListener("error", () => {
    entry.error = true;
    atlasState.failed += 1;
  });
  image.src = resolved;
  imageCache.set(resolved, entry);
  return entry;
}

async function loadTerrainAtlasManifest() {
  try {
    const response = await fetch(TERRAIN_ATLAS_MANIFEST_URL, { cache: "force-cache" });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    atlasState.manifest = await response.json();
    atlasState.manifestStatus = "ready";
  } catch (error) {
    atlasState.manifestStatus = "fallback";
    atlasState.error = error?.message || "manifest unavailable";
  }

  preloadTerrainAtlas();
}

function preloadTerrainAtlas() {
  const manifest = atlasState.manifest;
  Object.values(manifest.tiles || {}).forEach((src) => imageFor(src));
  Object.values(manifest.props || {}).forEach((src) => imageFor(src));
}

loadTerrainAtlasManifest();

function tileKeyForTerrain(terrainType) {
  const manifest = atlasState.manifest;
  return manifest.terrainMap?.[terrainType] || "grass";
}

function propKeyForObject(object) {
  const manifest = atlasState.manifest;
  if (object.atlasKey) return object.atlasKey;
  return manifest.propMap?.[object.type] || null;
}

export function drawAtlasTerrainTile(ctx, terrainType, x, y, size) {
  const manifest = atlasState.manifest;
  const tileKey = tileKeyForTerrain(terrainType);
  const entry = imageFor(manifest.tiles?.[tileKey]);

  if (!entry?.loaded || entry.error) return false;

  ctx.drawImage(entry.image, x, y, size, size);
  return true;
}

export function drawAtlasProp(ctx, object, camera, world) {
  const manifest = atlasState.manifest;
  const propKey = propKeyForObject(object);
  const entry = imageFor(manifest.props?.[propKey]);

  if (!entry?.loaded || entry.error) return false;

  const size = world.tileSize;
  const widthTiles = object.width || 1;
  const heightTiles = object.height || 1;
  const screenX = (object.x * size) - camera.x;
  const screenY = (object.y * size) - camera.y;
  const drawWidth = object.drawWidth || Math.max(size * widthTiles, propKey === "pineTree" ? 46 : size);
  const drawHeight = object.drawHeight || Math.max(size * heightTiles, propKey === "pineTree" ? 68 : size * 1.3);
  const anchorX = object.anchorX ?? .5;
  const anchorY = object.anchorY ?? 1;
  const baseX = screenX + ((widthTiles * size) / 2);
  const baseY = screenY + (heightTiles * size);

  ctx.save();
  ctx.imageSmoothingEnabled = true;
  ctx.shadowColor = propKey === "crystalEncounterGate"
    ? "rgba(255, 92, 255, .28)"
    : propKey === "waypointPedestal"
      ? "rgba(92, 244, 255, .26)"
      : "rgba(0, 0, 0, .18)";
  ctx.shadowBlur = propKey === "stoneRuinWall" ? 6 : 12;
  ctx.drawImage(
    entry.image,
    baseX - (drawWidth * anchorX),
    baseY - (drawHeight * anchorY),
    drawWidth,
    drawHeight,
  );
  ctx.restore();
  return true;
}

export function getTerrainAtlasStatus() {
  return {
    manifest: atlasState.manifest?.id || "unknown",
    manifestStatus: atlasState.manifestStatus,
    requested: atlasState.requested,
    loaded: atlasState.loaded,
    failed: atlasState.failed,
    cached: imageCache.size,
    error: atlasState.error || null,
  };
}
