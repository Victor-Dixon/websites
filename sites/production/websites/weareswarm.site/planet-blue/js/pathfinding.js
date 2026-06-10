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
    var tiles = [];
    for (var y = 0; y < rows; y++) {
      for (var x = 0; x < cols; x++) {
        var target = null;
        for (var i = 0; i < units.length; i++) {
          var u = units[i];
          if (u.hp > 0 && u.x === x && u.y === y) target = u;
        }
        if (!target || target.team === unit.team) continue;
        var dist = manhattan(unit.x, unit.y, x, y);
        if (dist <= unit.range) tiles.push({ x: x, y: y, targetId: target.id });
      }
    }
    return tiles;
  }

  global.PLANET_BLUE_PATH = {
    reachableTiles: reachableTiles,
    attackableTiles: attackableTiles,
    manhattan: manhattan
  };
})(typeof window !== "undefined" ? window : global);
