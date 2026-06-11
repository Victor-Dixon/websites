(function () {
  "use strict";

  const FD = window.FocusDashboard;
  if (!FD) return;

  const {
    fetchJson,
    el,
    setSyncMeta,
    resolveRepoName,
    resolveRationale,
    renderStatusGrid,
    renderMainFocuses,
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

  function collectBlockedRecommendations(recommendations) {
    return (recommendations || [])
      .filter(function (rec) {
        return (rec.blockers && rec.blockers.length) || rec.action === "inspect_manually";
      })
      .slice(0, 8)
      .map(function (rec) {
        const blockers = (rec.blockers || []).join(", ");
        const suffix = blockers ? " — " + blockers : " — manual review";
        return resolveRepoName(rec) + ": " + (rec.action || "review") + suffix;
      });
  }

  async function hydrate() {
    const [
      manifest,
      nextLane,
      recommendations,
      consolidationManifest,
      dynamicPanel,
      spark,
      revenue,
    ] = await Promise.all([
      fetchJson("manifest.json").catch(function () { return null; }),
      fetchJson("next_lane.json").catch(function () { return null; }),
      fetchJson("consolidation_recommendations.json").catch(function () { return null; }),
      fetchJson("project_consolidation_decision_manifest_001.json").catch(function () { return null; }),
      fetchJson("dynamic_planner_panel.json").catch(function () { return null; }),
      fetchJson("spark_panel.json").catch(function () { return null; }),
      fetchJson("revenue_operator_panel.json").catch(function () { return null; }),
    ]);

    setSyncMeta(manifest);

    const nextBest = dynamicPanel && dynamicPanel.next_best_task;
    const operatingMode = nextLane && nextLane.execute === false ? "PLAN_ONLY" : "EXECUTE_READY";
    renderStatusGrid(document.getElementById("operating-state-grid"), [
      ["Operating mode", operatingMode, true],
      ["Next best task", (nextBest && nextBest.task_id) || "—", false],
      ["Next lane", (nextLane && nextLane.next_lane) || (dynamicPanel && dynamicPanel.consolidation_next_lane) || "—", false],
      ["Prior lane", nextLane && nextLane.prior_lane || "—", false],
      ["Rationale", (nextBest && nextBest.rationale) || resolveRationale(nextLane), false],
      ["Next task", sanitizePath(nextLane && nextLane.next_task), false],
      ["Data refreshed", FD.formatSyncTime((manifest && manifest.synced_at) || (dynamicPanel && dynamicPanel.generated_at) || (nextLane && nextLane.generated_at)) || "—", false],
    ]);

    renderTaskList(
      document.getElementById("approved-tasks-list"),
      nextLane && nextLane.approved_tasks,
      "No approved tasks in next_lane.json."
    );

    const blocked = []
      .concat((nextLane && nextLane.blocked_until) || [])
      .concat(collectBlockedRecommendations(recommendations && recommendations.recommendations));
    renderBlockedList(document.getElementById("blocked-list"), blocked);

    renderMainFocuses(document.getElementById("main-focuses-grid"), {
      nextLane: nextLane,
      spark: spark,
      revenue: revenue,
      consolidationManifest: consolidationManifest,
    });

    const liveBadge = document.getElementById("planner-live-badge");
    if (liveBadge) liveBadge.textContent = "● Hub live";
  }

  document.addEventListener("DOMContentLoaded", function () {
    hydrate().catch(function (err) {
      console.error("planner-home hydrate failed", err);
      const meta = document.getElementById("sync-meta");
      if (meta) meta.textContent = "Planner data unavailable — check sync.";
    });
  });
})();
