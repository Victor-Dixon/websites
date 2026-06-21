(function () {
  "use strict";

  var runtime = window.SparkAccountRuntime;
  if (!runtime) return;

  function timeGreeting() {
    var h = new Date().getHours();
    if (h < 12) return "Good morning";
    if (h < 17) return "Good afternoon";
    return "Good evening";
  }

  function escapeHTML(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[ch];
    });
  }

  function setText(id, text) {
    var node = document.getElementById(id);
    if (node) node.textContent = text;
  }

  function setHTML(id, html) {
    var node = document.getElementById(id);
    if (node) node.innerHTML = html;
  }

  function missionCard(priority, title, body, href, label, locked) {
    var cls = "mission-card" + (locked ? " mission-locked" : "");
    var badge = priority ? '<span class="mission-priority">' + escapeHTML(priority) + "</span>" : "";
    var action = locked
      ? '<span class="comic-button mission-btn gauntlet-soon">' + escapeHTML(label) + "</span>"
      : '<a class="comic-button mission-btn ' + (priority === "Priority" ? "red" : "primary") + '" href="' + escapeHTML(href) + '">' + escapeHTML(label) + "</a>";
    return (
      '<article class="' + cls + '">' +
        badge +
        "<h3>" + escapeHTML(title) + "</h3>" +
        "<p>" + escapeHTML(body) + "</p>" +
        action +
      "</article>"
    );
  }

  function sparkRenameBlock(currentName, renameLimit) {
    var limitNote = "";
    if (renameLimit && typeof renameLimit.remaining === "number") {
      limitNote =
        '<p class="field-help">Renames remaining today: ' +
        escapeHTML(String(renameLimit.remaining)) +
        " of " +
        escapeHTML(String(renameLimit.limit || 3)) +
        ".</p>";
    }
    return (
      '<div class="spark-rename">' +
        '<label for="spark-rename-input">Rename your Spark</label>' +
        limitNote +
        '<p class="field-help">Keep it heroic — offensive names are blocked.</p>' +
        '<div class="spark-rename-row">' +
          '<input id="spark-rename-input" type="text" maxlength="64" value="' +
            escapeHTML(currentName) +
            '" autocomplete="off" placeholder="Hero codename">' +
          '<button type="button" class="comic-button primary" id="spark-rename-btn">Save Name</button>' +
        "</div>" +
        '<p class="spark-rename-status" id="spark-rename-status" hidden></p>' +
      "</div>"
    );
  }

  function wireSparkRename(spark, hasLockedHero) {
    var btn = document.getElementById("spark-rename-btn");
    var input = document.getElementById("spark-rename-input");
    var status = document.getElementById("spark-rename-status");
    if (!btn || !input) return;

    btn.addEventListener("click", async function () {
      var next = String(input.value || "").trim();
      if (!next) {
        if (status) {
          status.hidden = false;
          status.className = "spark-rename-status err";
          status.textContent = "Enter a hero name first.";
        }
        return;
      }

      btn.disabled = true;
      if (status) {
        status.hidden = false;
        status.className = "spark-rename-status";
        status.textContent = "Saving…";
      }

      try {
        if (hasLockedHero && typeof runtime.renameSpark === "function") {
          var result = await runtime.renameSpark(next);
          if (!result.ok) {
            throw new Error(
              (result.data && (result.data.message || result.data.code)) || "Rename failed"
            );
          }
        } else if (typeof runtime.renameLocalSpark === "function") {
          runtime.renameLocalSpark(next, spark);
        }

        setText("stat-spark", next);
        var nameNode = document.querySelector("#spark-identity-card .spark-name");
        if (nameNode) nameNode.textContent = next;
        if (status) {
          status.className = "spark-rename-status ok";
          var limit = result.data && result.data.rename_limit;
          var suffix =
            limit && typeof limit.remaining === "number"
              ? " (" + limit.remaining + " rename" + (limit.remaining === 1 ? "" : "s") + " left today)"
              : "";
          status.textContent = "Spark renamed to " + next + suffix + ".";
        }
      } catch (err) {
        if (status) {
          status.className = "spark-rename-status err";
          status.textContent = String(err && err.message ? err.message : err);
        }
      } finally {
        btn.disabled = false;
      }
    });
  }

  function metricLabel(boardId) {
    if (boardId === "wins") return "Wins";
    if (boardId === "dispatch") return "Responses";
    if (boardId === "missions") return "Missions";
    return "Notoriety";
  }

  function renderLeaderboardRows(leaders, boardId) {
    if (!leaders || !leaders.length) {
      return '<p class="leaderboard-note">No standings yet. Complete missions and answer the Dispatch to climb the boards.</p>';
    }
    var metric = metricLabel(boardId);
    var rows = leaders
      .map(function (row) {
        return (
          "<tr>" +
          "<td>#" + escapeHTML(String(row.place)) + "</td>" +
          "<td>" + escapeHTML(row.hero_name || "Unknown") + "</td>" +
          "<td>" + escapeHTML(row.rank || "—") + "</td>" +
          "<td>" + escapeHTML(row.lead_domain || "—") + "</td>" +
          "<td>" + escapeHTML(String(row.score != null ? row.score : row.notoriety || 0)) + "</td>" +
          "</tr>"
        );
      })
      .join("");
    return (
      '<table class="leaderboard-table">' +
      "<thead><tr><th>Place</th><th>Hero</th><th>Rank</th><th>Domain</th><th>" +
      escapeHTML(metric) +
      "</th></tr></thead><tbody>" +
      rows +
      "</tbody></table>"
    );
  }

  function heroPortraitBlock(sparkProfile) {
    var url =
      (sparkProfile && sparkProfile.portrait_url) ||
      "/assets/img/spark-hero-placeholder.svg";
    return (
      '<img class="hero-portrait" src="' +
      escapeHTML(url) +
      '" alt="Hero portrait" loading="lazy">'
    );
  }

  async function wireInboxPreview() {
    var panel = document.getElementById("maskzero-inbox-panel");
    var preview = document.getElementById("dash-inbox-preview");
    var countEl = document.getElementById("dash-inbox-count");
    if (!panel || !preview || typeof runtime.inboxSummary !== "function") {
      return;
    }

    panel.hidden = false;
    var summary = await runtime.inboxSummary();
    if (!summary.ok || !summary.data) {
      preview.textContent = "Inbox unavailable right now.";
      return;
    }

    var unread = summary.data.unread_count || 0;
    if (countEl) {
      countEl.hidden = unread <= 0;
      countEl.textContent = unread + " unread";
    }

    var latest = summary.data.latest;
    if (!latest) {
      preview.className = "inbox-preview";
      preview.innerHTML =
        "<strong>No messages yet.</strong><p>Submit a Dispatch response to receive your first MaskZero field report.</p>";
      return;
    }

    preview.className = "inbox-preview" + (latest.status === "unread" ? " unread" : "");
    preview.innerHTML =
      "<strong>" +
      escapeHTML(latest.from || "MaskZero") +
      "</strong><h3 style=\"margin:8px 0\">" +
      escapeHTML(latest.title || "Mission Report") +
      "</h3><p>" +
      escapeHTML(String(latest.body || "").slice(0, 220)) +
      (String(latest.body || "").length > 220 ? "…" : "") +
      "</p>";
  }

  async function wireLeaderboards(profile) {
    var panel = document.getElementById("leaderboard-panel");
    var tabsEl = document.getElementById("leaderboard-tabs");
    var bodyEl = document.getElementById("leaderboard-body");
    var noteEl = document.getElementById("leaderboard-note");
    if (!panel || !tabsEl || !bodyEl || typeof runtime.leaderboardsCatalog !== "function") {
      return;
    }

    panel.hidden = false;
    var catalogResult = await runtime.leaderboardsCatalog();
    var boards =
      catalogResult.ok && catalogResult.data && Array.isArray(catalogResult.data.boards)
        ? catalogResult.data.boards
        : [
            { id: "notoriety", label: "Notoriety", description: "Overall standing." },
            { id: "dispatch", label: "Dispatch Responses", description: "Headlines answered." },
            { id: "wins", label: "Battle Wins", description: "Duel victories." },
            { id: "missions", label: "Missions Completed", description: "Field ops complete." },
          ];

    var activeBoard = "notoriety";

    async function loadBoard(boardId) {
      activeBoard = boardId;
      bodyEl.innerHTML = "<p>Loading standings…</p>";
      tabsEl.querySelectorAll(".leaderboard-tab").forEach(function (btn) {
        btn.classList.toggle("active", btn.getAttribute("data-board") === boardId);
      });
      var boardMeta = boards.find(function (b) {
        return b.id === boardId;
      });
      if (noteEl && boardMeta) {
        noteEl.textContent = boardMeta.description || "Meridian City standings.";
      }
      var result = await runtime.leaderboard(boardId, 10);
      var leaders = result.ok && result.data ? result.data.leaders : [];
      bodyEl.innerHTML = renderLeaderboardRows(leaders, boardId);
    }

    tabsEl.innerHTML = boards
      .map(function (board) {
        return (
          '<button type="button" class="leaderboard-tab' +
          (board.id === activeBoard ? " active" : "") +
          '" data-board="' +
          escapeHTML(board.id) +
          '" role="tab">' +
          escapeHTML(board.label || board.id) +
          "</button>"
        );
      })
      .join("");

    tabsEl.querySelectorAll(".leaderboard-tab").forEach(function (btn) {
      btn.addEventListener("click", function () {
        loadBoard(btn.getAttribute("data-board") || "notoriety");
      });
    });

    await loadBoard(activeBoard);
  }

  async function bootDashboard() {
    var sessionResult = await runtime.session();
    var loggedIn = !!(sessionResult.ok && sessionResult.data && sessionResult.data.logged_in);

    runtime.applyAuthVisibility(loggedIn);

    if (!loggedIn) {
      if (typeof runtime.bootAccountHub === "function") {
        await runtime.bootAccountHub();
      }
      return;
    }

    var rosterResult =
      typeof runtime.me === "function" ? await runtime.me() : { ok: false, data: {} };
    var roster = (rosterResult.ok && rosterResult.data) || sessionResult.data || {};
    var canAccessOriginLab =
      typeof runtime.canAccessOriginLab === "function"
        ? runtime.canAccessOriginLab()
        : roster.can_access_origin_lab !== false;
    var plan = roster.plan || "free";
    var isPremium = plan === "paid";
    var charCount = roster.character_count || 0;
    var maxChars = roster.max_characters || (isPremium ? 5 : 1);
    var charsRemaining = roster.characters_remaining;

    var data = sessionResult.data || {};
    var label = data.display_name || data.user_login || "Operator";
    var localSpark = runtime.loadSparkIdentity();
    var serverHero = null;

    var heroResult = await runtime.hero();
    var profile = heroResult.ok && heroResult.data ? heroResult.data : null;
    if (profile && typeof profile === "object") {
      serverHero = profile.hero || profile;
      if (profile.hero_name && serverHero && typeof serverHero === "object") {
        serverHero = runtime.applySparkName
          ? runtime.applySparkName(serverHero, profile.hero_name)
          : Object.assign({}, serverHero, {
              name: profile.hero_name,
              hero_name: profile.hero_name,
            });
      }
    }

    var spark = serverHero || localSpark;
    var sparkProfile = profile && profile.spark ? profile.spark : null;
    var hasLockedHero = !!(
      sparkProfile &&
      sparkProfile.lead_domain &&
      sparkProfile.lead_domain !== "Unknown"
    );
    var hasDraft = !serverHero && !!localSpark;

    setText("dash-greeting", timeGreeting() + ", " + label);
    setText(
      "dash-lede",
      hasLockedHero
        ? "Meridian City is live. Your Spark is on the roster — pick a mission and deploy."
        : hasDraft
          ? "You have a browser draft. Lock it in with the Origin Quiz to unlock Dispatch."
          : "Your command post is ready. Create your hero identity to answer the Dispatch."
    );

    setText("stat-tier", isPremium ? "Premium Roster" : "Free Hero");
    if (isPremium) {
      if (typeof charsRemaining === "number" && charsRemaining === 0) {
        setText("stat-tier-note", charCount + " of " + maxChars + " Spark slots used");
      } else {
        setText(
          "stat-tier-note",
          (typeof charsRemaining === "number" ? charsRemaining : maxChars - charCount) +
            " roster slot" +
            ((typeof charsRemaining === "number" ? charsRemaining : maxChars - charCount) === 1 ? "" : "s") +
            " remaining"
        );
      }
    } else {
      setText("stat-tier-note", canAccessOriginLab ? "1 Spark roster slot" : "Origin Lab locked");
    }

    var launchGenerator = document.getElementById("launch-generator");
    if (launchGenerator) {
      launchGenerator.hidden = !canAccessOriginLab;
    }

    if (hasLockedHero) {
      setText("stat-spark", runtime.sparkDisplayName(spark));
      setText("stat-spark-note", "Account-locked");
      setText("stat-dispatch", "Unlocked");
      setText("stat-dispatch-note", "Deploy when ready");
    } else if (hasDraft) {
      setText("stat-spark", runtime.sparkDisplayName(spark));
      setText("stat-spark-note", "Local draft");
      setText("stat-dispatch", "Locked");
      setText("stat-dispatch-note", "Lock hero first");
    } else {
      setText("stat-spark", "Unassigned");
      setText("stat-spark-note", "Take the quiz");
      setText("stat-dispatch", "Locked");
      setText("stat-dispatch-note", "Hero required");
    }

    var missions = [];

    if (!hasLockedHero && canAccessOriginLab) {
      missions.push(
        missionCard(
          "Priority",
          "Manifest Your Spark",
          "28 questions reveal your domains. Lock one official hero to your account.",
          "/spark-generator/",
          hasDraft ? "Lock Draft" : "Start Origin Quiz",
          false
        )
      );
    } else if (!hasLockedHero && !canAccessOriginLab) {
      missions.push(
        missionCard(
          "Locked",
          "Origin Lab Closed",
          "Free accounts get one Spark. Upgrade for a five-hero roster.",
          "/spark-dashboard/#origin-rules",
          "Upgrade Roster",
          false
        )
      );
    }

    missions.push(
      missionCard(
        hasLockedHero ? "Priority" : "Standby",
        "Answer The Dispatch",
        "Headlines become missions. Direct, stealth, protect, or strategy — your call.",
        "/meridian-dispatch/",
        hasLockedHero ? "Open Dispatch" : "Needs Hero",
        !hasLockedHero
      )
    );

    missions.push(
      missionCard(
        "Intel",
        "Survey Meridian Map",
        "Grid sectors A1–P15. Inspect districts and pressure before you deploy.",
        "/meridian-map/",
        "Open Map",
        !hasLockedHero
      )
    );

    missions.push(
      missionCard(
        "Wire",
        "Read Meridian News",
        "Advisories, district reports, and the city wire after sign-in.",
        "/news/",
        "Read News",
        false
      )
    );

    setHTML("mission-board-grid", missions.join(""));

    if (spark && hasLockedHero) {
      var identityActions =
        '<div class="comic-actions">' +
        (canAccessOriginLab
          ? '<a class="comic-button" href="/spark-generator/">Generator</a>'
          : "") +
        '<a class="comic-button red" href="/meridian-dispatch/">Deploy</a>' +
        '<a class="comic-button" href="/spark-inbox/">Inbox</a>' +
        "</div>";
      setHTML(
        "spark-identity-card",
        "<h2>Your Spark</h2>" +
          '<div class="hero-profile-grid">' +
          heroPortraitBlock(sparkProfile) +
          '<div><p class="spark-name">' +
          escapeHTML(runtime.sparkDisplayName(spark)) +
          "</p>" +
          "<p class=\"spark-domains\">Domains: " +
          escapeHTML(runtime.sparkDisplayDomains(spark)) +
          "</p>" +
          "<p class=\"spark-domains\">Notoriety: " +
          escapeHTML(String((profile && profile.notoriety) || 0)) +
          "</p>" +
          (!canAccessOriginLab
            ? '<p class="spark-source">Origin Lab locked — free accounts get one Spark. See Origin Rules to upgrade.</p>'
            : "") +
          sparkRenameBlock(
            runtime.sparkDisplayName(spark),
            profile && profile.rename_limit ? profile.rename_limit : null
          ) +
          identityActions +
          "</div></div>"
      );
      wireSparkRename(spark, true);
    } else if (spark && hasDraft && canAccessOriginLab) {
      setHTML(
        "spark-identity-card",
        "<h2>Draft Spark</h2>" +
          "<p class=\"spark-name\">" + escapeHTML(runtime.sparkDisplayName(spark)) + "</p>" +
          "<p class=\"spark-domains\">Domains: " + escapeHTML(runtime.sparkDisplayDomains(spark)) + "</p>" +
          sparkRenameBlock(
            runtime.sparkDisplayName(spark),
            profile && profile.rename_limit ? profile.rename_limit : null
          ) +
          '<p class="spark-source">Browser draft — sign in and save to unlock Dispatch.</p>' +
          '<div class="comic-actions">' +
            '<a class="comic-button primary" href="/spark-generator/">Lock Hero</a>' +
          "</div>"
      );
      wireSparkRename(spark, false);
    } else if (spark && hasDraft && !canAccessOriginLab) {
      setHTML(
        "spark-identity-card",
        "<h2>Draft Spark</h2>" +
          "<p class=\"spark-name\">" + escapeHTML(runtime.sparkDisplayName(spark)) + "</p>" +
          "<p class=\"spark-domains\">Domains: " + escapeHTML(runtime.sparkDisplayDomains(spark)) + "</p>" +
          '<p class="spark-source">Origin Lab is locked for this account. Your free Spark slot is already used on the roster.</p>' +
          '<div class="comic-actions">' +
            '<a class="comic-button primary" href="/spark-dashboard/#origin-rules">Upgrade Roster</a>' +
          "</div>"
      );
    } else {
      var noSparkActions = canAccessOriginLab
        ? '<a class="comic-button primary" href="/spark-generator/">Start Origin Quiz</a>'
        : '<a class="comic-button primary" href="/spark-dashboard/#origin-rules">Upgrade Roster</a>';
      setHTML(
        "spark-identity-card",
        "<h2>No Spark Yet</h2>" +
          "<p>" +
          (canAccessOriginLab
            ? "Free accounts get one official hero. The Origin Quiz is your entry point."
            : "Your free Spark slot is used. Upgrade for a five-hero roster.") +
          "</p>" +
          '<div class="comic-actions">' +
          noSparkActions +
            '<a class="comic-button" href="/spark-signup/">Create Account</a>' +
          "</div>"
      );
    }

    document.documentElement.setAttribute("data-spark-nav", "in");
    if (window.SparkAuthNav && typeof window.SparkAuthNav.refreshRoster === "function") {
      window.SparkAuthNav.refreshRoster();
    } else if (window.SparkAuthNav && typeof window.SparkAuthNav.refresh === "function") {
      window.SparkAuthNav.refresh(true);
    }

    wireOwnerPanelLink(sessionResult.data || roster);

    await wireInboxPreview();
    await wireLeaderboards(profile);
  }

  function wireOwnerPanelLink(sessionData) {
    var isOwner = !!(sessionData && sessionData.is_owner);
    var existing = document.getElementById("launch-owner-panel");
    if (!isOwner) {
      if (existing) existing.remove();
      return;
    }
    if (existing) return;

    var grid = document.querySelector(".launch-grid");
    if (!grid) return;

    var tile = document.createElement("a");
    tile.className = "launch-tile";
    tile.id = "launch-owner-panel";
    tile.href = "/spark-owner/";
    tile.innerHTML = "<strong>Owner Panel</strong><span>Account lookup &amp; roles</span>";
    grid.appendChild(tile);
  }

  window.SparkDashboard = { boot: bootDashboard };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootDashboard);
  } else {
    bootDashboard();
  }
})();
