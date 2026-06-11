(function () {
  "use strict";

  var QUADRANT_COLORS = [
    "rgba(98,223,255,0.45)", "rgba(255,215,46,0.45)", "rgba(255,53,93,0.4)",
    "rgba(192,132,252,0.4)", "rgba(50,255,156,0.35)", "rgba(249,115,22,0.35)"
  ];

  function normalizeCoord(value) {
    return String(value || "").trim().toUpperCase();
  }

  function quadrantColorIndex(quadrants, id) {
    var idx = quadrants.findIndex(function (q) { return q.id === id; });
    return idx >= 0 ? idx % QUADRANT_COLORS.length : 0;
  }

  function missionById(missions, id) {
    return missions.find(function (m) { return m.id === id; });
  }

  function renderMissionCard(m) {
    return (
      '<article class="mission-card mission-' + m.status + '">' +
      '<div class="mission-meta">' + m.type + " · " + m.status + " · threat: " + (m.threat || "unknown") + "</div>" +
      "<h3>" + m.title + "</h3>" +
      "<p><strong>" + (m.district || "") + "</strong></p>" +
      "<p>" + (m.description || "") + "</p>" +
      '<p class="reward">Reward: ' + (m.reward || "World state update") + "</p>" +
      '<a class="mission-btn" href="/meridian-dispatch/">Open Mission</a>' +
      "</article>"
    );
  }

  function renderQuadrantPanel(q, missions, mount) {
    if (!q) {
      mount.querySelector("[data-panel-title]").textContent = "Select a district";
      mount.querySelector("[data-panel-body]").innerHTML =
        "<p>Click a colored district on the quadrant map to view lore and missions.</p>";
      return;
    }

    var linked = (q.mission_ids || []).map(function (id) {
      return missionById(missions, id);
    }).filter(Boolean);

    var imgHtml = q.image
      ? '<img src="/' + q.image.replace(/^\//, "") + '" alt="' + q.label + '" style="width:100%;max-height:180px;object-fit:cover;border:3px solid #050505;margin-bottom:12px" onerror="this.style.display=\'none\'">'
      : "";

    var tags = (q.tags || []).map(function (t) {
      return '<span class="pill">' + t.replace(/_/g, " ") + "</span>";
    }).join(" ");

    mount.querySelector("[data-panel-title]").textContent = q.label;
    mount.querySelector("[data-panel-body]").innerHTML =
      imgHtml +
      "<p>" + (q.lore || "No lore recorded for this district yet.") + "</p>" +
      (tags ? '<div class="legend" style="margin:12px 0">' + tags + "</div>" : "") +
      (linked.length
        ? "<h3 class=\"panel-section-title\">District Missions</h3>" + linked.map(renderMissionCard).join("")
        : "<p><em>No missions assigned to this district.</em></p>");
  }

  function buildQuadrantGrid(cityMap, missions, gridEl, panelEl) {
    var cols = cityMap.grid.cols;
    var rows = cityMap.grid.rows;
    var quadrants = cityMap.quadrants || [];

    gridEl.style.gridTemplateColumns = "repeat(" + cols + ", 1fr)";
    gridEl.style.gridTemplateRows = "repeat(" + rows + ", 1fr)";
    gridEl.innerHTML = "";

    function quadrantAt(x, y) {
      return quadrants.find(function (q) {
        var b = q.bounds;
        return x >= b.x1 && x <= b.x2 && y >= b.y1 && y <= b.y2;
      });
    }

    for (var y = 0; y < rows; y++) {
      for (var x = 0; x < cols; x++) {
        var q = quadrantAt(x, y);
        var cell = document.createElement("button");
        cell.type = "button";
        cell.className = "map-cell" + (q ? " has-quadrant" : "");
        cell.dataset.x = String(x);
        cell.dataset.y = String(y);
        if (q) {
          cell.dataset.quadrant = q.id;
          cell.style.background = QUADRANT_COLORS[quadrantColorIndex(quadrants, q.id)];
          cell.title = q.label;
        }
        cell.addEventListener("click", function () {
          var qid = this.dataset.quadrant;
          if (!qid) return;
          var selected = quadrants.find(function (item) { return item.id === qid; });
          gridEl.querySelectorAll(".is-selected").forEach(function (c) {
            c.classList.remove("is-selected");
          });
          gridEl.querySelectorAll('[data-quadrant="' + qid + '"]').forEach(function (c) {
            c.classList.add("is-selected");
          });
          renderQuadrantPanel(selected, missions, panelEl);

          var url = new URL(window.location.href);
          url.searchParams.set("district", qid);
          window.history.replaceState({}, "", url);
        });
        gridEl.appendChild(cell);
      }
    }

    var url = new URL(window.location.href);
    var district = url.searchParams.get("district");
    if (district) {
      var q = quadrants.find(function (item) { return item.id === district; });
      if (q) renderQuadrantPanel(q, missions, panelEl);
    }
  }

  window.SparkCityMapPlayer = {
    boot: function (opts) {
      opts = opts || {};
      var gridEl = opts.gridEl;
      var panelEl = opts.panelEl;
      if (!gridEl || !panelEl) return Promise.resolve(false);

      return Promise.all([
        fetch("/assets/data/spark/city_map.json", { cache: "no-store" }).then(function (r) {
          return r.ok ? r.json() : null;
        }),
        fetch("/assets/data/missions.json", { cache: "no-store" }).then(function (r) {
          return r.ok ? r.json() : [];
        })
      ]).then(function (results) {
        var cityMap = results[0];
        var missions = Array.isArray(results[1]) ? results[1] : [];
        if (!cityMap || !cityMap.quadrants || !cityMap.quadrants.length) {
          return false;
        }
        buildQuadrantGrid(cityMap, missions, gridEl, panelEl);
        return true;
      });
    },
    normalizeCoord: normalizeCoord
  };
})();
