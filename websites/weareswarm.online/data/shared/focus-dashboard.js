(function () {
  "use strict";

  const DATA_BASE = "/data/planner/";

  function toList(value) {
    if (value == null) return [];
    if (Array.isArray(value)) return value;
    if (typeof value === "string") return value ? [value] : [];
    return [];
  }

  async function fetchJson(name) {
    const res = await fetch(DATA_BASE + name, { cache: "no-store" });
    if (!res.ok) throw new Error(name + " HTTP " + res.status);
    return res.json();
  }

  async function fetchJsonSettled(name, fallback) {
    try {
      return { ok: true, data: await fetchJson(name) };
    } catch (err) {
      return { ok: false, data: fallback != null ? fallback : {}, error: err };
    }
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

  function formatSyncTime(isoString) {
    if (!isoString || isoString === "unknown") return "unknown";
    try {
      const d = new Date(isoString);
      if (Number.isNaN(d.getTime())) return isoString;
      return d.toLocaleString(undefined, {
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
        timeZoneName: "short",
      });
    } catch (_err) {
      return isoString;
    }
  }

  function setSyncMeta(manifest, options) {
    const node = document.getElementById("sync-meta");
    if (!node || !manifest) return;
    const raw = manifest.synced_at || manifest.generated_at || "unknown";
    const when = formatSyncTime(raw);
    const src = manifest.source || "DreamVault planner";
    const prefix = options && options.prefix ? options.prefix + " " : "";
    node.textContent = prefix + "Data refreshed: " + when + " · Source: " + src;
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
    const raw = toList(cap.unlocks).filter(Boolean);
    if (!raw.length) return [primary];
    if (raw.length === 1) return raw;
    if (raw.includes(primary)) return [primary];
    if (raw.every(function (u) { return u.indexOf("enable_") === 0; })) return [primary];
    return raw.slice(0, 1);
  }

  function statusTone(value) {
    const v = String(value || "").toUpperCase();
    if (v === "FIXED" || v === "MVP_SHIPPED" || v === "VERIFIED" || v === "LIVE_AUTH_FIXED") {
      return "ok";
    }
    if (v.indexOf("BLOCK") >= 0 || v === "OPEN" || v.indexOf("DNS_BLOCKED") >= 0) {
      return "block";
    }
    if (v.indexOf("NEED") >= 0 || v === "UNKNOWN" || v === "CHECKING") {
      return "warn";
    }
    return "neutral";
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
      const blockers = toList(cap.blockers);

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

  function formatUsd(value) {
    const n = Number(value);
    if (!Number.isFinite(n)) return "—";
    return "$" + n.toLocaleString("en-US", { maximumFractionDigits: 0 });
  }

  function renderRevenuePanel(panel) {
    const data = panel || {};
    const statusBadge = document.getElementById("revenue-status");
    if (statusBadge) {
      statusBadge.textContent = data.status || "—";
      statusBadge.className = "badge " + statusTone(data.status || "");
    }

    renderStatusGrid(document.getElementById("revenue-kv"), [
      ["Revenue Goal", formatUsd(data.revenue_goal_usd), false],
      ["Pipeline Value", formatUsd(data.pipeline_value_usd), false],
      ["Active Leads", data.active_leads != null ? String(data.active_leads) : "—", false],
      ["Status", data.status || "—", true],
      ["Next Action", data.next_action || "—", false],
      ["Bottleneck", data.bottleneck || "—", true],
    ]);

    const targets = data.daily_targets || {};
    const targetsNode = document.getElementById("revenue-targets");
    if (targetsNode) {
      targetsNode.innerHTML = "";
      const targetRows = [
        ["Prospects", targets.prospects_found],
        ["Outreach", targets.outreach_sent],
        ["Follow-ups", targets.followups],
        ["Conversations", targets.conversations],
        ["Posts", targets.posts],
        ["DMs", targets.dms],
        ["Audit offers", targets.audit_offers_sent],
      ];
      targetRows.forEach(function (row) {
        if (row[1] == null) return;
        targetsNode.appendChild(
          el("span", { className: "pill", text: row[0] + ": " + row[1] })
        );
      });
    }

    const funnelNode = document.getElementById("revenue-funnel");
    if (funnelNode) {
      funnelNode.innerHTML = "";
      const funnel = data.funnel || {};
      [
        ["Prospects", funnel.prospects],
        ["Contacted", funnel.contacted],
        ["Conversations", funnel.conversations],
        ["Proposals", funnel.proposals],
        ["Customers", funnel.customers],
        ["Revenue", funnel.revenue_usd != null ? formatUsd(funnel.revenue_usd) : null],
      ].forEach(function (row) {
        if (row[1] == null) return;
        funnelNode.appendChild(
          el("span", { className: "pill pill--funnel", text: row[0] + ": " + row[1] })
        );
      });
    }

    const mo = data.marketing_operator || {};
    const moNode = document.getElementById("revenue-marketing-operator");
    if (moNode) {
      moNode.innerHTML = "";
      [
        ["Prospects found", mo.prospects_found],
        ["Audits", mo.audits_generated],
        ["Outreach ready", mo.outreach_ready],
        ["Awaiting approval", mo.awaiting_approval],
        ["Sent today", mo.sent_today],
        ["Responses", mo.responses],
        ["Active convos", mo.active_conversations],
        ["Leads", mo.leads],
        ["Pipeline", mo.revenue_pipeline != null ? formatUsd(mo.revenue_pipeline) : null],
      ].forEach(function (row) {
        if (row[1] == null) return;
        moNode.appendChild(
          el("span", { className: "pill pill--funnel", text: row[0] + ": " + row[1] })
        );
      });
    }

    const aq = mo.approval_queue || {};
    const aqNode = document.getElementById("revenue-approval-queue");
    if (aqNode) {
      aqNode.innerHTML = "";
      [
        ["Pending", aq.pending],
        ["Approved", aq.approved],
        ["Rejected", aq.rejected],
      ].forEach(function (row) {
        if (row[1] == null) return;
        aqNode.appendChild(
          el("span", { className: "pill", text: row[0] + ": " + row[1] })
        );
      });
    }

    const operatorNode = document.getElementById("revenue-operator");
    if (operatorNode) {
      const op = data.daily_operator || {};
      operatorNode.textContent = op.command || "—";
    }
  }

  function renderOnboardingFlowB(flowB) {
    const block = flowB || {};

    renderStatusGrid(document.getElementById("onboarding-flow-b-kv"), [
      ["Capability", block.capability_id || "—", false],
      ["Label", block.label || "—", false],
      ["Description", block.description || "—", false],
    ]);

    const stepsNode = document.getElementById("onboarding-flow-b-steps");
    if (stepsNode) {
      stepsNode.innerHTML = "";
      toList(block.steps).forEach(function (item, index) {
        stepsNode.appendChild(el("li", { text: (index + 1) + ". " + item }));
      });
    }

    const proofNode = document.getElementById("onboarding-flow-b-proof");
    if (proofNode) {
      proofNode.innerHTML = "";
      toList(block.proof_paths).forEach(function (path) {
        proofNode.appendChild(el("span", { className: "pill", text: path }));
      });
    }

    const lanesNode = document.getElementById("onboarding-flow-b-lanes");
    if (lanesNode) {
      lanesNode.innerHTML = "";
      toList(block.enables_lanes).forEach(function (lane) {
        lanesNode.appendChild(el("span", { className: "pill", text: lane }));
      });
    }
  }

  function renderOnboardingPanel(panel) {
    const data = panel || {};
    const captain = data.captain || {};
    const log = captain.log || {};
    const cursor = data.cursor_agent || {};
    const swarm = data.swarm_onboarding || {};
    const closeout = data.closeout || {};

    const statusBadge = document.getElementById("onboarding-status");
    if (statusBadge) {
      const hasLog = log.log_count > 0;
      statusBadge.textContent = hasLog ? "Captain log active" : "Awaiting first log";
      statusBadge.className = "badge " + (hasLog ? "ok" : "warn");
    }

    renderStatusGrid(document.getElementById("onboarding-captain-kv"), [
      ["Captain", captain.display_name || captain.agent_id || "—", false],
      ["Pattern", captain.execution_pattern || "—", false],
      ["Job", captain.job_summary || "—", false],
      ["Log files", log.log_count != null ? String(log.log_count) : "0", false],
      [
        "Latest log",
        (log.latest_log && log.latest_log.path) || "No CAPTAINS_LOG_CYCLE_*.md yet",
        false,
      ],
      ["Update log", log.update_command || "—", false],
    ]);

    const captainList = document.getElementById("onboarding-captain-responsibilities");
    if (captainList) {
      captainList.innerHTML = "";
      toList(captain.responsibilities).forEach(function (item) {
        captainList.appendChild(el("li", { text: item }));
      });
    }

    const principlesNode = document.getElementById("onboarding-captain-principles");
    if (principlesNode) {
      principlesNode.innerHTML = "";
      toList(captain.principles).forEach(function (item) {
        principlesNode.appendChild(el("span", { className: "pill", text: item }));
      });
    }

    const logEntries = document.getElementById("onboarding-captain-log-entries");
    if (logEntries) {
      logEntries.innerHTML = "";
      const entries = toList(log.recent_entries);
      if (!entries.length) {
        logEntries.appendChild(
          el("li", {
            className: "empty-state",
            text:
              "No log entries yet — create via captain_update_log.py in D:/agent-tools",
          })
        );
      } else {
        entries.forEach(function (entry) {
          const line =
            (entry.timestamp ? "[" + entry.timestamp + "] " : "") +
            (entry.event || "—") +
            (entry.agent ? " · " + entry.agent : "");
          logEntries.appendChild(el("li", { text: line }));
        });
      }
    }

    const workflow = captain.workflow || {};
    const workflowNode = document.getElementById("onboarding-captain-workflow");
    if (workflowNode) {
      workflowNode.innerHTML = "";
      [
        ["Start of cycle", workflow.start_of_cycle],
        ["During cycle", workflow.during_cycle],
        ["End of cycle", workflow.end_of_cycle],
      ].forEach(function (row) {
        const block = el("div", { style: "margin-bottom:0.75rem;" }, [
          el("p", { className: "section-title", text: row[0] }),
          renderList(row[1], function (item) {
            return el("span", { text: item });
          }),
        ]);
        workflowNode.appendChild(block);
      });
    }

    renderStatusGrid(document.getElementById("onboarding-cursor-kv"), [
      ["Core rule", cursor.core_rule || "—", false],
      ["Closeout CLI", closeout.cli || "—", false],
      ["Soft onboard", swarm.soft_onboard || "—", false],
      ["Hard onboard", swarm.hard_onboard || "—", false],
    ]);

    const aliasesNode = document.getElementById("onboarding-cli-aliases");
    if (aliasesNode) {
      aliasesNode.innerHTML = "";
      toList(cursor.cli_aliases).forEach(function (row) {
        aliasesNode.appendChild(
          el("span", {
            className: "pill",
            text: (row.label || "—") + ": " + (row.detail || ""),
          })
        );
      });
    }

    const boundaries = document.getElementById("onboarding-boundaries");
    if (boundaries) {
      boundaries.innerHTML = "";
      toList(data.dreamvault_boundaries).forEach(function (item) {
        boundaries.appendChild(el("li", { text: item }));
      });
    }

    renderOnboardingFlowB(data.flow_b_strategy_loop);

    const sourcesNode = document.getElementById("onboarding-sources");
    if (sourcesNode) {
      sourcesNode.textContent = toList(data.sources).join(" · ") || "—";
    }
  }

  function renderSparkPanel(spark) {
    const sp = (spark && spark.spark_project) || {};
    const ml = (spark && spark.mission_loop) || {};
    const dd = (spark && spark.dadudekc_site) || {};

    renderStatusGrid(document.getElementById("spark-kv"), [
      ["Brand", sp.display_name || sp.brand_id || "—", false],
      ["Status", sp.status || "—", true],
      ["Lane", sp.lane || "—", false],
      ["Description", sp.description || "—", false],
      ["MISSION_LOOP", ml.MISSION_LOOP || "—", true],
      ["Commit", ml.commit || "—", false],
      ["Tests", ml.tests != null ? String(ml.tests) : "—", false],
      ["Build target", ml.build_target || "—", false],
    ]);

    const sparkActions = document.getElementById("spark-actions");
    if (sparkActions) {
      sparkActions.innerHTML = "";
      toList(sp.next_actions).forEach(function (a) {
        sparkActions.appendChild(el("li", { text: a }));
      });
    }

    const authFixed = dd.AUTH_IMMERSION === "FIXED";
    const statusBadge = document.getElementById("dadudekc-status");
    if (statusBadge) {
      statusBadge.textContent = authFixed ? "AUTH_IMMERSION=FIXED" : (dd.status || "unknown");
      statusBadge.className = "badge " + (
        authFixed ? "ok" : (toList(dd.blockers).length ? "block" : "ok")
      );
    }

    renderStatusGrid(document.getElementById("dadudekc-kv"), [
      ["Live domain", dd.live_domain || dd.canonical_domain || "—", false],
      ["AUTH_IMMERSION", dd.AUTH_IMMERSION || "—", true],
      ["wp-login redirect", dd.wp_login_redirect || "—", false],
      ["Alias note", dd.alias_note || "—", false],
      ["Linkage", dd.linkage_status || "—", false],
      ["Repo", dd.github_repo || "—", false],
      ["Blockers", toList(dd.blockers).join(", ") || "none", false],
    ]);

    const dadActions = document.getElementById("dadudekc-actions");
    if (dadActions) {
      dadActions.innerHTML = "";
      toList(dd.next_actions).forEach(function (a) {
        dadActions.appendChild(el("li", { text: a }));
      });
    }
  }

  function resolveRepoBuckets(domainIndex, publicBoard) {
    if (domainIndex && domainIndex.repo_buckets && Object.keys(domainIndex.repo_buckets).length) {
      return domainIndex.repo_buckets;
    }
    const buckets = {};
    const boardBuckets = (publicBoard && publicBoard.buckets) || {};
    Object.entries(boardBuckets).forEach(([bucket, rows]) => {
      const repos = toList(rows)
        .map((row) => row.project || row.repo || repoBasename(row.repo_root))
        .filter(Boolean);
      if (repos.length) buckets[bucket] = repos;
    });
    if (Object.keys(buckets).length) return buckets;
    (domainIndex && domainIndex.domains || []).forEach((d) => {
      const label = d.domain || "unknown";
      const repos = toList(d.repos);
      if (repos.length) buckets[label] = repos;
    });
    return buckets;
  }

  window.FocusDashboard = {
    fetchJson,
    fetchJsonSettled,
    toList,
    el,
    renderList,
    formatSyncTime,
    setSyncMeta,
    repoBasename,
    resolveRepoName,
    resolveRationale,
    resolveCapabilityLabel,
    resolveCapabilityUnlocks,
    renderCapabilityCards,
    renderStatusGrid,
    renderRevenuePanel,
    renderOnboardingPanel,
    renderSparkPanel,
    formatUsd,
    statusTone,
    resolveRepoBuckets,
    DATA_BASE,
  };
})();
