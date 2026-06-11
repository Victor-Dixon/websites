/* Digital Dreamscape — BFS pathfinding */
(function (global) {
  "use strict";

  var DATA = null;

  function idx(x, y, cols) {
    return y * cols + x;
  }

  function findPath(sx, sy, ex, ey, isWalkable, cols, rows) {
    if (sx === ex && sy === ey) return [];
    if (!isWalkable(ex, ey)) return null;

    var startIdx = idx(sx, sy, cols);
    var endIdx = idx(ex, ey, cols);
    var dist = new Array(cols * rows);
    var parent = new Array(cols * rows);
    var i;
    for (i = 0; i < dist.length; i++) {
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

      var neighbors = [[x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1]];
      var n;
      for (n = 0; n < neighbors.length; n++) {
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

  global.DD_PATHFINDING = {
    findPath: findPath
  };
})(typeof window !== "undefined" ? window : global);
