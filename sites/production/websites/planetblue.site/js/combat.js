/* Planet Blue — attack resolution */
(function (global) {
  "use strict";

  function rollDamage(atk, def) {
    var raw = atk + Math.floor(Math.random() * 5) - 2;
    var afterDef = raw - (def || 0);
    return Math.max(1, afterDef);
  }

  function resolveAttack(attacker, target) {
    var damage = rollDamage(attacker.atk, target.def);
    target.hp = Math.max(0, target.hp - damage);
    return {
      damage: damage,
      defeated: target.hp <= 0
    };
  }

  global.PLANET_BLUE_COMBAT = {
    rollDamage: rollDamage,
    resolveAttack: resolveAttack
  };
})(typeof window !== "undefined" ? window : global);
