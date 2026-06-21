(function () {
  "use strict";

  var PAGE_SIZE = 25;
  var EXECUTOR_LABELS = {
    agent: "Agent",
    kids: "Kids",
    victor: "Victor only",
  };
  var EXECUTOR_CLASS = {
    agent: "executor-agent",
    kids: "executor-kids",
    victor: "executor-victor",
  };

  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (key) {
        if (key === "text") node.textContent = attrs[key];
        else if (key === "html") node.innerHTML = attrs[key];
        else node.setAttribute(key, attrs[key]);
      });
    }
    (children || []).forEach(function (child) {
      if (child) node.appendChild(child);
    });
    return node;
  }

  function parseFilter() {
    var params = new URLSearchParams(window.location.search);
    return {
      executor: (params.get("executor") || "all").toLowerCase(),
      page: Math.max(1, parseInt(params.get("page") || "1", 10) || 1),
      q: (params.get("q") || "").trim().toLowerCase(),
    };
  }

  function buildQuery(filter) {
    var params = new URLSearchParams();
    if (filter.executor && filter.executor !== "all") params.set("executor", filter.executor);
    if (filter.page > 1) params.set("page", String(filter.page));
    if (filter.q) params.set("q", filter.q);
    var qs = params.toString();
    return qs ? "?" + qs : "";
  }

  function resolveExecutor(task) {
    var key = task && task.executor;
    if (key === "agent" || key === "kids" || key === "victor") return key;
    if (task && task.kids_routable) return "kids";
    var routing = (task && task.kids_routing) || {};
    if (routing.requires_adult_gate) return "victor";
    var owner = String((task && task.owner) || "").toLowerCase();
    if (owner === "victor" || owner === "operator" || owner === "human") return "victor";
    return "agent";
  }

  function filterTasks(tasks, filter) {
    return tasks.filter(function (task) {
      if (filter.executor !== "all" && resolveExecutor(task) !== filter.executor) return false;
      if (!filter.q) return true;
      var hay = [
        task.id,
        task.title,
        task.owner,
        task.lane,
        task.status,
        task.desc,
        task.victor_reason,
      ]
        .join(" ")
        .toLowerCase();
      return hay.indexOf(filter.q) !== -1;
    });
  }

  function renderExecutorBadge(executor, task) {
    var key = executor || resolveExecutor(task || {});
    var span = el("span", {
      class: "status-pill executor-badge " + (EXECUTOR_CLASS[key] || "executor-agent"),
      text: EXECUTOR_LABELS[key] || key,
    });
    return span;
  }

  function renderPagination(container, filter, totalPages) {
    container.innerHTML = "";
    if (totalPages <= 1) return;

    var prev = filter.page > 1 ? filter.page - 1 : null;
    var next = filter.page < totalPages ? filter.page + 1 : null;

    if (prev) {
      container.appendChild(
        el("a", {
          class: "page-btn",
          href: buildQuery({ executor: filter.executor, page: prev, q: filter.q }),
          text: "← Prev",
        })
      );
    }

    container.appendChild(
      el("span", { class: "page-meta", text: "Page " + filter.page + " / " + totalPages })
    );

    if (next) {
      container.appendChild(
        el("a", {
          class: "page-btn",
          href: buildQuery({ executor: filter.executor, page: next, q: filter.q }),
          text: "Next →",
        })
      );
    }
  }

  function normalizeTasksPayload(payload) {
    if (!payload || typeof payload !== "object") {
      return { tasks: [], task_count: 0 };
    }
    var tasks = Array.isArray(payload.tasks) ? payload.tasks : [];
    return {
      tasks: tasks,
      task_count: payload.task_count != null ? payload.task_count : tasks.length,
      generated_at: payload.generated_at,
    };
  }

  function renderFeedTable(root, payload, options) {
    var normalized = normalizeTasksPayload(payload);
    var tasks = normalized.tasks;
    var filter = parseFilter();
    var filtered = filterTasks(tasks, filter);
    var totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
    if (filter.page > totalPages) filter.page = totalPages;
    var start = (filter.page - 1) * PAGE_SIZE;
    var pageItems = filtered.slice(start, start + PAGE_SIZE);

    var metaEl = root.querySelector("[data-feed-meta]");
    if (metaEl) {
      metaEl.textContent =
        (normalized.task_count || tasks.length) +
        " exported · " +
        filtered.length +
        " shown · updated " +
        (normalized.generated_at || "—");
    }

    var tbody = root.querySelector("[data-feed-tbody]");
    if (!tbody) return;
    tbody.innerHTML = "";

    pageItems.forEach(function (task) {
      var tr = document.createElement("tr");
      var reason = task.victor_reason
        ? '<div class="muted-small">' + task.victor_reason + "</div>"
        : "";
      tr.innerHTML =
        "<td>" +
        (task.id || "—") +
        "</td>" +
        "<td>" +
        (task.title || "—") +
        reason +
        "</td>" +
        "<td>" +
        (task.owner || "—") +
        "</td>" +
        "<td>" +
        (task.lane || "—") +
        "</td>" +
        "<td><span class=\"status-pill\">" +
        (task.status || "—") +
        "</span></td>" +
        "<td class=\"executor-cell\"></td>";
      tr.querySelector(".executor-cell").appendChild(renderExecutorBadge(task.executor, task));
      tbody.appendChild(tr);
    });

    var empty = root.querySelector("[data-feed-empty]");
    if (empty) empty.hidden = pageItems.length > 0;

    var filters = root.querySelector("[data-feed-filters]");
    if (filters) {
      ["all", "agent", "kids", "victor"].forEach(function (key) {
        var btn = filters.querySelector('[data-filter="' + key + '"]');
        if (!btn) return;
        btn.classList.toggle("active", filter.executor === key);
        btn.href = buildQuery({ executor: key, page: 1, q: filter.q });
      });
    }

    var pagination = root.querySelector("[data-feed-pagination]");
    if (pagination) renderPagination(pagination, filter, totalPages);

    var kidsLink = root.querySelector("[data-kids-count]");
    if (kidsLink && options.kidsCount != null) {
      kidsLink.textContent = String(options.kidsCount) + " kids-lane tasks";
    }
  }

  function plannerJsonName(urlOrName) {
    return String(urlOrName || "")
      .replace(/^\/data\/planner\//, "")
      .replace(/^\//, "");
  }

  async function mountTasksFeed(root, feedUrl, kidsFeedUrl) {
    var fetchJson = (window.FocusDashboard && window.FocusDashboard.fetchJson) || null;
    if (!fetchJson) {
      throw new Error("FocusDashboard.fetchJson unavailable");
    }
    var allPayload;
    try {
      allPayload = normalizeTasksPayload(await fetchJson(plannerJsonName(feedUrl)));
    } catch (e) {
      allPayload = { tasks: [], task_count: 0 };
      var metaEl = root.querySelector("[data-feed-meta]");
      if (metaEl) metaEl.textContent = "Feed unavailable — " + (e && e.message ? e.message : String(e));
    }
    var kidsPayload = null;
    try {
      kidsPayload = normalizeTasksPayload(await fetchJson(plannerJsonName(kidsFeedUrl)));
    } catch (e) {
      kidsPayload = { tasks: [], task_count: 0 };
    }
    renderFeedTable(root, allPayload, { kidsCount: kidsPayload.task_count || 0 });
    return { allPayload: allPayload, kidsPayload: kidsPayload };
  }

  window.TasksPaginated = {
    PAGE_SIZE: PAGE_SIZE,
    mountTasksFeed: mountTasksFeed,
    renderFeedTable: renderFeedTable,
    parseFilter: parseFilter,
    resolveExecutor: resolveExecutor,
  };
})();
