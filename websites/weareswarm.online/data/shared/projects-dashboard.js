(function () {
  "use strict";

  var FD = window.FocusDashboard;
  if (!FD) return;

  var el = FD.el;
  var toList = FD.toList;
  var statusTone = FD.statusTone;

  var BUCKET_LABELS = {
    canonical_core: "Canonical core",
    promotion_candidate: "Promotion candidate",
    public_surface: "Public surface",
    toolbelt: "Toolbelt",
    inspect_manually: "Review needed",
    variant: "Variant",
    archive_candidate: "Archive candidate",
    plugin: "Plugin",
  };

  function bucketLabel(key) {
    return BUCKET_LABELS[key] || String(key || "unknown").replace(/_/g, " ");
  }

  function actionLabel(action) {
    if (!action) return "—";
    return String(action).replace(/_/g, " ");
  }

  function statusPercent(value) {
    var n = Number(value);
    if (!Number.isFinite(n)) return "—";
    return n + "% readiness";
  }

  function githubUrl(projectName) {
    if (!projectName || typeof projectName !== "string") return null;
    if (projectName.indexOf("__") >= 0) {
      var parts = projectName.split("__");
      return "https://github.com/" + parts[0] + "/" + parts[1];
    }
    return "https://github.com/Victor-Dixon/" + projectName;
  }

  function flattenBoard(board) {
    var rows = [];
    var buckets = (board && board.buckets) || {};
    Object.keys(buckets).forEach(function (bucket) {
      toList(buckets[bucket]).forEach(function (row) {
        rows.push({
          project: row.project || "—",
          bucket: bucket,
          domain: row.domain || "—",
          action: row.action || "—",
          status: row.status,
          is_canonical: !!row.is_canonical,
          canonical_id: row.canonical_id,
          tagline: row.tagline,
          description: row.description,
          capabilities: toList(row.capabilities),
          public_surfaces: toList(row.public_surfaces),
        });
      });
    });
    rows.sort(function (a, b) {
      var bucketCmp = a.bucket.localeCompare(b.bucket);
      if (bucketCmp !== 0) return bucketCmp;
      return a.project.localeCompare(b.project);
    });
    return rows;
  }

  function mergeEcosystemMeta(rows, ecosystem, enriched) {
    var byName = {};
    toList(ecosystem && ecosystem.projects).forEach(function (p) {
      var keys = [p.id, p.name, p.canonical_id, (p.name || "").toLowerCase()];
      keys.forEach(function (k) {
        if (k) byName[String(k).toLowerCase()] = p;
      });
    });
    var aliases = (enriched && enriched.canonical_aliases) || {};
    return rows.map(function (row) {
      var key = String(row.project || "").toLowerCase();
      var canonicalId = row.canonical_id || aliases[key] || aliases[key.replace(/-/g, "")];
      var meta =
        byName[key] ||
        byName[key.replace(/-/g, "")] ||
        (canonicalId && byName[String(canonicalId).toLowerCase()]);
      return Object.assign({}, row, {
        canonical_id: canonicalId || row.canonical_id,
        tagline: row.tagline || (meta && meta.tagline),
        description: row.description || (meta && meta.description),
        repo_url: (meta && meta.repo_url) || row.repo_url || githubUrl(row.project),
        highlights: row.highlights || (meta && meta.highlights),
      });
    });
  }

  function setText(id, value) {
    var node = document.getElementById(id);
    if (node) node.textContent = value != null ? String(value) : "—";
  }

  function renderStatsHero(stats) {
    if (!stats) return;
    setText("stat-projects", stats.projects);
    setText("stat-domains", stats.domains);
    setText("stat-productized", stats.productized);
    setText("stat-tasks", stats.open_tasks);
    setText("stat-scanner", stats.authority_nodes != null ? stats.authority_nodes.toLocaleString() : "—");
  }

  function renderLanePanel(nextLane) {
    var panel = document.getElementById("lane-panel");
    if (!panel || !nextLane || !nextLane.next_lane) return;
    panel.style.display = "";
    setText("lane-badge", nextLane.next_lane);
    setText("lane-subtitle", nextLane.next_task || "Active productization lane");
    var rationale = document.getElementById("lane-rationale");
    if (rationale) {
      rationale.textContent = nextLane.rationale || "Governed lane from DreamVault planner.";
    }
  }

  function renderProductsGrid(container, products) {
    if (!container) return;
    container.innerHTML = "";
    var items = toList(products);
    if (!items.length) {
      container.appendChild(el("p", { className: "empty-state", text: "No productized offers indexed yet." }));
      return;
    }
    var grid = el("div", { className: "product-card-grid" });
    items.forEach(function (p) {
      var isProductized = p.status === "productized";
      var tags = el("div", { className: "card-tags" }, [
        el("span", {
          className: "badge " + (isProductized ? "ok" : "warn"),
          text: isProductized ? "productized" : (p.maturity || p.status || "offer"),
        }),
        p.price_usd != null ? el("span", { className: "badge", text: "$" + p.price_usd }) : null,
      ]);
      var body = [
        tags,
        el("h3", { text: p.name || p.offer || p.tag || "Offer" }),
      ];
      if (p.revenue_model || p.price_model) {
        body.push(el("p", { className: "project-desc", text: p.revenue_model || p.price_model }));
      }
      if (p.sessions != null) {
        body.push(el("p", { className: "proof-line", text: "Proof: " + p.sessions + " debug sessions logged" }));
      }
      if (p.landing_page) {
        body.push(el("div", { className: "card-links" }, [
          el("a", { href: p.landing_page, target: "_blank", rel: "noopener", text: "Landing page" }),
        ]));
      }
      grid.appendChild(el("article", { className: "product-card" }, body));
    });
    container.appendChild(grid);
  }

  function renderScannerSummary(container, enriched) {
    if (!container) return;
    container.innerHTML = "";
    var auth = enriched.authority_summary || {};
    var scan = enriched.scanner_summary || {};
    var roots = toList(scan.top_roots).slice(0, 6);
    var badge = document.getElementById("scanner-badge");
    if (badge) {
      badge.textContent = (auth.matched_projects || []).length + " board repos matched";
    }
    var grid = el("dl", { className: "status-grid status-grid--compact" });
    [
      ["Authority nodes", auth.node_count],
      ["Duplicate groups", auth.duplicate_group_count],
      ["Distinct roots", scan.distinct_roots],
      ["Files scanned", scan.total_files != null ? scan.total_files.toLocaleString() : null],
    ].forEach(function (row) {
      if (row[1] == null) return;
      grid.appendChild(el("dt", { text: row[0] }));
      grid.appendChild(el("dd", { text: String(row[1]) }));
    });
    container.appendChild(grid);
    if (roots.length) {
      container.appendChild(el("p", { className: "section-title", text: "Top scan roots" }));
      var pills = el("div", { className: "pill-row" });
      roots.forEach(function (r) {
        pills.appendChild(el("span", {
          className: "pill",
          text: r.root + " · " + (r.file_count || 0).toLocaleString() + " files",
        }));
      });
      container.appendChild(pills);
    }
  }

  function renderProjectCard(row) {
    var capabilities = toList(row && row.capabilities);
    var publicSurfaces = toList(row && row.public_surfaces);
    var tone = statusTone(
      row.is_canonical ? "VERIFIED" :
      row.status >= 80 ? "MVP_SHIPPED" :
      row.status >= 50 ? "CHECKING" : "OPEN"
    );
    var tags = el("div", { className: "card-tags" }, [
      el("span", {
        className: "badge " + (row.is_canonical ? "ok" : tone === "ok" ? "ok" : tone === "warn" ? "warn" : ""),
        text: bucketLabel(row.bucket),
      }),
      row.is_canonical ? el("span", { className: "badge ok", text: "canonical" }) : null,
      el("span", { className: "badge", text: statusPercent(row.status) }),
      row.scanner && row.scanner.classification
        ? el("span", { className: "badge", text: row.scanner.classification })
        : null,
    ]);

    var bodyChildren = [
      tags,
      el("h3", { text: row.project }),
      el("p", { className: "project-domain", text: row.domain }),
    ];

    if (row.tagline || row.description) {
      bodyChildren.push(
        el("p", { className: "project-desc", text: [row.tagline, row.description].filter(Boolean).join(" — ") })
      );
    } else {
      bodyChildren.push(
        el("p", {
          className: "project-desc",
          text: actionLabel(row.action) + " · " + capabilities.slice(0, 4).join(", "),
        })
      );
    }

    if (capabilities.length) {
      bodyChildren.push(
        el("div", { className: "pill-row" }, capabilities.slice(0, 6).map(function (cap) {
          return el("span", { className: "pill", text: cap });
        }))
      );
    }

    if (publicSurfaces.length) {
      bodyChildren.push(
        el("p", { className: "project-surfaces", text: "Surfaces: " + publicSurfaces.join(", ") })
      );
    }

    if (row.github && row.github.description) {
      bodyChildren.push(el("p", { className: "github-meta", text: row.github.description }));
    }

    var linkChildren = [
      row.repo_url ? el("a", { href: row.repo_url, target: "_blank", rel: "noopener", text: "GitHub" }) : null,
      el("span", { className: "muted-inline", text: actionLabel(row.action) }),
    ];
    if (row.github && row.github.stars != null) {
      linkChildren.push(el("span", { className: "muted-inline", text: "★ " + row.github.stars }));
    }
    bodyChildren.push(el("div", { className: "card-links" }, linkChildren));

    return el("article", { className: "project-card" }, bodyChildren);
  }

  function renderEnrichedBoard(container, enriched) {
    if (!container) return;
    container.innerHTML = "";
    var projects = toList(enriched.projects);
    if (!projects.length) {
      container.appendChild(el("p", {
        className: "empty-state",
        text: "No projects in enriched board — run export_projects_board.py.",
      }));
      return;
    }

    var meta = enriched.board_meta || {};
    var summary = el("p", {
      className: "projects-summary",
      text: projects.length + " repos · " + toList(enriched.domains).length + " domains · " +
        toList(enriched.products).length + " offers · source " + (meta.source || enriched.source || "planner"),
    });
    container.appendChild(summary);

    var grid = el("div", { className: "project-card-grid" });
    projects.forEach(function (row) {
      grid.appendChild(renderProjectCard(row));
    });
    container.appendChild(grid);
  }

  function renderEnrichedPage(opts) {
    var enriched = opts.enriched || {};
    var sourceBadge = document.getElementById("source-badge");
    if (sourceBadge) {
      sourceBadge.textContent = (enriched.board_meta && enriched.board_meta.source) || enriched.source || "enriched";
      sourceBadge.className = "badge ok";
    }

    renderStatsHero(enriched.stats);
    renderLanePanel(enriched.next_lane);
    renderEnrichedBoard(document.getElementById("project-cards"), enriched);
    renderProductsGrid(document.getElementById("products-grid"), enriched.products);
    renderDomainTable(document.getElementById("domains-table"), { domains: enriched.domains });
    renderScannerSummary(document.getElementById("scanner-summary"), enriched);
  }

  function renderProjectCards(container, board, ecosystem, domainIndex, enriched) {
    if (!container) return;
    container.innerHTML = "";

    var rows = mergeEcosystemMeta(flattenBoard(board), ecosystem, enriched);
    if (!rows.length) {
      container.appendChild(el("p", {
        className: "empty-state",
        text: "No projects in public_project_board.json — run DreamVault sync.",
      }));
      return;
    }

    var summary = el("p", {
      className: "projects-summary",
      text: rows.length + " repos across " + Object.keys((board && board.buckets) || {}).length + " buckets · " +
        ((domainIndex && domainIndex.domains) || []).length + " bounded domains indexed",
    });
    container.appendChild(summary);

    var grid = el("div", { className: "project-card-grid" });
    rows.forEach(function (row) {
      grid.appendChild(renderProjectCard(row));
    });
    container.appendChild(grid);
  }

  function ownerBadge(project) {
    if (project.owner === "Kids" || project.owner === "Aria") return "Kids";
    if (project.owner === "Agent") return "Agent";
    var tags = toList(project.tags);
    if (tags.some(function (t) { return String(t).toLowerCase() === "kids"; })) return "Kids";
    if (tags.some(function (t) { return String(t).toLowerCase() === "agent"; })) return "Agent";
    return project.owner || "Victor";
  }

  function badgeClass(owner) {
    if (owner === "Kids") return "badge kids";
    if (owner === "Agent") return "badge agent";
    return "badge victor";
  }

  function formatDate(iso) {
    if (!iso) return "—";
    return String(iso).slice(0, 10);
  }

  function renderProjectsFeedGrid(container, feed) {
    if (!container) return;
    container.innerHTML = "";

    var projects = toList(feed && feed.projects);
    if (!projects.length) {
      container.appendChild(el("p", {
        className: "empty-state",
        text: "No projects in projects_full.json — run export_projects_feed.py.",
      }));
      return;
    }

    var generated = (feed && feed.generated) ? " · synced " + formatDate(feed.generated) : "";
    container.appendChild(el("p", {
      className: "projects-summary",
      text: projects.length + " repos from projectscanner + GitHub" + generated,
    }));

    var grid = el("div", { className: "projects-wow-grid" });
    projects.forEach(function (p) {
      var owner = ownerBadge(p);
      var title = p.display_name || p.repo_name || "—";
      var header = el("header", { className: "project-card__header" }, [
        el("h3", { text: title }),
        el("span", { className: badgeClass(owner), text: owner }),
      ]);

      var desc = p.tagline || p.description || "—";
      var meta = el("ul", { className: "project-meta" }, [
        el("li", { text: "★ " + (p.stars != null ? p.stars : 0) }),
        el("li", { text: "Issues: " + (p.issues != null ? p.issues : 0) }),
        el("li", { text: "Push: " + formatDate(p.pushed_at || p.last_updated) }),
      ]);

      if (p.last_commit) {
        meta.appendChild(el("li", { text: "Commit: " + formatDate(p.last_commit) }));
      }

      var bodyChildren = [header, el("p", { className: "project-desc", text: desc }), meta];

      var pills = toList(p.tags).slice(0, 5);
      if (p.language && pills.indexOf(p.language) < 0) pills.unshift(p.language);
      if (pills.length) {
        bodyChildren.push(
          el("div", { className: "pill-row" }, pills.map(function (tag) {
            return el("span", { className: "pill", text: tag });
          }))
        );
      }

      if (p.bucket) {
        bodyChildren.push(el("p", { className: "project-surfaces", text: "Bucket: " + p.bucket }));
      }

      var repoUrl = p.html_url || githubUrl(p.repo_name);
      if (repoUrl) {
        bodyChildren.push(
          el("div", { className: "card-links" }, [
            el("a", { href: repoUrl, target: "_blank", rel: "noopener", text: "GitHub ↗" }),
          ])
        );
      }

      grid.appendChild(el("article", { className: "project-card project-card--wow" }, bodyChildren));
    });
    container.appendChild(grid);
  }

  function renderDomainTable(table, domainIndex) {
    if (!table) return;
    var tbody = table.querySelector("tbody");
    if (!tbody) return;
    tbody.innerHTML = "";
    (domainIndex.domains || []).forEach(function (d) {
      var tr = document.createElement("tr");
      var bucket = d.bucket || (d.repos && d.repos.length ? "mapped" : "—");
      var repos = (d.repos || []).slice(0, 4).join(", ");
      if ((d.repos || []).length > 4) repos += " +" + (d.repos.length - 4);
      tr.innerHTML =
        "<td>" + (d.domain || "—") + "</td>" +
        "<td>" + ((d.bounded_contexts || []).join(", ") || "—") + "</td>" +
        "<td>" + (d.canonical_repo || "—") + "</td>" +
        "<td><span class=\"status-pill\">" + bucket + "</span></td>" +
        "<td class=\"muted-cell\">" + (repos || "—") + "</td>";
      tbody.appendChild(tr);
    });
  }

  function renderProductOutcomesTable(tbody, products) {
    if (!tbody) return;
    tbody.innerHTML = "";
    var items = toList(products);
    if (!items.length) {
      var emptyRow = document.createElement("tr");
      emptyRow.innerHTML = '<td colspan="5" class="empty-state">No products in portfolio_products.json</td>';
      tbody.appendChild(emptyRow);
      return;
    }
    items.forEach(function (p) {
      var tr = document.createElement("tr");
      var status = p.status || "—";
      var statusClass = statusTone(status);
      var surface = p.live_surface || "—";
      var surfaceCell = surface;
      if (String(surface).indexOf("http") === 0) {
        surfaceCell = '<a href="' + surface + '" target="_blank" rel="noopener">' + surface + "</a>";
      }
      tr.innerHTML =
        "<td><strong>" + (p.name || "—") + "</strong></td>" +
        '<td><span class="field-pill field-pill--' + statusClass + '">' + status + "</span></td>" +
        '<td class="muted-cell">' + (p.proof || "—") + "</td>" +
        '<td class="muted-cell">' + (p.revenue_potential || "—") + "</td>" +
        '<td class="muted-cell">' + surfaceCell + "</td>";
      tbody.appendChild(tr);
    });
  }

  window.ProjectsDashboard = {
    renderEnrichedPage: renderEnrichedPage,
    renderEnrichedBoard: renderEnrichedBoard,
    renderLanePanel: renderLanePanel,
    renderProductsGrid: renderProductsGrid,
    renderScannerSummary: renderScannerSummary,
    renderStatsHero: renderStatsHero,
    renderProjectsFeedGrid: renderProjectsFeedGrid,
    renderProjectCards: renderProjectCards,
    renderDomainTable: renderDomainTable,
    renderProductOutcomesTable: renderProductOutcomesTable,
    flattenBoard: flattenBoard,
  };
})();
