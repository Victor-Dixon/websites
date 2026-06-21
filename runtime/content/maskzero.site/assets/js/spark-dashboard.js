(function () {
  "use strict";

  function $(id) {
    return document.getElementById(id);
  }

  function setText(id, value) {
    var el = $(id);
    if (el) {
      el.textContent = value;
    }
  }

  function revealAuthPanels(loggedIn) {
    document.querySelectorAll("[data-spark-auth-only]").forEach(function (el) {
      if (loggedIn) {
        el.removeAttribute("hidden");
      } else {
        el.setAttribute("hidden", "hidden");
      }
    });
    document.querySelectorAll("[data-spark-guest-only]").forEach(function (el) {
      if (loggedIn) {
        el.setAttribute("hidden", "hidden");
      } else {
        el.removeAttribute("hidden");
      }
    });
  }

  function missionCard(title, priority, copy, href) {
    return (
      '<article class="mission-card">' +
      '<span class="mission-priority">' + priority + "</span>" +
      "<h3>" + title + "</h3>" +
      "<p>" + copy + "</p>" +
      '<a class="comic-button mission-btn" href="' + href + '">Open</a>' +
      "</article>"
    );
  }

  function renderMissions() {
    var grid = $("mission-board-grid");
    if (!grid) {
      return;
    }
    grid.innerHTML =
      missionCard("Origin Lab", "Start", "Generate or update the Spark profile that powers your account.", "/spark-generator/") +
      missionCard("Meridian Dispatch", "Active", "Pick a mission seed and carry your Spark into the city.", "/meridian-dispatch/") +
      missionCard("What-If Arena", "Optional", "Test matchups and battle conditions after your Spark is created.", "/spark-battle/");
  }

  function renderSparkCard(loggedIn, user) {
    var card = $("spark-identity-card");
    if (!card) {
      return;
    }
    var name = (user && (user.display_name || user.name || user.user_login)) || "Spark Hero";
    card.innerHTML =
      "<h2>Your Spark</h2>" +
      '<p class="spark-name"><strong>' + name + "</strong></p>" +
      '<p class="spark-domains">' +
      (loggedIn
        ? "Account session detected. Generate or reopen your Spark to refresh this dossier."
        : "Create an account or log in to lock one official Spark to this Command Post.") +
      "</p>" +
      '<div class="comic-actions">' +
      '<a class="comic-button primary" href="/spark-generator/">Generate Spark</a>' +
      '<a class="comic-button blue" href="/spark-account/">Account Rules</a>' +
      "</div>";
  }

  function applySession(loggedIn, user) {
    revealAuthPanels(loggedIn);
    setText("dash-greeting", loggedIn ? "Welcome Back" : "Command Post");
    setText(
      "dash-lede",
      loggedIn
        ? "Your MaskZero Command Post is online."
        : "Log in or create an account to unlock your hero identity, missions, and Meridian field ops."
    );
    setText("stat-tier", loggedIn ? "Free" : "Guest");
    setText("stat-tier-note", loggedIn ? "One official Spark slot active." : "Preview mode.");
    setText("stat-spark", loggedIn ? "Ready" : "Unlocked after login");
    setText("stat-spark-note", "Use the Origin Lab to generate your dossier.");
    setText("stat-dispatch", "Online");
    setText("stat-dispatch-note", "Mission board restored.");
    renderMissions();
    renderSparkCard(loggedIn, user);
    if (window.SparkAccountRuntime && typeof window.SparkAccountRuntime.announce === "function") {
      window.SparkAccountRuntime.announce(loggedIn, user || null);
    }
  }

  function boot() {
    var runtime = window.SparkAccountRuntime;
    if (!runtime || typeof runtime.session !== "function") {
      applySession(false, null);
      return;
    }
    runtime.session().then(function (result) {
      var data = (result && result.data) || {};
      applySession(!!data.logged_in, data.user || null);
    }).catch(function () {
      applySession(false, null);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
