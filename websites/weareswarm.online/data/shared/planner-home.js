(function () {
  "use strict";

  const FD = window.FocusDashboard;
  if (!FD) return;

  const {
    fetchJson,
    fetchJsonSettled,
    el,
    setSyncMeta,
    resolveRationale,
    renderStatusGrid,
    renderMainFocuses,
    renderHomepageHero,
  } = FD;

  function sanitizePath(value) {
    if (!value || typeof value !== "string") return "—";
    return value
      .replace(/^[A-Za-z]:[\\/][^\\/]+[\\/]?/g, "")
      .replace(/\\/g, "/");
  }

  function renderTaskList(container, tasks, emptyLabel) {
    if (!container) return;
    container.innerHTML = "";
    const items = tasks || [];
    if (!items.length) {
      container.appendChild(el("p", { className: "empty-state", text: emptyLabel || "No items yet." }));
      return;
    }
    const ul = el("ul", { className: "list" });
    items.forEach(function (task) {
      const id = task.task_id || task.lane_id || task.id || "—";
      const repo = task.repo ? " · " + task.repo : "";
      const pri = task.priority ? " [" + task.priority + "]" : "";
      ul.appendChild(
        el("li", null, [
          el("strong", { text: id + pri }),
          el("span", { className: "muted-inline", text: repo }),
          task.rationale ? el("p", { className: "item-rationale", text: task.rationale }) : null,
        ])
      );
    });
    container.appendChild(ul);
  }

  function renderBlockedList(container, items) {
    if (!container) return;
    container.innerHTML = "";
    const blocked = items || [];
    if (!blocked.length) {
      container.appendChild(el("p", { className: "empty-state", text: "No blockers — clear to execute." }));
      return;
    }
    const ul = el("ul", { className: "list list--blockers" });
    blocked.forEach(function (item) {
      const label = typeof item === "string" ? item : item.label || item.reason || JSON.stringify(item);
      ul.appendChild(el("li", { text: sanitizePath(label) }));
    });
    container.appendChild(ul);
  }

  async function hydrate() {
    const [
      manifestResult,
      homepageResult,
      focusPanelResult,
      nextLaneResult,
      dynamicPanel,
      sparkResult,
      revenueResult,
    ] = await Promise.all([
      fetchJsonSettled("manifest.json"),
      fetchJsonSettled("homepage_panel.json", {}),
      fetchJsonSettled("focus_panel.json", {}),
      fetchJsonSettled("next_lane.json", {}),
      fetchJsonSettled("dynamic_planner_panel.json", {}),
      fetchJsonSettled("spark_panel.json", {}),
      fetchJsonSettled("revenue_operator_panel.json", {}),
    ]);

    const manifest = manifestResult.data || {};
    const homepage = homepageResult.data || {};
    const focusPanel = focusPanelResult.data || {};
    const nextLane = nextLaneResult.data || {};
    const dynamic = dynamicPanel.data || {};
    const spark = sparkResult.data || {};
    const revenue = revenueResult.data || {};

    if (manifestResult.ok) setSyncMeta(manifest);

    renderHomepageHero(homepage.hero);

    const nextBest = dynamic.next_best_task;
    const remaining = (focusPanel.consolidation && focusPanel.consolidation.remaining) || [];
    renderStatusGrid(document.getElementById("operating-state-grid"), [
      ["Dream.OS status", "Operational", true],
      ["Consolidation", (focusPanel.consolidation && focusPanel.consolidation.status_heading) || "Status: COMPLETE", true],
      ["Current phase", (focusPanel.consolidation && focusPanel.consolidation.operating_phase) || "—", false],
      ["Next best task", (nextBest && nextBest.task_id) || "—", false],
      ["Active work", remaining.slice(0, 2).join("; ") || "—", false],
      ["Data refreshed", FD.formatSyncTime(manifest.synced_at || dynamic.generated_at || nextLane.generated_at) || "—", false],
    ]);

    renderTaskList(
      document.getElementById("approved-tasks-list"),
      nextLane.approved_tasks,
      "No approved tasks in next_lane.json."
    );

    const blocked = []
      .concat(nextLane.blocked_until || [])
      .concat(remaining.filter(function (item) {
        return String(item).toLowerCase().indexOf("robinhood") >= 0;
      }));
    renderBlockedList(document.getElementById("blocked-list"), blocked);

    renderMainFocuses(document.getElementById("main-focuses-grid"), {
      focusPanel: focusPanel,
      spark: spark,
      revenue: revenue,
    });

    const liveBadge = document.getElementById("planner-live-badge");
    if (liveBadge) liveBadge.textContent = "● Operational";
  }

  document.addEventListener("DOMContentLoaded", function () {
    hydrate().catch(function (err) {
      console.error("planner-home hydrate failed", err);
      const meta = document.getElementById("sync-meta");
      if (meta) meta.textContent = "Planner data unavailable — check sync.";
    });
  });
})();
