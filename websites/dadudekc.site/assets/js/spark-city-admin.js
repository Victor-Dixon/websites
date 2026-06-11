(function () {
  "use strict";

  var LS_DRAFT = "spark.city_map.draft.v1";
  var LS_IMAGES = "spark.city_map.images.v1";
  var GRID_MIN = 4;
  var GRID_MAX = 120;
  var QUADRANT_COLORS = [
    "#62dfff88", "#ffd72e88", "#ff355d88", "#c084fc88",
    "#32ff9c88", "#f9731688", "#38bdf888", "#f472b688"
  ];

  var state = {
    map: null,
    missions: [],
    activeQuadrantId: null,
    dragStart: null,
    dragEnd: null,
    imageCache: {}
  };

  function $(sel) { return document.querySelector(sel); }

  function escapeHTML(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[ch];
    });
  }

  function readJSON(key, fallback) {
    try {
      var raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (e) {
      return fallback;
    }
  }

  function writeJSON(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  function defaultMap() {
    return {
      schema: "spark.city_map.v1",
      city_id: "meridian",
      city_name: "Meridian City",
      grid: { cols: 40, rows: 40 },
      map_image: "/assets/maps/meridian-city-map.png",
      quadrants: []
    };
  }

  function quadrantAtCell(x, y) {
    return (state.map.quadrants || []).find(function (q) {
      var b = q.bounds;
      return x >= b.x1 && x <= b.x2 && y >= b.y1 && y <= b.y2;
    });
  }

  function quadrantColorIndex(id) {
    var idx = (state.map.quadrants || []).findIndex(function (q) { return q.id === id; });
    return idx >= 0 ? idx % QUADRANT_COLORS.length : 0;
  }

  function clampBounds() {
    var cols = state.map.grid.cols;
    var rows = state.map.grid.rows;
    (state.map.quadrants || []).forEach(function (q) {
      q.bounds.x1 = Math.max(0, Math.min(q.bounds.x1, cols - 1));
      q.bounds.y1 = Math.max(0, Math.min(q.bounds.y1, rows - 1));
      q.bounds.x2 = Math.max(q.bounds.x1, Math.min(q.bounds.x2, cols - 1));
      q.bounds.y2 = Math.max(q.bounds.y1, Math.min(q.bounds.y2, rows - 1));
    });
  }

  function normalizeDrag(a, b) {
    return {
      x1: Math.min(a.x, b.x),
      y1: Math.min(a.y, b.y),
      x2: Math.max(a.x, b.x),
      y2: Math.max(a.y, b.y)
    };
  }

  function renderGrid() {
    var shell = $("#city-admin-grid-shell");
    var grid = $("#city-admin-grid");
    if (!grid || !shell) return;

    var cols = state.map.grid.cols;
    var rows = state.map.grid.rows;
    grid.style.gridTemplateColumns = "repeat(" + cols + ", 1fr)";
    grid.innerHTML = "";

    var cellSize = Math.max(8, Math.min(18, Math.floor(600 / Math.max(cols, rows))));
    grid.style.setProperty("--cell-size", cellSize + "px");

    for (var y = 0; y < rows; y++) {
      for (var x = 0; x < cols; x++) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "city-admin-cell";
        btn.dataset.x = String(x);
        btn.dataset.y = String(y);
        btn.setAttribute("aria-label", "Cell " + x + "," + y);

        var q = quadrantAtCell(x, y);
        if (q) {
          btn.classList.add("in-quadrant");
          btn.style.background = QUADRANT_COLORS[quadrantColorIndex(q.id)];
          if (q.id === state.activeQuadrantId) {
            btn.classList.add("is-active-quadrant");
          }
        }

        if (state.dragStart && state.dragEnd) {
          var b = normalizeDrag(state.dragStart, state.dragEnd);
          if (x >= b.x1 && x <= b.x2 && y >= b.y1 && y <= b.y2) {
            btn.classList.add("is-selecting");
          }
        }

        btn.addEventListener("mousedown", onCellMouseDown);
        btn.addEventListener("mouseenter", onCellMouseEnter);
        grid.appendChild(btn);
      }
    }
  }

  function onCellMouseDown(e) {
    e.preventDefault();
    var x = parseInt(e.currentTarget.dataset.x, 10);
    var y = parseInt(e.currentTarget.dataset.y, 10);
    state.dragStart = { x: x, y: y };
    state.dragEnd = { x: x, y: y };
    renderGrid();

    function onMove(ev) {
      var cell = ev.target.closest(".city-admin-cell");
      if (!cell) return;
      state.dragEnd = {
        x: parseInt(cell.dataset.x, 10),
        y: parseInt(cell.dataset.y, 10)
      };
      renderGrid();
    }

    function onUp() {
      document.removeEventListener("mousemove", onMove);
      document.removeEventListener("mouseup", onUp);
      if (state.dragStart && state.dragEnd && state.activeQuadrantId) {
        var q = state.map.quadrants.find(function (item) {
          return item.id === state.activeQuadrantId;
        });
        if (q) {
          q.bounds = normalizeDrag(state.dragStart, state.dragEnd);
          saveDraft();
          renderEditor();
        }
      }
      state.dragStart = null;
      state.dragEnd = null;
      renderGrid();
    }

    document.addEventListener("mousemove", onMove);
    document.addEventListener("mouseup", onUp);
  }

  function onCellMouseEnter(e) {
    if (!state.dragStart) return;
    state.dragEnd = {
      x: parseInt(e.currentTarget.dataset.x, 10),
      y: parseInt(e.currentTarget.dataset.y, 10)
    };
    renderGrid();
  }

  function renderQuadrantList() {
    var list = $("#city-admin-quadrant-list");
    if (!list) return;

    list.innerHTML = (state.map.quadrants || []).map(function (q) {
      return (
        '<li class="' + (q.id === state.activeQuadrantId ? "active" : "") + '" data-qid="' +
        escapeHTML(q.id) + '">' +
        "<span>" + escapeHTML(q.label) + "</span>" +
        '<button type="button" class="city-admin-btn danger" data-del="' + escapeHTML(q.id) + '" style="padding:2px 6px;font-size:0.7rem">×</button>' +
        "</li>"
      );
    }).join("") || "<li><em>No quadrants yet.</em></li>";

    list.querySelectorAll("[data-qid]").forEach(function (li) {
      li.addEventListener("click", function (e) {
        if (e.target.closest("[data-del]")) return;
        state.activeQuadrantId = li.getAttribute("data-qid");
        renderAll();
      });
    });

    list.querySelectorAll("[data-del]").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        var id = btn.getAttribute("data-del");
        state.map.quadrants = state.map.quadrants.filter(function (q) { return q.id !== id; });
        if (state.activeQuadrantId === id) state.activeQuadrantId = null;
        saveDraft();
        renderAll();
      });
    });
  }

  function renderEditor() {
    var mount = $("#city-admin-editor");
    if (!mount) return;

    var q = state.map.quadrants.find(function (item) {
      return item.id === state.activeQuadrantId;
    });

    if (!q) {
      mount.innerHTML = "<p>Select a quadrant or add one to edit bounds, lore, image, and missions.</p>";
      return;
    }

    var imagePreview = "";
    var cached = state.imageCache[q.id];
    if (cached) {
      imagePreview = '<img class="city-admin-preview-img" src="' + cached + '" alt="Preview">';
    } else if (q.image) {
      imagePreview = '<img class="city-admin-preview-img" src="/' + escapeHTML(q.image.replace(/^\//, "")) + '" alt="District art" onerror="this.style.display=\'none\'">';
    }

    var missionChecks = state.missions.map(function (m) {
      var checked = (q.mission_ids || []).indexOf(m.id) !== -1 ? " checked" : "";
      return (
        '<label><input type="checkbox" data-mission="' + escapeHTML(m.id) + '"' + checked + "> " +
        escapeHTML(m.title) + " (" + escapeHTML(m.id) + ")</label>"
      );
    }).join("");

    mount.innerHTML =
      '<div class="city-admin-controls">' +
      '<label>ID<input type="text" id="q-id" value="' + escapeHTML(q.id) + '"></label>' +
      '<label>Label<input type="text" id="q-label" value="' + escapeHTML(q.label) + '"></label>' +
      "</div>" +
      '<div class="city-admin-controls">' +
      '<label>x1<input type="number" id="q-x1" min="0" value="' + q.bounds.x1 + '"></label>' +
      '<label>y1<input type="number" id="q-y1" min="0" value="' + q.bounds.y1 + '"></label>' +
      '<label>x2<input type="number" id="q-x2" min="0" value="' + q.bounds.x2 + '"></label>' +
      '<label>y2<input type="number" id="q-y2" min="0" value="' + q.bounds.y2 + '"></label>' +
      '<button type="button" class="city-admin-btn" id="q-apply-bounds">Apply Bounds</button>' +
      "</div>" +
      '<label>Lore<textarea id="q-lore">' + escapeHTML(q.lore || "") + "</textarea></label>" +
      '<div class="city-admin-controls">' +
      '<label>Image path<input type="text" id="q-image" value="' + escapeHTML(q.image || "") + '" placeholder="assets/spark/map/district.png"></label>' +
      '<label>Upload preview<input type="file" id="q-image-file" accept="image/*"></label>' +
      "</div>" +
      imagePreview +
      '<p style="font-size:0.75rem;color:var(--muted)">Deploy images to <code>assets/spark/map/</code> via SFTP. Path above is stored in exported JSON.</p>' +
      "<h3>Missions</h3>" +
      '<div class="city-admin-missions" id="q-missions">' + (missionChecks || "<em>No missions loaded.</em>") + "</div>" +
      '<div class="city-admin-controls" style="margin-top:0.5rem">' +
      '<label>Tags (comma-separated)<input type="text" id="q-tags" value="' + escapeHTML((q.tags || []).join(", ")) + '"></label>' +
      "</div>";

    $("#q-apply-bounds").addEventListener("click", function () {
      q.bounds = {
        x1: parseInt($("#q-x1").value, 10) || 0,
        y1: parseInt($("#q-y1").value, 10) || 0,
        x2: parseInt($("#q-x2").value, 10) || 0,
        y2: parseInt($("#q-y2").value, 10) || 0
      };
      clampBounds();
      saveDraft();
      renderGrid();
      renderEditor();
    });

    ["q-id", "q-label", "q-lore", "q-image", "q-tags"].forEach(function (id) {
      var el = document.getElementById(id);
      if (!el) return;
      el.addEventListener("change", function () {
        if (id === "q-id") {
          var newId = el.value.trim().replace(/\s+/g, "_").toLowerCase();
          if (newId && newId !== q.id) {
            if (state.imageCache[q.id]) {
              state.imageCache[newId] = state.imageCache[q.id];
              delete state.imageCache[q.id];
            }
            q.id = newId;
            state.activeQuadrantId = newId;
          }
        } else if (id === "q-label") {
          q.label = el.value;
        } else if (id === "q-lore") {
          q.lore = el.value;
        } else if (id === "q-image") {
          q.image = el.value;
        } else if (id === "q-tags") {
          q.tags = el.value.split(",").map(function (t) { return t.trim(); }).filter(Boolean);
        }
        saveDraft();
        renderQuadrantList();
        renderGrid();
      });
    });

    var fileInput = $("#q-image-file");
    if (fileInput) {
      fileInput.addEventListener("change", function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function () {
          state.imageCache[q.id] = reader.result;
          var images = readJSON(LS_IMAGES, {});
          images[q.id] = reader.result;
          writeJSON(LS_IMAGES, images);
          if (!q.image) {
            q.image = "assets/spark/map/" + q.id + ".png";
            var imgEl = $("#q-image");
            if (imgEl) imgEl.value = q.image;
          }
          saveDraft();
          renderEditor();
        };
        reader.readAsDataURL(file);
      });
    }

    var missionsEl = $("#q-missions");
    if (missionsEl) {
      missionsEl.querySelectorAll("[data-mission]").forEach(function (cb) {
        cb.addEventListener("change", function () {
          q.mission_ids = Array.from(missionsEl.querySelectorAll("[data-mission]:checked")).map(function (c) {
            return c.getAttribute("data-mission");
          });
          saveDraft();
        });
      });
    }
  }

  function renderGridControls() {
    var colsEl = $("#grid-cols");
    var rowsEl = $("#grid-rows");
    if (colsEl) colsEl.value = String(state.map.grid.cols);
    if (rowsEl) rowsEl.value = String(state.map.grid.rows);
  }

  function renderAll() {
    renderGridControls();
    renderGrid();
    renderQuadrantList();
    renderEditor();
  }

  function saveDraft() {
    clampBounds();
    writeJSON(LS_DRAFT, state.map);
  }

  function addQuadrant(preset) {
    var template = preset || {
      id: "district_" + String((state.map.quadrants.length + 1)).padStart(2, "0"),
      label: "New District",
      bounds: { x1: 0, y1: 0, x2: 9, y2: 9 },
      image: "",
      lore: "",
      mission_ids: [],
      tags: []
    };
    state.map.quadrants.push(template);
    state.activeQuadrantId = template.id;
    saveDraft();
    renderAll();
  }

  function applyCompassPresets() {
    var cols = state.map.grid.cols;
    var rows = state.map.grid.rows;
    var mx = Math.floor(cols / 2) - 1;
    var my = Math.floor(rows / 2) - 1;
    state.map.quadrants = [
      { id: "nw_district", label: "Northwest Quarter", bounds: { x1: 0, y1: 0, x2: mx, y2: my }, image: "assets/spark/map/nw_district.png", lore: "", mission_ids: [], tags: [] },
      { id: "ne_district", label: "Northeast Quarter", bounds: { x1: mx + 1, y1: 0, x2: cols - 1, y2: my }, image: "assets/spark/map/ne_district.png", lore: "", mission_ids: [], tags: [] },
      { id: "sw_district", label: "Southwest Quarter", bounds: { x1: 0, y1: my + 1, x2: mx, y2: rows - 1 }, image: "assets/spark/map/sw_district.png", lore: "", mission_ids: [], tags: [] },
      { id: "se_district", label: "Southeast Quarter", bounds: { x1: mx + 1, y1: my + 1, x2: cols - 1, y2: rows - 1 }, image: "assets/spark/map/se_district.png", lore: "", mission_ids: [], tags: [] }
    ];
    state.activeQuadrantId = "nw_district";
    saveDraft();
    renderAll();
    setStatus("Applied 4 compass quadrants.", true);
  }

  function setStatus(msg, ok) {
    var el = $("#city-admin-status");
    if (!el) return;
    el.textContent = msg || "";
    el.className = "city-admin-status" + (ok ? " ok" : msg ? " err" : "");
    el.hidden = !msg;
  }

  function exportJson() {
    var blob = new Blob([JSON.stringify(state.map, null, 2)], { type: "application/json" });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "city_map.json";
    a.click();
    URL.revokeObjectURL(a.href);
    setStatus("Exported city_map.json — upload to assets/data/spark/city_map.json", true);
  }

  function copyAiPrompt() {
    var ids = state.missions.map(function (m) { return m.id; }).join(", ");
    var prompt =
      "Generate quadrants for spark.city_map.v1.\n" +
      "Grid: " + state.map.grid.cols + "×" + state.map.grid.rows + "\n" +
      "Mission IDs: " + (ids || "none") + "\n" +
      "See docs/spark/CITY_MAP_AI_PROMPT.md for full template.";
    navigator.clipboard.writeText(prompt).then(function () {
      setStatus("AI prompt copied to clipboard.", true);
    }).catch(function () {
      setStatus("Could not copy — see docs/spark/CITY_MAP_AI_PROMPT.md", false);
    });
  }

  function loadFromServer() {
    return Promise.all([
      fetch("/assets/data/spark/city_map.json", { cache: "no-store" }).then(function (r) {
        return r.ok ? r.json() : null;
      }),
      fetch("/assets/data/missions.json", { cache: "no-store" }).then(function (r) {
        return r.ok ? r.json() : [];
      })
    ]).then(function (results) {
      var draft = readJSON(LS_DRAFT, null);
      state.map = draft || results[0] || defaultMap();
      state.missions = Array.isArray(results[1]) ? results[1] : [];
      state.imageCache = readJSON(LS_IMAGES, {});
      if (!state.activeQuadrantId && state.map.quadrants.length) {
        state.activeQuadrantId = state.map.quadrants[0].id;
      }
    });
  }

  function wireControls() {
    $("#grid-apply").addEventListener("click", function () {
      var cols = Math.max(GRID_MIN, Math.min(GRID_MAX, parseInt($("#grid-cols").value, 10) || 40));
      var rows = Math.max(GRID_MIN, Math.min(GRID_MAX, parseInt($("#grid-rows").value, 10) || 40));
      state.map.grid.cols = cols;
      state.map.grid.rows = rows;
      clampBounds();
      saveDraft();
      renderAll();
      setStatus("Grid resized to " + cols + "×" + rows, true);
    });

    $("#btn-add-quadrant").addEventListener("click", function () { addQuadrant(); });
    $("#btn-compass-presets").addEventListener("click", applyCompassPresets);
    $("#btn-export").addEventListener("click", exportJson);
    $("#btn-copy-prompt").addEventListener("click", copyAiPrompt);
    $("#btn-reload-server").addEventListener("click", function () {
      localStorage.removeItem(LS_DRAFT);
      loadFromServer().then(function () {
        renderAll();
        setStatus("Reloaded from server JSON.", true);
      });
    });
    $("#btn-clear-draft").addEventListener("click", function () {
      localStorage.removeItem(LS_DRAFT);
      loadFromServer().then(renderAll);
      setStatus("Local draft cleared.", true);
    });
  }

  function boot() {
    loadFromServer().then(function () {
      wireControls();
      renderAll();
    }).catch(function (err) {
      setStatus(err.message, false);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
