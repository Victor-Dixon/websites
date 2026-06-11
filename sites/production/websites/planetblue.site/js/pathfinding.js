/* Planet Blue — BFS movement range */
(function (global) {
  "use strict";

  function idx(x, y, cols) {
    return y * cols + x;
  }

  function reachableTiles(unit, terrain, units, cols, rows, terrainGrass) {
    var move = unit.move;
    var dist = new Array(cols * rows);
    for (var i = 0; i < dist.length; i++) dist[i] = Infinity;

    var queue = [[unit.x, unit.y, 0]];
    dist[idx(unit.x, unit.y, cols)] = 0;
    var tiles = [];

    while (queue.length) {
      var item = queue.shift();
      var x = item[0];
      var y = item[1];
      var d = item[2];

      if (d > 0) tiles.push({ x: x, y: y });

      if (d >= move) continue;

      var neighbors = [
        [x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1]
      ];

      for (var n = 0; n < neighbors.length; n++) {
        var nx = neighbors[n][0];
        var ny = neighbors[n][1];
        if (nx < 0 || ny < 0 || nx >= cols || ny >= rows) continue;

        var ti = idx(nx, ny, cols);
        if (terrain[ti] !== terrainGrass) continue;

        var blocked = false;
        for (var u = 0; u < units.length; u++) {
          var ou = units[u];
          if (ou.hp > 0 && ou.x === nx && ou.y === ny && !(nx === unit.x && ny === unit.y)) {
            blocked = true;
            break;
          }
        }
        if (blocked) continue;

        var nd = d + 1;
        if (nd < dist[ti]) {
          dist[ti] = nd;
          queue.push([nx, ny, nd]);
        }
      }
    }

    return tiles.filter(function (t) {
      return t.x !== unit.x || t.y !== unit.y;
    });
  }

  function manhattan(ax, ay, bx, by) {
    return Math.abs(ax - bx) + Math.abs(ay - by);
  }

  function attackableTiles(unit, units, cols, rows) {
    return combinedAttackFromPositions([{ x: unit.x, y: unit.y }], unit, units, cols, rows).enemies;
  }

  /* Shining Force style: union of attack zones from one or more positions (current + reachable). */
  function combinedAttackFromPositions(positions, unit, units, cols, rows) {
    var enemies = [];
    var enemySeen = {};
    var zones = [];
    var zoneSeen = {};
    var range = unit.range;

    for (var p = 0; p < positions.length; p++) {
      var px = positions[p].x;
      var py = positions[p].y;
      for (var y = 0; y < rows; y++) {
        for (var x = 0; x < cols; x++) {
          if (manhattan(px, py, x, y) > range) continue;
          var key = x + "," + y;
          if (!zoneSeen[key]) {
            zoneSeen[key] = true;
            zones.push({ x: x, y: y });
          }
          var target = null;
          for (var i = 0; i < units.length; i++) {
            var u = units[i];
            if (u.hp > 0 && u.x === x && u.y === y) target = u;
          }
          if (!target || target.team === unit.team || enemySeen[target.id]) continue;
          enemySeen[target.id] = true;
          enemies.push({ x: x, y: y, targetId: target.id });
        }
      }
    }

    return { enemies: enemies, zones: zones };
  }

  function attackRangeForUnit(unit, terrain, units, cols, rows, terrainGrass) {
    var positions = [{ x: unit.x, y: unit.y }];
    if (!unit.moved) {
      positions = positions.concat(reachableTiles(unit, terrain, units, cols, rows, terrainGrass));
    }
    return combinedAttackFromPositions(positions, unit, units, cols, rows);
  }

  function findPath(sx, sy, ex, ey, isWalkable, cols, rows) {
    if (sx === ex && sy === ey) return [];
    if (!isWalkable(ex, ey)) return null;

    var startIdx = idx(sx, sy, cols);
    var endIdx = idx(ex, ey, cols);
    var dist = new Array(cols * rows);
    var parent = new Array(cols * rows);
    for (var i = 0; i < dist.length; i++) {
      dist[i] = Infinity;
      parent[i] = -1;
    }

    var queue = [[sx, sy]];
    dist[startIdx] = 0;

    while (queue.length) {
      var item = queue.shift();
      var x = item[0];
      var y = item[1];
      var ti = idx(x, y, cols);

      if (ti === endIdx) {
        var path = [];
        var cur = endIdx;
        while (cur !== startIdx && parent[cur] !== -1) {
          path.unshift({ x: cur % cols, y: Math.floor(cur / cols) });
          cur = parent[cur];
        }
        return path;
      }

      var neighbors = [
        [x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1]
      ];

      for (var n = 0; n < neighbors.length; n++) {
        var nx = neighbors[n][0];
        var ny = neighbors[n][1];
        if (nx < 0 || ny < 0 || nx >= cols || ny >= rows) continue;
        if (!isWalkable(nx, ny)) continue;

        var ni = idx(nx, ny, cols);
        var nd = dist[ti] + 1;
        if (nd < dist[ni]) {
          dist[ni] = nd;
          parent[ni] = ti;
          queue.push([nx, ny]);
        }
      }
    }

    return null;
  }

  global.PLANET_BLUE_PATH = {
    reachableTiles: reachableTiles,
    attackableTiles: attackableTiles,
    combinedAttackFromPositions: combinedAttackFromPositions,
    attackRangeForUnit: attackRangeForUnit,
    manhattan: manhattan,
    findPath: findPath
  };
})(typeof window !== "undefined" ? window : global);
