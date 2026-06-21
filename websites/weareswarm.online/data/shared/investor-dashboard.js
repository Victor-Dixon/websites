(function () {
  "use strict";

  var DATA_URL = "/investor-dashboard/data/investor_dashboard.json";

  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (k) {
        if (k === "className") node.className = attrs[k];
        else if (k === "text") node.textContent = attrs[k];
        else if (k === "href") node.href = attrs[k];
        else node.setAttribute(k, attrs[k]);
      });
    }
    (children || []).forEach(function (c) {
      if (typeof c === "string") node.appendChild(document.createTextNode(c));
      else if (c) node.appendChild(c);
    });
    return node;
  }

  function setText(id, value) {
    var node = document.getElementById(id);
    if (node) node.textContent = value == null || value === "" ? "—" : String(value);
  }

  function formatPct(value) {
    var n = Number(value);
    if (!Number.isFinite(n)) return "—";
    return n + "%";
  }

  function formatTimestamp(value) {
    if (!value) return "—";
    var FD = window.FocusDashboard;
    if (FD && FD.formatSyncTime) return FD.formatSyncTime(value);
    try {
      return new Date(value).toLocaleString();
    } catch (_err) {
      return String(value);
    }
  }

  function deployTone(status) {
    if (status === "deployed") return "ok";
    if (status === "surfaces_synced") return "warn";
    return "warn";
  }

  function renderHealth(metrics) {
    var kv = document.getElementById("health-kv");
    var badge = document.getElementById("deploy-badge");
    if (!kv) return;

    var sync = metrics.surface_sync_health || {};
    var deploy = metrics.deploy_status || {};
    kv.innerHTML = "";

    [
      ["Surface sync", sync.value || "—", sync.source || "—"],
      ["Sync generated", formatTimestamp(sync.generated_at), sync.source || "—"],
      ["Deploy status", deploy.status || "—", deploy.detail || "—"],
      ["Latest closeout", formatTimestamp((metrics.latest_closeout_feed_timestamp || {}).value), (metrics.latest_closeout_feed_timestamp || {}).source || "—"],
    ].forEach(function (row) {
      var dt = el("dt", { text: row[0] });
      var dd = el("dd", null, [el("strong", { text: row[1] }), document.createTextNode(" · " + row[2])]);
      kv.appendChild(dt);
      kv.appendChild(dd);
    });

    if (badge) {
      badge.textContent = deploy.status || "unknown";
      badge.className = "badge " + deployTone(deploy.status);
    }
  }

  function renderLinks(links) {
    var container = document.getElementById("surface-links");
    if (!container) return;
    container.innerHTML = "";
    (links || []).forEach(function (link) {
      var a = el("a", {
        className: "pill",
        href: link.href,
        title: link.proof || "",
      }, [link.label]);
      container.appendChild(a);
    });
  }

  function renderMetricsTable(metrics, linksByHref) {
    var body = document.getElementById("metrics-table-body");
    if (!body) return;
    body.innerHTML = "";

    Object.keys(metrics || {}).forEach(function (key) {
      var metric = metrics[key];
      if (!metric || typeof metric !== "object") return;
      var value = metric.value;
      if (key === "deploy_status") value = metric.status;
      if (key === "revenue_class_coverage") value = formatPct(metric.value) + " (" + metric.classified + "/" + metric.total + ")";
      if (key === "latest_closeout_feed_timestamp") value = formatTimestamp(metric.value);

      var proofHref = metric.proof_href;
      var proofCell = proofHref
        ? el("a", { href: proofHref, text: proofHref })
        : el("span", { text: "—" });

      var tr = el("tr", null, [
        el("td", { text: key.replace(/_/g, " ") }),
        el("td", { text: value == null ? "—" : String(value) }),
        el("td", { className: "mono-note", text: metric.source || "—" }),
        el("td", null, [proofCell]),
      ]);
      body.appendChild(tr);
    });
  }

  function mount() {
    fetch(DATA_URL, { cache: "no-store" })
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
      })
      .then(function (data) {
        var metrics = data.metrics || {};
        var m = metrics;

        setText("metric-active-tasks", (m.active_task_count || {}).value);
        setText("metric-kids-routable", (m.kids_routable_task_count || {}).value);
        setText("metric-portfolio-projects", (m.portfolio_project_count || {}).value);
        setText("metric-revenue-coverage", formatPct((m.revenue_class_coverage || {}).value));
        setText("metric-completed-prune", (m.completed_prune_count || {}).value);
        setText("metric-stale-prune", (m.stale_prune_count || {}).value);

        var FD = window.FocusDashboard;
        if (FD && FD.setSyncMeta) {
          FD.setSyncMeta(data, { prefix: "Investor dashboard" });
        } else {
          setText("sync-meta", "Generated " + formatTimestamp(data.generated_at));
        }

        var schemaBadge = document.getElementById("schema-badge");
        if (schemaBadge) schemaBadge.textContent = data.schema || "unknown";

        renderHealth(metrics);
        renderLinks(data.links || []);
        renderMetricsTable(metrics);
      })
      .catch(function (err) {
        var errNode = document.getElementById("load-error");
        if (errNode) {
          errNode.hidden = false;
          errNode.textContent = "Could not load investor dashboard JSON — run build_investor_dashboard.py --write. " + (err && err.message ? err.message : String(err));
        }
        var badge = document.getElementById("live-badge");
        if (badge) {
          badge.className = "badge warn";
          badge.textContent = "● Data unavailable";
        }
      });
  }

  window.InvestorDashboard = { mount: mount };
})();
