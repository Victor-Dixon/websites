(function () {
  "use strict";

  var STORAGE = {
    user: "kidsPlanner.currentUser",
    tasks: "kidsPlanner.tasks",
    worklogs: "kidsPlanner.worklogs",
    activeClock: "kidsPlanner.activeClock",
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

  var state = {
    tasks: [],
    worklogs: [],
    currentUser: "Guest",
    selectedTaskId: null,
    activeClock: null,
    sampleTasks: [],
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
    return state.currentUser || "Guest";
  }

  function findTask(id) {
    return state.tasks.find(function (t) {
      return t.id === id;
    });
  }

  function findWorklogForTask(taskId) {
    var logs = state.worklogs.filter(function (w) {
      return w.task_id === taskId && w.kid_owner === getCurrentUser();
    });
    return logs.length ? logs[logs.length - 1] : null;
  }

  function isKidSafeTask(task) {
    if (!task || !task.parent_approved) return false;
    var blob = JSON.stringify(task);
    return !KID_SAFE_BLOCKLIST.some(function (re) {
      return re.test(blob);
    });
  }

  function normalizeTask(raw) {
    return {
      id: raw.id,
      title: raw.title || "Untitled",
      source: raw.source || "weareswarm.online",
      project: raw.project || "General",
      status: raw.status || "available",
      difficulty: raw.difficulty || "easy",
      estimated_minutes: raw.estimated_minutes || 15,
      kid_owner: raw.kid_owner || null,
      agent_type: raw.agent_type || "helper",
      skills: Array.isArray(raw.skills) ? raw.skills : [],
      parent_approved: raw.parent_approved !== false,
      objective: raw.objective || "",
      kid_instructions: raw.kid_instructions || "",
      agent_prompt_template: raw.agent_prompt_template || {},
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
    var lines = [
      "PROJECT: " + (tpl.PROJECT || task.project),
      "TASK: " + (tpl.TASK || task.title),
      "OBJECTIVE: " + (tpl.OBJECTIVE || task.objective),
      "RULES: " + (tpl.RULES || "Public-safe only. No secrets. Kid-friendly output."),
      "ACTION: " + (tpl.ACTION || "Help the kid complete the objective using their notes."),
      "VERIFY: " + (tpl.VERIFY || "Output is public-safe and matches the objective."),
      "CLOSEOUT: " + (tpl.CLOSEOUT || "Return result for kid to paste into work log."),
      "",
      "--- Kid context ---",
      "Kid: " + user,
      "Task ID: " + task.id,
      "Difficulty: " + task.difficulty,
      "Estimated: " + task.estimated_minutes + " min",
      "Instructions: " + (task.kid_instructions || "—"),
    ];
    return lines.join("\n");
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
        empty.className = "empty-state";
        empty.style.fontSize = "0.8rem";
        empty.style.padding = "0.5rem";
        empty.textContent = "No tasks";
        list.appendChild(empty);
      }

      cards.forEach(function (task) {
        var card = document.createElement("article");
        card.className = "task-card";
        if (task.id === state.selectedTaskId) card.classList.add("selected");
        card.dataset.taskId = task.id;

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
          " min</span>";
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

        card.addEventListener("click", function () {
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

  function selectTask(taskId) {
    state.selectedTaskId = taskId;
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
    $("parent-review").value = (log && log.parent_review) || "pending_review";
    $("agent-prompt").value = generateAgentPrompt(task);
  }

  function claimTask() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.status !== "available" || !task.parent_approved) {
      toast("Cannot claim this task");
      return;
    }
    task.status = "claimed";
    task.kid_owner = getCurrentUser();
    persistTasks();
    renderKanban();
    renderDetail();
    toast("Task claimed!");
  }

  function moveTaskNext() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.kid_owner !== getCurrentUser()) return;
    var idx = STATUS_FLOW.indexOf(task.status);
    if (idx >= 0 && idx < STATUS_FLOW.length - 1) {
      task.status = STATUS_FLOW[idx + 1];
      persistTasks();
      renderKanban();
      renderDetail();
      toast("Moved to " + task.status.replace(/_/g, " "));
    }
  }

  function parkTask() {
    var task = findTask(state.selectedTaskId);
    if (!task || task.kid_owner !== getCurrentUser()) return;
    task.status = "parked";
    persistTasks();
    renderKanban();
    renderDetail();
    toast("Task parked");
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
    log.parent_review = $("parent-review").value;
    log.status = task.status;
    log.agent_used = task.agent_type;

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
    var payload = {
      exported_at: new Date().toISOString(),
      kid_owner: user,
      worklogs: state.worklogs.filter(function (w) {
        return w.kid_owner === user;
      }),
    };
    var blob = new Blob([JSON.stringify(payload, null, 2)], { type: "application/json" });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "kids-planner-worklogs-" + user.toLowerCase() + ".json";
    a.click();
    URL.revokeObjectURL(a.href);
    toast("Work logs exported");
  }

  function resetTasksFromSample() {
    if (
      !confirm(
        "Reset tasks from sample file? Your work logs stay saved, but task board resets."
      )
    ) {
      return;
    }
    state.tasks = state.sampleTasks.map(function (t) {
      return normalizeTask(JSON.parse(JSON.stringify(t)));
    });
    persistTasks();
    closeDetail();
    renderKanban();
    toast("Tasks reset from sample");
  }

  function mergeKidSafePlannerTasks(plannerTasks) {
    if (!Array.isArray(plannerTasks)) return;
    var existingIds = {};
    state.tasks.forEach(function (t) {
      existingIds[t.id] = true;
    });
    plannerTasks.forEach(function (raw) {
      if (!raw || !raw.id || existingIds[raw.id]) return;
      var candidate = normalizeTask({
        id: "planner_" + raw.id,
        title: raw.title || raw.id,
        source: "planner_bridge",
        project: raw.repo || raw.lane || "DreamVault",
        status: "available",
        difficulty: raw.priority === "P0" ? "medium" : "easy",
        estimated_minutes: 30,
        kid_owner: null,
        agent_type: "operator_helper",
        skills: raw.lane ? [raw.lane] : [],
        parent_approved: false,
        objective: (raw.objective || raw.title || "").slice(0, 280),
        kid_instructions: "Parent must approve before claiming planner tasks.",
        agent_prompt_template: {},
      });
      if (isKidSafeTask(candidate)) {
        state.tasks.push(candidate);
        existingIds[candidate.id] = true;
      }
    });
  }

  function loadTasks() {
    var cached = loadJson(STORAGE.tasks, null);
    if (cached && cached.length) {
      state.tasks = cached.map(normalizeTask);
      return Promise.resolve();
    }
    return fetch("tasks.sample.json")
      .then(function (r) {
        if (!r.ok) throw new Error("sample fetch failed");
        return r.json();
      })
      .then(function (data) {
        state.sampleTasks = (data.tasks || []).map(normalizeTask);
        state.tasks = state.sampleTasks.map(function (t) {
          return JSON.parse(JSON.stringify(t));
        });
        return fetch("/data/planner/strategic_active_queue.json")
          .then(function (r) {
            if (!r.ok) return null;
            return r.json();
          })
          .catch(function () {
            return null;
          });
      })
      .then(function (planner) {
        if (planner) {
          var items = []
            .concat(planner.active_queue || [])
            .concat(planner.runtime_active || [])
            .concat(planner.master_ledger || []);
          mergeKidSafePlannerTasks(items);
        }
        persistTasks();
      })
      .catch(function () {
        state.tasks = [];
        toast("Could not load tasks.sample.json");
      });
  }

  function bindEvents() {
    $("kid-select").addEventListener("change", function (e) {
      state.currentUser = e.target.value;
      localStorage.setItem(STORAGE.user, state.currentUser);
      renderKanban();
      renderDetail();
      renderWorklogHistory();
      toast("Hello, " + state.currentUser + "!");
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
    $("btn-reset-tasks").addEventListener("click", resetTasksFromSample);
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

  function init() {
    initNav();
    state.currentUser = localStorage.getItem(STORAGE.user) || "Guest";
    state.worklogs = loadJson(STORAGE.worklogs, []);
    state.activeClock = loadJson(STORAGE.activeClock, null);

    $("kid-select").value = state.currentUser;
    bindEvents();

    loadTasks().then(function () {
      renderKanban();
      renderWorklogHistory();
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
