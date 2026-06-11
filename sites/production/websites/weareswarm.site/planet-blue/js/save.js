/* Planet Blue — localStorage persistence (v2) */
(function (global) {
  "use strict";

  var STORAGE_KEY = "planet-blue-save";
  var SAVE_VERSION = 2;
  var DATA = global.PLANET_BLUE_DATA;
  var WORLD = global.PLANET_BLUE_WORLD;

  function createNewSave() {
    var save = {
      version: SAVE_VERSION,
      profileCreated: false,
      character: Object.assign({}, DATA.DEFAULT_CHARACTER),
      missions: Object.assign({}, DATA.DEFAULT_MISSIONS),
      world: null,
      morality: null,
      nemesis: null,
      quests: null
    };
    if (WORLD) {
      save = WORLD.ensureWorldSystems(save);
      save.world.zones = WORLD.initZones();
      save.world.lastUpdated = new Date().toISOString();
      save = WORLD.initQuestLog(save);
    }
    return save;
  }

  function ensureMissionState(save) {
    if (!save.missions || typeof save.missions !== "object") {
      save.missions = Object.assign({}, DATA.DEFAULT_MISSIONS);
    }
    Object.keys(DATA.DEFAULT_MISSIONS).forEach(function (id) {
      if (!save.missions[id]) {
        save.missions[id] = DATA.DEFAULT_MISSIONS[id];
      }
    });
    return save;
  }

  function normalizeMissionStatus(status) {
    if (status === "complete") return "completed";
    return status;
  }

  function syncMissionUnlocks(save) {
    ensureMissionState(save);
    Object.keys(save.missions).forEach(function (id) {
      save.missions[id] = normalizeMissionStatus(save.missions[id]);
    });
    Object.keys(DATA.MISSIONS).forEach(function (id) {
      var m = DATA.MISSIONS[id];
      if (!m.requires) return;
      var reqStatus = normalizeMissionStatus(save.missions[m.requires]);
      if (reqStatus === "completed" && save.missions[id] === "locked") {
        save.missions[id] = "unlocked";
      }
    });
    return save;
  }

  function persistUnlockRepairs(synced) {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return synced;
      var parsed = JSON.parse(raw);
      if (!parsed.missions || JSON.stringify(parsed.missions) === JSON.stringify(synced.missions)) {
        return synced;
      }
      saveGame(synced);
    } catch (e) {
      /* ignore parse errors */
    }
    return synced;
  }

  function migrateSave(parsed) {
    if (!parsed || typeof parsed !== "object") return createNewSave();
    if (parsed.version >= SAVE_VERSION) {
      if (WORLD) parsed = WORLD.ensureWorldSystems(parsed);
      return syncMissionUnlocks(parsed);
    }
    parsed.version = SAVE_VERSION;
    if (WORLD) {
      parsed = WORLD.ensureWorldSystems(parsed);
      if (!parsed.world.lastUpdated) {
        parsed.world.zones = WORLD.initZones();
        parsed.world.lastUpdated = new Date().toISOString();
      }
      parsed = WORLD.initQuestLog(parsed);
    }
    return syncMissionUnlocks(parsed);
  }

  function loadSave() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      var parsed = JSON.parse(raw);
      return migrateSave(parsed);
    } catch (e) {
      return null;
    }
  }

  function saveGame(data) {
    ensureMissionState(data);
    syncMissionUnlocks(data);
    if (WORLD) WORLD.syncQuestStatus(data);
    data.version = SAVE_VERSION;
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
      return s;
    }
    return persistUnlockRepairs(s);
  }

  function resetSave() {
    var s = createNewSave();
    saveGame(s);
    return s;
  }

  function completeMission(missionId, rewards) {
    var s = getOrCreateSave();
    ensureMissionState(s);
    s.missions[missionId] = "completed";
    if (rewards) {
      s.character.xp += rewards.xp || 0;
      s.character.currency += rewards.currency || 0;
    }
    syncMissionUnlocks(s);
    if (WORLD) {
      WORLD.applyMissionOutcome(s, missionId, "win");
      WORLD.syncQuestStatus(s);
    }
    saveGame(s);
    return s;
  }

  function recordDefeat(missionId) {
    var s = getOrCreateSave();
    if (WORLD) WORLD.applyMissionOutcome(s, missionId, "lose");
    saveGame(s);
    return s;
  }

  global.PLANET_BLUE_SAVE = {
    STORAGE_KEY: STORAGE_KEY,
    SAVE_VERSION: SAVE_VERSION,
    createNewSave: createNewSave,
    loadSave: loadSave,
    saveGame: saveGame,
    hasSave: hasSave,
    getOrCreateSave: getOrCreateSave,
    resetSave: resetSave,
    completeMission: completeMission,
    recordDefeat: recordDefeat,
    migrateSave: migrateSave,
    syncMissionUnlocks: syncMissionUnlocks
  };
})(typeof window !== "undefined" ? window : global);
