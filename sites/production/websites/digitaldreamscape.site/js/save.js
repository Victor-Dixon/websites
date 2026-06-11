/* Digital Dreamscape — local save */
(function (global) {
  "use strict";

  var STORAGE_KEY = "digital_dreamscape_save_v1";
  var DATA = global.DD_WORLD_DATA;

  function defaultSave() {
    return {
      version: 1,
      player: {
        x: 10,
        y: 10,
        name: "Dream Explorer",
        level: 1,
        xp: 0,
        currency: 0
      },
      worldSeed: DATA ? DATA.DEFAULT_SEED : "digital-dreamscape-v1",
      visitedInteractions: []
    };
  }

  function loadSave() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      return JSON.parse(raw);
    } catch (e) {
      return null;
    }
  }

  function saveGame(data) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  }

  function getOrCreateSave() {
    var save = loadSave();
    if (!save || !save.player) {
      save = defaultSave();
      saveGame(save);
    }
    if (!save.visitedInteractions) save.visitedInteractions = [];
    if (!save.worldSeed) save.worldSeed = DATA.DEFAULT_SEED;
    return save;
  }

  function markVisited(save, interactionId) {
    if (save.visitedInteractions.indexOf(interactionId) === -1) {
      save.visitedInteractions.push(interactionId);
      saveGame(save);
    }
  }

  function hasVisited(save, interactionId) {
    return save.visitedInteractions.indexOf(interactionId) !== -1;
  }

  global.DD_SAVE = {
    STORAGE_KEY: STORAGE_KEY,
    defaultSave: defaultSave,
    loadSave: loadSave,
    saveGame: saveGame,
    getOrCreateSave: getOrCreateSave,
    markVisited: markVisited,
    hasVisited: hasVisited
  };
})(typeof window !== "undefined" ? window : global);
