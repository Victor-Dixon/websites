(function () {
  "use strict";

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;
  var WORLD = window.PLANET_BLUE_WORLD;
  var PATH = window.PLANET_BLUE_PATH;
  var COMBAT = window.PLANET_BLUE_COMBAT;
  var AI = window.PLANET_BLUE_AI;
  var GRID = window.PLANET_BLUE_GRID;
  var TERRAIN = DATA.TERRAIN;

  var params = new URLSearchParams(window.location.search);
  var missionId = params.get("mission") || "first_landing";
  var mission = DATA.MISSIONS[missionId];

  if (!mission) {
    document.body.innerHTML = "<p class='error-msg'>Unknown mission. <a href='map.html'>Return to map</a></p>";
    return;
  }

  var save = SAVE.getOrCreateSave();
  WORLD.ensureWorldSystems(save);
  SAVE.syncMissionUnlocks(save);

  var missionStatus = save.missions[missionId];
  if (missionStatus === "locked") {
    window.location.href = "map.html";
    return;
  }

  var zoneId = DATA.MISSION_ZONE[missionId];
  var COLS = mission.gridCols;
  var ROWS = mission.gridRows;
  var lastAbilityUsed = "basic_attack";
  var playerMaxHp = 0;
  var preBattleDone = false;
  var postBattlePending = false;
  var mercyOffered = false;

  var els = {
    battlefield: document.getElementById("battlefield"),
    phaseBar: document.getElementById("phase-bar"),
    unitCard: document.getElementById("unit-card"),
    battleLog: document.getElementById("battle-log"),
    overlay: document.getElementById("overlay"),
    overlayCard: document.getElementById("overlay-card"),
    overlayTitle: document.getElementById("overlay-title"),
    overlayMessage: document.getElementById("overlay-message"),
    overlayChoices: document.getElementById("overlay-choices"),
    btnEndTurn: document.getElementById("btn-end-turn"),
    btnMove: document.getElementById("btn-move"),
    btnAttack: document.getElementById("btn-attack"),
    btnWait: document.getElementById("btn-wait"),
    btnMercy: document.getElementById("btn-mercy"),
    btnOverlay: document.getElementById("btn-overlay-action"),
    missionTitle: document.getElementById("mission-title")
  };

  if (els.missionTitle) els.missionTitle.textContent = mission.name;

  var state = null;
  var selectedId = null;
  var actionMode = null;
  var moveTargets = [];
  var attackTargets = [];
  var rewardsApplied = false;

  function choiceAlreadyMade(choiceKey) {
    return save.morality.history.some(function (h) {
      return h.choiceKey === choiceKey;
    });
  }

  function recordChoice(choiceKey, option) {
    var choiceDef = DATA.MORAL_CHOICES[choiceKey];
    WORLD.applyMoralityDelta(save, option.delta, option.label, choiceDef.zoneId, choiceKey);
    SAVE.saveGame(save);
  }

  function showChoiceOverlay(choiceKey, onComplete) {
    var choiceDef = DATA.MORAL_CHOICES[choiceKey];
    if (!choiceDef) {
      if (onComplete) onComplete();
      return;
    }

    els.overlayTitle.textContent = "Decision";
    els.overlayMessage.textContent = choiceDef.prompt;
    els.overlayChoices.innerHTML = "";
    els.overlayChoices.classList.remove("hidden");
    els.btnOverlay.classList.add("hidden");
    els.overlayCard.classList.add("wide");

    choiceDef.options.forEach(function (opt) {
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn " + (opt.delta > 0 ? "good" : "evil");
      btn.innerHTML = opt.label + " <span class=\"choice-hint\">" + opt.hint + "</span>";
      btn.addEventListener("click", function () {
        recordChoice(choiceKey, opt);
        hideOverlay();
        if (onComplete) onComplete();
      });
      els.overlayChoices.appendChild(btn);
    });

    els.overlay.classList.remove("hidden");
  }

  function hideOverlay() {
    els.overlay.classList.add("hidden");
    els.overlayChoices.classList.add("hidden");
    els.overlayChoices.innerHTML = "";
    els.btnOverlay.classList.remove("hidden");
    els.overlayCard.classList.remove("wide");
  }

  function buildEnemyUnits() {
    var enemies = mission.enemies.slice();
    enemies = WORLD.maybeSpawnNemesis(save, missionId, enemies);

    var units = [];
    for (var i = 0; i < enemies.length; i++) {
      var e = enemies[i];
      var def = DATA.ENEMIES[e.type];
      var hp = e.nemesisHp || def.hp;
      var atk = e.nemesisAtk || def.atk;
      var label = e.nemesisName || def.name;
      units.push({
        id: "enemy" + i,
        team: "enemy",
        type: e.type,
        nemesisId: e.nemesisId || null,
        isNemesis: !!e.nemesisId,
        x: e.x,
        y: e.y,
        hp: hp,
        maxHp: hp,
        atk: atk,
        def: e.nemesisDef || 0,
        move: def.move,
        range: def.range,
        glyph: def.glyph,
        label: label,
        damageDealt: 0,
        nemesisKills: 0,
        moved: false,
        acted: false
      });
    }
    return units;
  }

  function createInitialState() {
    var terrain = [];
    for (var y = 0; y < ROWS; y++) {
      for (var x = 0; x < COLS; x++) {
        var ch = mission.mapLayout[y][x];
        terrain.push(ch === "R" ? TERRAIN.ROCK : ch === "W" ? TERRAIN.WATER : TERRAIN.GRASS);
      }
    }

    var stats = DATA.computeStats(save.character.race, save.character.class);
    playerMaxHp = stats.hp;
    var units = [];

    units.push({
      id: "player0",
      team: "player",
      x: mission.playerStart.x,
      y: mission.playerStart.y,
      hp: stats.hp,
      maxHp: stats.hp,
      atk: stats.atk,
      def: stats.def,
      move: stats.move,
      range: stats.range,
      glyph: save.character.name.slice(0, 2).toUpperCase(),
      label: save.character.name,
      moved: false,
      acted: false
    });

    units = units.concat(buildEnemyUnits());
    SAVE.saveGame(save);

    return { terrain: terrain, units: units, phase: "player", gameOver: null };
  }

  function unitById(id) {
    for (var i = 0; i < state.units.length; i++) {
      if (state.units[i].id === id) return state.units[i];
    }
    return null;
  }

  function livingUnits(team) {
    return AI.livingUnits(state.units, team);
  }

  function log(message) {
    var li = document.createElement("li");
    li.textContent = message;
    els.battleLog.prepend(li);
  }

  function resetPlayerTurnFlags() {
    state.units.forEach(function (u) {
      if (u.team === "player" && u.hp > 0) {
        u.moved = false;
        u.acted = false;
      }
    });
  }

  function allPlayerUnitsSpent() {
    return livingUnits("player").every(function (u) { return u.moved && u.acted; });
  }

  function clearSelection() {
    selectedId = null;
    actionMode = null;
    moveTargets = [];
    attackTargets = [];
  }

  function selectUnit(id) {
    var unit = unitById(id);
    if (!unit || unit.hp <= 0) return;
    if (state.phase !== "player" || unit.team !== "player") return;
    if (unit.moved && unit.acted) return;
    selectedId = id;
    actionMode = null;
    moveTargets = [];
    attackTargets = [];
    render();
  }

  function beginMove() {
    var unit = unitById(selectedId);
    if (!unit || unit.moved) return;
    actionMode = "move";
    moveTargets = PATH.reachableTiles(unit, state.terrain, state.units, COLS, ROWS, TERRAIN.GRASS);
    attackTargets = [];
    render();
  }

  function beginAttack() {
    var unit = unitById(selectedId);
    if (!unit || unit.acted) return;
    actionMode = "attack";
    attackTargets = PATH.attackableTiles(unit, state.units, COLS, ROWS);
    moveTargets = [];
    render();
  }

  function waitUnit() {
    var unit = unitById(selectedId);
    if (!unit) return;
    unit.moved = true;
    unit.acted = true;
    log(unit.label + " holds position.");
    clearSelection();
    checkAutoEndPlayerPhase();
    render();
  }

  function moveUnit(unit, x, y) {
    unit.x = x;
    unit.y = y;
    unit.moved = true;
    log(unit.label + " moves to (" + (x + 1) + "," + (y + 1) + ").");
    actionMode = null;
    moveTargets = [];
    render();
  }

  function applyDamageToUnit(target, damage, attacker) {
    var resist = 0;
    if (target.isNemesis && target.nemesisId) {
      var nem = save.nemesis.registry.find(function (n) { return n.id === target.nemesisId; });
      if (nem && nem.resistances[lastAbilityUsed]) {
        resist = nem.resistances[lastAbilityUsed];
        damage = Math.max(1, Math.floor(damage * (1 - resist)));
      }
    }
    target.hp = Math.max(0, target.hp - damage);
    if (attacker && attacker.team === "enemy") {
      attacker.damageDealt = (attacker.damageDealt || 0) + damage;
    }
    return damage;
  }

  function resolvePlayerAttack(attacker, target) {
    lastAbilityUsed = "basic_attack";
    var raw = COMBAT.resolveAttack(attacker, target);
    var damage = applyDamageToUnit(target, raw.damage, null);
    log(attacker.label + " hits " + target.label + " for " + damage + ".");
    if (target.hp <= 0) {
      log(target.label + " is defeated.");
      if (target.nemesisId) WORLD.onNemesisDefeated(save, target.nemesisId);
    }
    attacker.acted = true;
    if (!attacker.moved) attacker.moved = true;
    actionMode = null;
    attackTargets = [];
    clearSelection();
    checkMercyOffer();
    checkVictory();
    checkAutoEndPlayerPhase();
    render();
  }

  function checkMercyOffer() {
    var enemies = livingUnits("enemy");
    if (enemies.length === 1 && !mercyOffered && !state.gameOver) {
      mercyOffered = true;
      els.btnMercy.classList.remove("hidden");
    } else if (enemies.length !== 1) {
      els.btnMercy.classList.add("hidden");
    }
  }

  function offerMercy() {
    var choiceDef = DATA.MORAL_CHOICES.battle_mercy;
    showChoiceOverlay("battle_mercy", function () {
      var enemy = livingUnits("enemy")[0];
      if (enemy) {
        log("Mercy shown to " + enemy.label + ".");
        enemy.hp = 0;
        if (enemy.nemesisId) {
          WORLD.registerNemesis(save, enemy, zoneId, lastAbilityUsed);
          log(enemy.label + " escapes — a nemesis is born!");
        }
      }
      els.btnMercy.classList.add("hidden");
      SAVE.saveGame(save);
      checkVictory();
      render();
    });
  }

  function handleNemesisCreation(outcome) {
    var player = livingUnits("player")[0];
    var playerHp = player ? player.hp : 0;
    var candidate = WORLD.pickNemesisCandidate(state.units, outcome, playerMaxHp, playerHp);

    if (candidate) {
      if (candidate.nemesisId) {
        WORLD.onNemesisSurvivesBattle(save, candidate.nemesisId, lastAbilityUsed);
        log(candidate.label + " grows stronger — nemesis power rises!");
      } else {
        var record = WORLD.registerNemesis(save, candidate, zoneId, lastAbilityUsed);
        log(record.displayName + " has sworn vengeance!");
      }
      SAVE.saveGame(save);
    }
  }

  function checkVictory() {
    if (livingUnits("enemy").length === 0) {
      state.gameOver = "win";
      handleNemesisCreation("win");
      if (missionId === "first_landing" && !choiceAlreadyMade("first_landing_post")) {
        postBattlePending = true;
        applyVictoryRewards();
        showChoiceOverlay("first_landing_post", function () {
          finishVictoryOverlay();
        });
      } else {
        applyVictoryRewards();
        finishVictoryOverlay();
      }
    } else if (livingUnits("player").length === 0) {
      state.gameOver = "lose";
      SAVE.recordDefeat(missionId);
      handleNemesisCreation("lose");
      showOverlay("Defeat", "Your squad has fallen. The zone grows more dangerous.");
    }
  }

  function finishVictoryOverlay() {
    var zone = save.world.zones[zoneId];
    var zoneMsg = zone ? " Zone safety now " + zone.safety + "%." : "";
    showOverlay("Victory", "Mission complete! +" + mission.rewards.xp + " XP, +" + mission.rewards.currency + " gp." + zoneMsg);
  }

  function applyVictoryRewards() {
    if (rewardsApplied) return;
    rewardsApplied = true;
    if (missionStatus !== "completed") {
      SAVE.completeMission(missionId, mission.rewards);
    } else {
      SAVE.syncMissionUnlocks(save);
      SAVE.saveGame(save);
    }
    save = SAVE.loadSave();
    missionStatus = save.missions[missionId];
  }

  function checkAutoEndPlayerPhase() {
    if (state.phase === "player" && allPlayerUnitsSpent()) endPlayerPhase();
  }

  function endPlayerPhase() {
    if (state.gameOver) return;
    clearSelection();
    state.phase = "enemy";
    render();
    window.setTimeout(runEnemyPhase, 450);
  }

  function runEnemyPhase() {
    if (state.gameOver) return;
    var enemies = livingUnits("enemy");
    var delay = 0;

    enemies.forEach(function (enemy) {
      window.setTimeout(function () {
        if (state.gameOver) return;
        var playerBefore = livingUnits("player")[0];
        AI.enemyTurn(enemy, state, COLS, ROWS, TERRAIN.GRASS, log);
        if (playerBefore && playerBefore.hp > 0) {
          var playerAfter = unitById(playerBefore.id);
          if (playerAfter && playerAfter.hp < playerBefore.hp) {
            enemy.damageDealt = (enemy.damageDealt || 0) + (playerBefore.hp - playerAfter.hp);
          }
        }
        checkVictory();
        render();
      }, delay);
      delay += 380;
    });

    window.setTimeout(function () {
      if (state.gameOver) return;
      state.phase = "player";
      resetPlayerTurnFlags();
      log("— Player phase —");
      render();
    }, delay + 200);
  }

  function showOverlay(title, message) {
    els.overlayTitle.textContent = title;
    els.overlayMessage.textContent = message;
    els.overlayChoices.classList.add("hidden");
    els.btnOverlay.classList.remove("hidden");
    els.overlay.classList.remove("hidden");
    if (state.gameOver === "win") {
      els.btnOverlay.textContent = "Return to Map";
      els.btnOverlay.onclick = function () { window.location.href = "map.html"; };
    } else {
      els.btnOverlay.textContent = "Try Again";
      els.btnOverlay.onclick = function () { restartBattle(); };
    }
  }

  function restartBattle() {
    hideOverlay();
    mercyOffered = false;
    preBattleDone = false;
    state = createInitialState();
    clearSelection();
    els.battleLog.innerHTML = "";
    beginBattleFlow();
  }

  function renderUnitCard() {
    var unit = selectedId ? unitById(selectedId) : null;
    if (!unit) {
      els.unitCard.className = "unit-card empty";
      els.unitCard.textContent = "Select your unit";
      return;
    }
    els.unitCard.className = "unit-card";
    var tag = unit.isNemesis ? " <span class=\"badge nemesis\">Nemesis</span>" : "";
    els.unitCard.innerHTML =
      "<p class=\"name\">" + unit.label + tag + "</p>" +
      "<p class=\"stat\">HP " + unit.hp + " / " + unit.maxHp + "</p>" +
      "<p class=\"stat\">Move " + unit.move + " · Range " + unit.range + " · ATK " + unit.atk + "</p>" +
      "<p class=\"stat\">Tile (" + (unit.x + 1) + "," + (unit.y + 1) + ")</p>";
  }

  function renderControls() {
    var playerPhase = state.phase === "player" && !state.gameOver;
    var unit = selectedId ? unitById(selectedId) : null;
    var canSelect = playerPhase && unit && unit.team === "player";

    els.btnEndTurn.disabled = !playerPhase;
    els.btnMove.disabled = !canSelect || unit.moved || actionMode === "move";
    els.btnAttack.disabled = !canSelect || unit.acted || actionMode === "attack";
    els.btnWait.disabled = !canSelect;
    els.btnMercy.disabled = livingUnits("enemy").length !== 1;

    els.phaseBar.textContent = state.gameOver
      ? "Battle Over"
      : state.phase === "player" ? "Player Phase" : "Enemy Phase";
    els.phaseBar.classList.toggle("enemy", state.phase === "enemy");
  }

  function onCellClick(x, y) {
    if (state.gameOver || state.phase !== "player") return;

    var clickedUnit = GRID.unitAt(state.units, x, y);

    if (actionMode === "move" && selectedId) {
      for (var i = 0; i < moveTargets.length; i++) {
        if (moveTargets[i].x === x && moveTargets[i].y === y) {
          moveUnit(unitById(selectedId), x, y);
          return;
        }
      }
    }

    if (actionMode === "attack" && selectedId) {
      for (var j = 0; j < attackTargets.length; j++) {
        if (attackTargets[j].x === x && attackTargets[j].y === y) {
          var attacker = unitById(selectedId);
          var target = unitById(attackTargets[j].targetId);
          if (attacker && target) resolvePlayerAttack(attacker, target);
          return;
        }
      }
    }

    if (clickedUnit && clickedUnit.team === "player") selectUnit(clickedUnit.id);
  }

  function render() {
    GRID.renderBattlefield(els.battlefield, {
      cols: COLS,
      rows: ROWS,
      terrain: state.terrain,
      units: state.units,
      selectedId: selectedId,
      moveTargets: moveTargets,
      attackTargets: attackTargets,
      phase: state.phase,
      onCellClick: onCellClick,
      onUnitClick: selectUnit
    });
    renderUnitCard();
    renderControls();
  }

  function beginBattleFlow() {
    state = createInitialState();
    var greeting = WORLD.getMoralityDialogue(save, "battle_start");
    log(greeting);
    log("Battle begins — " + mission.name);

    if (missionId === "first_landing" && !choiceAlreadyMade("first_landing_pre")) {
      showChoiceOverlay("first_landing_pre", function () {
        preBattleDone = true;
        render();
      });
    } else {
      preBattleDone = true;
      render();
    }
  }

  els.btnMove.addEventListener("click", beginMove);
  els.btnAttack.addEventListener("click", beginAttack);
  els.btnWait.addEventListener("click", waitUnit);
  els.btnMercy.addEventListener("click", offerMercy);
  els.btnEndTurn.addEventListener("click", endPlayerPhase);

  beginBattleFlow();
})();
