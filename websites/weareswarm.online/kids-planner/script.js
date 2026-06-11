(function () {
  "use strict";

  var STORAGE = {
    user: "kidsPlanner.currentUser",
    tasks: "kidsPlanner.tasks",
    worklogs: "kidsPlanner.worklogs",
    activeClock: "kidsPlanner.activeClock",
    profiles: "kidsPlanner.profiles",
  };

  var DIFFICULTY_POINTS = {
    beginner_manager: 10,
    beginner: 10,
    easy: 10,
    intermediate: 25,
    medium: 25,
    advanced: 50,
    hard: 50,
    expert: 100,
  };

  var BADGES = {
    first_claim: {
      id: "first_claim",
      name: "First Mission",
      emoji: "🚀",
      description: "Claim your first task",
      points_required: 0,
      trigger: "first_task_claimed",
    },
    agent_manager_10: {
      id: "agent_manager_10",
      name: "Agent Manager",
      emoji: "⭐",
      description: "Earn 10 Swarm Points",
      points_required: 10,
    },
    agent_manager_50: {
      id: "agent_manager_50",
      name: "Supervisor",
      emoji: "🌟",
      description: "Earn 50 Swarm Points",
      points_required: 50,
    },
    agent_manager_100: {
      id: "agent_manager_100",
      name: "Swarm Captain",
      emoji: "👑",
      description: "Earn 100 Swarm Points",
      points_required: 100,
    },
    streak_3: {
      id: "streak_3",
      name: "3-Day Streak",
      emoji: "🔥",
      description: "Work 3 days in a row",
      trigger: "streak_days_3",
    },
    celebrated_5: {
      id: "celebrated_5",
      name: "Star Performer",
      emoji: "✨",
      description: "Get 5 celebrated reviews",
      trigger: "celebrated_count_5",
    },
  };

  var COLUMNS = [
    { id: "available", label: "Available" },
    { id: "claimed", label: "Claimed" },
    { id: "in_progress", label: "In Progress" },
    { id: "waiting_on_agent", label: "Waiting on Agent" },
    { id: "ready_for_review", label: "Ready for Review" },
    { id: "done", label: "Done" },
    { id: "parked", label: "Parked" },
  ];

  var STATUS_FLOW = [
    "available",
    "claimed",
    "in_progress",
    "waiting_on_agent",
    "ready_for_review",
    "done",
  ];

  var KID_SAFE_BLOCKLIST = [
    /secret/i,
    /password/i,
    /token/i,
    /api_key/i,
    /credential/i,
    /\.env\b/i,
    /private_key/i,
  ];

  var KIDS_TASKS_FEED = "/data/planner/kids_planner_tasks.json";
  var DEFAULT_RULES =
    "- Make small safe changes.\n" +
    "- Do not delete unrelated files.\n" +
    "- Do not touch secrets.\n" +
    "- Explain errors clearly.\n" +
    "- After each step, tell the kid what happened.\n" +
    "- End with: Files Changed, Verification, Next Step.";

  var state = {
    tasks: [],
    worklogs: [],
    currentUser: "",
    authSession: null,
    selectedTaskId: null,
    activeClock: null,
    sampleTasks: [],
    rewards: [],
    currentTab: "board",
  };

  var dragState = {
    taskId: null,
    isDragging: false,
    suppressClick: false,
  };

  var touchDrag = {
    taskId: null,
    startX: 0,
    startY: 0,
    active: false,
    clone: null,
    hoverColumn: null,
  };

  function $(id) {
    return document.getElementById(id);
  }

  function loadJson(key, fallback) {
    try {
      var raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (e) {
      return fallback;
    }
  }

  function saveJson(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  function toast(msg) {
    var el = $("toast");
    if (!el) return;
    el.textContent = msg;
    el.classList.add("show");
    setTimeout(function () {
      el.classList.remove("show");
    }, 2400);
  }

  function uid(prefix) {
    return prefix + "_" + Date.now().toString(36) + "_" + Math.random().toString(36).slice(2, 7);
  }

  function getCurrentUser() {
    return state.currentUser || "";
  }

  function isParent() {
    return !!(state.authSession && state.authSession.role === "parent");
  }

  function isKid() {
    return !!(state.authSession && state.authSession.role === "kid");
  }

  function allKidDisplayNames() {
    return ["Aria", "Charlie"];
  }

  function getProfileForUser(user) {
    return getProfile(user || getCurrentUser());
  }

  function findTask(id) {
    return state.tasks.find(function (t) {
      return t.id === id;
    });
  }

  function findWorklogForTask(taskId) {
    var logs = state.worklogs.filter(function (w) {
      if (w.task_id !== taskId) return false;
      if (isParent()) return true;
      return w.kid_owner === getCurrentUser();
    });
    return logs.length ? logs[logs.length - 1] : null;
  }

  function hasSecretMarkers(task) {
    if (!task) return true;
    var blob = JSON.stringify(task);
    return KID_SAFE_BLOCKLIST.some(function (re) {
      return re.test(blob);
    });
  }

  function isKidSafeTask(task) {
    if (!task || !task.parent_approved) return false;
    return !hasSecretMarkers(task);
  }

  function mapSwarmStatusToKanban(rawStatus) {
    var s = String(rawStatus || "available").toLowerCase();
    if (
      s === "available" ||
      s === "claimed" ||
      s === "in_progress" ||
      s === "waiting_on_agent" ||
      s === "ready_for_review" ||
      s === "done" ||
      s === "parked"
    ) {
      return s;
    }
    if (s === "ready" || s === "active" || s === "pending" || s === "blocked" || s === "in_progress") {
      return "available";
    }
    if (s === "complete" || s === "done" || s === "closed" || s === "cancelled" || s === "archived") {
      return "done";
    }
    return "available";
  }

  function normalizeTask(raw) {
    var difficulty = raw.difficulty || "easy";
    var points =
      raw.points != null
        ? Number(raw.points)
        : DIFFICULTY_POINTS[String(difficulty).toLowerCase().replace(/[\s-]/g, "_")] || 10;
    var reviewPts =
      raw.points_on_review != null ? Number(raw.points_on_review) : Math.max(1, Math.floor(points / 2));
    return {
      id: raw.id,
      title: raw.title || "Untitled",
      source: raw.source || "weareswarm.online",
      project: raw.project || raw.system || "General",
      status: mapSwarmStatusToKanban(raw.status),
      difficulty: difficulty,
      estimated_minutes: raw.estimated_minutes || 15,
      kid_owner: raw.kid_owner || null,
      agent_type: raw.agent_type || "helper",
      skills: Array.isArray(raw.skills) ? raw.skills : [],
      parent_approved: raw.parent_approved === true,
      objective: raw.objective || raw.public_summary || "",
      kid_instructions: raw.kid_instructions || "",
      agent_prompt_template: raw.agent_prompt_template || {},
      lane: raw.lane || "",
      priority: raw.priority || "",
      swarm_status: raw.swarm_status || raw.status || "",
      points: points,
      points_on_complete: raw.points_on_complete != null ? Number(raw.points_on_complete) : points,
      points_on_review: reviewPts,
    };
  }

  function persistTasks() {
    saveJson(STORAGE.tasks, state.tasks);
  }

  function persistWorklogs() {
    saveJson(STORAGE.worklogs, state.worklogs);
  }

  function formatDuration(minutes) {
    if (!minutes && minutes !== 0) return "";
    var h = Math.floor(minutes / 60);
    var m = minutes % 60;
    if (h) return h + "h " + m + "m";
    return m + "m";
  }

  function formatTime(iso) {
    if (!iso) return "—";
    try {
      return new Date(iso).toLocaleString();
    } catch (e) {
      return iso;
    }
  }

  function generateAgentPrompt(task) {
    var tpl = task.agent_prompt_template || {};
    var user = getCurrentUser();
    var rules = tpl.RULES || DEFAULT_RULES;
    var lines = [
      "You are an agent working under a kid project manager.",
      "",
      "PROJECT: " + (tpl.PROJECT || task.project),
      "TASK: " + (tpl.TASK || task.title),
      "OBJECTIVE: " + (tpl.OBJECTIVE || task.objective),
      "RULES:",
      rules,
      "",
      "KID MANAGER: The kid will supervise you. Use simple explanations.",
      "",
      "ACTION: " +
        (tpl.ACTION || "Help the kid complete the objective using their notes and instructions."),
      "VERIFY: " + (tpl.VERIFY || "Output is public-safe and matches the objective."),
      "CLOSEOUT: " + (tpl.CLOSEOUT || "Return a short report the kid can paste into the work log."),
      "",
      "--- Kid context ---",
      "Kid: " + user,
      "Task ID: " + task.id,
      "Lane: " + (task.lane || "—"),
      "Difficulty: " + task.difficulty,
      "Estimated: " + task.estimated_minutes + " min",
      "Instructions: " + (task.kid_instructions || "—"),
    ];
    return lines.join("\n");
  }

  function validateStatusMove(task, newStatus) {
    if (!task || newStatus === task.status) return { ok: false, reason: "same" };

    if (!task.parent_approved && task.status === "parked" && newStatus !== "parked") {
      return {
        ok: false,
        reason: "Parent must approve before moving this task out of Parked.",
      };
    }

    if (
      (newStatus === "claimed" || newStatus === "in_progress") &&
      !isKid()
    ) {
      return {
        ok: false,
        reason: "Only kid accounts can claim or start tasks.",
      };
    }

    if (
      (newStatus === "claimed" || newStatus === "in_progress") &&
      !task.parent_approved
    ) {
      return { ok: false, reason: "Parent has not approved this task yet." };
    }

    var ownedStatuses = [
      "claimed",
      "in_progress",
      "waiting_on_agent",
      "ready_for_review",
      "done",
      "parked",
    ];
    if (
      ownedStatuses.indexOf(task.status) >= 0 &&
      task.kid_owner &&
      task.kid_owner !== getCurrentUser()
    ) {
      return { ok: false, reason: "Only " + task.kid_owner + " can move this task." };
    }

    if (newStatus === "parked" && task.kid_owner && task.kid_owner !== getCurrentUser()) {
      return { ok: false, reason: "Only the task owner can park it." };
    }

    return { ok: true };
  }

  function moveTaskToStatus(taskId, newStatus, options) {
    options = options || {};
    var task = findTask(taskId);
    if (!task) return false;

    var check = validateStatusMove(task, newStatus);
    if (!check.ok) {
      if (check.reason && check.reason !== "same") toast(check.reason);
      return false;
    }

    var prev = task.status;
    task.status = newStatus;

    if (
      newStatus === "claimed" ||
      (prev === "available" &&
        newStatus !== "available" &&
        newStatus !== "parked" &&
        isKid())
    ) {
      if (!task.kid_owner) task.kid_owner = getCurrentUser();
    }

    if (newStatus === "available") {
      task.kid_owner = null;
    }

    persistTasks();
    renderKanban();
    if (state.selectedTaskId === task.id) renderDetail();
    if (newStatus === "ready_for_review") onTaskReadyForReview(task);
    if (newStatus === "claimed" && prev !== "claimed") onTaskClaimed(task);
    if (options.silent !== true) {
      toast("Moved to " + newStatus.replace(/_/g, " "));
    }
    return true;
  }

  function clearDragHighlights() {
    document.querySelectorAll(".kanban-column.drag-over").forEach(function (el) {
      el.classList.remove("drag-over");
    });
    document.querySelectorAll(".kanban-cards.drag-over").forEach(function (el) {
      el.classList.remove("drag-over");
    });
  }

  function highlightDropColumn(columnEl) {
    clearDragHighlights();
    if (!columnEl) {
      touchDrag.hoverColumn = null;
      return;
    }
    touchDrag.hoverColumn = columnEl;
    columnEl.classList.add("drag-over");
    var list = columnEl.querySelector(".kanban-cards");
    if (list) list.classList.add("drag-over");
  }

  function cleanupTouchDrag() {
    document.querySelectorAll(".task-card.dragging").forEach(function (el) {
      el.classList.remove("dragging");
    });
    if (touchDrag.clone && touchDrag.clone.parentNode) {
      touchDrag.clone.parentNode.removeChild(touchDrag.clone);
    }
    touchDrag.clone = null;
    touchDrag.active = false;
    touchDrag.hoverColumn = null;
    clearDragHighlights();
  }

  function setupDragDrop() {
    var board = $("kanban-board");
    if (!board || board.dataset.dragBound) return;
    board.dataset.dragBound = "1";

    board.addEventListener("dragstart", function (e) {
      var card = e.target.closest(".task-card");
      if (!card || !card.draggable) return;
      dragState.taskId = card.dataset.taskId;
      dragState.isDragging = true;
      card.classList.add("dragging");
      card.setAttribute("aria-grabbed", "true");
      if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.setData("text/plain", dragState.taskId);
        if (e.dataTransfer.setDragImage) {
          var ghost = card.cloneNode(true);
          ghost.classList.add("drag-ghost-clone");
          ghost.style.width = card.offsetWidth + "px";
          document.body.appendChild(ghost);
          e.dataTransfer.setDragImage(ghost, card.offsetWidth / 2, 20);
          setTimeout(function () {
            if (ghost.parentNode) ghost.parentNode.removeChild(ghost);
          }, 0);
        }
      }
    });

    board.addEventListener("dragend", function (e) {
      var card = e.target.closest(".task-card");
      if (card) {
        card.classList.remove("dragging");
        card.setAttribute("aria-grabbed", "false");
      }
      dragState.isDragging = false;
      dragState.taskId = null;
      clearDragHighlights();
      dragState.suppressClick = true;
      setTimeout(function () {
        dragState.suppressClick = false;
      }, 100);
    });

    board.addEventListener("dragover", function (e) {
      if (!dragState.taskId) return;
      var col = e.target.closest(".kanban-column");
      if (!col) return;
      e.preventDefault();
      if (e.dataTransfer) e.dataTransfer.dropEffect = "move";
      highlightDropColumn(col);
    });

    board.addEventListener("dragleave", function (e) {
      var col = e.target.closest(".kanban-column");
      if (!col) return;
      var related = e.relatedTarget;
      if (related && col.contains(related)) return;
      col.classList.remove("drag-over");
      var list = col.querySelector(".kanban-cards");
      if (list) list.classList.remove("drag-over");
    });

    board.addEventListener("drop", function (e) {
      e.preventDefault();
      var col = e.target.closest(".kanban-column");
      clearDragHighlights();
      if (!col || !dragState.taskId) return;
      moveTaskToStatus(dragState.taskId, col.dataset.status);
      dragState.taskId = null;
      dragState.isDragging = false;
    });

    board.addEventListener(
      "touchstart",
      function (e) {
        var card = e.target.closest(".task-card");
        if (!card || e.touches.length !== 1) return;
        touchDrag.taskId = card.dataset.taskId;
        touchDrag.startX = e.touches[0].clientX;
        touchDrag.startY = e.touches[0].clientY;
        touchDrag.active = false;
      },
      { passive: true }
    );

    board.addEventListener(
      "touchmove",
      function (e) {
        if (!touchDrag.taskId) return;
        var touch = e.touches[0];
        var dx = touch.clientX - touchDrag.startX;
        var dy = touch.clientY - touchDrag.startY;
        if (!touchDrag.active && (Math.abs(dx) > 14 || Math.abs(dy) > 14)) {
          touchDrag.active = true;
          var card = board.querySelector('[data-task-id="' + touchDrag.taskId + '"]');
          if (card) {
            card.classList.add("dragging");
            touchDrag.clone = card.cloneNode(true);
            touchDrag.clone.classList.add("touch-drag-ghost");
            touchDrag.clone.style.width = card.offsetWidth + "px";
            document.body.appendChild(touchDrag.clone);
          }
        }
        if (touchDrag.active) {
          e.preventDefault();
          if (touchDrag.clone) {
            touchDrag.clone.style.left = touch.clientX - 40 + "px";
            touchDrag.clone.style.top = touch.clientY - 24 + "px";
          }
          var el = document.elementFromPoint(touch.clientX, touch.clientY);
          highlightDropColumn(el ? el.closest(".kanban-column") : null);
        }
      },
      { passive: false }
    );

    board.addEventListener("touchend", function (e) {
      if (!touchDrag.taskId) return;
      if (touchDrag.active) {
        if (touchDrag.hoverColumn) {
          moveTaskToStatus(touchDrag.taskId, touchDrag.hoverColumn.dataset.status);
          dragState.suppressClick = true;
          setTimeout(function () {
            dragState.suppressClick = false;
          }, 200);
        }
        cleanupTouchDrag();
        e.preventDefault();
      }
      touchDrag.taskId = null;
    });

    board.addEventListener("touchcancel", function () {
      cleanupTouchDrag();
      touchDrag.taskId = null;
    });
  }

  function renderKanban() {
    var board = $("kanban-board");
    if (!board) return;
    board.innerHTML = "";

    COLUMNS.forEach(function (col) {
      var columnEl = document.createElement("div");
      columnEl.className = "kanban-column";
      columnEl.dataset.status = col.id;

      var cards = state.tasks.filter(function (t) {
        return t.status === col.id;
      });

      var header = document.createElement("div");
      header.className = "kanban-column-header";
      header.innerHTML =
        "<span>" + col.label + '</span><span class="count">' + cards.length + "</span>";
      columnEl.appendChild(header);

      var list = document.createElement("div");
      list.className = "kanban-cards";

      if (!cards.length) {
        var empty = document.createElement("p");
        empty.className = "empty-state kanban-drop-hint";
        empty.style.fontSize = "0.8rem";
        empty.style.padding = "0.5rem";
        empty.textContent = "Drop tasks here";
        list.appendChild(empty);
      }

      cards.forEach(function (task) {
        var card = document.createElement("article");
        card.className = "task-card";
        if (task.id === state.selectedTaskId) card.classList.add("selected");
        card.dataset.taskId = task.id;
        card.draggable = true;
        card.setAttribute("role", "button");
        card.setAttribute("aria-grabbed", "false");
        card.title = "Drag to another column or tap to open";

        var title = document.createElement("h3");
        title.className = "task-card-title";
        title.textContent = task.title;
        card.appendChild(title);

        var meta = document.createElement("div");
        meta.className = "task-card-meta";
        meta.innerHTML =
          '<span class="task-pill">' +
          escapeHtml(task.project) +
          '</span><span class="task-pill difficulty-' +
          escapeHtml(task.difficulty) +
          '">' +
          escapeHtml(task.difficulty) +
          "</span>" +
          '<span class="task-pill">' +
          task.estimated_minutes +
          " min</span>" +
          '<span class="task-pill points-pill">⭐ ' +
          (task.points_on_complete || task.points || 10) +
          " pts</span>";
        if (task.kid_owner) {
          meta.innerHTML +=
            '<span class="task-pill owner">' + escapeHtml(task.kid_owner) + "</span>";
        }
        card.appendChild(meta);

        if (task.skills && task.skills.length) {
          var skills = document.createElement("p");
          skills.className = "task-card-skills";
          skills.textContent = "Skills: " + task.skills.join(", ");
          card.appendChild(skills);
        }

        var grip = document.createElement("span");
        grip.className = "task-card-grip";
        grip.setAttribute("aria-hidden", "true");
        grip.textContent = "⠿";
        card.appendChild(grip);

        card.addEventListener("click", function () {
          if (dragState.suppressClick || dragState.isDragging || touchDrag.active) return;
          selectTask(task.id);
        });

        list.appendChild(card);
      });

      columnEl.appendChild(list);
      board.appendChild(columnEl);
    });

    var badge = $("task-count-badge");
    if (badge) badge.textContent = state.tasks.length + " tasks";
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function profileKey(user) {
    return user || "Guest";
  }

  function defaultProfile() {
    return {
      points: 0,
      badges: [],
      redemptions: [],
      streak: { count: 0, lastDate: null },
      taskAwards: {},
      stats: { claimsCount: 0, celebratedCount: 0 },
    };
  }

  function loadProfiles() {
    return loadJson(STORAGE.profiles, {});
  }

  function getProfile(user) {
    var profiles = loadProfiles();
    var key = profileKey(user);
    if (!profiles[key]) {
      profiles[key] = defaultProfile();
      saveJson(STORAGE.profiles, profiles);
    }
    return profiles[key];
  }

  function saveProfile(user, profile) {
    var profiles = loadProfiles();
    profiles[profileKey(user)] = profile;
    saveJson(STORAGE.profiles, profiles);
  }

  function getTaskPoints(task) {
    if (!task) return 10;
    if (task.points_on_complete != null) return task.points_on_complete;
    if (task.points != null) return task.points;
    var d = String(task.difficulty || "easy").toLowerCase().replace(/[\s-]/g, "_");
    return DIFFICULTY_POINTS[d] || 10;
  }

  function getReviewPoints(task) {
    if (task.points_on_review != null) return task.points_on_review;
    return Math.max(1, Math.floor(getTaskPoints(task) / 2));
  }

  function todayDateStr() {
    return new Date().toISOString().slice(0, 10);
  }

  function updateStreak(profile) {
    var today = todayDateStr();
    var streak = profile.streak || { count: 0, lastDate: null };
    if (streak.lastDate === today) return streak;
    if (!streak.lastDate) {
      streak.count = 1;
    } else {
      var last = new Date(streak.lastDate + "T12:00:00");
      var now = new Date(today + "T12:00:00");
      var diffDays = Math.round((now - last) / 86400000);
      streak.count = diffDays === 1 ? streak.count + 1 : 1;
    }
    streak.lastDate = today;
    profile.streak = streak;
    return streak;
  }

  function triggerPointsCelebration(extra) {
    var bar = $("profile-bar");
    if (bar) {
      bar.classList.add("points-burst");
      if (extra) bar.classList.add("celebrated-burst");
      setTimeout(function () {
        bar.classList.remove("points-burst", "celebrated-burst");
      }, 900);
    }
  }

  function checkBadges(profile, user) {
    var pts = profile.points || 0;
    var stats = profile.stats || {};
    if ((stats.claimsCount || 0) >= 1) grantBadge(profile, "first_claim");
    if (pts >= 10) grantBadge(profile, "agent_manager_10");
    if (pts >= 50) grantBadge(profile, "agent_manager_50");
    if (pts >= 100) grantBadge(profile, "agent_manager_100");
    if ((profile.streak || {}).count >= 3) grantBadge(profile, "streak_3");
    if ((stats.celebratedCount || 0) >= 5) grantBadge(profile, "celebrated_5");
    saveProfile(user, profile);
  }

  function hasBadge(profile, id) {
    return (profile.badges || []).indexOf(id) >= 0;
  }

  function grantBadge(profile, id) {
    if (hasBadge(profile, id)) return false;
    profile.badges = profile.badges || [];
    profile.badges.push(id);
    var badge = BADGES[id];
    if (badge) toast("New badge: " + badge.emoji + " " + badge.name + "!");
    return true;
  }

  function ensureTaskAward(profile, taskId) {
    profile.taskAwards = profile.taskAwards || {};
    if (!profile.taskAwards[taskId]) {
      profile.taskAwards[taskId] = { review: false, complete: false, celebrated: false };
    }
    return profile.taskAwards[taskId];
  }

  function awardPoints(user, amount, reason) {
    if (!amount || amount <= 0) return 0;
    var profile = getProfile(user);
    profile.points = (profile.points || 0) + amount;
    saveProfile(user, profile);
    checkBadges(profile, user);
    renderProfileBar();
    renderShop();
    renderBadges();
    if (reason) toast("+" + amount + " Swarm Points! " + reason);
    return amount;
  }

  function onTaskReadyForReview(task) {
    var user = task.kid_owner;
    if (!user) return;
    var profile = getProfile(user);
    var award = ensureTaskAward(profile, task.id);
    if (award.review) return;
    var pts = getReviewPoints(task);
    award.review = true;
    saveProfile(user, profile);
    awardPoints(user, pts, "Ready for review!");
    triggerPointsCelebration();
  }

  function onTaskClaimed(task) {
    var user = task.kid_owner || getCurrentUser();
    if (!user) return;
    var profile = getProfile(user);
    updateStreak(profile);
    profile.stats = profile.stats || {};
    profile.stats.claimsCount = (profile.stats.claimsCount || 0) + 1;
    saveProfile(user, profile);
    checkBadges(profile, user);
    renderProfileBar();
    renderBadges();
  }

  function onParentReviewChange(task, newReview, oldReview) {
    if (newReview === oldReview) return;
    if (!isParent()) return;
    var user = task.kid_owner;
    if (!user) return;
    if (newReview !== "approved" && newReview !== "celebrated") return;

    var profile = getProfile(user);
    var award = ensureTaskAward(profile, task.id);
    if (award.complete) return;

    var full = getTaskPoints(task);
    var reviewPts = award.review ? getReviewPoints(task) : 0;
    var remainder = Math.max(0, full - reviewPts);
    award.complete = true;

    if (newReview === "celebrated") {
      award.celebrated = true;
      remainder += Math.max(1, Math.round(full * 0.1));
      profile.stats = profile.stats || {};
      profile.stats.celebratedCount = (profile.stats.celebratedCount || 0) + 1;
      saveProfile(user, profile);
      awardPoints(user, remainder, "Celebrated! Bonus included!");
      triggerPointsCelebration(true);
    } else {
      saveProfile(user, profile);
      awardPoints(user, remainder, "Parent approved!");
    }
    checkBadges(getProfile(user), user);
  }

  function renderProfileBar() {
    var profile = getProfile(getCurrentUser());
    var ptsEl = $("profile-points");
    if (ptsEl) ptsEl.textContent = String(profile.points || 0);

    var badgesEl = $("profile-badges");
    if (!badgesEl) return;
    badgesEl.innerHTML = "";
    var earned = (profile.badges || []).slice(-3);
    if (!earned.length) {
      badgesEl.innerHTML = '<span class="profile-badge-hint">Earn badges by completing tasks!</span>';
      return;
    }
    earned.forEach(function (id) {
      var b = BADGES[id];
      if (!b) return;
      var chip = document.createElement("span");
      chip.className = "profile-badge-chip";
      chip.title = b.name;
      chip.textContent = b.emoji + " " + b.name;
      badgesEl.appendChild(chip);
    });
  }

  function switchTab(tabId) {
    if (tabId === "admin" && !isParent()) {
      toast("Admin panel is for parents only");
      return;
    }
    state.currentTab = tabId;
    ["board", "shop", "badges", "history", "admin"].forEach(function (id) {
      var panel = $("panel-" + id);
      var tab = $("tab-" + id);
      if (panel) panel.hidden = id !== tabId;
      if (tab) tab.classList.toggle("active", id === tabId);
    });
    if (tabId === "shop") renderShop();
    if (tabId === "badges") renderBadges();
    if (tabId === "history") renderWorklogHistory();
    if (tabId === "admin") renderAdmin();
  }

  function renderShop() {
    if (isParent()) {
      var balanceEl = $("shop-balance");
      if (balanceEl) balanceEl.textContent = "—";
      var catalog = $("shop-catalog");
      if (catalog) {
        catalog.innerHTML =
          '<p class="empty-state">Parents approve redemptions in the Admin tab.</p>';
      }
      var list = $("redemption-list");
      if (list) list.innerHTML = "";
      return;
    }
    var profile = getProfile(getCurrentUser());
    var balanceEl = $("shop-balance");
    if (balanceEl) balanceEl.textContent = String(profile.points || 0);

    var catalog = $("shop-catalog");
    if (!catalog) return;
    catalog.innerHTML = "";
    if (!state.rewards.length) {
      catalog.innerHTML = '<p class="empty-state">Rewards loading…</p>';
      return;
    }
    state.rewards.forEach(function (reward) {
      var card = document.createElement("article");
      card.className = "shop-card";
      var canAfford = (profile.points || 0) >= reward.cost;
      card.innerHTML =
        '<div class="shop-card-emoji">' +
        escapeHtml(reward.emoji || "🎁") +
        "</div>" +
        "<h3>" +
        escapeHtml(reward.name) +
        "</h3>" +
        '<p class="shop-card-desc">' +
        escapeHtml(reward.description || "") +
        "</p>" +
        '<div class="shop-card-footer">' +
        '<span class="shop-cost">⭐ ' +
        reward.cost +
        "</span>" +
        '<span class="shop-category">' +
        escapeHtml(reward.category || "") +
        "</span>" +
        "</div>";
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn btn-primary shop-redeem-btn";
      btn.textContent = canAfford ? "Redeem" : "Need more points";
      btn.disabled = !canAfford;
      btn.addEventListener("click", function () {
        redeemReward(reward.id);
      });
      card.appendChild(btn);
      catalog.appendChild(card);
    });

    var list = $("redemption-list");
    if (!list) return;
    var redemptions = (profile.redemptions || []).slice().reverse();
    if (!redemptions.length) {
      list.innerHTML =
        '<p class="empty-state">No redemptions yet — earn points and pick a reward!</p>';
      return;
    }
    list.innerHTML = "";
    redemptions.forEach(function (r) {
      var row = document.createElement("div");
      row.className = "redemption-row";
      row.innerHTML =
        "<strong>" +
        escapeHtml(r.name) +
        "</strong> · ⭐ " +
        r.cost +
        ' · <span class="redemption-status ' +
        escapeHtml(r.status) +
        '">' +
        escapeHtml(r.status) +
        "</span>";
      var sel = document.createElement("select");
      sel.className = "redemption-approval";
      sel.title = "Parent approval";
      ["pending", "approved"].forEach(function (opt) {
        var o = document.createElement("option");
        o.value = opt;
        o.textContent = opt === "pending" ? "Pending parent OK" : "Approved ✓";
        if (r.status === opt) o.selected = true;
        sel.appendChild(o);
      });
      if (isParent()) {
        sel.addEventListener("change", function () {
          var p = getProfile(getCurrentUser());
          var rev = (p.redemptions || []).find(function (x) {
            return x.id === r.id;
          });
          if (rev) {
            rev.status = sel.value;
            saveProfile(getCurrentUser(), p);
            renderShop();
          }
        });
        row.appendChild(sel);
      }
      list.appendChild(row);
    });
  }

  function redeemReward(rewardId) {
    var reward = state.rewards.find(function (r) {
      return r.id === rewardId;
    });
    if (!reward) return;
    var user = getCurrentUser();
    if (!isKid()) {
      toast("Only kid accounts can redeem rewards");
      return;
    }
    var profile = getProfile(user);
    if ((profile.points || 0) < reward.cost) {
      toast("Not enough points yet — keep going!");
      return;
    }
    if (!confirm("Redeem " + reward.name + " for " + reward.cost + " points?")) return;
    profile.points -= reward.cost;
    profile.redemptions = profile.redemptions || [];
    profile.redemptions.push({
      id: uid("rd"),
      rewardId: reward.id,
      name: reward.name,
      cost: reward.cost,
      category: reward.category,
      status: "pending",
      redeemedAt: new Date().toISOString(),
    });
    saveProfile(user, profile);
    renderProfileBar();
    renderShop();
    toast("Redeemed! Waiting for parent approval.");
  }

  function renderBadges() {
    var profile = getProfile(getCurrentUser());
    var grid = $("badges-grid");
    if (!grid) return;
    grid.innerHTML = "";
    Object.keys(BADGES).forEach(function (id) {
      var badge = BADGES[id];
      var earned = hasBadge(profile, id);
      var card = document.createElement("article");
      card.className = "badge-card" + (earned ? " earned" : " locked");
      var req = badge.points_required
        ? badge.points_required + " pts"
        : badge.trigger
          ? "Special"
          : "";
      card.innerHTML =
        '<div class="badge-emoji">' +
        badge.emoji +
        "</div>" +
        "<h3>" +
        escapeHtml(badge.name) +
        "</h3>" +
        "<p>" +
        escapeHtml(badge.description) +
        "</p>" +
        (req ? '<span class="badge-req">' + escapeHtml(req) + "</span>" : "") +
        (earned ? '<span class="badge-earned-tag">Earned!</span>' : '<span class="badge-lock">🔒</span>');
      grid.appendChild(card);
    });
  }

  function loadRewards() {
    return fetch("rewards.json")
      .then(function (r) {
        if (!r.ok) throw new Error("rewards fetch failed");
        return r.json();
      })
      .then(function (data) {
        state.rewards = (data && data.rewards) || [];
      })
      .catch(function () {
        state.rewards = [];
      });
  }

  function selectTask(taskId) {
    state.selectedTaskId = taskId;
    if (state.currentTab !== "board") switchTab("board");
    var panel = $("side-panel");
    var layout = document.querySelector(".planner-layout");
    if (panel) panel.hidden = false;
    if (layout) layout.classList.add("has-detail");
    renderKanban();
    renderDetail();
    renderWorklogHistory();
  }

  function closeDetail() {
    state.selectedTaskId = null;
    var panel = $("side-panel");
    var layout = document.querySelector(".planner-layout");
    if (panel) panel.hidden = true;
    if (layout) layout.classList.remove("has-detail");
    renderKanban();
  }

  function renderDetail() {
    var task = findTask(state.selectedTaskId);
    if (!task) return;

    $("detail-title").textContent = task.title;
    $("detail-project").textContent = task.project + " · " + task.source;

    var meta = $("detail-meta");
    meta.innerHTML =
      "<dt>Status</dt><dd>" +
      escapeHtml(task.status.replace(/_/g, " ")) +
      "</dd>" +
      "<dt>Difficulty</dt><dd>" +
      escapeHtml(task.difficulty) +
      "</dd>" +
      "<dt>Points</dt><dd>⭐ " +
      getTaskPoints(task) +
      " (" +
      getReviewPoints(task) +
      " on review)</dd>" +
      "<dt>Estimate</dt><dd>" +
      task.estimated_minutes +
      " min</dd>" +
      "<dt>Owner</dt><dd>" +
      escapeHtml(task.kid_owner || "—") +
      "</dd>" +
      "<dt>Agent type</dt><dd>" +
      escapeHtml(task.agent_type) +
      "</dd>" +
      "<dt>Skills</dt><dd>" +
      escapeHtml((task.skills || []).join(", ") || "—") +
      "</dd>" +
      "<dt>Parent OK</dt><dd>" +
      (task.parent_approved ? "Yes ✓" : "Not yet") +
      "</dd>";

    $("detail-instructions").textContent = task.kid_instructions || "—";
    $("detail-objective").textContent = task.objective || "—";

    var canClaim =
      task.status === "available" && task.parent_approved && !task.kid_owner;
    var isOwner = task.kid_owner === getCurrentUser();
    $("btn-claim").disabled = !canClaim;
    $("btn-claim").textContent = canClaim
      ? "Claim task"
      : isOwner
        ? "You claimed this"
        : "Claim task";
    $("btn-move-next").disabled = !isOwner || task.status === "done";
    $("btn-park").disabled = !isOwner;

    renderClockUI(task);
    loadWorklogFields(task);
  }

  function renderClockUI(task) {
    var clock = state.activeClock;
    var isActive =
      clock && clock.task_id === task.id && clock.kid_owner === getCurrentUser();
    $("btn-clock-in").disabled = !task.kid_owner || task.kid_owner !== getCurrentUser() || !!isActive;
    $("btn-clock-out").disabled = !isActive;

    if (isActive) {
      $("clock-status").textContent = "Clocked in since " + formatTime(clock.clock_in);
      var elapsed = Math.round((Date.now() - new Date(clock.clock_in).getTime()) / 60000);
      $("clock-duration").textContent = "Elapsed: ~" + formatDuration(elapsed);
    } else {
      $("clock-status").textContent = "Not clocked in";
      $("clock-duration").textContent = "";
    }
  }

  function loadWorklogFields(task) {
    var log = findWorklogForTask(task.id);
    $("kid-summary").value = (log && log.kid_summary) || "";
    $("agent-result").value = (log && log.agent_result) || "";
    $("kid-summary").readOnly = isParent();
    $("agent-result").readOnly = isParent();
    var reviewVal = (log && log.parent_review) || "pending_review";
    $("parent-review").value = reviewVal;
    var reviewSelect = $("parent-review");
    var reviewReadonly = $("parent-review-readonly");
    if (isParent()) {
      if (reviewSelect) reviewSelect.hidden = false;
      if (reviewReadonly) reviewReadonly.hidden = true;
    } else {
      if (reviewSelect) reviewSelect.hidden = true;
      if (reviewReadonly) {
        reviewReadonly.hidden = false;
        reviewReadonly.textContent = reviewVal.replace(/_/g, " ");
      }
    }
    $("agent-prompt").value = generateAgentPrompt(task);
  }

  function claimTask() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.status !== "available" || !task.parent_approved) {
      toast("Cannot claim this task");
      return;
    }
    if (moveTaskToStatus(task.id, "claimed", { silent: true })) {
      task = findTask(state.selectedTaskId);
      $("agent-prompt").value = generateAgentPrompt(task);
      toast("Task claimed — prompt ready!");
    }
  }

  function moveTaskNext() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.kid_owner !== getCurrentUser()) return;
    var idx = STATUS_FLOW.indexOf(task.status);
    if (idx >= 0 && idx < STATUS_FLOW.length - 1) {
      moveTaskToStatus(task.id, STATUS_FLOW[idx + 1]);
    }
  }

  function parkTask() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.kid_owner !== getCurrentUser()) return;
    if (moveTaskToStatus(task.id, "parked", { silent: true })) {
      toast("Task parked");
    }
  }

  function clockIn() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.kid_owner !== getCurrentUser()) return;
    if (state.activeClock) {
      toast("Already clocked in elsewhere");
      return;
    }
    state.activeClock = {
      task_id: task.id,
      kid_owner: getCurrentUser(),
      clock_in: new Date().toISOString(),
    };
    saveJson(STORAGE.activeClock, state.activeClock);
    if (task.status === "claimed") {
      task.status = "in_progress";
      persistTasks();
      renderKanban();
    }
    renderDetail();
    toast("Clocked in!");
  }

  function clockOut() {
    var clock = state.activeClock;
    if (!clock) return;
    var task = findTask(clock.task_id);
    var clockOut = new Date().toISOString();
    var duration = Math.max(
      1,
      Math.round((new Date(clockOut).getTime() - new Date(clock.clock_in).getTime()) / 60000)
    );

    var log = findWorklogForTask(clock.task_id);
    if (!log) {
      log = {
        id: uid("wl"),
        task_id: clock.task_id,
        kid_owner: getCurrentUser(),
        clock_in: clock.clock_in,
        clock_out: null,
        duration_minutes: 0,
        agent_used: task ? task.agent_type : "helper",
        status: task ? task.status : "in_progress",
        kid_summary: "",
        agent_result: "",
        parent_review: "pending_review",
      };
      state.worklogs.push(log);
    }
    log.clock_in = clock.clock_in;
    log.clock_out = clockOut;
    log.duration_minutes = duration;
    log.status = task ? task.status : log.status;

    state.activeClock = null;
    localStorage.removeItem(STORAGE.activeClock);
    persistWorklogs();
    renderDetail();
    renderWorklogHistory();
    toast("Clocked out — " + formatDuration(duration));
  }

  function saveWorklog() {
    var task = findTask(state.selectedTaskId);
    if (!task) return;

    if (isParent()) {
      var existing = findWorklogForTask(task.id);
      if (!existing) {
        toast("No kid work log to review on this task yet");
        return;
      }
      var oldReview = existing.parent_review || "pending_review";
      existing.parent_review = $("parent-review").value;
      onParentReviewChange(task, existing.parent_review, oldReview);
      persistWorklogs();
      renderWorklogHistory();
      if (isParent()) renderAdmin();
      toast("Parent review saved");
      return;
    }

    var log = findWorklogForTask(task.id);
    if (!log) {
      log = {
        id: uid("wl"),
        task_id: task.id,
        kid_owner: getCurrentUser(),
        clock_in: null,
        clock_out: null,
        duration_minutes: 0,
        agent_used: task.agent_type,
        status: task.status,
        kid_summary: "",
        agent_result: "",
        parent_review: "pending_review",
      };
      state.worklogs.push(log);
    }

    log.kid_summary = $("kid-summary").value.trim();
    log.agent_result = $("agent-result").value.trim();
    var oldReview = log.parent_review || "pending_review";
    log.parent_review = $("parent-review").value;
    log.status = task.status;
    log.agent_used = task.agent_type;

    onParentReviewChange(task, log.parent_review, oldReview);

    persistWorklogs();
    renderWorklogHistory();
    toast("Work log saved");
  }

  function renderWorklogHistory() {
    var container = $("worklog-list");
    if (!container) return;

    var user = getCurrentUser();
    var logs = state.worklogs
      .filter(function (w) {
        return w.kid_owner === user;
      })
      .slice()
      .reverse();

    if (!logs.length) {
      container.innerHTML =
        '<p class="empty-state">No work logs yet. Clock in on a task to start.</p>';
      return;
    }

    container.innerHTML = "";
    logs.forEach(function (log) {
      var task = findTask(log.task_id);
      var entry = document.createElement("div");
      entry.className = "worklog-entry";

      var review = log.parent_review || "pending_review";
      entry.innerHTML =
        '<div class="worklog-entry-header">' +
        "<strong>" +
        escapeHtml(task ? task.title : log.task_id) +
        "</strong>" +
        '<span class="review-badge ' +
        review +
        '">' +
        review.replace(/_/g, " ") +
        "</span>" +
        (log.duration_minutes
          ? '<span class="task-pill">' + formatDuration(log.duration_minutes) + "</span>"
          : "") +
        "</div>" +
        "<p>" +
        escapeHtml(log.kid_summary || "(no summary)") +
        "</p>" +
        (log.agent_result
          ? '<p><em>Agent:</em> ' + escapeHtml(log.agent_result.slice(0, 120)) + "…</p>"
          : "") +
        '<p class="mono-note">' +
        formatTime(log.clock_in) +
        " → " +
        formatTime(log.clock_out) +
        "</p>";

      entry.addEventListener("click", function () {
        selectTask(log.task_id);
      });

      container.appendChild(entry);
    });
  }

  function copyPrompt() {
    var text = $("agent-prompt").value;
    if (!text) {
      toast("Generate a prompt first");
      return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function () {
        toast("Prompt copied!");
      });
    } else {
      $("agent-prompt").select();
      document.execCommand("copy");
      toast("Prompt copied!");
    }
  }

  function exportWorklogs() {
    var user = getCurrentUser();
    var logs = isParent()
      ? state.worklogs.slice()
      : state.worklogs.filter(function (w) {
          return w.kid_owner === user;
        });
    var payload = {
      exported_at: new Date().toISOString(),
      exported_by: user,
      role: state.authSession ? state.authSession.role : "",
      worklogs: logs,
    };
    var blob = new Blob([JSON.stringify(payload, null, 2)], { type: "application/json" });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download =
      "kids-planner-worklogs-" +
      (isParent() ? "all" : user.toLowerCase()) +
      ".json";
    a.click();
    URL.revokeObjectURL(a.href);
    toast("Work logs exported");
  }

  function renderSessionBar() {
    var nameEl = $("session-user");
    var roleEl = $("session-role");
    if (nameEl) nameEl.textContent = getCurrentUser() || "—";
    if (roleEl) {
      var role = state.authSession ? state.authSession.role : "";
      roleEl.textContent = role === "parent" ? "Parent" : role === "kid" ? "Kid" : role;
      roleEl.className = "session-role badge " + (role === "parent" ? "ok" : "");
    }
  }

  function applyRoleUI() {
    var adminTab = $("tab-admin");
    if (adminTab) adminTab.hidden = !isParent();
    renderSessionBar();
    if (isParent()) {
      $("btn-claim") && ($("btn-claim").disabled = true);
    }
  }

  function collectAllRedemptions() {
    var rows = [];
    allKidDisplayNames().forEach(function (kid) {
      var profile = getProfile(kid);
      (profile.redemptions || []).forEach(function (r) {
        rows.push({ kid: kid, redemption: r });
      });
    });
    return rows.sort(function (a, b) {
      return String(b.redemption.redeemedAt || "").localeCompare(String(a.redemption.redeemedAt || ""));
    });
  }

  function renderAdmin() {
    if (!isParent()) return;
    var statsEl = $("admin-stats");
    var redEl = $("admin-redemptions");
    var logsEl = $("admin-worklogs");
    if (!statsEl || !redEl || !logsEl) return;

    var kidStats = allKidDisplayNames().map(function (kid) {
      var profile = getProfile(kid);
      var minutes = state.worklogs
        .filter(function (w) {
          return w.kid_owner === kid;
        })
        .reduce(function (sum, w) {
          return sum + (w.duration_minutes || 0);
        }, 0);
      return {
        kid: kid,
        points: profile.points || 0,
        badges: (profile.badges || []).length,
        minutes: minutes,
        logs: state.worklogs.filter(function (w) {
          return w.kid_owner === kid;
        }).length,
      };
    });

    statsEl.innerHTML = "";
    kidStats.forEach(function (s) {
      var card = document.createElement("div");
      card.className = "admin-stat-card";
      card.innerHTML =
        "<strong>" +
        escapeHtml(s.kid) +
        "</strong><span>⭐ " +
        s.points +
        " pts</span><span>🏅 " +
        s.badges +
        " badges</span><span>⏱ " +
        formatDuration(s.minutes) +
        "</span><span>📝 " +
        s.logs +
        " logs</span>";
      statsEl.appendChild(card);
    });

    var reds = collectAllRedemptions().filter(function (x) {
      return x.redemption.status === "pending";
    });
    if (!reds.length) {
      redEl.innerHTML = '<p class="empty-state">No pending redemptions</p>';
    } else {
      redEl.innerHTML = "";
      reds.forEach(function (item) {
        var r = item.redemption;
        var row = document.createElement("div");
        row.className = "redemption-row";
        row.innerHTML =
          "<strong>" +
          escapeHtml(item.kid) +
          "</strong> · " +
          escapeHtml(r.name) +
          " · ⭐ " +
          r.cost;
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-primary";
        btn.textContent = "Approve";
        btn.addEventListener("click", function () {
          var p = getProfile(item.kid);
          var rev = (p.redemptions || []).find(function (x) {
            return x.id === r.id;
          });
          if (rev) {
            rev.status = "approved";
            saveProfile(item.kid, p);
            renderAdmin();
            toast("Approved " + r.name + " for " + item.kid);
          }
        });
        row.appendChild(btn);
        redEl.appendChild(row);
      });
    }

    var logs = state.worklogs.slice().reverse();
    if (!logs.length) {
      logsEl.innerHTML = '<p class="empty-state">No work logs yet</p>';
      return;
    }
    logsEl.innerHTML = "";
    logs.forEach(function (log) {
      var task = findTask(log.task_id);
      var entry = document.createElement("div");
      entry.className = "worklog-entry";
      var review = log.parent_review || "pending_review";
      entry.innerHTML =
        '<div class="worklog-entry-header">' +
        "<strong>" +
        escapeHtml(task ? task.title : log.task_id) +
        "</strong>" +
        '<span class="task-pill owner">' +
        escapeHtml(log.kid_owner || "—") +
        "</span>" +
        '<span class="review-badge ' +
        review +
        '">' +
        review.replace(/_/g, " ") +
        "</span>" +
        (log.duration_minutes
          ? '<span class="task-pill">' + formatDuration(log.duration_minutes) + "</span>"
          : "") +
        "</div>" +
        "<p>" +
        escapeHtml(log.kid_summary || "(no summary)") +
        "</p>";
      var sel = document.createElement("select");
      sel.className = "redemption-approval";
      ["pending_review", "approved", "needs_revision", "celebrated"].forEach(function (opt) {
        var o = document.createElement("option");
        o.value = opt;
        o.textContent = opt.replace(/_/g, " ");
        if (review === opt) o.selected = true;
        sel.appendChild(o);
      });
      sel.addEventListener("change", function () {
        var oldReview = log.parent_review || "pending_review";
        log.parent_review = sel.value;
        if (task) onParentReviewChange(task, log.parent_review, oldReview);
        persistWorklogs();
        renderAdmin();
      });
      entry.appendChild(sel);
      entry.addEventListener("click", function (e) {
        if (e.target === sel) return;
        selectTask(log.task_id);
      });
      logsEl.appendChild(entry);
    });
  }

  function resetTasksFromFeed() {
    if (
      !confirm(
        "Reset tasks from the live Swarm feed? Your work logs stay saved, but the task board resets."
      )
    ) {
      return;
    }
    loadTasks({ forceRefresh: true }).then(function () {
      closeDetail();
      renderKanban();
      toast("Tasks reset from feed");
    });
  }

  function mergeFeedTasks(feedTasks, preserveLocal) {
    if (!Array.isArray(feedTasks)) return;
    var byId = {};
    if (preserveLocal) {
      state.tasks.forEach(function (t) {
        byId[t.id] = t;
      });
    }
    feedTasks.forEach(function (raw) {
      if (!raw || !raw.id) return;
      var incoming = normalizeTask(raw);
      if (hasSecretMarkers(incoming)) return;
      var existing = byId[incoming.id];
      if (existing && (existing.kid_owner || existing.status !== "available")) {
        byId[incoming.id] = Object.assign({}, incoming, {
          status: existing.status,
          kid_owner: existing.kid_owner,
        });
      } else {
        byId[incoming.id] = incoming;
      }
    });
    state.tasks = Object.keys(byId).map(function (id) {
      return byId[id];
    });
  }

  function fetchKidsTaskFeed() {
    return fetch(KIDS_TASKS_FEED)
      .then(function (r) {
        if (!r.ok) throw new Error("kids feed fetch failed");
        return r.json();
      })
      .then(function (data) {
        return (data && data.tasks) || [];
      });
  }

  function fetchSampleTasks() {
    return fetch("tasks.sample.json")
      .then(function (r) {
        if (!r.ok) throw new Error("sample fetch failed");
        return r.json();
      })
      .then(function (data) {
        state.sampleTasks = (data.tasks || []).map(normalizeTask);
        return state.sampleTasks;
      });
  }

  function loadTasks(options) {
    options = options || {};
    var cached = options.forceRefresh ? null : loadJson(STORAGE.tasks, null);
    var preserveLocal = !!(cached && cached.length && !options.forceRefresh);

    return fetchKidsTaskFeed()
      .then(function (feedTasks) {
        if (feedTasks.length) {
          if (preserveLocal) {
            state.tasks = cached.map(normalizeTask);
          } else {
            state.tasks = [];
          }
          mergeFeedTasks(feedTasks, preserveLocal);
          persistTasks();
          return;
        }
        throw new Error("empty kids feed");
      })
      .catch(function () {
        if (preserveLocal) {
          state.tasks = cached.map(normalizeTask);
          return;
        }
        return fetchSampleTasks().then(function (sample) {
          state.tasks = sample.map(function (t) {
            return JSON.parse(JSON.stringify(t));
          });
          persistTasks();
        });
      })
      .catch(function () {
        if (!state.tasks.length) {
          state.tasks = [];
          toast("Could not load task feed");
        }
      });
  }

  function bindEvents() {
    var logoutBtn = $("btn-logout");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", function () {
        if (window.KidsAuth) KidsAuth.logout();
      });
    }

    document.querySelectorAll(".planner-tab").forEach(function (tab) {
      tab.addEventListener("click", function () {
        switchTab(tab.dataset.tab || "board");
      });
    });

    $("btn-close-detail").addEventListener("click", closeDetail);
    $("btn-claim").addEventListener("click", claimTask);
    $("btn-move-next").addEventListener("click", moveTaskNext);
    $("btn-park").addEventListener("click", parkTask);
    $("btn-clock-in").addEventListener("click", clockIn);
    $("btn-clock-out").addEventListener("click", clockOut);
    $("btn-generate-prompt").addEventListener("click", function () {
      var task = findTask(state.selectedTaskId);
      if (!task) return;
      $("agent-prompt").value = generateAgentPrompt(task);
      toast("Prompt generated");
    });
    $("btn-copy-prompt").addEventListener("click", copyPrompt);
    $("btn-save-worklog").addEventListener("click", saveWorklog);
    $("btn-export-logs").addEventListener("click", exportWorklogs);
    $("btn-reset-tasks").addEventListener("click", resetTasksFromFeed);
  }

  function initNav() {
    if (window.SiteNav && window.SiteNav.LINKS) {
      var exists = window.SiteNav.LINKS.some(function (l) {
        return l.href === "/kids-planner/";
      });
      if (!exists) {
        window.SiteNav.LINKS.splice(2, 0, {
          href: "/kids-planner/",
          label: "Kids Planner",
        });
      }
    }
  }

  function startPlanner(session) {
    state.authSession = session;
    state.currentUser = session.displayName;
    state.worklogs = loadJson(STORAGE.worklogs, []);
    state.activeClock = loadJson(STORAGE.activeClock, null);

    applyRoleUI();
    bindEvents();
    setupDragDrop();

    loadRewards()
      .then(function () {
        return loadTasks();
      })
      .then(function () {
        renderKanban();
        renderWorklogHistory();
        renderProfileBar();
        renderShop();
        renderBadges();
        if (isParent()) renderAdmin();
      });
  }

  function init() {
    initNav();
    if (!window.KidsAuth) {
      window.location.href = "/kids-planner/login.html";
      return;
    }
    KidsAuth.requireAuth().then(function (session) {
      if (!session) return;
      startPlanner(session);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
