(function () {
  "use strict";

  var STORAGE_KEY = "swarm_skill_tree_last_visit";
  var SESSION_MS = 30 * 60 * 1000;

  function statusClass(status) {
    var value = String(status || "building").toLowerCase();
    if (value === "unlocked") return "unlocked";
    if (value === "advanced") return "advanced";
    if (value === "locked") return "locked";
    return "building";
  }

  function isUnlocked(status) {
    var s = statusClass(status);
    return s === "unlocked" || s === "advanced";
  }

  function scrubProof(path) {
    if (!path) return "";
    var p = String(path).replace(/\\/g, "/");
    if (p.indexOf("D:/") === 0 || p.indexOf("D:\\") === 0) {
      return p.split("/").pop() || p;
    }
    if (p.indexOf("runtime/tasks/") >= 0) {
      return p.split("/").pop() || p;
    }
    if (p.indexOf("data/") === 0) {
      return p.replace(/^data\//, "");
    }
    return p.split("/").pop() || p;
  }

  function flattenNodes(panel) {
    var map = {};
    var list = [];
    (panel.branches || []).forEach(function (branch) {
      (branch.nodes || []).forEach(function (node) {
        var copy = Object.assign({}, node, { branchTitle: branch.title });
        map[node.id] = copy;
        list.push(copy);
      });
    });
    return { map: map, list: list };
  }

  function inferLocked(node, nodeMap) {
    if (statusClass(node.status) === "locked") return true;
    var prereqs = node.prerequisites || [];
    for (var i = 0; i < prereqs.length; i++) {
      var pre = nodeMap[prereqs[i]];
      if (pre && pre.id !== "dreamos_core" && !isUnlocked(pre.status)) {
        return true;
      }
    }
    return false;
  }

  function resolveCurrentNode(panel, nextLane, nodeMap) {
    var lane = String((nextLane && nextLane.next_lane) || "").toLowerCase();
    var task = String((nextLane && nextLane.next_task) || "").toLowerCase();
    var candidates = Object.keys(nodeMap);
    var match = candidates.find(function (id) {
      var n = nodeMap[id];
      var lid = id.toLowerCase();
      var label = String(n.label || "").toLowerCase();
      return (
        (lane && (lid.indexOf(lane) >= 0 || lane.indexOf(lid) >= 0 || label.indexOf(lane) >= 0)) ||
        (task && (lid.indexOf(task.replace(/\.yaml$/, "")) >= 0 || label.indexOf(task) >= 0))
      );
    });
    if (match) return nodeMap[match];
    var nu = panel.next_unlock;
    if (nu && nu.label) {
      var byLabel = candidates.find(function (id) {
        return String(nodeMap[id].label || "").toLowerCase() === String(nu.label).toLowerCase();
      });
      if (byLabel) return nodeMap[byLabel];
    }
    var building = Object.keys(nodeMap).map(function (id) { return nodeMap[id]; })
      .filter(function (n) { return statusClass(n.status) === "building"; })
      .sort(function (a, b) { return (a.grid && a.grid.tier || 99) - (b.grid && b.grid.tier || 99); });
    return building[0] || null;
  }

  function recentUnlockIds(events) {
    var lastVisit = 0;
    try {
      lastVisit = parseInt(localStorage.getItem(STORAGE_KEY) || "0", 10) || 0;
    } catch (e) { /* ignore */ }
    var cutoff = Date.now() - SESSION_MS;
    var threshold = Math.max(lastVisit, cutoff);
    var ids = new Set();
    (events || []).forEach(function (ev) {
      var ts = Date.parse(ev.timestamp || ev.completed_at || "");
      if (!isNaN(ts) && ts >= threshold) {
        (ev.unlocked || []).forEach(function (id) { ids.add(id); });
        if (ev.task_id) ids.add(ev.task_id);
      }
    });
    try {
      localStorage.setItem(STORAGE_KEY, String(Date.now()));
    } catch (e) { /* ignore */ }
    return ids;
  }

  function el(name, attrs, text) {
    var node = document.createElementNS("http://www.w3.org/2000/svg", name);
    Object.keys(attrs || {}).forEach(function (key) {
      node.setAttribute(key, attrs[key]);
    });
    if (text != null) node.textContent = text;
    return node;
  }

  function renderSphereGrid(panel, nextLane, skillEvents) {
    var shell = document.getElementById("sphere-shell");
    var viewport = document.getElementById("sphere-viewport");
    if (!shell || !viewport) return;

    var flat = flattenNodes(panel);
    var nodeMap = flat.map;
    var hub = (panel.layout && panel.layout.hub) || { id: "dreamos_core", grid: { x: 0, y: 0, tier: 0 } };
    var bounds = (panel.layout && panel.layout.bounds) || { min_x: -200, min_y: -200, max_x: 200, max_y: 200 };
    var current = resolveCurrentNode(panel, nextLane, nodeMap);
    var unlockAnim = recentUnlockIds((skillEvents && skillEvents.events) || []);

    viewport.innerHTML = "";
    var tooltip = document.getElementById("sphere-tooltip");
    if (!tooltip) {
      tooltip = document.createElement("div");
      tooltip.id = "sphere-tooltip";
      tooltip.className = "sphere-tooltip";
      shell.appendChild(tooltip);
    }

    var pad = 80;
    var vbX = bounds.min_x - pad;
    var vbY = bounds.min_y - pad;
    var vbW = bounds.max_x - bounds.min_x + pad * 2;
    var vbH = bounds.max_y - bounds.min_y + pad * 2;

    var svg = el("svg", {
      class: "sphere-svg",
      viewBox: vbX + " " + vbY + " " + vbW + " " + vbH,
      role: "img",
      "aria-label": "Dream.OS sphere grid skill tree",
    });
    var world = el("g", { class: "sphere-world" });
    svg.appendChild(world);

    var maxTier = 0;
    flat.list.forEach(function (n) {
      if (n.grid && n.grid.tier > maxTier) maxTier = n.grid.tier;
    });
    for (var t = 1; t <= maxTier; t++) {
      world.appendChild(el("circle", {
        class: "sphere-grid-ring",
        cx: "0",
        cy: "0",
        r: String(t * 95),
      }));
    }

    function linkPath(from, to) {
      return "M" + from.x + "," + from.y + " L" + to.x + "," + to.y;
    }

    var linksLayer = el("g", { class: "sphere-links" });
    world.appendChild(linksLayer);

    function drawLink(fromId, toId) {
      var fromNode = fromId === hub.id ? hub : nodeMap[fromId];
      var toNode = nodeMap[toId];
      if (!fromNode || !toNode || !fromNode.grid || !toNode.grid) return;
      var locked = inferLocked(toNode, nodeMap);
      var unlocked = isUnlocked(toNode.status) && !locked;
      var onPath = current && (toNode.id === current.id || fromId === current.id);
      var anim = unlockAnim.has(toNode.id);
      var path = el("path", {
        class: "sphere-link" +
          (unlocked ? " unlocked" : "") +
          (onPath ? " active-path" : "") +
          (anim ? " pulse-unlock" : ""),
        d: linkPath(fromNode.grid, toNode.grid),
      });
      linksLayer.appendChild(path);
    }

    flat.list.forEach(function (node) {
      var prereqs = node.prerequisites || [hub.id];
      prereqs.forEach(function (pid) { drawLink(pid, node.id); });
    });

    var nodesLayer = el("g", { class: "sphere-nodes" });
    world.appendChild(nodesLayer);

  function showTooltip(node, clientX, clientY) {
      var locked = inferLocked(node, nodeMap);
      var status = locked ? "locked" : statusClass(node.status);
      var caps = (node.capabilities || []).slice(0, 6);
      var proof = (node.proof || []).slice(0, 3).map(scrubProof);
      var taskHint = node.id && node.id.indexOf("project_") === 0
        ? node.id.replace(/^project_/, "")
        : (node.source === "structural_proof" ? node.id : "");
      tooltip.innerHTML =
        "<h4>" + (node.label || node.id) + "</h4>" +
        "<span class=\"tt-status\">" + status + "</span>" +
        "<p>" + (node.description || "") + "</p>" +
        (taskHint ? "<p class=\"tt-muted\">Task ref: <code>" + taskHint + "</code></p>" : "") +
        (caps.length ? "<p><strong>Unlocks:</strong> " + caps.join(", ") + "</p>" : "") +
        (proof.length ? "<p class=\"tt-muted\"><strong>Proof:</strong> " + proof.join(" · ") + "</p>" : "");
      tooltip.classList.add("visible");
      var rect = shell.getBoundingClientRect();
      tooltip.style.left = (clientX - rect.left) + "px";
      tooltip.style.top = (clientY - rect.top) + "px";
    }

    function hideTooltip() {
      tooltip.classList.remove("visible");
    }

    function addNode(node, isHub) {
      var g = el("g", { class: "sphere-node", "data-id": node.id });
      var grid = node.grid || { x: 0, y: 0 };
      var locked = !isHub && inferLocked(node, nodeMap);
      var cls = isHub ? "unlocked" : (locked ? "locked" : statusClass(node.status));
      if (!isHub && current && node.id === current.id) cls += " current";
      if (!isHub && unlockAnim.has(node.id)) cls += " unlock-anim";
      g.setAttribute("class", "sphere-node " + cls);
      g.setAttribute("transform", "translate(" + grid.x + "," + grid.y + ")");

      var r = isHub ? 14 : 12;
      g.appendChild(el("circle", { class: "node-ring", r: "20" }));
      g.appendChild(el("circle", { class: "node-orb", r: String(r), "stroke-width": "2" }));
      if (locked) {
        g.appendChild(el("text", { class: "lock-icon", y: "1" }, "🔒"));
      }
      if (!isHub) {
        var label = String(node.label || "").split(" ").slice(0, 3).join(" ");
        if (label.length > 18) label = label.slice(0, 16) + "…";
        g.appendChild(el("text", { class: "node-label", y: String(r + 14) }, label));
      } else {
        g.appendChild(el("text", { class: "node-label", y: String(r + 14) }, "Core"));
      }

      g.addEventListener("mouseenter", function (ev) { showTooltip(node, ev.clientX, ev.clientY); });
      g.addEventListener("mousemove", function (ev) { showTooltip(node, ev.clientX, ev.clientY); });
      g.addEventListener("mouseleave", hideTooltip);
      nodesLayer.appendChild(g);
    }

    addNode(hub, true);
    flat.list.forEach(function (node) { addNode(node, false); });

    viewport.appendChild(svg);
    initPanZoom(viewport, world, vbX, vbY, vbW, vbH);
    wireControls(viewport, world);
  }

  function initPanZoom(viewport, world, vbX, vbY, vbW, vbH) {
    var scale = 1;
    var tx = 0;
    var ty = 0;
    var dragging = false;
    var lastX = 0;
    var lastY = 0;

    function apply() {
      var cx = vbX + vbW / 2;
      var cy = vbY + vbH / 2;
      world.setAttribute(
        "transform",
        "translate(" + (tx + cx) + "," + (ty + cy) + ") scale(" + scale + ") translate(" + (-cx) + "," + (-cy) + ")"
      );
    }

    apply();

    viewport.addEventListener("wheel", function (ev) {
      ev.preventDefault();
      var delta = ev.deltaY > 0 ? 0.92 : 1.08;
      scale = Math.min(2.5, Math.max(0.35, scale * delta));
      apply();
    }, { passive: false });

    viewport.addEventListener("pointerdown", function (ev) {
      dragging = true;
      lastX = ev.clientX;
      lastY = ev.clientY;
      viewport.classList.add("dragging");
      viewport.setPointerCapture(ev.pointerId);
    });

    viewport.addEventListener("pointermove", function (ev) {
      if (!dragging) return;
      var rect = viewport.getBoundingClientRect();
      var dx = (ev.clientX - lastX) * (vbW / rect.width) / scale;
      var dy = (ev.clientY - lastY) * (vbH / rect.height) / scale;
      tx += dx;
      ty += dy;
      lastX = ev.clientX;
      lastY = ev.clientY;
      apply();
    });

    viewport.addEventListener("pointerup", function () {
      dragging = false;
      viewport.classList.remove("dragging");
    });

    viewport._sphereZoom = function (factor) {
      scale = Math.min(2.5, Math.max(0.35, scale * factor));
      apply();
    };
    viewport._sphereReset = function () {
      scale = 1;
      tx = 0;
      ty = 0;
      apply();
    };
  }

  function wireControls(viewport, world) {
    var controls = document.getElementById("sphere-controls");
    if (!controls) return;
    controls.querySelector("[data-zoom-in]").onclick = function () {
      viewport._sphereZoom(1.15);
    };
    controls.querySelector("[data-zoom-out]").onclick = function () {
      viewport._sphereZoom(0.87);
    };
    controls.querySelector("[data-reset]").onclick = function () {
      viewport._sphereReset();
    };
  }

  function initTabs() {
    var tabs = document.querySelectorAll(".view-tab");
    var sphereView = document.getElementById("sphere-view");
    var cardView = document.getElementById("card-view");
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var target = tab.getAttribute("data-view");
        tabs.forEach(function (t) { t.classList.toggle("active", t === tab); });
        if (sphereView) sphereView.classList.toggle("hidden", target !== "sphere");
        if (cardView) cardView.classList.toggle("hidden", target !== "cards");
      });
    });
  }

  window.SkillTreeSphere = {
    renderSphereGrid: renderSphereGrid,
    initTabs: initTabs,
    scrubProof: scrubProof,
  };
})();
