export function createCamera(canvas, world) {
  return {
    x: 0,
    y: 0,
    width: canvas.width,
    height: canvas.height,
    worldPixelWidth: world.width * world.tileSize,
    worldPixelHeight: world.height * world.tileSize,
  };
}

export function resizeCamera(camera, canvas, world) {
  const rect = canvas.getBoundingClientRect();
  const width = Math.max(320, Math.floor(rect.width));
  const height = Math.max(320, Math.floor(rect.height));

  if (canvas.width !== width || canvas.height !== height) {
    canvas.width = width;
    canvas.height = height;
  }

  camera.width = width;
  camera.height = height;
  camera.worldPixelWidth = world.width * world.tileSize;
  camera.worldPixelHeight = world.height * world.tileSize;
}

export function clampCamera(camera) {
  camera.x = Math.max(0, Math.min(camera.x, Math.max(0, camera.worldPixelWidth - camera.width)));
  camera.y = Math.max(0, Math.min(camera.y, Math.max(0, camera.worldPixelHeight - camera.height)));
}

export function followPlayer(camera, player, world) {
  const playerCenterX = (player.x + 0.5) * world.tileSize;
  const playerCenterY = (player.y + 0.5) * world.tileSize;

  camera.x = playerCenterX - (camera.width / 2);
  camera.y = playerCenterY - (camera.height / 2);
  clampCamera(camera);
}

export function screenToWorldTile(camera, world, clientX, clientY, canvas) {
  const rect = canvas.getBoundingClientRect();
  const screenX = clientX - rect.left;
  const screenY = clientY - rect.top;
  const worldX = screenX + camera.x;
  const worldY = screenY + camera.y;

  return {
    x: Math.floor(worldX / world.tileSize),
    y: Math.floor(worldY / world.tileSize),
  };
}
