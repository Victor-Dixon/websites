/* Planet Blue — localStorage persistence */
(function (global) {
  "use strict";

  var STORAGE_KEY = "planet-blue-save";
  var DATA = global.PLANET_BLUE_DATA;

  function createNewSave() {
    return {
      version: 1,
      profileCreated: false,
      character: Object.assign({}, DATA.DEFAULT_CHARACTER),
      missions: Object.assign({}, DATA.DEFAULT_MISSIONS)
    };
  }

  function loadSave() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      var parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== "object") return null;
      return parsed;
    } catch (e) {
      return null;
    }
  }

  function saveGame(data) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  }

  function hasSave() {
    var s = loadSave();
    return s && s.profileCreated;
  }

  function getOrCreateSave() {
    var s = loadSave();
    if (!s) {
      s = createNewSave();
      saveGame(s);
    }
    return s;
  }

  function resetSave() {
    var s = createNewSave();
    saveGame(s);
    return s;
  }

  function completeMission(missionId, rewards) {
    var s = getOrCreateSave();
    s.missions[missionId] = "completed";
    if (rewards) {
      s.character.xp += rewards.xp || 0;
      s.character.currency += rewards.currency || 0;
    }
    var mission = DATA.MISSIONS[missionId];
    if (mission && mission.requires) {
      /* unlock next missions that require this one */
      Object.keys(DATA.MISSIONS).forEach(function (id) {
        var m = DATA.MISSIONS[id];
        if (m.requires === missionId && s.missions[id] === "locked") {
          s.missions[id] = "unlocked";
        }
      });
    }
    saveGame(s);
    return s;
  }

  global.PLANET_BLUE_SAVE = {
    STORAGE_KEY: STORAGE_KEY,
    createNewSave: createNewSave,
    loadSave: loadSave,
    saveGame: saveGame,
    hasSave: hasSave,
    getOrCreateSave: getOrCreateSave,
    resetSave: resetSave,
    completeMission: completeMission
  };
})(typeof window !== "undefined" ? window : global);
