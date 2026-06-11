(function () {
  "use strict";

  var LS_DRAFT = "spark.missions.draft.v1";
  var MISSIONS_PATH = "/assets/data/missions.json";
  var TEMPLATE_PATH = "/missions/mission.template.json";

  var state = { missions: [] };

  function $(sel) { return document.querySelector(sel); }

  function readJSON(key, fallback) {
    try {
      var raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (e) {
      return fallback;
    }
  }

  function setStatus(msg, ok) {
    var el = $("#mission-admin-status");
    if (!el) return;
    el.textContent = msg || "";
    el.className = "city-admin-status" + (ok ? " ok" : msg ? " err" : "");
    el.hidden = !msg;
  }

  function renderEditor() {
    var ta = $("#mission-json-editor");
    if (ta) ta.value = JSON.stringify(state.missions, null, 2);
  }

  function validateMissions() {
    if (!Array.isArray(state.missions)) {
      throw new Error("missions.json must be a JSON array");
    }
    state.missions.forEach(function (m, i) {
      if (!m.id || !m.title) {
        throw new Error("Mission at index " + i + " missing id or title");
      }
    });
  }

  function saveDraft() {
    localStorage.setItem(LS_DRAFT, JSON.stringify(state.missions));
  }

  function exportJson() {
    validateMissions();
    var blob = new Blob([JSON.stringify(state.missions, null, 2)], { type: "application/json" });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "missions.json";
    a.click();
    URL.revokeObjectURL(a.href);
    setStatus("Exported — upload to assets/data/missions.json via SFTP", true);
  }

  function copyAiPrompt() {
    fetch("/docs/spark/MISSION_AI_PROMPT.md", { cache: "no-store" })
      .then(function (r) { return r.ok ? r.text() : ""; })
      .then(function (doc) {
        var snippet = doc.split("## Copy-paste prompt")[1] || doc;
        return navigator.clipboard.writeText(snippet.trim());
      })
      .then(function () { setStatus("Mission AI prompt copied from docs/spark/MISSION_AI_PROMPT.md", true); })
      .catch(function () {
        setStatus("See docs/spark/MISSION_AI_PROMPT.md for the full prompt", false);
      });
  }

  function load() {
    var draft = readJSON(LS_DRAFT, null);
    if (draft) {
      state.missions = draft;
      return Promise.resolve();
    }
    return fetch(MISSIONS_PATH, { cache: "no-store" })
      .then(function (r) { return r.ok ? r.json() : []; })
      .then(function (doc) { state.missions = Array.isArray(doc) ? doc : []; });
  }

  function wire() {
    $("#btn-apply-json").addEventListener("click", function () {
      try {
        state.missions = JSON.parse($("#mission-json-editor").value);
        validateMissions();
        saveDraft();
        setStatus("JSON applied to local draft.", true);
      } catch (e) {
        setStatus(e.message, false);
      }
    });

    $("#btn-export").addEventListener("click", function () {
      try {
        state.missions = JSON.parse($("#mission-json-editor").value);
        validateMissions();
        saveDraft();
        exportJson();
      } catch (e) {
        setStatus(e.message, false);
      }
    });

    $("#btn-copy-prompt").addEventListener("click", copyAiPrompt);
    $("#btn-reload").addEventListener("click", function () {
      localStorage.removeItem(LS_DRAFT);
      load().then(function () {
        renderEditor();
        setStatus("Reloaded from server.", true);
      });
    });

    $("#btn-add-template").addEventListener("click", function () {
      fetch(TEMPLATE_PATH, { cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (tpl) {
          state.missions.push(tpl);
          saveDraft();
          renderEditor();
          setStatus("Added template mission.", true);
        });
    });
  }

  function boot() {
    load().then(function () {
      renderEditor();
      wire();
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
