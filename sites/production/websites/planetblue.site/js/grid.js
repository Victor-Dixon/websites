/* Planet Blue — grid rendering and highlights */
(function (global) {
  "use strict";

  function unitAt(units, x, y) {
    for (var i = 0; i < units.length; i++) {
      var u = units[i];
      if (u.hp > 0 && u.x === x && u.y === y) return u;
    }
    return null;
  }

  function renderBattlefield(container, options) {
    var cols = options.cols;
    var rows = options.rows;
    var terrain = options.terrain;
    var units = options.units;
    var selectedId = options.selectedId;
    var moveTargets = options.moveTargets || [];
    var attackTargets = options.attackTargets || [];
    var phase = options.phase;
    var onCellClick = options.onCellClick;
    var onUnitClick = options.onUnitClick;

    container.innerHTML = "";
    container.style.gridTemplateColumns = "repeat(" + cols + ", 1fr)";

    for (var y = 0; y < rows; y++) {
      for (var x = 0; x < cols; x++) {
        var cell = document.createElement("div");
        var ti = y * cols + x;
        cell.className = "cell " + terrain[ti];
        cell.dataset.x = String(x);
        cell.dataset.y = String(y);
        cell.setAttribute("role", "gridcell");

        if (selectedId) {
          var sel = null;
          for (var s = 0; s < units.length; s++) {
            if (units[s].id === selectedId) sel = units[s];
          }
          if (sel && sel.x === x && sel.y === y) cell.classList.add("selected");
        }

        for (var m = 0; m < moveTargets.length; m++) {
          if (moveTargets[m].x === x && moveTargets[m].y === y) cell.classList.add("move-target");
        }
        for (var a = 0; a < attackTargets.length; a++) {
          if (attackTargets[a].x === x && attackTargets[a].y === y) cell.classList.add("attack-target");
        }

        var unit = unitAt(units, x, y);
        if (unit) {
          var token = document.createElement("div");
          token.className = "unit " + unit.team;
          if (unit.team === "player" && unit.moved && unit.acted) token.classList.add("spent");
          token.textContent = unit.glyph || "??";
          token.title = unit.label || "";

          var pip = document.createElement("div");
          pip.className = "hp-pip";
          var fill = document.createElement("span");
          fill.style.width = Math.round((unit.hp / unit.maxHp) * 100) + "%";
          pip.appendChild(fill);
          token.appendChild(pip);

          if (unit.team === "player" && phase === "player" && !(unit.moved && unit.acted)) {
            token.style.cursor = "pointer";
            (function (uid) {
              token.addEventListener("click", function (e) {
                e.stopPropagation();
                if (onUnitClick) onUnitClick(uid);
              });
            })(unit.id);
          }

          cell.appendChild(token);
        }

        cell.addEventListener("click", function (cx, cy) {
          if (onCellClick) onCellClick(cx, cy);
        }.bind(null, x, y));

        container.appendChild(cell);
      }
    }
  }

  global.PLANET_BLUE_GRID = {
    renderBattlefield: renderBattlefield,
    unitAt: unitAt
  };
})(typeof window !== "undefined" ? window : global);
