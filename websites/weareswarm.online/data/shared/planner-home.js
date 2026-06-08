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
    renderCapabilityCards,
    renderStatusGrid,
    statusTone,
    resolveRepoBuckets,
  } = FD;

  function sanitizePath(value) {
    if (!value || typeof value !== "string") return "—";
    return value.replace(/^[A-Za-z]:\\[^\\]+\\?/g, "").replace(/\\/g, "/");
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

  function renderLanePills(container, lanes) {
    if (!container) return;
    container.innerHTML = "";
    const items = lanes || [];
    if (!items.length) {
      container.appendChild(el("p", { className: "empty-state", text: "No active lanes queued." }));
      return;
    }
    const wrap = el("div", { className: "pill-row" });
    items.forEach(function (lane) {
      wrap.appendChild(el("span", { className: "pill", text: lane }));
    });
    container.appendChild(wrap);
  }

  function renderRepoBuckets(container, buckets) {
    if (!container) return;
    container.innerHTML = "";
    const entries = Object.entries(buckets || {});
    if (!entries.length) {
      container.appendChild(el("p", { className: "empty-state", text: "Consolidation buckets not synced yet." }));
      return;
    }
    entries.forEach(function (pair) {
      const label = pair[0];
      const repos = pair[1] || [];
      container.appendChild(
        el("div", { className: "bucket-row" }, [
          el("span", { className: "bucket-label", text: label.replace(/_/g, " ") }),
          el("span", { className: "bucket-repos", text: repos.join(", ") || "—" }),
        ])
      );
    });
  }

  function renderConsolidationSummary(container, manifest, domainIndex) {
    if (!container) return;
    container.innerHTML = "";

    if (manifest && manifest.buckets) {
      const summary = el("dl", { className: "kv" });
      Object.entries(manifest.buckets).forEach(function (pair) {
        const key = pair[0];
        const repos = pair[1] || [];
        summary.appendChild(el("dt", { text: key.replace(/_/g, " ") }));
        summary.appendChild(el("dd", { text: repos.join(", ") || "—" }));
      });
      container.appendChild(summary);

      if (manifest.dreamvault_conflict_resolution) {
        const res = manifest.dreamvault_conflict_resolution;
        container.appendChild(
          el("p", {
            className: "item-rationale",
            text: "Canonical: " + (res.canonical_repo || "—") + " — " + sanitizePath(res.conflict || ""),
          })
        );
      }
      return;
    }

    renderRepoBuckets(container, resolveRepoBuckets(domainIndex, null));
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
      domainIndex,
      recommendations,
      activeQueue,
      capabilities,
      consolidationManifest,
      publicBoard,
    ] = await Promise.all([
      fetchJson("manifest.json").catch(function () { return null; }),
      fetchJson("next_lane.json").catch(function () { return null; }),
      fetchJson("project_domain_index.json").catch(function () { return null; }),
      fetchJson("consolidation_recommendations.json").catch(function () { return null; }),
      fetchJson("strategic_active_queue.json").catch(function () { return null; }),
      fetchJson("project_capability_index.json").catch(function () { return null; }),
      fetchJson("project_consolidation_decision_manifest_001.json").catch(function () { return null; }),
      fetchJson("public_project_board.json").catch(function () { return null; }),
    ]);

    setSyncMeta(manifest);

    const operatingMode = nextLane && nextLane.execute === false ? "PLAN_ONLY" : "EXECUTE_READY";
    renderStatusGrid(document.getElementById("operating-state-grid"), [
      ["Operating mode", operatingMode, true],
      ["Next lane", nextLane && nextLane.next_lane || "—", false],
      ["Prior lane", nextLane && nextLane.prior_lane || "—", false],
      ["Rationale", resolveRationale(nextLane), false],
      ["Next task", sanitizePath(nextLane && nextLane.next_task), false],
      ["Data refreshed", (manifest && manifest.synced_at) || (nextLane && nextLane.generated_at) || "—", false],
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

    renderConsolidationSummary(
      document.getElementById("consolidation-summary"),
      consolidationManifest,
      domainIndex
    );

    const lanes = (activeQueue && activeQueue.active_queue) || [];
    renderLanePills(document.getElementById("active-lanes-list"), lanes);

    const nextLaneEl = document.getElementById("queue-next-lane");
    if (nextLaneEl) {
      nextLaneEl.textContent = (activeQueue && activeQueue.next_lane) || (nextLane && nextLane.next_lane) || "—";
    }

    renderCapabilityCards(
      document.getElementById("capability-grid"),
      (capabilities && capabilities.capabilities) || []
    );

    const liveBadge = document.getElementById("planner-live-badge");
    if (liveBadge) liveBadge.textContent = "● Planner live";
  }

  document.addEventListener("DOMContentLoaded", function () {
    hydrate().catch(function (err) {
      console.error("planner-home hydrate failed", err);
      const meta = document.getElementById("sync-meta");
      if (meta) meta.textContent = "Planner data unavailable — check sync.";
    });
  });
})();
