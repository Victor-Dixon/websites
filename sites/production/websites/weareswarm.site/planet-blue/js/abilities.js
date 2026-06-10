/* Planet Blue — ability definitions (level 1 MVP) */
(function (global) {
  "use strict";

  var DATA = global.PLANET_BLUE_DATA;

  function getAbilityForClass(classId) {
    var cls = DATA.CLASSES[classId];
    if (!cls || !cls.ability) return null;
    return DATA.ABILITIES[cls.ability] || null;
  }

  function getAbility(abilityId) {
    return DATA.ABILITIES[abilityId] || null;
  }

  function describeAbility(abilityId) {
    var ab = getAbility(abilityId);
    if (!ab) return "No ability assigned.";
    return ab.name + " (Lv " + ab.level + ") — " + ab.desc +
      " [Power " + ab.power + ", Range " + ab.range + "]";
  }

  /* MVP: abilities shown on character page; combat uses basic attack */
  global.PLANET_BLUE_ABILITIES = {
    getAbilityForClass: getAbilityForClass,
    getAbility: getAbility,
    describeAbility: describeAbility
  };
})(typeof window !== "undefined" ? window : global);
