(function () {
  "use strict";

  const DATA_BASE = "/data/planner/";

  async function fetchJson(name) {
    const res = await fetch(DATA_BASE + name, { cache: "no-store" });
    if (!res.ok) throw new Error(name + " HTTP " + res.status);
    return res.json();
  }

  function el(tag, attrs, children) {
    const node = document.createElement(tag);
    if (attrs) {
      Object.entries(attrs).forEach(([k, v]) => {
        if (k === "className") node.className = v;
        else if (k === "text") node.textContent = v;
        else node.setAttribute(k, v);
      });
    }
    (children || []).forEach((c) => {
      if (typeof c === "string") node.appendChild(document.createTextNode(c));
      else if (c) node.appendChild(c);
    });
    return node;
  }

  function renderList(items, mapFn) {
    const ul = el("ul", { className: "list" });
    if (!items || !items.length) {
      ul.appendChild(el("li", { className: "empty-state", text: "No items yet." }));
      return ul;
    }
    items.forEach((item) => ul.appendChild(el("li", null, [mapFn(item)])));
    return ul;
  }

  function setSyncMeta(manifest) {
    const node = document.getElementById("sync-meta");
    if (!node || !manifest) return;
    const when = manifest.synced_at || manifest.generated_at || "unknown";
    const src = manifest.source || "DreamVault planner";
    node.textContent = "Data refreshed: " + when + " · Source: " + src;
  }

  function repoBasename(repoRoot) {
    if (!repoRoot || typeof repoRoot !== "string") return "";
    const parts = repoRoot.replace(/\\/g, "/").split("/").filter(Boolean);
    return parts.length ? parts[parts.length - 1] : "";
  }

  function resolveRepoName(rec) {
    if (!rec) return "—";
    return (
      rec.repo ||
      rec.project ||
      rec.lane_id ||
      repoBasename(rec.repo_root) ||
      "—"
    );
  }

  function resolveRationale(nextLane) {
    if (!nextLane) return "—";
    return nextLane.rationale || nextLane.reason || "—";
  }

  function capabilityId(cap) {
    return cap.capability_id || cap.id || cap.domain || "unknown";
  }

  function resolveCapabilityLabel(cap) {
    if (!cap) return "—";
    const id = capabilityId(cap);
    const domain = cap.domain && cap.domain !== id ? " · " + cap.domain : "";
    return id + domain + " (" + (cap.status || "?") + ")";
  }

  function resolveCapabilityUnlocks(cap) {
    if (!cap) return [];
    const capId = capabilityId(cap);
    const primary = "enable_" + capId;
    const raw = (cap.unlocks || []).filter(Boolean);
    if (!raw.length) return [primary];
    if (raw.length === 1) return raw;
    if (raw.includes(primary)) return [primary];
    if (raw.every(function (u) { return u.indexOf("enable_") === 0; })) return [primary];
    return raw.slice(0, 1);
  }

  function toList(value) {
    if (value == null) return [];
    if (Array.isArray(value)) return value;
    if (typeof value === "string") return value ? [value] : [];
    return [];
  }

  function statusIcon(icon) {
    if (icon === "ok") return "✅ ";
    if (icon === "warn") return "⚠️ ";
    if (icon === "block") return "⛔ ";
    return "";
  }

  function statusTone(value) {
    const v = String(value || "").toUpperCase();
    if (v === "FIXED" || v === "MVP_SHIPPED" || v === "VERIFIED" || v === "LIVE_AUTH_FIXED") {
      return "ok";
    }
    if (v.indexOf("BLOCK") >= 0 || v === "OPEN" || v.indexOf("DNS_BLOCKED") >= 0) {
      return "block";
    }
    if (
      v.indexOf("NEED") >= 0 ||
      v === "UNKNOWN" ||
      v === "CHECKING" ||
      v.indexOf("RELOCATION") >= 0 ||
      v.indexOf("MIGRATION") >= 0
    ) {
      return "warn";
    }
    return "neutral";
  }

  function renderProgressWidget(container, items) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "progress-widget";
    const rows = items || [];
    if (!rows.length) {
      container.appendChild(el("p", { className: "empty-state", text: "No progress metrics yet." }));
      return;
    }
    rows.forEach(function (item) {
      const pct = Math.max(0, Math.min(100, Number(item.percent) || 0));
      container.appendChild(
        el("div", { className: "progress-row" }, [
          el("span", { className: "progress-label", text: item.label || "—" }),
          el("div", { className: "progress-track" }, [
            el("div", {
              className: "progress-fill",
              style: "width:" + pct + "%",
            }),
          ]),
          el("span", { className: "progress-pct", text: pct + "%" }),
        ])
      );
    });
  }

  function renderSparkStateList(container, projectState) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "status-checklist";
    const state = projectState || {};
    toList(state.completed).forEach(function (item) {
      container.appendChild(
        el("li", { className: "status-ok", text: statusIcon("ok") + item })
      );
    });
    toList(state.in_progress).forEach(function (item) {
      container.appendChild(
        el("li", { className: "status-warn", text: "🔄 " + item })
      );
    });
    if (!container.children.length) {
      container.appendChild(el("li", { className: "empty-state", text: "No project state items yet." }));
    }
  }

  function renderPills(items, className) {
    const wrap = el("div", { className: className || "pill-row" });
    (items || []).forEach(function (item) {
      wrap.appendChild(el("span", { className: "pill", text: item }));
    });
    return wrap;
  }

  function renderCapabilityCards(container, capabilities) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "capability-grid";
    const caps = capabilities || [];
    if (!caps.length) {
      container.appendChild(el("p", { className: "empty-state", text: "No capabilities indexed yet." }));
      return;
    }
    caps.forEach(function (cap) {
      const capId = capabilityId(cap);
      const status = String(cap.status || "unknown").toLowerCase();
      const statusClass = status === "verified" ? "ok" : status.indexOf("need") >= 0 ? "warn" : "neutral";
      const unlocks = resolveCapabilityUnlocks(cap);
      const blockers = cap.blockers || [];

      const header = el("div", { className: "cap-card__header" }, [
        el("h3", { className: "cap-card__title", text: capId }),
        el("span", { className: "cap-badge cap-badge--" + statusClass, text: cap.status || "?" }),
      ]);

      const bodyChildren = [
        el("p", { className: "cap-card__domain", text: cap.domain || "—" }),
        el("div", { className: "cap-card__section" }, [
          el("span", { className: "cap-card__label", text: "Unlocks" }),
          renderPills(unlocks, "pill-row pill-row--unlock"),
        ]),
      ];

      if (blockers.length) {
        bodyChildren.push(
          el("div", { className: "cap-card__section cap-card__section--blockers" }, [
            el("span", { className: "cap-card__label", text: "Blockers" }),
            renderPills(blockers, "pill-row pill-row--blocker"),
          ])
        );
      }

      if (cap.owner_repo) {
        bodyChildren.push(
          el("p", { className: "cap-card__meta", text: "Owner: " + cap.owner_repo })
        );
      }

      container.appendChild(
        el("article", { className: "cap-card" }, [
          header,
          el("div", { className: "cap-card__body" }, bodyChildren),
        ])
      );
    });
  }

  function renderStatusGrid(container, rows, options) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "status-grid" + (options && options.compact ? " status-grid--compact" : "");
    (rows || []).forEach(function (row) {
      const label = row[0];
      const value = row[1] != null ? String(row[1]) : "—";
      const asPill = row[2];
      const dd = el("dd");
      if (asPill) {
        dd.appendChild(
          el("span", {
            className: "field-pill field-pill--" + statusTone(value),
            text: value,
          })
        );
      } else if (label === "Blockers" && value !== "none" && value !== "—") {
        dd.appendChild(renderPills(value.split(",").map(function (s) { return s.trim(); }), "pill-row pill-row--blocker"));
      } else {
        dd.textContent = value;
      }
      container.appendChild(el("dt", { text: label }));
      container.appendChild(dd);
    });
  }

  function renderSparkPanel(spark) {
    const data = spark || {};
    const sp = data.spark_project || {};
    const ml = data.mission_loop || {};

    const titleNode = document.getElementById("spark-panel-title");
    if (titleNode) titleNode.textContent = data.title || "Spark / MaskZero";

    const subtitleNode = document.getElementById("spark-panel-subtitle");
    if (subtitleNode) {
      subtitleNode.textContent =
        data.subtitle ||
        "MZSpark universe, character systems, battle arcs, and manifestation engine.";
    }

    const statusBadge = document.getElementById("spark-status") || document.getElementById("dadudekc-status");
    if (statusBadge) {
      const status = sp.status || sp.mission_state || "ACTIVE_RELOCATION";
      statusBadge.textContent = status;
      statusBadge.className = "badge " + statusTone(status);
    }

    renderStatusGrid(document.getElementById("spark-kv"), [
      ["Brand", sp.brand || sp.display_name || sp.brand_id || "—", false],
      ["Status", sp.status || "—", true],
      ["Lane", sp.lane || "—", false],
      ["Description", sp.description || "—", false],
      ["Current Canonical Domain", sp.canonical_domain || "maskzero.site", false],
      ["Legacy Surface", sp.legacy_surface || "dadudekc.site", false],
      ["Repository", sp.repository || "—", false],
      ["Mission State", sp.mission_state || "—", true],
      ["Public Surface", sp.public_surface || "—", false],
      ["MISSION_LOOP", ml.MISSION_LOOP || "—", true],
      ["Commit", ml.commit || "—", false],
      ["Tests", ml.tests != null ? String(ml.tests) : "—", false],
      ["Build target", ml.build_target || "—", false],
    ]);

    renderSparkStateList(document.getElementById("spark-state-list"), data.project_state);
    renderProgressWidget(document.getElementById("spark-progress"), data.progress);

    const reasonNode = document.getElementById("spark-reason");
    if (reasonNode) reasonNode.textContent = data.reason || "—";

    const sparkActions = document.getElementById("spark-actions");
    if (sparkActions) {
      sparkActions.innerHTML = "";
      toList(data.next_actions || sp.next_actions).forEach(function (a) {
        sparkActions.appendChild(el("li", { text: a }));
      });
    }

    const blockersNode = document.getElementById("spark-blockers");
    if (blockersNode) {
      const blockers = toList(data.blockers);
      blockersNode.textContent = blockers.length
        ? blockers.join("; ")
        : (data.blockers_note || "None");
    }
  }

  function resolveRepoBuckets(domainIndex, publicBoard) {
    if (domainIndex && domainIndex.repo_buckets && Object.keys(domainIndex.repo_buckets).length) {
      return domainIndex.repo_buckets;
    }
    const buckets = {};
    const boardBuckets = (publicBoard && publicBoard.buckets) || {};
    Object.entries(boardBuckets).forEach(([bucket, rows]) => {
      const repos = (rows || [])
        .map((row) => row.project || row.repo || repoBasename(row.repo_root))
        .filter(Boolean);
      if (repos.length) buckets[bucket] = repos;
    });
    if (Object.keys(buckets).length) return buckets;
    (domainIndex && domainIndex.domains || []).forEach((d) => {
      const label = d.domain || "unknown";
      const repos = d.repos || [];
      if (repos.length) buckets[label] = repos;
    });
    return buckets;
  }

  window.FocusDashboard = {
    fetchJson,
    el,
    renderList,
    setSyncMeta,
    repoBasename,
    resolveRepoName,
    resolveRationale,
    resolveCapabilityLabel,
    resolveCapabilityUnlocks,
    renderCapabilityCards,
    renderStatusGrid,
    renderSparkPanel,
    renderProgressWidget,
    renderSparkStateList,
    toList,
    statusTone,
    resolveRepoBuckets,
    DATA_BASE,
  };
})();
