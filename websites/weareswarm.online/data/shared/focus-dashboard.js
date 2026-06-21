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
    if (
      v.indexOf("BLOCK") >= 0 ||
      v === "OPEN" ||
      v.indexOf("DNS_BLOCKED") >= 0
    ) {
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

  function statusIcon(icon) {
    if (icon === "ok") return "✅ ";
    if (icon === "warn") return "⚠️ ";
    if (icon === "block") return "⛔ ";
    return "";
  }

  function metricToneClass(tone) {
    if (tone === "ok") return "metric-card__status--ok";
    if (tone === "warn") return "metric-card__status--warn";
    if (tone === "block") return "metric-card__status--block";
    return "";
  }

  function renderMetricCards(container, metrics) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "metric-grid";
    (metrics || []).forEach(function (metric) {
      const tone = metric.tone || statusTone(metric.status);
      container.appendChild(
        el("article", { className: "metric-card" }, [
          el("p", { className: "metric-card__label", text: metric.label || "—" }),
          el("p", {
            className: "metric-card__status " + metricToneClass(tone),
            text: metric.status || "—",
          }),
        ])
      );
    });
  }

  function renderFocusStatus(container, items) {
    if (!container) return;
    container.innerHTML = "";
    container.className = "status-checklist";
    (items || []).forEach(function (item) {
      const icon = item.icon || "ok";
      container.appendChild(
        el("li", {
          className: "status-" + icon,
          text: statusIcon(icon) + (item.text || "—"),
        })
      );
    });
  }

  function renderConsolidationPanel(container, panel) {
    if (!container || !panel) return;
    container.innerHTML = "";
    container.className = "consolidation-prose";

    container.appendChild(el("h3", { text: panel.title || "Consolidation Status" }));
    container.appendChild(el("h3", { text: panel.status_heading || "Status: COMPLETE" }));

    container.appendChild(el("h3", { text: "Completed" }));
    const completed = el("ul");
    toList(panel.completed).forEach(function (item) {
      completed.appendChild(el("li", { text: item }));
    });
    container.appendChild(completed);

    container.appendChild(el("h3", { text: "Remaining" }));
    const remaining = el("ul");
    toList(panel.remaining || panel.active_work).forEach(function (item) {
      remaining.appendChild(el("li", { text: item }));
    });
    container.appendChild(remaining);

    if (panel.operating_phase) {
      container.appendChild(
        el("p", {
          className: "result-line",
          text: "Current Phase: " + panel.operating_phase,
        })
      );
    }
  }

  function renderHomepageHero(hero) {
    if (!hero) return;
    var eyebrow = document.getElementById("hero-eyebrow");
    var title = document.getElementById("hero-title");
    var subhero = document.getElementById("hero-subhero");
    var desc = document.getElementById("hero-description");
    var capabilities = document.getElementById("hero-capabilities");
    var milestones = document.getElementById("hero-milestones");
    var focus = document.getElementById("hero-current-focus");
    var phaseBadge = document.getElementById("phase-badge");
    var liveBadge = document.getElementById("planner-live-badge");
    if (eyebrow && hero.eyebrow) eyebrow.textContent = hero.eyebrow;
    if (title && hero.title) title.textContent = hero.title;
    if (subhero && hero.subhero) subhero.textContent = hero.subhero;
    if (desc && hero.description) desc.textContent = hero.description;
    if (phaseBadge && hero.phase_badge) phaseBadge.textContent = hero.phase_badge;
    if (liveBadge && hero.live_badge) liveBadge.textContent = hero.live_badge;
    var capItems = toList(hero.capabilities || hero.milestones);
    var capNode = capabilities || milestones;
    if (capNode) {
      capNode.innerHTML = "";
      capItems.forEach(function (item) {
        capNode.appendChild(el("li", { text: item }));
      });
    }
    if (focus && hero.current_focus) focus.textContent = hero.current_focus;
  }

  function renderPortfolioNetwork(network) {
    if (!network) return;
    var titleNode = document.getElementById("portfolio-network-title");
    var subtitleNode = document.getElementById("portfolio-network-subtitle");
    var rootNode = document.getElementById("portfolio-root");
    var grid = document.getElementById("portfolio-network-grid");
    if (titleNode && network.title) titleNode.textContent = network.title;
    if (subtitleNode && network.subtitle) subtitleNode.textContent = network.subtitle;
    if (rootNode && network.root) {
      rootNode.innerHTML = "";
      rootNode.appendChild(el("h3", { text: network.root.name || "Dream.OS" }));
      rootNode.appendChild(el("p", { text: network.root.role || "" }));
    }
    if (!grid) return;
    grid.innerHTML = "";
    toList(network.domains).forEach(function (domain) {
      var links = el("div", { className: "card-links" });
      if (domain.href) {
        var linkAttrs = { href: domain.href, text: domain.domain || domain.name };
        if (domain.external) {
          linkAttrs.target = "_blank";
          linkAttrs.rel = "noopener noreferrer";
        }
        links.appendChild(el("a", linkAttrs));
      }
      grid.appendChild(
        el("article", { className: "ecosystem-card" }, [
          el("h3", { text: domain.name || "—" }),
          el("p", { text: domain.role || "" }),
          links,
        ])
      );
    });
  }

  function renderProofLayer(proof, themes) {
    if (!proof && !themes) return;
    var titleNode = document.getElementById("proof-layer-title");
    var flowNode = document.getElementById("proof-layer-flow");
    var descNode = document.getElementById("proof-layer-description");
    var themesNode = document.getElementById("hero-themes");
    if (proof) {
      if (titleNode && proof.title) titleNode.textContent = proof.title;
      if (flowNode && proof.flow) flowNode.textContent = proof.flow;
      if (descNode && proof.description) descNode.textContent = proof.description;
    }
    if (themesNode) {
      themesNode.innerHTML = "";
      toList(themes).forEach(function (theme) {
        themesNode.appendChild(el("span", { className: "pill", text: theme }));
      });
    }
  }

  function renderHomepagePanel(homepage) {
    if (!homepage) return;
    var hero = Object.assign({}, homepage.hero || {}, {
      capabilities: homepage.capabilities || (homepage.hero && homepage.hero.capabilities),
    });
    renderHomepageHero(hero);
    renderPortfolioNetwork(homepage.portfolio_network);
    renderProofLayer(homepage.proof_layer, homepage.themes);
  }

  function operationalHighlights(focusPanel) {
    const panel = (focusPanel && focusPanel.consolidation) || {};
    return toList(panel.remaining || panel.active_work).slice(0, 3);
  }

  function renderMainFocuses(container, opts) {
    if (!container) return;
    container.innerHTML = "";

    const nextLane = (opts && opts.nextLane) || {};
    const spark = (opts && opts.spark) || {};
    const revenue = (opts && opts.revenue) || {};
    const focusPanel = (opts && opts.focusPanel) || {};

    const sp = spark.spark_project || {};
    const phase =
      focusPanel.phase === "maintenance" ? "Productization" : "Active";

    const cards = [
      {
        title: "DreamOS",
        status: phase,
        statusClass: "ok",
        description:
          "Operating system for building — governed runtime, planner authority, and investor-facing proof.",
        highlights: operationalHighlights(focusPanel),
        links: [
          { href: "/projects/", text: "Product portfolio" },
          { href: "/focus/", text: "Mission control" },
        ],
      },
      {
        title: "Spark / MaskZero",
        status: sp.status || spark.mission_state || "ACTIVE_RELOCATION",
        statusClass: statusTone(sp.status || "ACTIVE_RELOCATION"),
        description:
          spark.subtitle ||
          sp.description ||
          "MZSpark character universe — migration to maskzero.site in progress.",
        highlights: toList(spark.next_actions || sp.next_actions).slice(0, 2),
        links: [
          { href: "https://maskzero.site", text: "Open maskzero.site", external: true },
          { href: "https://dadudekc.site", text: "Legacy surface", external: true },
          { href: "/focus/#spark-panel", text: "Spark status" },
        ],
      },
      {
        title: "Revenue",
        status: revenue.status || "Active",
        statusClass: (revenue.active_leads || 0) > 0 ? "ok" : "warn",
        description:
          revenue.next_action ||
          "DreamScan offer, lead pipeline, and swarm_60k_60d campaign.",
        highlights: [
          revenue.bottleneck ? "Bottleneck: " + revenue.bottleneck : null,
          revenue.revenue_goal_usd != null
            ? "Goal: " + formatUsd(revenue.revenue_goal_usd)
            : null,
          revenue.campaign_id || null,
        ].filter(Boolean).slice(0, 3),
        links: [{ href: "/focus/#revenue-panel", text: "Revenue operator" }],
      },
    ];

    cards.forEach(function (card) {
      const tags = el("div", { className: "card-tags" }, [
        el("span", {
          className: "badge " + (card.statusClass || ""),
          text: card.status,
        }),
      ]);

      const highlights = card.highlights || [];
      const list = highlights.length
        ? el(
            "ul",
            null,
            highlights.map(function (item) {
              return el("li", { text: item });
            })
          )
        : null;

      const links = el(
        "div",
        { className: "card-links" },
        (card.links || []).map(function (link) {
          const attrs = { href: link.href, text: link.text };
          if (link.external) {
            attrs.target = "_blank";
            attrs.rel = "noopener noreferrer";
          }
          return el("a", attrs);
        })
      );

      container.appendChild(
        el("article", { className: "focus-card" }, [
          tags,
          el("h3", { text: card.title }),
          el("p", { text: card.description }),
          list,
          links,
        ])
      );
    });
  }

  function renderSparkPanel(spark) {
    const data = spark || {};
    const sp = data.spark_project || {};
    const ml = data.mission_loop || {};
    const legacy = data.legacy_site || {};

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
      ["Legacy Surface", sp.legacy_surface || legacy.domain || "dadudekc.site", false],
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
    renderMainFocuses,
    renderSparkPanel,
    renderProgressWidget,
    renderSparkStateList,
    renderMetricCards,
    renderFocusStatus,
    renderConsolidationPanel,
    renderHomepageHero,
    renderHomepagePanel,
    renderPortfolioNetwork,
    renderProofLayer,
    formatUsd,
    statusTone,
    resolveRepoBuckets,
    DATA_BASE,
  };
})();
