/* Planet Blue — enemy turn AI */
(function (global) {
  "use strict";

  var PATH = global.PLANET_BLUE_PATH;
  var COMBAT = global.PLANET_BLUE_COMBAT;

  function livingUnits(units, team) {
    return units.filter(function (u) { return u.hp > 0 && u.team === team; });
  }

  function findNearest(enemy, players) {
    var nearest = players[0];
    var best = PATH.manhattan(enemy.x, enemy.y, nearest.x, nearest.y);
    for (var i = 1; i < players.length; i++) {
      var d = PATH.manhattan(enemy.x, enemy.y, players[i].x, players[i].y);
      if (d < best) {
        best = d;
        nearest = players[i];
      }
    }
    return nearest;
  }

  function unitById(units, id) {
    for (var i = 0; i < units.length; i++) {
      if (units[i].id === id) return units[i];
    }
    return null;
  }

  function enemyTurn(enemy, state, cols, rows, terrainGrass, logFn) {
    var players = livingUnits(state.units, "player");
    if (!players.length) return null;

    var nearest = findNearest(enemy, players);
    var attacks = PATH.attackableTiles(enemy, state.units, cols, rows);
    var attackNow = null;
    for (var i = 0; i < attacks.length; i++) {
      if (attacks[i].targetId === nearest.id) attackNow = attacks[i];
    }

    var result = { moved: false, attacked: false, attackResult: null };

    if (attackNow) {
      var target = unitById(state.units, attackNow.targetId);
      if (target) {
        result.attackResult = COMBAT.resolveAttack(enemy, target);
        result.attacked = true;
        if (logFn) logFn(enemy.label + " hits " + target.label + " for " + result.attackResult.damage + ".");
        if (result.attackResult.defeated && logFn) logFn(target.label + " is defeated.");
      }
      return result;
    }

    var reachable = PATH.reachableTiles(enemy, state.terrain, state.units, cols, rows, terrainGrass);
    if (!reachable.length) return result;

    var step = reachable[0];
    var stepDist = PATH.manhattan(step.x, step.y, nearest.x, nearest.y);
    for (var j = 0; j < reachable.length; j++) {
      var tile = reachable[j];
      var d = PATH.manhattan(tile.x, tile.y, nearest.x, nearest.y);
      if (d < stepDist) {
        stepDist = d;
        step = tile;
      }
    }

    enemy.x = step.x;
    enemy.y = step.y;
    result.moved = true;
    if (logFn) logFn(enemy.label + " advances.");

    attacks = PATH.attackableTiles(enemy, state.units, cols, rows);
    for (var k = 0; k < attacks.length; k++) {
      if (attacks[k].targetId === nearest.id) {
        target = unitById(state.units, attacks[k].targetId);
        if (target) {
          result.attackResult = COMBAT.resolveAttack(enemy, target);
          result.attacked = true;
          if (logFn) logFn(enemy.label + " hits " + target.label + " for " + result.attackResult.damage + ".");
          if (result.attackResult.defeated && logFn) logFn(target.label + " is defeated.");
        }
        break;
      }
    }

    return result;
  }

  global.PLANET_BLUE_AI = {
    enemyTurn: enemyTurn,
    livingUnits: livingUnits
  };
})(typeof window !== "undefined" ? window : global);
