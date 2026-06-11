(function () {
  "use strict";

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;
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
  var missionStatus = save.missions[missionId];
  if (missionStatus === "locked") {
    window.location.href = "map.html";
    return;
  }

  var COLS = mission.gridCols;
  var ROWS = mission.gridRows;

  var els = {
    battlefield: document.getElementById("battlefield"),
    phaseBar: document.getElementById("phase-bar"),
    unitCard: document.getElementById("unit-card"),
    battleLog: document.getElementById("battle-log"),
    overlay: document.getElementById("overlay"),
    overlayTitle: document.getElementById("overlay-title"),
    overlayMessage: document.getElementById("overlay-message"),
    btnEndTurn: document.getElementById("btn-end-turn"),
    btnMove: document.getElementById("btn-move"),
    btnAttack: document.getElementById("btn-attack"),
    btnWait: document.getElementById("btn-wait"),
    btnOverlay: document.getElementById("btn-overlay-action"),
    missionTitle: document.getElementById("mission-title")
  };

  if (els.missionTitle) els.missionTitle.textContent = mission.name;

  var state = createInitialState();
  var selectedId = null;
  var actionMode = null;
  var moveTargets = [];
  var attackTargets = [];
  var rewardsApplied = false;

  function createInitialState() {
    var terrain = [];
    for (var y = 0; y < ROWS; y++) {
      for (var x = 0; x < COLS; x++) {
        var ch = mission.mapLayout[y][x];
        terrain.push(ch === "R" ? TERRAIN.ROCK : ch === "W" ? TERRAIN.WATER : TERRAIN.GRASS);
      }
    }

    var stats = DATA.computeStats(save.character.race, save.character.class);
    var cls = DATA.CLASSES[save.character.class];
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

    for (var i = 0; i < mission.enemies.length; i++) {
      var e = mission.enemies[i];
      var def = DATA.ENEMIES[e.type];
      units.push({
        id: "enemy" + i,
        team: "enemy",
        type: e.type,
        x: e.x,
        y: e.y,
        hp: def.hp,
        maxHp: def.hp,
        atk: def.atk,
        def: 0,
        move: def.move,
        range: def.range,
        glyph: def.glyph,
        label: def.name,
        moved: false,
        acted: false
      });
    }

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

  function resolvePlayerAttack(attacker, target) {
    var result = COMBAT.resolveAttack(attacker, target);
    log(attacker.label + " hits " + target.label + " for " + result.damage + ".");
    if (result.defeated) log(target.label + " is defeated.");
    attacker.acted = true;
    if (!attacker.moved) attacker.moved = true;
    actionMode = null;
    attackTargets = [];
    clearSelection();
    checkVictory();
    checkAutoEndPlayerPhase();
    render();
  }

  function checkVictory() {
    if (livingUnits("enemy").length === 0) {
      state.gameOver = "win";
      applyVictoryRewards();
      showOverlay("Victory", "Mission complete! +" + mission.rewards.xp + " XP, +" + mission.rewards.currency + " currency.");
    } else if (livingUnits("player").length === 0) {
      state.gameOver = "lose";
      showOverlay("Defeat", "Your squad has fallen. Return to the map and try again.");
    }
  }

  function applyVictoryRewards() {
    if (rewardsApplied || missionStatus === "completed") return;
    rewardsApplied = true;
    SAVE.completeMission(missionId, mission.rewards);
    missionStatus = "completed";
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
        AI.enemyTurn(enemy, state, COLS, ROWS, TERRAIN.GRASS, log);
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
    els.overlay.classList.remove("hidden");
    if (state.gameOver === "win") {
      els.btnOverlay.textContent = "Return to Map";
      els.btnOverlay.onclick = function () { window.location.href = "map.html"; };
    } else {
      els.btnOverlay.textContent = "Try Again";
      els.btnOverlay.onclick = function () { restartBattle(); };
    }
  }

  function hideOverlay() {
    els.overlay.classList.add("hidden");
  }

  function restartBattle() {
    state = createInitialState();
    clearSelection();
    hideOverlay();
    els.battleLog.innerHTML = "";
    log("Planet Blue battle begins — " + mission.name);
    render();
  }

  function renderUnitCard() {
    var unit = selectedId ? unitById(selectedId) : null;
    if (!unit) {
      els.unitCard.className = "unit-card empty";
      els.unitCard.textContent = "Select your unit";
      return;
    }
    els.unitCard.className = "unit-card";
    els.unitCard.innerHTML =
      "<p class=\"name\">" + unit.label + " (Blue)</p>" +
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

  els.btnMove.addEventListener("click", beginMove);
  els.btnAttack.addEventListener("click", beginAttack);
  els.btnWait.addEventListener("click", waitUnit);
  els.btnEndTurn.addEventListener("click", endPlayerPhase);

  log("Planet Blue battle begins — " + mission.name);
  render();
})();
