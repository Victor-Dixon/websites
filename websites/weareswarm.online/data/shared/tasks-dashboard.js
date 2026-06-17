(function () {
  "use strict";

  var LOAD_TIMEOUT_MS = 20000;

  function setBadge(id, text, tone) {
    var node = document.getElementById(id);
    if (!node) return;
    node.textContent = text;
    if (tone) {
      node.className = "badge " + tone;
    }
  }

  function setText(id, text) {
    var node = document.getElementById(id);
    if (node) node.textContent = text;
  }

  function failSyncMeta(message) {
    setBadge("sync-meta", message, "block");
  }

  function campaignLabel(queue) {
    if (!queue) return "—";
    if (queue.campaign) return queue.campaign;
    var campaigns = queue.campaigns;
    if (campaigns && typeof campaigns === "object") {
      return campaigns.primary || campaigns.parallel_revenue || "—";
    }
    return "—";
  }

  async function mountTasksDashboard() {
    var dashboard = window.FocusDashboard;
    if (!dashboard || typeof dashboard.fetchJson !== "function") {
      failSyncMeta("Load error: FocusDashboard unavailable");
      return;
    }

    var fetchJson = dashboard.fetchJson;
    var el = dashboard.el;
    var setSyncMeta = dashboard.setSyncMeta;
    var timedOut = false;
    var timer = window.setTimeout(function () {
      timedOut = true;
      failSyncMeta("Load error: planner JSON timed out after " + LOAD_TIMEOUT_MS / 1000 + "s");
    }, LOAD_TIMEOUT_MS);

    try {
      var results = await Promise.allSettled([
        fetchJson("strategic_active_queue.json"),
        fetchJson("next_lane.json"),
        fetchJson("manifest.json"),
      ]);
      if (timedOut) return;

      if (results[0].status !== "fulfilled") {
        throw results[0].reason;
      }
      if (results[1].status !== "fulfilled") {
        throw results[1].reason;
      }

      var queue = results[0].value;
      var nextLane = results[1].value;
      if (results[2].status === "fulfilled") {
        setSyncMeta(results[2].value);
      } else {
        setBadge("sync-meta", "Queue loaded · manifest unavailable", "");
      }

      setText("campaign", campaignLabel(queue));
      setText("queue-next", queue.next_lane || "—");
      setText("deferred-rule", queue.deferred_rule || "—");
      setText(
        "runtime-count",
        queue.runtime_active_count != null ? String(queue.runtime_active_count) : "—"
      );
      setText(
        "master-count",
        queue.master_task_count != null ? String(queue.master_task_count) : "—"
      );

      var masterSource = document.getElementById("master-source");
      if (masterSource && queue.master_task_source) {
        masterSource.textContent = queue.master_task_source;
      }

      var aq = document.getElementById("active-queue");
      if (aq) {
        aq.innerHTML = "";
        (queue.active_queue || []).forEach(function (t) {
          aq.appendChild(el("li", { text: t }));
        });
        if (!aq.children.length) {
          aq.appendChild(el("li", { className: "empty-state", text: "No queued campaign tasks." }));
        }
      }

      var nextKv = document.getElementById("next-lane-kv");
      if (nextKv) {
        nextKv.innerHTML = "";
        [
          ["Lane", nextLane.next_lane || "—"],
          ["Task", nextLane.next_task || "—"],
          ["Rationale", nextLane.rationale || nextLane.reason || "—"],
        ].forEach(function (pair) {
          nextKv.appendChild(el("dt", { text: pair[0] }));
          nextKv.appendChild(el("dd", { text: pair[1] }));
        });
      }

      var tbody = document.querySelector("#approved-table tbody");
      if (tbody) {
        tbody.innerHTML = "";
        (nextLane.approved_tasks || []).forEach(function (t) {
          var tr = document.createElement("tr");
          tr.innerHTML =
            "<td>" +
            (t.task_id || "—") +
            "</td>" +
            "<td>" +
            (t.priority || "—") +
            "</td>" +
            "<td>" +
            (t.repo || "—") +
            "</td>" +
            "<td><span class=\"status-pill\">" +
            (t.status || "—") +
            "</span></td>";
          tbody.appendChild(tr);
        });
        if (!tbody.children.length) {
          var emptyTr = document.createElement("tr");
          emptyTr.innerHTML = "<td colspan=\"4\" class=\"empty-state\">No approved tasks in next lane.</td>";
          tbody.appendChild(emptyTr);
        }
      }

      var masterBody = document.querySelector("#master-table tbody");
      if (masterBody) {
        masterBody.innerHTML = "";
        (queue.master_tasks || []).forEach(function (t) {
          var tr = document.createElement("tr");
          tr.innerHTML =
            "<td>" +
            (t.priority || "—") +
            "</td>" +
            "<td><span class=\"status-pill\">" +
            (t.status || "—") +
            "</span></td>" +
            "<td>" +
            (t.project || "—") +
            "</td>" +
            "<td>" +
            (t.lane || "—") +
            "</td>" +
            "<td>" +
            (t.title || t.id || "—") +
            "</td>";
          masterBody.appendChild(tr);
        });
        if (!masterBody.children.length) {
          var masterEmpty = document.createElement("tr");
          masterEmpty.innerHTML =
            "<td colspan=\"5\" class=\"empty-state\">Master ledger unavailable — run planner sync.</td>";
          masterBody.appendChild(masterEmpty);
        }
      }

      var feedRoot = document.getElementById("full-task-feed");
      if (feedRoot && window.TasksPaginated) {
        await window.TasksPaginated.mountTasksFeed(
          feedRoot,
          "/data/planner/all_tasks.json",
          "/data/planner/kids_tasks.json"
        );
        var search = document.getElementById("feed-search");
        if (search) {
          var params = new URLSearchParams(window.location.search);
          search.value = params.get("q") || "";
          search.addEventListener("change", function () {
            var f = window.TasksPaginated.parseFilter();
            var q = search.value.trim();
            var qs = new URLSearchParams();
            if (f.executor !== "all") qs.set("executor", f.executor);
            if (q) qs.set("q", q);
            window.location.search = qs.toString();
          });
        }
      } else if (feedRoot) {
        var feedMeta = feedRoot.querySelector("[data-feed-meta]");
        if (feedMeta) feedMeta.textContent = "Feed unavailable — TasksPaginated missing";
      }
    } catch (err) {
      if (!timedOut) {
        failSyncMeta("Load error: " + (err && err.message ? err.message : String(err)));
      }
    } finally {
      window.clearTimeout(timer);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountTasksDashboard);
  } else {
    mountTasksDashboard();
  }

  window.TasksDashboard = { mount: mountTasksDashboard };
})();
