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

  function sparkRenameBlock(currentName) {
    return (
      '<div class="spark-rename">' +
        '<label for="spark-rename-input">Rename your Spark</label>' +
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
          status.textContent = "Spark renamed to " + next + ".";
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

  async function bootDashboard() {
    var sessionResult = await runtime.session();
    var loggedIn = !!(sessionResult.ok && sessionResult.data && sessionResult.data.logged_in);

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

    setText("stat-tier", "Free Hero");
    setText("stat-tier-note", "1 Spark roster slot");

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

    if (!hasLockedHero) {
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
      setHTML(
        "spark-identity-card",
        "<h2>Your Spark</h2>" +
          "<p class=\"spark-name\">" + escapeHTML(runtime.sparkDisplayName(spark)) + "</p>" +
          "<p class=\"spark-domains\">Domains: " + escapeHTML(runtime.sparkDisplayDomains(spark)) + "</p>" +
          sparkRenameBlock(runtime.sparkDisplayName(spark)) +
          '<div class="comic-actions">' +
            '<a class="comic-button" href="/spark-generator/">Generator</a>' +
            '<a class="comic-button red" href="/meridian-dispatch/">Deploy</a>' +
          "</div>"
      );
      wireSparkRename(spark, true);
    } else if (spark && hasDraft) {
      setHTML(
        "spark-identity-card",
        "<h2>Draft Spark</h2>" +
          "<p class=\"spark-name\">" + escapeHTML(runtime.sparkDisplayName(spark)) + "</p>" +
          "<p class=\"spark-domains\">Domains: " + escapeHTML(runtime.sparkDisplayDomains(spark)) + "</p>" +
          sparkRenameBlock(runtime.sparkDisplayName(spark)) +
          '<p class="spark-source">Browser draft — sign in and save to unlock Dispatch.</p>' +
          '<div class="comic-actions">' +
            '<a class="comic-button primary" href="/spark-generator/">Lock Hero</a>' +
          "</div>"
      );
      wireSparkRename(spark, false);
    } else {
      setHTML(
        "spark-identity-card",
        "<h2>No Spark Yet</h2>" +
          "<p>Free accounts get one official hero. The Origin Quiz is your entry point.</p>" +
          '<div class="comic-actions">' +
            '<a class="comic-button primary" href="/spark-generator/">Start Origin Quiz</a>' +
            '<a class="comic-button" href="/spark-signup/">Create Account</a>' +
          "</div>"
      );
    }

    document.documentElement.setAttribute("data-spark-nav", "in");
    if (window.SparkAuthNav && typeof window.SparkAuthNav.refresh === "function") {
      window.SparkAuthNav.refresh(true);
    }
  }

  window.SparkDashboard = { boot: bootDashboard };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootDashboard);
  } else {
    bootDashboard();
  }
})();
