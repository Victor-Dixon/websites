/* Planet Blue — static game data */
(function (global) {
  "use strict";

  var TERRAIN = { GRASS: "grass", ROCK: "rock", WATER: "water" };

  var RACES = {
    human: { id: "human", name: "Human", hp: 2, atk: 0, def: 0, move: 0, range: 0, desc: "Balanced explorers of the blue world." },
    robot: { id: "robot", name: "Robot", hp: 0, atk: 0, def: 1, move: -1, range: 0, desc: "Durable chassis, slower stride." },
    celestial: { id: "celestial", name: "Celestial", hp: -2, atk: 0, def: 0, move: 0, range: 1, desc: "Star-touched, fragile but far-reaching." },
    infernal: { id: "infernal", name: "Infernal", hp: 0, atk: 2, def: -1, move: 0, range: 0, desc: "Fierce striker, light armor." },
    beastborn: { id: "beastborn", name: "Beastborn", hp: 0, atk: 0, def: 0, move: 1, range: -1, desc: "Swift predator, close-range fighter." },
    beastmen: { id: "beastmen", name: "Beastmen", hp: 1, atk: 2, def: -1, move: 2, range: -1, desc: "Tribal warriors with claw and fang. Fast strikers of the wild clans." },
    stoneborn: { id: "stoneborn", name: "Stoneborn", hp: 3, atk: -1, def: 1, move: 0, range: 0, desc: "Living stone, slow but sturdy." },
    shadowborn: { id: "shadowborn", name: "Shadowborn", hp: -1, atk: 1, def: 0, move: 0, range: 0, desc: "Umbral hunter with keen strikes." },
    aquatic: { id: "aquatic", name: "Aquatic", hp: 1, atk: 0, def: 1, move: 0, range: 0, desc: "Tide-adapted, resilient shell." },
    spirit: { id: "spirit", name: "Spirit", hp: 0, atk: -1, def: 0, move: 0, range: 1, desc: "Ethereal caster, distant touch." },
    ancient: { id: "ancient", name: "Ancient", hp: 0, atk: 1, def: 1, move: -2, range: 0, desc: "Primordial power, ponderous pace." }
  };

  var CLASSES = {
    fire: { id: "fire", name: "Fire", hp: 28, atk: 14, def: 2, move: 3, range: 2, ability: "firebolt", desc: "High ATK, low DEF. Burns through foes." },
    rock: { id: "rock", name: "Rock", hp: 36, atk: 10, def: 6, move: 2, range: 1, ability: "guard", desc: "Tank class. High HP/DEF, short range." },
    light_magic: { id: "light_magic", name: "Light Magic", hp: 22, atk: 12, def: 3, move: 2, range: 3, ability: "light_beam", desc: "Ranged caster, fragile frame." },
    dark_magic: { id: "dark_magic", name: "Dark Magic", hp: 20, atk: 16, def: 2, move: 2, range: 2, ability: "shadow_pulse", desc: "Burst damage with self-risk." },
    robot_class: { id: "robot_class", name: "Robot", hp: 30, atk: 11, def: 5, move: 3, range: 1, ability: "overclock", desc: "Balanced chassis with repair protocols." },
    /* Chris draft classes — source: data/classes/chris_classes.json */
    assassin: { id: "assassin", name: "Assassin", hp: 24, atk: 8, def: 3, move: 6, range: 1, ability: "backstab", desc: "Stealth fighter. Speed, flanking, and critical strikes.", status: "draft", created_by: "Chris" },
    mage: { id: "mage", name: "Mage", hp: 22, atk: 10, def: 3, move: 4, range: 3, ability: "arcane_bolt", desc: "Ranged spellcaster. Elemental magic and battlefield control.", status: "draft", created_by: "Chris" }
  };

  var ABILITIES = {
    firebolt: { id: "firebolt", name: "Firebolt", type: "attack", power: 6, range: 2, class: "fire", level: 1, desc: "Launch a bolt of blue flame." },
    guard: { id: "guard", name: "Guard", type: "defense", power: 4, range: 0, class: "rock", level: 1, desc: "Brace and reduce incoming damage." },
    light_beam: { id: "light_beam", name: "Light Beam", type: "attack", power: 5, range: 3, class: "light_magic", level: 1, desc: "Piercing ray of starlight." },
    shadow_pulse: { id: "shadow_pulse", name: "Shadow Pulse", type: "attack", power: 8, range: 2, class: "dark_magic", level: 1, desc: "Volatile void eruption." },
    overclock: { id: "overclock", name: "Overclock", type: "buff", power: 3, range: 0, class: "robot_class", level: 1, desc: "Boost ATK for one turn." },
    backstab: { id: "backstab", name: "Backstab", type: "attack", power: 9, range: 1, class: "assassin", level: 1, desc: "Deal extra damage if attacking from behind or from the side." },
    arcane_bolt: { id: "arcane_bolt", name: "Arcane Bolt", type: "attack", power: 7, range: 3, class: "mage", level: 1, desc: "Basic ranged magic attack." }
  };

  var ENEMIES = {
    scout_drone: { id: "scout_drone", name: "Scout Drone", hp: 16, atk: 8, move: 3, range: 1, glyph: "SD" },
    rust_crawler: { id: "rust_crawler", name: "Rust Crawler", hp: 22, atk: 10, move: 2, range: 1, glyph: "RC" },
    void_stalker: { id: "void_stalker", name: "Void Stalker", hp: 18, atk: 12, move: 4, range: 1, glyph: "VS" }
  };

  var MISSIONS = {
    first_landing: {
      id: "first_landing",
      name: "First Landing",
      desc: "Secure the landing zone against hostile drones.",
      gridCols: 8,
      gridRows: 6,
      mapLayout: [
        "GGGGGGGG",
        "GGGRRGGG",
        "GGGRRGGG",
        "GGWWWWGG",
        "GGGGGGGG",
        "GGGGGGGG"
      ],
      playerStart: { x: 1, y: 4 },
      enemies: [
        { type: "scout_drone", x: 6, y: 1 },
        { type: "rust_crawler", x: 7, y: 2 }
      ],
      rewards: { xp: 50, currency: 25 },
      order: 1
    },
    deep_caverns: {
      id: "deep_caverns",
      name: "Deep Caverns",
      desc: "Explore the crystal caves beneath the surface.",
      gridCols: 8,
      gridRows: 6,
      mapLayout: [
        "GGGGGGGG",
        "GRRGGGRR",
        "GRRGGGRR",
        "GGGGGGGG",
        "GWWWWGGG",
        "GGGGGGGG"
      ],
      playerStart: { x: 0, y: 5 },
      enemies: [
        { type: "rust_crawler", x: 5, y: 1 },
        { type: "void_stalker", x: 7, y: 0 }
      ],
      rewards: { xp: 80, currency: 40 },
      order: 2,
      requires: "first_landing"
    },
    sky_spire: {
      id: "sky_spire",
      name: "Sky Spire",
      desc: "Ascend the floating spire above the blue horizon.",
      gridCols: 8,
      gridRows: 6,
      mapLayout: [
        "GGGGGGGG",
        "GGWWWWGG",
        "GGGGGGGG",
        "GGRRGGGG",
        "GGRRGGGG",
        "GGGGGGGG"
      ],
      playerStart: { x: 1, y: 5 },
      enemies: [
        { type: "scout_drone", x: 6, y: 0 },
        { type: "scout_drone", x: 7, y: 1 },
        { type: "void_stalker", x: 5, y: 2 }
      ],
      rewards: { xp: 120, currency: 60 },
      order: 3,
      requires: "deep_caverns"
    }
  };

  var DEFAULT_CHARACTER = {
    name: "Explorer",
    race: "human",
    class: "fire",
    level: 1,
    xp: 0,
    currency: 0
  };

  var DEFAULT_MISSIONS = {
    first_landing: "unlocked",
    deep_caverns: "locked",
    sky_spire: "locked"
  };

  var ZONES = {
    landing_bay: {
      id: "landing_bay",
      name: "Landing Bay",
      missionId: "first_landing",
      threat: 45,
      safety: 55,
      factionControl: "contested",
      playerContributions: 0
    },
    deep_caverns: {
      id: "deep_caverns",
      name: "Deep Caverns",
      missionId: "deep_caverns",
      threat: 60,
      safety: 40,
      factionControl: "contested",
      playerContributions: 0
    },
    sky_spire: {
      id: "sky_spire",
      name: "Sky Spire",
      missionId: "sky_spire",
      threat: 70,
      safety: 30,
      factionControl: "hostile",
      playerContributions: 0
    }
  };

  var MISSION_ZONE = {
    first_landing: "landing_bay",
    deep_caverns: "deep_caverns",
    sky_spire: "sky_spire"
  };

  var MORAL_CHOICES = {
    first_landing_pre: {
      id: "first_landing_pre",
      zoneId: "landing_bay",
      prompt: "A wounded scout drone sparks in the brush. The colonists aren't watching.",
      options: [
        { id: "spare", label: "Spare it — mercy costs nothing", delta: 15, hint: "+Good" },
        { id: "finish", label: "Finish it — no loose ends", delta: -15, hint: "-Evil" }
      ]
    },
    first_landing_post: {
      id: "first_landing_post",
      zoneId: "landing_bay",
      prompt: "The landing zone is secure. A survivor waves from the wreckage.",
      options: [
        { id: "aid", label: "Help the survivor to camp", delta: 12, hint: "+Good" },
        { id: "loot", label: "Strip the wreck for parts", delta: -12, hint: "-Evil" }
      ]
    },
    battle_mercy: {
      id: "battle_mercy",
      zoneId: null,
      prompt: "One enemy still breathes. Show mercy?",
      options: [
        { id: "mercy", label: "Spare them", delta: 10, hint: "+Good" },
        { id: "execute", label: "No witnesses", delta: -10, hint: "-Evil" }
      ]
    }
  };

  var MORALITY_GATES = {};

  var MORALITY_DIALOGUE = {
    good: {
      default: "The frontier remembers your kindness.",
      map_greeting: "Colonists nod as you pass — they trust your banner.",
      battle_start: "Fight with honor. The bay depends on it."
    },
    neutral: {
      default: "The frontier holds its breath.",
      map_greeting: "Merchants barter under watchful guards.",
      battle_start: "Steel your nerve. The grid awaits."
    },
    evil: {
      default: "Fear travels faster than footfalls here.",
      map_greeting: "Whispers follow you — some call you useful, others cursed.",
      battle_start: "Crush them. Leave nothing standing."
    }
  };

  function computeStats(raceId, classId) {
    var race = RACES[raceId] || RACES.human;
    var cls = CLASSES[classId] || CLASSES.fire;
    return {
      hp: Math.max(10, cls.hp + race.hp),
      atk: Math.max(1, cls.atk + race.atk),
      def: Math.max(0, cls.def + race.def),
      move: Math.max(1, cls.move + race.move),
      range: Math.max(1, cls.range + race.range),
      ability: cls.ability
    };
  }

  global.PLANET_BLUE_DATA = {
    TERRAIN: TERRAIN,
    RACES: RACES,
    CLASSES: CLASSES,
    ABILITIES: ABILITIES,
    ENEMIES: ENEMIES,
    MISSIONS: MISSIONS,
    ZONES: ZONES,
    MISSION_ZONE: MISSION_ZONE,
    MORAL_CHOICES: MORAL_CHOICES,
    MORALITY_GATES: MORALITY_GATES,
    MORALITY_DIALOGUE: MORALITY_DIALOGUE,
    DEFAULT_CHARACTER: DEFAULT_CHARACTER,
    DEFAULT_MISSIONS: DEFAULT_MISSIONS,
    computeStats: computeStats
  };
})(typeof window !== "undefined" ? window : global);
