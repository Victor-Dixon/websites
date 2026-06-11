(function () {
  "use strict";

  var runtime = window.SparkAccountRuntime;
  var state = {
    session: null,
    caps: [],
    selectedUserId: null,
    activeSection: "lookup",
  };

  var SECTIONS = [
    { id: "lookup", label: "Account Lookup", cap: "lookup_accounts" },
    { id: "detail", label: "Account Detail", cap: "view_account_detail" },
    { id: "roles", label: "Role Manager", cap: "grant_roles" },
    { id: "character", label: "Character Tools", cap: "reset_own_character" },
    { id: "lore", label: "Map/Lore Admin", cap: "manage_map_lore" },
    { id: "missions", label: "Mission Admin", cap: "manage_missions" },
    { id: "events", label: "World Events", cap: "manage_world_events" },
    { id: "broadcast", label: "MaskZero Broadcast", cap: "send_maskzero_broadcast" },
    { id: "audit", label: "Admin Action Log", cap: "view_admin_log" },
    { id: "debug", label: "System Debug Status", cap: "view_debug_status" },
  ];

  function escapeHTML(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[ch];
    });
  }

  function nonceHeaders() {
    var h = { Accept: "application/json", "Content-Type": "application/json" };
    if (window.SPARK_ACCOUNT && window.SPARK_ACCOUNT.restNonce) {
      h["X-WP-Nonce"] = window.SPARK_ACCOUNT.restNonce;
    }
    return h;
  }

  function apiFetch(path, options) {
    options = options || {};
    options.credentials = "same-origin";
    options.cache = "no-store";
    options.headers = Object.assign({}, nonceHeaders(), options.headers || {});
    return fetch("/wp-json/spark/v1" + path, options).then(async function (res) {
      var data = {};
      try {
        data = await res.json();
      } catch (e) {}
      return { ok: res.ok, status: res.status, data: data };
    });
  }

  function hasCap(cap) {
    return state.caps.indexOf(cap) !== -1;
  }

  function visibleSections() {
    return SECTIONS.filter(function (s) {
      if (s.id === "roles" && !hasCap("grant_roles") && !hasCap("revoke_roles")) {
        return false;
      }
      if (s.id === "character" && !hasCap("reset_any_character") && !hasCap("reset_own_character")) {
        return false;
      }
      return hasCap(s.cap);
    });
  }

  function setStatus(el, message, ok) {
    if (!el) return;
    el.textContent = message || "";
    el.className = "owner-status" + (ok ? " ok" : message ? " err" : "");
    el.hidden = !message;
  }

  function renderSectionNav() {
    var nav = document.getElementById("owner-section-nav");
    var sections = visibleSections();
    if (!nav) return;

    if (!sections.some(function (s) {
      return s.id === state.activeSection;
    })) {
      state.activeSection = sections[0] ? sections[0].id : "lookup";
    }

    nav.innerHTML = sections
      .map(function (s) {
        return (
          '<button type="button" class="owner-tab' +
          (s.id === state.activeSection ? " active" : "") +
          '" data-section="' +
          escapeHTML(s.id) +
          '">' +
          escapeHTML(s.label) +
          "</button>"
        );
      })
      .join("");

    nav.querySelectorAll(".owner-tab").forEach(function (btn) {
      btn.addEventListener("click", function () {
        state.activeSection = btn.getAttribute("data-section") || "lookup";
        renderAll();
      });
    });
  }

  function sectionLookup() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "lookup" ? " active" : "") +
      '" data-id="lookup">' +
      "<h2>Account Lookup</h2>" +
      '<p>Search by email, username, display name, hero name, spark name, or user ID.</p>' +
      '<div class="owner-field-row">' +
      '<input type="search" id="owner-search-q" placeholder="Search accounts…" autocomplete="off">' +
      '<button type="button" class="owner-btn primary" id="owner-search-btn">Search</button>' +
      "</div>" +
      '<div class="owner-results" id="owner-search-results"><p>Enter a query to search game accounts.</p></div>' +
      "</section>"
    );
  }

  function sectionDetail() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "detail" ? " active" : "") +
      '" data-id="detail">' +
      "<h2>Account Detail</h2>" +
      '<div class="owner-field-row">' +
      '<input type="number" id="owner-detail-id" placeholder="User ID" min="1">' +
      '<button type="button" class="owner-btn primary" id="owner-detail-btn">Load Account</button>' +
      "</div>" +
      '<div id="owner-detail-body"><p>Select an account from lookup or enter a user ID.</p></div>' +
      "</section>"
    );
  }

  function sectionRoles() {
    if (!hasCap("grant_roles") && !hasCap("revoke_roles")) return "";
    return (
      '<section class="owner-section' +
      (state.activeSection === "roles" ? " active" : "") +
      '" data-id="roles">' +
      "<h2>Role Manager</h2>" +
      '<p>Grant or revoke game roles on existing accounts only.</p>' +
      '<div class="owner-field-row">' +
      '<input type="number" id="role-target-id" placeholder="Target user ID" min="1">' +
      '<select id="role-grant-select">' +
      '<option value="admin">Grant Admin</option>' +
      '<option value="dev">Grant Dev</option>' +
      '<option value="moderator">Grant Moderator</option>' +
      '<option value="owner">Grant Owner</option>' +
      "</select>" +
      '<input type="text" id="role-grant-confirm" placeholder="TRANSFER OWNERSHIP (for owner grant)">' +
      '<button type="button" class="owner-btn primary" id="role-grant-btn">Grant Role</button>' +
      "</div>" +
      '<p class="owner-confirm-hint">Granting owner requires confirmation: TRANSFER OWNERSHIP</p>' +
      '<div class="owner-field-row">' +
      '<input type="number" id="role-revoke-id" placeholder="Target user ID" min="1">' +
      '<input type="text" id="role-revoke-confirm" placeholder="REVOKE OWNER (if revoking owner)">' +
      '<button type="button" class="owner-btn danger" id="role-revoke-btn">Revoke Role</button>' +
      "</div>" +
      '<p class="owner-confirm-hint">Revoking owner requires confirmation: REVOKE OWNER. Last owner cannot be removed.</p>' +
      '<p class="owner-status" id="role-status" hidden></p>' +
      "</section>"
    );
  }

  function sectionCharacter() {
    if (!hasCap("reset_any_character") && !hasCap("reset_own_character")) return "";
    return (
      '<section class="owner-section' +
      (state.activeSection === "character" ? " active" : "") +
      '" data-id="character">' +
      "<h2>Character Tools</h2>" +
      '<div class="owner-field-row">' +
      '<input type="number" id="reset-target-id" placeholder="User ID (blank = self)" min="1">' +
      '<button type="button" class="owner-btn danger" id="reset-character-btn">Reset Character</button>' +
      "</div>" +
      '<p class="owner-confirm-hint">Clears dossiers, hero profile, feats, and record meta.</p>' +
      '<p class="owner-status" id="reset-status" hidden></p>' +
      "</section>"
    );
  }

  function sectionLore() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "lore" ? " active" : "") +
      '" data-id="lore">' +
      "<h2>Map/Lore Admin</h2>" +
      "<p>City map quadrants, district lore, artwork. SSOT: <code>assets/data/spark/city_map.json</code></p>" +
      '<div class="owner-actions">' +
      '<a class="owner-btn primary" href="/spark-city-admin/">City Map Admin</a>' +
      '<a class="owner-btn" href="/meridian-map/">Player Map (District View)</a>' +
      '<a class="owner-btn" href="/meridian-city/">Meridian City Hub</a>' +
      "</div>" +
      "</section>"
    );
  }

  function sectionMissions() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "missions" ? " active" : "") +
      '" data-id="missions">' +
      "<h2>Mission Admin</h2>" +
      "<p>SSOT: <code>assets/data/missions.json</code> · Schema: <code>docs/spark/MISSION_SCHEMA.md</code></p>" +
      '<div class="owner-actions">' +
      '<a class="owner-btn primary" href="/spark-mission-admin/">Mission JSON Editor</a>' +
      '<a class="owner-btn" href="/spark-city-admin/">Assign Missions to Districts</a>' +
      '<a class="owner-btn" href="/meridian-dispatch/">Open Dispatch</a>' +
      '<a class="owner-btn" href="/meridian-map/">Meridian Map</a>' +
      "</div>" +
      '<p class="owner-confirm-hint">Use Mission Admin to view JSON, validate missions, copy the AI prompt, and follow deploy steps.</p>' +
      "</section>"
    );
  }

  function sectionEvents() {
    if (!hasCap("manage_world_events")) return "";
    return (
      '<section class="owner-section' +
      (state.activeSection === "events" ? " active" : "") +
      '" data-id="events">' +
      "<h2>World Events</h2>" +
      '<div class="owner-field-row"><input type="text" id="event-headline" placeholder="Event headline"></div>' +
      '<div class="owner-field-row"><textarea id="event-detail" placeholder="Event detail"></textarea></div>' +
      '<button type="button" class="owner-btn primary" id="event-submit-btn">Record World Event</button>' +
      '<p class="owner-status" id="event-status" hidden></p>' +
      "</section>"
    );
  }

  function sectionBroadcast() {
    if (!hasCap("send_maskzero_broadcast")) return "";
    return (
      '<section class="owner-section' +
      (state.activeSection === "broadcast" ? " active" : "") +
      '" data-id="broadcast">' +
      "<h2>MaskZero Broadcast</h2>" +
      '<div class="owner-field-row"><input type="text" id="broadcast-title" placeholder="Title" value="MaskZero Broadcast"></div>' +
      '<div class="owner-field-row"><textarea id="broadcast-body" placeholder="Message body"></textarea></div>' +
      '<div class="owner-field-row"><input type="number" id="broadcast-target" placeholder="User ID (blank = all)" min="1"></div>' +
      '<button type="button" class="owner-btn primary" id="broadcast-submit-btn">Send Broadcast</button>' +
      '<p class="owner-status" id="broadcast-status" hidden></p>' +
      "</section>"
    );
  }

  function sectionAudit() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "audit" ? " active" : "") +
      '" data-id="audit">' +
      "<h2>Admin Action Log</h2>" +
      '<div class="owner-field-row">' +
      '<input type="number" id="audit-target-id" placeholder="Filter by target user ID (optional)" min="1">' +
      '<button type="button" class="owner-btn primary" id="audit-load-btn">Load Log</button>' +
      "</div>" +
      '<div id="audit-log-body"><p>Loading audit log…</p></div>' +
      "</section>"
    );
  }

  function sectionDebug() {
    return (
      '<section class="owner-section' +
      (state.activeSection === "debug" ? " active" : "") +
      '" data-id="debug">' +
      "<h2>System Debug Status</h2>" +
      '<div id="debug-body"><p>Loading debug status…</p></div>' +
      "</section>"
    );
  }

  function renderSections() {
    var html =
      sectionLookup() +
      sectionDetail() +
      sectionRoles() +
      sectionCharacter() +
      sectionLore() +
      sectionMissions() +
      sectionEvents() +
      sectionBroadcast() +
      sectionAudit() +
      sectionDebug();
    var container = document.getElementById("owner-sections");
    if (container) container.innerHTML = html;
  }

  function renderDetailBody(data) {
    var acct = data.account || {};
    var status = data.character_status || {};
    var hero = data.hero_profile || {};
    var history = data.admin_history || [];

    var historyHtml = history.length
      ? history
          .map(function (entry) {
            return (
              "<tr><td>" +
              escapeHTML(entry.created_at || "") +
              "</td><td>" +
              escapeHTML(entry.action || "") +
              "</td><td>" +
              escapeHTML(String(entry.actor_user_id || "")) +
              "</td><td>" +
              escapeHTML(JSON.stringify(entry.metadata || {})) +
              "</td></tr>"
            );
          })
          .join("")
      : "<tr><td colspan=\"4\">No admin history for this account.</td></tr>";

    var actions = "";
    if (hasCap("grant_roles")) {
      actions +=
        '<button type="button" class="owner-btn primary" data-grant="admin" data-uid="' +
        escapeHTML(String(acct.user_id)) +
        '">Grant Admin</button>' +
        '<button type="button" class="owner-btn primary" data-grant="dev" data-uid="' +
        escapeHTML(String(acct.user_id)) +
        '">Grant Dev</button>' +
        '<button type="button" class="owner-btn primary" data-grant="moderator" data-uid="' +
        escapeHTML(String(acct.user_id)) +
        '">Grant Moderator</button>';
    }
    if (hasCap("revoke_roles")) {
      actions +=
        '<button type="button" class="owner-btn danger" data-revoke="1" data-uid="' +
        escapeHTML(String(acct.user_id)) +
        '">Revoke Role</button>';
    }
    if (hasCap("reset_any_character")) {
      actions +=
        '<button type="button" class="owner-btn danger" data-reset="1" data-uid="' +
        escapeHTML(String(acct.user_id)) +
        '">Reset Character</button>';
    }

    return (
      '<div class="owner-detail-grid">' +
      '<div class="owner-detail-card"><strong>User ID</strong>' +
      escapeHTML(String(acct.user_id)) +
      "</div>" +
      '<div class="owner-detail-card"><strong>Email</strong>' +
      escapeHTML(acct.email || "") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Username</strong>' +
      escapeHTML(acct.username || "") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Display Name</strong>' +
      escapeHTML(acct.display_name || "") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Hero Name</strong>' +
      escapeHTML(acct.hero_name || "") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Game Role</strong>' +
      escapeHTML(acct.game_role || "player") +
      (acct.is_root_owner ? " (root)" : "") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Plan</strong>' +
      escapeHTML(data.plan || "free") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Character</strong>' +
      (status.has_character ? "Active (" + status.character_count + ")" : "None") +
      "</div>" +
      '<div class="owner-detail-card"><strong>Inbox</strong>' +
      escapeHTML(String(data.inbox_count || 0)) +
      " messages</div>" +
      '<div class="owner-detail-card"><strong>Missions</strong>' +
      escapeHTML(String(data.mission_submission_count || 0)) +
      " submissions</div>" +
      '<div class="owner-detail-card"><strong>Notoriety</strong>' +
      escapeHTML(String(hero.notoriety || 0)) +
      "</div>" +
      '<div class="owner-detail-card"><strong>Created</strong>' +
      escapeHTML(acct.created_date || "") +
      "</div>" +
      "</div>" +
      '<div class="owner-actions">' +
      actions +
      '<a class="owner-btn" href="/spark-dashboard/">View Dashboard (readonly)</a>' +
      "</div>" +
      "<h3>Admin History</h3>" +
      '<table class="owner-log-table"><thead><tr><th>When</th><th>Action</th><th>Actor</th><th>Meta</th></tr></thead><tbody>' +
      historyHtml +
      "</tbody></table>"
    );
  }

  async function loadAccountDetail(userId) {
    var body = document.getElementById("owner-detail-body");
    if (!body) return;
    body.innerHTML = "<p>Loading account…</p>";
    var result = await apiFetch("/owner/account/" + encodeURIComponent(userId));
    if (!result.ok) {
      body.innerHTML =
        "<p class=\"owner-status err\">" +
        escapeHTML((result.data && result.data.message) || "Could not load account.") +
        "</p>";
      return;
    }
    state.selectedUserId = userId;
    body.innerHTML = renderDetailBody(result.data);
    wireDetailActions(body);
  }

  function wireDetailActions(container) {
    container.querySelectorAll("[data-grant]").forEach(function (btn) {
      btn.addEventListener("click", async function () {
        var uid = parseInt(btn.getAttribute("data-uid"), 10);
        var role = btn.getAttribute("data-grant");
        var confirmText = role === "owner" ? prompt("Type TRANSFER OWNERSHIP to grant owner:") : "";
        var result = await apiFetch("/owner/role", {
          method: "POST",
          body: JSON.stringify({
            target_user_id: uid,
            role: role,
            confirmation: confirmText || "",
          }),
        });
        alert(
          result.ok
            ? "Role granted: " + role
            : (result.data && result.data.message) || "Grant failed"
        );
        if (result.ok) loadAccountDetail(uid);
      });
    });

    container.querySelectorAll("[data-revoke]").forEach(function (btn) {
      btn.addEventListener("click", async function () {
        var uid = parseInt(btn.getAttribute("data-uid"), 10);
        var confirmText = prompt("Type REVOKE OWNER if revoking an owner role:") || "";
        var result = await apiFetch("/owner/revoke", {
          method: "POST",
          body: JSON.stringify({
            target_user_id: uid,
            confirmation: confirmText,
          }),
        });
        alert(
          result.ok
            ? "Role revoked."
            : (result.data && result.data.message) || "Revoke failed"
        );
        if (result.ok) loadAccountDetail(uid);
      });
    });

    container.querySelectorAll("[data-reset]").forEach(function (btn) {
      btn.addEventListener("click", async function () {
        if (!window.confirm("Reset character data for user " + btn.getAttribute("data-uid") + "?")) {
          return;
        }
        var uid = parseInt(btn.getAttribute("data-uid"), 10);
        var result = await apiFetch("/owner/reset-character", {
          method: "POST",
          body: JSON.stringify({ target_user_id: uid }),
        });
        alert(
          result.ok
            ? "Character reset."
            : (result.data && result.data.message) || "Reset failed"
        );
        if (result.ok) loadAccountDetail(uid);
      });
    });
  }

  async function runSearch() {
    var input = document.getElementById("owner-search-q");
    var results = document.getElementById("owner-search-results");
    if (!input || !results) return;

    var q = String(input.value || "").trim();
    if (!q) {
      results.innerHTML = "<p>Enter a query.</p>";
      return;
    }

    results.innerHTML = "<p>Searching…</p>";
    var result = await apiFetch("/owner/search?q=" + encodeURIComponent(q));
    if (!result.ok) {
      results.innerHTML =
        "<p class=\"owner-status err\">" +
        escapeHTML((result.data && result.data.message) || "Search failed.") +
        "</p>";
      return;
    }

    var rows = (result.data && result.data.results) || [];
    if (!rows.length) {
      results.innerHTML = "<p>No accounts matched.</p>";
      return;
    }

    results.innerHTML = rows
      .map(function (row) {
        return (
          '<div class="owner-result-row">' +
          "<div><strong>#" +
          escapeHTML(String(row.user_id)) +
          "</strong> " +
          escapeHTML(row.hero_name || row.display_name || row.username) +
          " · " +
          escapeHTML(row.email || "") +
          " · <em>" +
          escapeHTML(row.game_role || "player") +
          "</em></div>" +
          '<button type="button" class="owner-btn" data-view-id="' +
          escapeHTML(String(row.user_id)) +
          '">View</button>' +
          "</div>"
        );
      })
      .join("");

    results.querySelectorAll("[data-view-id]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var uid = parseInt(btn.getAttribute("data-view-id"), 10);
        state.activeSection = "detail";
        renderAll();
        var detailInput = document.getElementById("owner-detail-id");
        if (detailInput) detailInput.value = String(uid);
        loadAccountDetail(uid);
      });
    });
  }

  async function loadAuditLog() {
    var body = document.getElementById("audit-log-body");
    if (!body || !hasCap("view_admin_log")) return;

    var targetInput = document.getElementById("audit-target-id");
    var target = targetInput ? parseInt(targetInput.value, 10) : 0;
    var path = "/owner/audit-log?limit=50";
    if (target > 0) path += "&target_user_id=" + encodeURIComponent(target);

    body.innerHTML = "<p>Loading…</p>";
    var result = await apiFetch(path);
    if (!result.ok) {
      body.innerHTML = "<p class=\"owner-status err\">Could not load audit log.</p>";
      return;
    }

    var entries = (result.data && result.data.entries) || [];
    if (!entries.length) {
      body.innerHTML = "<p>No audit entries.</p>";
      return;
    }

    body.innerHTML =
      '<table class="owner-log-table"><thead><tr><th>When</th><th>Action</th><th>Actor</th><th>Target</th><th>Meta</th></tr></thead><tbody>' +
      entries
        .map(function (e) {
          return (
            "<tr><td>" +
            escapeHTML(e.created_at || "") +
            "</td><td>" +
            escapeHTML(e.action || "") +
            "</td><td>" +
            escapeHTML(String(e.actor_user_id || "")) +
            "</td><td>" +
            escapeHTML(String(e.target_user_id != null ? e.target_user_id : "—")) +
            "</td><td>" +
            escapeHTML(JSON.stringify(e.metadata || {})) +
            "</td></tr>"
          );
        })
        .join("") +
      "</tbody></table>";
  }

  async function loadDebug() {
    var body = document.getElementById("debug-body");
    if (!body || !hasCap("view_debug_status")) return;
    body.innerHTML = "<p>Loading…</p>";
    var result = await apiFetch("/owner/debug");
    if (!result.ok) {
      body.innerHTML = "<p class=\"owner-status err\">Debug status unavailable.</p>";
      return;
    }
    body.innerHTML =
      "<pre style=\"white-space:pre-wrap;font-family:monospace;font-size:0.85rem\">" +
      escapeHTML(JSON.stringify(result.data, null, 2)) +
      "</pre>";
  }

  function wireInteractions() {
    var searchBtn = document.getElementById("owner-search-btn");
    var searchInput = document.getElementById("owner-search-q");
    if (searchBtn) searchBtn.addEventListener("click", runSearch);
    if (searchInput) {
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") runSearch();
      });
    }

    var detailBtn = document.getElementById("owner-detail-btn");
    if (detailBtn) {
      detailBtn.addEventListener("click", function () {
        var input = document.getElementById("owner-detail-id");
        var uid = input ? parseInt(input.value, 10) : 0;
        if (uid > 0) loadAccountDetail(uid);
      });
    }

    var grantBtn = document.getElementById("role-grant-btn");
    if (grantBtn) {
      grantBtn.addEventListener("click", async function () {
        var status = document.getElementById("role-status");
        var uid = parseInt((document.getElementById("role-target-id") || {}).value, 10);
        var role = (document.getElementById("role-grant-select") || {}).value;
        var confirmText = (document.getElementById("role-grant-confirm") || {}).value || "";
        var result = await apiFetch("/owner/role", {
          method: "POST",
          body: JSON.stringify({
            target_user_id: uid,
            role: role,
            confirmation: confirmText,
          }),
        });
        setStatus(
          status,
          result.ok
            ? "Granted " + role + " to user " + uid
            : (result.data && result.data.message) || "Grant failed",
          result.ok
        );
      });
    }

    var revokeBtn = document.getElementById("role-revoke-btn");
    if (revokeBtn) {
      revokeBtn.addEventListener("click", async function () {
        var status = document.getElementById("role-status");
        var uid = parseInt((document.getElementById("role-revoke-id") || {}).value, 10);
        var confirmText = (document.getElementById("role-revoke-confirm") || {}).value || "";
        var result = await apiFetch("/owner/revoke", {
          method: "POST",
          body: JSON.stringify({
            target_user_id: uid,
            confirmation: confirmText,
          }),
        });
        setStatus(
          status,
          result.ok
            ? "Revoked role for user " + uid
            : (result.data && result.data.message) || "Revoke failed",
          result.ok
        );
      });
    }

    var resetBtn = document.getElementById("reset-character-btn");
    if (resetBtn) {
      resetBtn.addEventListener("click", async function () {
        var status = document.getElementById("reset-status");
        var input = document.getElementById("reset-target-id");
        var uid = input && input.value ? parseInt(input.value, 10) : state.session.user_id;
        if (!window.confirm("Reset character for user " + uid + "?")) return;
        var result = await apiFetch("/owner/reset-character", {
          method: "POST",
          body: JSON.stringify({ target_user_id: uid }),
        });
        setStatus(
          status,
          result.ok ? "Character reset." : (result.data && result.data.message) || "Reset failed",
          result.ok
        );
      });
    }

    var eventBtn = document.getElementById("event-submit-btn");
    if (eventBtn) {
      eventBtn.addEventListener("click", async function () {
        var status = document.getElementById("event-status");
        var headline = (document.getElementById("event-headline") || {}).value || "";
        var detail = (document.getElementById("event-detail") || {}).value || "";
        var result = await apiFetch("/owner/world-event", {
          method: "POST",
          body: JSON.stringify({ headline: headline, detail: detail }),
        });
        setStatus(
          status,
          result.ok ? "World event recorded." : (result.data && result.data.message) || "Failed",
          result.ok
        );
      });
    }

    var broadcastBtn = document.getElementById("broadcast-submit-btn");
    if (broadcastBtn) {
      broadcastBtn.addEventListener("click", async function () {
        var status = document.getElementById("broadcast-status");
        var title = (document.getElementById("broadcast-title") || {}).value || "";
        var body = (document.getElementById("broadcast-body") || {}).value || "";
        var targetVal = (document.getElementById("broadcast-target") || {}).value;
        var payload = { title: title, body: body };
        if (targetVal) payload.target_user_id = parseInt(targetVal, 10);
        var result = await apiFetch("/owner/broadcast", {
          method: "POST",
          body: JSON.stringify(payload),
        });
        setStatus(
          status,
          result.ok
            ? "Broadcast sent to " + ((result.data && result.data.sent) || 0) + " account(s)."
            : (result.data && result.data.message) || "Broadcast failed",
          result.ok
        );
      });
    }

    var auditBtn = document.getElementById("audit-load-btn");
    if (auditBtn) auditBtn.addEventListener("click", loadAuditLog);

    if (state.activeSection === "audit" && hasCap("view_admin_log")) {
      loadAuditLog();
    }
    if (state.activeSection === "debug" && hasCap("view_debug_status")) {
      loadDebug();
    }
  }

  function renderAll() {
    renderSectionNav();
    renderSections();
    wireInteractions();
  }

  async function boot() {
    var denied = document.getElementById("owner-denied");
    var app = document.getElementById("owner-app");
    var lede = document.getElementById("owner-lede");
    var pill = document.getElementById("owner-role-pill");

    if (runtime && typeof runtime.session === "function") {
      await runtime.session();
    }

    var result = await apiFetch("/owner/session");
    if (!result.ok || !result.data || !result.data.logged_in) {
      if (denied) denied.hidden = false;
      if (app) app.hidden = true;
      if (lede) lede.textContent = "Sign in required.";
      window.location.href = "/spark-login/?redirect_to=" + encodeURIComponent("/spark-owner/");
      return;
    }

    state.session = result.data;
    state.caps = result.data.capabilities || [];

    if (!result.data.can_access_admin_panel && !state.caps.length) {
      var sessionData = window.SPARK_ACCOUNT || {};
      if (!sessionData.can_access_admin_panel) {
        if (denied) denied.hidden = false;
        if (app) app.hidden = true;
        if (lede) lede.textContent = "Elevated game role required.";
        return;
      }
      state.caps = sessionData.capabilities || [];
    }

    if (denied) denied.hidden = true;
    if (app) app.hidden = false;

    if (lede) {
      lede.textContent =
        "Signed in as " +
        (result.data.display_name || result.data.email || "operator") +
        ". Capabilities gated by game role.";
    }
    if (pill) {
      pill.hidden = false;
      pill.textContent =
        (result.data.is_root_owner ? "Root Owner · " : "") +
        (result.data.game_role || "player").toUpperCase();
    }

    document.documentElement.setAttribute("data-spark-nav", "in");
    if (window.SparkAuthNav && typeof window.SparkAuthNav.refresh === "function") {
      window.SparkAuthNav.refresh(true);
    }

    renderAll();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
