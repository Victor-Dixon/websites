/* Planet Blue — zone influence, morality, nemesis, quests */
(function (global) {
  "use strict";

  var DATA = global.PLANET_BLUE_DATA;

  var NEMESIS_NAMES = [
    "Grak", "Vex", "Skarn", "Morr", "Threx", "Zul", "Karn", "Nix", "Drav", "Oss"
  ];
  var NEMESIS_TITLES = [
    "The Unbroken", "Scar-Bearer", "Last Stand", "Iron Hide", "The Returned",
    "Pain-Proof", "Grudge-Keeper", "Night Stalker", "The Persistent", "Blood Debt"
  ];

  var DEFAULT_WORLD = {
    syncVersion: 1,
    lastUpdated: null,
    communityPlaceholder: true,
    zones: {}
  };

  var DEFAULT_MORALITY = {
    score: 0,
    alignment: "neutral",
    history: []
  };

  var DEFAULT_NEMESIS = { registry: [] };

  var DEFAULT_QUESTS = {
    active: ["first_landing"],
    completed: [],
    log: []
  };

  function clone(obj) {
    return JSON.parse(JSON.stringify(obj));
  }

  function initZones() {
    var zones = {};
    Object.keys(DATA.ZONES).forEach(function (id) {
      zones[id] = clone(DATA.ZONES[id]);
    });
    return zones;
  }

  function ensureWorldSystems(save) {
    if (!save.world || !save.world.zones) {
      save.world = clone(DEFAULT_WORLD);
      save.world.zones = initZones();
      save.world.lastUpdated = new Date().toISOString();
    }
    if (!save.morality) save.morality = clone(DEFAULT_MORALITY);
    if (!save.nemesis) save.nemesis = clone(DEFAULT_NEMESIS);
    if (!save.quests) save.quests = clone(DEFAULT_QUESTS);
    return save;
  }

  function zoneStatus(safety) {
    if (safety >= 70) return "safe";
    if (safety >= 40) return "contested";
    return "overrun";
  }

  function zoneStatusLabel(status) {
    if (status === "safe") return "Safe";
    if (status === "contested") return "Contested";
    return "Overrun";
  }

  function zoneStatusIcon(status) {
    if (status === "safe") return "\u2694";
    if (status === "contested") return "\u2694";
    return "\u2620";
  }

  function computeAlignment(score) {
    if (score <= -30) return "evil";
    if (score >= 30) return "good";
    return "neutral";
  }

  function alignmentLabel(alignment) {
    if (alignment === "evil") return "Dark Path";
    if (alignment === "good") return "Light Path";
    return "Neutral";
  }

  function clampMorality(score) {
    return Math.max(-100, Math.min(100, score));
  }

  function applyMoralityDelta(save, delta, label, zoneId, choiceKey) {
    ensureWorldSystems(save);
    save.morality.score = clampMorality(save.morality.score + delta);
    save.morality.alignment = computeAlignment(save.morality.score);
    save.morality.history.unshift({
      id: "moral_" + Date.now(),
      choiceKey: choiceKey || null,
      label: label,
      delta: delta,
      zoneId: zoneId || null,
      timestamp: new Date().toISOString()
    });
    if (save.morality.history.length > 20) {
      save.morality.history.length = 20;
    }
    if (zoneId && save.world.zones[zoneId]) {
      if (delta > 0) {
        save.world.zones[zoneId].safety = Math.min(100, save.world.zones[zoneId].safety + 3);
        save.world.zones[zoneId].threat = Math.max(0, save.world.zones[zoneId].threat - 3);
      } else if (delta < 0) {
        save.world.zones[zoneId].threat = Math.min(100, save.world.zones[zoneId].threat + 3);
        save.world.zones[zoneId].safety = Math.max(0, save.world.zones[zoneId].safety - 3);
      }
    }
    save.world.lastUpdated = new Date().toISOString();
    return save;
  }

  function applyMissionOutcome(save, missionId, outcome) {
    ensureWorldSystems(save);
    var zoneId = DATA.MISSION_ZONE[missionId];
    if (!zoneId || !save.world.zones[zoneId]) return save;

    var zone = save.world.zones[zoneId];
    if (outcome === "win") {
      zone.safety = Math.min(100, zone.safety + 12);
      zone.threat = Math.max(0, zone.threat - 12);
      zone.playerContributions = (zone.playerContributions || 0) + 1;
      if (zone.safety >= 60) zone.factionControl = "colonists";
    } else if (outcome === "lose") {
      zone.safety = Math.max(0, zone.safety - 15);
      zone.threat = Math.min(100, zone.threat + 15);
      if (zone.threat >= 60) zone.factionControl = "hostile";
      else zone.factionControl = "contested";
    }
    save.world.lastUpdated = new Date().toISOString();
    return save;
  }

  function randomId(prefix) {
    return prefix + "_" + Math.random().toString(36).slice(2, 8);
  }

  function pickRandom(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
  }

  function createNemesisRecord(enemyUnit, zoneId, lastAbilityUsed) {
    var name = pickRandom(NEMESIS_NAMES);
    var title = pickRandom(NEMESIS_TITLES);
    var ability = lastAbilityUsed || "basic_attack";
    return {
      id: randomId("nem"),
      name: name,
      title: title,
      baseType: enemyUnit.type || "scout_drone",
      displayName: name + " " + title,
      killsVsPlayer: enemyUnit.nemesisKills || 1,
      playerKills: 0,
      encounters: 1,
      powerLevel: 1,
      resistances: {},
      lastAbilityUsed: ability,
      zoneId: zoneId,
      active: true,
      createdAt: new Date().toISOString(),
      lastSeenAt: new Date().toISOString()
    };
  }

  function addResistance(nemesis, abilityId, amount) {
    nemesis.resistances[abilityId] = Math.min(0.5, (nemesis.resistances[abilityId] || 0) + amount);
    nemesis.lastAbilityUsed = abilityId;
  }

  function registerNemesis(save, enemyUnit, zoneId, lastAbilityUsed) {
    ensureWorldSystems(save);
    var record = createNemesisRecord(enemyUnit, zoneId, lastAbilityUsed);
    addResistance(record, lastAbilityUsed || "basic_attack", 0.15);
    save.nemesis.registry.push(record);
    return record;
  }

  function findActiveNemesisForZone(save, zoneId) {
    ensureWorldSystems(save);
    return save.nemesis.registry.filter(function (n) {
      return n.active && n.zoneId === zoneId;
    });
  }

  function scaleNemesisUnit(nemesis, baseDef) {
    var pl = nemesis.powerLevel || 1;
    var hpBonus = Math.floor(baseDef.hp * 0.2 * pl);
    var resist = nemesis.resistances.basic_attack || 0;
    return {
      type: nemesis.baseType,
      nemesisId: nemesis.id,
      name: nemesis.displayName,
      hp: baseDef.hp + hpBonus,
      atk: baseDef.atk + pl * 2,
      move: baseDef.move,
      range: baseDef.range,
      glyph: baseDef.glyph,
      def: Math.floor(resist * 10),
      isNemesis: true
    };
  }

  function maybeSpawnNemesis(save, missionId, enemySlots) {
    ensureWorldSystems(save);
    var zoneId = DATA.MISSION_ZONE[missionId];
    var nemeses = findActiveNemesisForZone(save, zoneId);
    if (!nemeses.length || !enemySlots.length) return enemySlots;
    if (Math.random() > 0.35) return enemySlots;

    var nem = nemeses[Math.floor(Math.random() * nemeses.length)];
    var idx = Math.floor(Math.random() * enemySlots.length);
    var slot = enemySlots[idx];
    var baseDef = DATA.ENEMIES[slot.type];
    if (!baseDef) return enemySlots;

    var scaled = scaleNemesisUnit(nem, baseDef);
    enemySlots[idx] = Object.assign({}, slot, {
      type: scaled.type,
      nemesisId: scaled.nemesisId,
      nemesisName: scaled.name,
      nemesisHp: scaled.hp,
      nemesisAtk: scaled.atk,
      nemesisDef: scaled.def
    });
    nem.encounters += 1;
    nem.lastSeenAt = new Date().toISOString();
    nem.powerLevel = Math.min(5, nem.powerLevel + 1);
    return enemySlots;
  }

  function onNemesisSurvivesBattle(save, nemesisId, playerUsedAbility) {
    ensureWorldSystems(save);
    for (var i = 0; i < save.nemesis.registry.length; i++) {
      var n = save.nemesis.registry[i];
      if (n.id === nemesisId) {
        n.playerKills += 1;
        addResistance(n, playerUsedAbility || "basic_attack", 0.1);
        n.powerLevel = Math.min(5, n.powerLevel + 1);
        n.lastSeenAt = new Date().toISOString();
        break;
      }
    }
    return save;
  }

  function onNemesisDefeated(save, nemesisId) {
    ensureWorldSystems(save);
    for (var i = 0; i < save.nemesis.registry.length; i++) {
      if (save.nemesis.registry[i].id === nemesisId) {
        save.nemesis.registry[i].active = false;
        break;
      }
    }
    return save;
  }

  function pickNemesisCandidate(units, outcome, playerMaxHp, playerCurrentHp) {
    if (outcome === "lose") {
      var survivors = units.filter(function (u) { return u.team === "enemy" && u.hp > 0; });
      if (!survivors.length) return null;
      survivors.sort(function (a, b) {
        return (b.damageDealt || 0) - (a.damageDealt || 0);
      });
      return survivors[0];
    }
    if (outcome === "win" && playerCurrentHp <= playerMaxHp * 0.35) {
      var enemies = units.filter(function (u) { return u.team === "enemy"; });
      if (!enemies.length) return null;
      enemies.sort(function (a, b) {
        return (b.damageDealt || 0) - (a.damageDealt || 0);
      });
      return enemies[0];
    }
    return null;
  }

  function initQuestLog(save) {
    ensureWorldSystems(save);
    if (save.quests.log.length) return save;

    Object.keys(DATA.MISSIONS).forEach(function (id) {
      var m = DATA.MISSIONS[id];
      var status = save.missions[id] === "completed" ? "completed" : "active";
      if (save.missions[id] === "locked") status = "locked";
      save.quests.log.push({
        id: "quest_" + id,
        missionId: id,
        title: m.name,
        body: m.desc,
        status: status
      });
    });
    return save;
  }

  function syncQuestStatus(save) {
    ensureWorldSystems(save);
    save.quests.log.forEach(function (q) {
      var st = save.missions[q.missionId];
      if (st === "completed") q.status = "completed";
      else if (st === "locked") q.status = "locked";
      else q.status = "active";
    });
    return save;
  }

  function missionAllowedByMorality(save, missionId) {
    ensureWorldSystems(save);
    var gate = DATA.MORALITY_GATES[missionId];
    if (!gate) return true;
    return save.morality.alignment === gate;
  }

  function getMoralityDialogue(save, context) {
    ensureWorldSystems(save);
    var lines = DATA.MORALITY_DIALOGUE[save.morality.alignment] || DATA.MORALITY_DIALOGUE.neutral;
    return lines[context] || lines.default;
  }

  global.PLANET_BLUE_WORLD = {
    DEFAULT_WORLD: DEFAULT_WORLD,
    DEFAULT_MORALITY: DEFAULT_MORALITY,
    DEFAULT_NEMESIS: DEFAULT_NEMESIS,
    DEFAULT_QUESTS: DEFAULT_QUESTS,
    ensureWorldSystems: ensureWorldSystems,
    initZones: initZones,
    zoneStatus: zoneStatus,
    zoneStatusLabel: zoneStatusLabel,
    zoneStatusIcon: zoneStatusIcon,
    computeAlignment: computeAlignment,
    alignmentLabel: alignmentLabel,
    applyMoralityDelta: applyMoralityDelta,
    applyMissionOutcome: applyMissionOutcome,
    registerNemesis: registerNemesis,
    findActiveNemesisForZone: findActiveNemesisForZone,
    maybeSpawnNemesis: maybeSpawnNemesis,
    scaleNemesisUnit: scaleNemesisUnit,
    onNemesisSurvivesBattle: onNemesisSurvivesBattle,
    onNemesisDefeated: onNemesisDefeated,
    pickNemesisCandidate: pickNemesisCandidate,
    initQuestLog: initQuestLog,
    syncQuestStatus: syncQuestStatus,
    missionAllowedByMorality: missionAllowedByMorality,
    getMoralityDialogue: getMoralityDialogue
  };
})(typeof window !== "undefined" ? window : global);
