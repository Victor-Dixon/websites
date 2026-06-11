/* Digital Dreamscape — player movement state */
(function (global) {
  "use strict";

  var STEP_MS = global.DD_WORLD_DATA.STEP_MS;

  function createPlayer(x, y) {
    return {
      x: x,
      y: y,
      facing: "down",
      moving: false,
      renderX: x,
      renderY: y,
      moveQueue: [],
      animStart: 0,
      animFrom: { x: x, y: y },
      animTo: { x: x, y: y }
    };
  }

  function setFacing(player, dx, dy) {
    if (dx < 0) player.facing = "left";
    else if (dx > 0) player.facing = "right";
    else if (dy < 0) player.facing = "up";
    else if (dy > 0) player.facing = "down";
  }

  function beginStep(player, tx, ty) {
    if (player.moving) return false;
    player.animFrom = { x: player.x, y: player.y };
    player.animTo = { x: tx, y: ty };
    setFacing(player, tx - player.x, ty - player.y);
    player.moving = true;
    player.animStart = performance.now();
    return true;
  }

  function finishStep(player) {
    player.x = player.animTo.x;
    player.y = player.animTo.y;
    player.renderX = player.x;
    player.renderY = player.y;
    player.moving = false;
  }

  function updateAnimation(player, now) {
    if (!player.moving) return false;
    var t = Math.min(1, (now - player.animStart) / STEP_MS);
    player.renderX = player.animFrom.x + (player.animTo.x - player.animFrom.x) * t;
    player.renderY = player.animFrom.y + (player.animTo.y - player.animFrom.y) * t;
    if (t >= 1) {
      finishStep(player);
      return true;
    }
    return false;
  }

  function queuePath(player, path) {
    if (!path || !path.length) return;
    player.moveQueue = path.slice();
  }

  function stepQueue(player, isWalkable) {
    if (!player.moveQueue.length || player.moving) return null;
    var next = player.moveQueue.shift();
    if (next.x === player.x && next.y === player.y) {
      return stepQueue(player, isWalkable);
    }
    if (!isWalkable(next.x, next.y)) {
      player.moveQueue = [];
      return null;
    }
    if (!beginStep(player, next.x, next.y)) {
      player.moveQueue = [];
      return null;
    }
    return next;
  }

  global.DD_PLAYER = {
    createPlayer: createPlayer,
    setFacing: setFacing,
    beginStep: beginStep,
    finishStep: finishStep,
    updateAnimation: updateAnimation,
    queuePath: queuePath,
    stepQueue: stepQueue
  };
})(typeof window !== "undefined" ? window : global);
