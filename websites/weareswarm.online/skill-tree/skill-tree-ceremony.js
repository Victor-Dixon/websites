(function () {
  "use strict";

  var SEEN_KEY = "swarm_skill_tree_seen_events";
  var DATA_BASE = "/data/planner/";

  function el(tag, cls, html) {
    var node = document.createElement(tag);
    if (cls) node.className = cls;
    if (html != null) node.innerHTML = html;
    return node;
  }

  function formatWhen(raw) {
    if (!raw) return "—";
    var d = new Date(raw);
    if (isNaN(d.getTime())) return String(raw);
    return d.toLocaleString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  function humanize(id) {
    return String(id || "")
      .replace(/\.yaml$/i, "")
      .replace(/_/g, " ")
      .replace(/\s+/g, " ")
      .trim();
  }

  function loadSeen() {
    try {
      var raw = localStorage.getItem(SEEN_KEY);
      var parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function saveSeen(ids) {
    try {
      localStorage.setItem(SEEN_KEY, JSON.stringify(ids.slice(-200)));
    } catch (e) { /* ignore */ }
  }

  function eventKey(ev) {
    return String(
      ev.event_id ||
        ev.id ||
        (ev.task_id + "|" + (ev.completed_at || ev.timestamp || ""))
    );
  }

  function sortEvents(events) {
    return (events || []).slice().sort(function (a, b) {
      var ta = Date.parse(a.completed_at || a.timestamp || "") || 0;
      var tb = Date.parse(b.completed_at || b.timestamp || "") || 0;
      return tb - ta;
    });
  }

  function proofBadgeClass(status) {
    var s = String(status || "PENDING").toUpperCase();
    if (s === "VERIFIED") return "proof-verified";
    if (s === "BUILDING") return "proof-building";
    return "proof-pending";
  }

  function resolveFeedMatch(taskId, feed) {
    if (!taskId || !feed || !feed.items) return null;
    var tid = String(taskId).toLowerCase();
    for (var i = 0; i < feed.items.length; i++) {
      var item = feed.items[i];
      var itemTid = String(item.task_id || item.lane || "").toLowerCase();
      if (itemTid === tid || itemTid.indexOf(tid) >= 0 || tid.indexOf(itemTid) >= 0) {
        return item;
      }
    }
    return null;
  }

  function inferProofStatus(ev, feedMatch, panelNode) {
    if (ev.proof_status) return String(ev.proof_status).toUpperCase();
    if (feedMatch && String(feedMatch.status || "").toUpperCase() === "PASS") return "VERIFIED";
    if (panelNode) {
      var st = String(panelNode.status || "").toLowerCase();
      if (st === "unlocked" || st === "advanced") return "VERIFIED";
      if (st === "building") return "BUILDING";
    }
    return "PENDING";
  }

  function proofRef(ev, panelNode) {
    if (ev.proof_path) return ev.proof_path;
    if (panelNode && panelNode.proof && panelNode.proof.length) {
      var STS = window.SkillTreeSphere;
      var scrub = STS && STS.scrubProof ? STS.scrubProof : function (p) { return p; };
      return scrub(panelNode.proof[0]);
    }
    return "";
  }

  function buildCeremonyMarkdown(ev, panel) {
    var unlocked = (ev.unlocked || []).map(humanize).join(", ") || "none";
    var lines = [
      "## WeAreSwarm Skill Tree — Unlock Ceremony",
      "",
      "**Task completed:** " + humanize(ev.task_id),
      "**Unlocked:** " + unlocked,
      "**Next lane:** " + humanize(ev.next_lane || "—"),
      "**When:** " + formatWhen(ev.completed_at || ev.timestamp),
    ];
    if (ev.kid_summary) lines.push("", "> " + ev.kid_summary);
    if (ev.proof_path) lines.push("", "**Proof:** " + ev.proof_path);
    lines.push("", "— weareswarm.online/skill-tree/");
    return lines.join("\n");
  }

  function findPanelNode(panel, taskOrNodeId) {
    if (!panel || !taskOrNodeId) return null;
    var tid = String(taskOrNodeId).toLowerCase().replace(/\.yaml$/, "");
    var branches = panel.branches || [];
    for (var b = 0; b < branches.length; b++) {
      var nodes = branches[b].nodes || [];
      for (var n = 0; n < nodes.length; n++) {
        var node = nodes[n];
        var nid = String(node.id || "").toLowerCase();
        var label = String(node.label || "").toLowerCase();
        if (nid === tid || label === tid) return node;
        if (nid.indexOf(tid) >= 0 || tid.indexOf(nid) >= 0) return node;
        if (label.indexOf(tid) >= 0 || tid.indexOf(label.replace(/\s+/g, "_")) >= 0) return node;
      }
    }
    return null;
  }

  function renderProofCard(ev, panel, feed) {
    var panelNode = findPanelNode(panel, ev.task_id);
    var feedMatch = resolveFeedMatch(ev.task_id, feed);
    var status = inferProofStatus(ev, feedMatch, panelNode);
    var proof = proofRef(ev, panelNode);
    var card = el("div", "ceremony-proof-card");
    card.innerHTML =
      "<div class=\"proof-card-head\">" +
      "<span class=\"proof-badge " + proofBadgeClass(status) + "\">" + status + "</span>" +
      "<span class=\"proof-label\">Proof shipped</span>" +
      "</div>" +
      (proof
        ? "<p class=\"proof-artifact\"><code>" + proof + "</code></p>"
        : "<p class=\"proof-artifact muted\">No public proof artifact indexed yet.</p>") +
      (feedMatch
        ? "<p class=\"proof-feed-link\"><a href=\"/feed/#" +
          encodeURIComponent(feedMatch.feed_id || feedMatch.id || "") +
          "\">View closeout feed entry</a></p>"
        : "");
    return card;
  }

  function renderShareActions(ev, panel, mount) {
    var row = el("div", "ceremony-share-row");
    var copyBtn = el("button", "ceremony-btn secondary", "Copy ceremony text");
    copyBtn.type = "button";
    copyBtn.addEventListener("click", function () {
      var text = buildCeremonyMarkdown(ev, panel);
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          copyBtn.textContent = "Copied!";
          setTimeout(function () { copyBtn.textContent = "Copy ceremony text"; }, 2000);
        });
      } else {
        window.prompt("Copy ceremony summary:", text);
      }
    });
    row.appendChild(copyBtn);

    var cardBtn = el("button", "ceremony-btn secondary", "Download card");
    cardBtn.type = "button";
    cardBtn.addEventListener("click", function () {
      var card = el("div", "ceremony-export-card");
      card.innerHTML =
        "<p class=\"export-brand\">WeAreSwarm × Dream.OS</p>" +
        "<h3>Unlock Ceremony</h3>" +
        "<p><strong>Task:</strong> " + humanize(ev.task_id) + "</p>" +
        "<p><strong>Unlocked:</strong> " +
        ((ev.unlocked || []).map(humanize).join(", ") || "none") + "</p>" +
        "<p><strong>Next lane:</strong> " + humanize(ev.next_lane || "—") + "</p>" +
        "<p class=\"export-when\">" + formatWhen(ev.completed_at || ev.timestamp) + "</p>";
      document.body.appendChild(card);
      if (window.html2canvas) {
        window.html2canvas(card, { backgroundColor: "#0a0e14", scale: 2 }).then(function (canvas) {
          var link = document.createElement("a");
          link.download = "swarm-unlock-" + (ev.task_id || "ceremony") + ".png";
          link.href = canvas.toDataURL("image/png");
          link.click();
          card.remove();
        });
      } else {
        card.remove();
        copyBtn.click();
        cardBtn.textContent = "Text copied (no html2canvas)";
        setTimeout(function () { cardBtn.textContent = "Download card"; }, 2500);
      }
    });
    row.appendChild(cardBtn);
    mount.appendChild(row);
  }

  function showModal(ev, panel, feed, onDismiss, onViewGrid) {
    var overlay = el("div", "ceremony-overlay");
    overlay.setAttribute("role", "dialog");
    overlay.setAttribute("aria-modal", "true");
    overlay.setAttribute("aria-label", "Skill tree unlock ceremony");

    var modal = el("div", "ceremony-modal ceremony-enter");
    modal.innerHTML =
      "<p class=\"ceremony-kicker\">Unlock ceremony</p>" +
      "<h2 class=\"ceremony-title\">" + humanize(ev.task_id) + "</h2>" +
      "<p class=\"ceremony-sub\">Task completed · " + formatWhen(ev.completed_at || ev.timestamp) + "</p>" +
      "<div class=\"ceremony-section\">" +
      "<h3>Unlocked</h3>" +
      "<ul class=\"ceremony-unlock-list\">" +
      ((ev.unlocked || []).length
        ? (ev.unlocked || [])
            .map(function (id) { return "<li>" + humanize(id) + "</li>"; })
            .join("")
        : "<li class=\"muted\">No new nodes unlocked this closeout</li>") +
      "</ul></div>" +
      "<div class=\"ceremony-section\">" +
      "<h3>Next lane</h3>" +
      "<p class=\"ceremony-next-lane\">" + humanize(ev.next_lane || "—") + "</p>" +
      (ev.kid_summary ? "<p class=\"ceremony-kid\">" + ev.kid_summary + "</p>" : "") +
      "</div>" +
      "<div class=\"ceremony-proof-slot\"></div>" +
      "<div class=\"ceremony-actions\">" +
      "<button type=\"button\" class=\"ceremony-btn primary\" data-view-grid>View on grid</button>" +
      "<button type=\"button\" class=\"ceremony-btn\" data-dismiss>Dismiss</button>" +
      "</div>" +
      "<div class=\"ceremony-share-slot\"></div>";

    var proofSlot = modal.querySelector(".ceremony-proof-slot");
    proofSlot.appendChild(renderProofCard(ev, panel, feed));
    renderShareActions(ev, panel, modal.querySelector(".ceremony-share-slot"));

    modal.querySelector("[data-dismiss]").addEventListener("click", function () {
      overlay.classList.add("ceremony-leave");
      setTimeout(function () {
        overlay.remove();
        if (onDismiss) onDismiss();
      }, 220);
    });

    modal.querySelector("[data-view-grid]").addEventListener("click", function () {
      if (onViewGrid) onViewGrid(ev);
      overlay.classList.add("ceremony-leave");
      setTimeout(function () {
        overlay.remove();
        if (onDismiss) onDismiss();
      }, 220);
    });

    overlay.addEventListener("click", function (e) {
      if (e.target === overlay) modal.querySelector("[data-dismiss]").click();
    });

    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    requestAnimationFrame(function () {
      modal.classList.add("ceremony-enter-active");
    });
  }

  function renderTimeline(events, panel, feed, onSelect) {
    var root = document.getElementById("ceremony-timeline");
    if (!root) return;
    root.innerHTML = "";
    var sorted = sortEvents(events);
    if (!sorted.length) {
      root.innerHTML = "<p class=\"empty-state movement-empty\">No ceremonies yet — the swarm waits for your next ascension. Complete a task and run dreamclose to emit unlock events.</p>";
      return;
    }
    sorted.forEach(function (ev) {
      var item = el("button", "ceremony-timeline-item");
      item.type = "button";
      var panelNode = findPanelNode(panel, ev.task_id);
      var status = inferProofStatus(ev, resolveFeedMatch(ev.task_id, feed), panelNode);
      item.innerHTML =
        "<span class=\"tl-date\">" + formatWhen(ev.completed_at || ev.timestamp) + "</span>" +
        "<span class=\"tl-task\">" + humanize(ev.task_id) + "</span>" +
        "<span class=\"tl-unlocks\">+" + ((ev.unlocked || []).length) + " unlocked</span>" +
        "<span class=\"tl-lane\">→ " + humanize(ev.next_lane || "—") + "</span>" +
        (ev.kid_summary ? "<span class=\"tl-kid\">" + ev.kid_summary + "</span>" : "") +
        "<span class=\"proof-badge " + proofBadgeClass(status) + " tl-badge\">" + status + "</span>";
      item.addEventListener("click", function () {
        root.querySelectorAll(".ceremony-timeline-item").forEach(function (n) {
          n.classList.remove("active");
        });
        item.classList.add("active");
        if (onSelect) onSelect(ev);
      });
      root.appendChild(item);
    });
  }

  function showFeedNotice(message) {
    var notice = document.getElementById("ceremony-feed-notice");
    if (!notice) return;
    notice.textContent = message;
    notice.classList.remove("hidden");
  }

  function hideFeedNotice() {
    var notice = document.getElementById("ceremony-feed-notice");
    if (notice) notice.classList.add("hidden");
  }

  function markSeen(ev) {
    var key = eventKey(ev);
    var seen = loadSeen();
    if (seen.indexOf(key) < 0) {
      seen.push(key);
      saveSeen(seen);
    }
  }

  function findLatestUnseen(events) {
    var seen = loadSeen();
    var sorted = sortEvents(events);
    for (var i = 0; i < sorted.length; i++) {
      if (seen.indexOf(eventKey(sorted[i])) < 0) return sorted[i];
    }
    return null;
  }

  function focusNodeForEvent(ev) {
    var STS = window.SkillTreeSphere;
    if (!STS) return;
    var target =
      (ev.unlocked && ev.unlocked[0]) ||
      ev.next_lane ||
      ev.task_id;
    if (STS.switchToSphere) STS.switchToSphere();
    if (STS.panToNode) STS.panToNode(target);
    if (STS.pulseNode) STS.pulseNode(target);
  }

  function initCeremony(options) {
    options = options || {};
    var eventsPayload = options.events;
    var eventsOk = options.eventsOk !== false;
    var panel = options.panel || {};
    var feed = options.feed || null;

    if (!eventsOk) {
      showFeedNotice("Ceremony feed unavailable — grid still works.");
      renderTimeline([], panel, feed, focusNodeForEvent);
      return { ok: false };
    }
    hideFeedNotice();

    var events = (eventsPayload && eventsPayload.events) || [];
    renderTimeline(events, panel, feed, focusNodeForEvent);

    var latest = findLatestUnseen(events);
    if (latest) {
      showModal(
        latest,
        panel,
        feed,
        function () { markSeen(latest); },
        function () { focusNodeForEvent(latest); }
      );
    }

    return { ok: true, events: events, latest: latest };
  }

  window.SkillTreeCeremony = {
    initCeremony: initCeremony,
    renderTimeline: renderTimeline,
    buildCeremonyMarkdown: buildCeremonyMarkdown,
    eventKey: eventKey,
    loadSeen: loadSeen,
    markSeen: markSeen,
    SEEN_KEY: SEEN_KEY,
    DATA_BASE: DATA_BASE,
  };
})();
