/*!
 * MaskZero Spark Engine v1.0
 * Unified manifestation system — root of all game modes.
 *
 * All modes (Campaign, Gauntlet, What-If Arena) read and write through
 * this single module. One character record. One persistent identity.
 *
 * Canon tiers:
 *   1 → Campaign + Dream.OS  (world-changing)
 *   2 → Gauntlet             (character-influencing)
 *   3 → What-If Arena        (sandbox / alternate timeline)
 */
(function (root) {
  'use strict';

  /* ── Keys ───────────────────────────────────────────────────────────── */
  var RECORD_KEY  = 'mz.manifestation.v1';
  var LEGACY_KEY  = 'dreamos.currentSparkCharacter.v1';
  var LEGACY_KEY2 = 'dreamos.singleSparkCharacter.v1';
  var API_KEY     = 'mz.ai.api_key.v1';
  var MODEL_KEY   = 'mz.ai.model.v1';

  var TRAIT_KEYS   = ['leadership','creativity','discipline','compassion','ambition','power'];
  var FACTION_KEYS = ['vanguard','archivists','shadow_market','crimson_forge','the_order'];

  /* ── Helpers ─────────────────────────────────────────────────────────── */
  function now() { return new Date().toISOString(); }

  var ESC_MAP = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'};
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){ return ESC_MAP[c]; }); }

  function uid() {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
      return 'spark_' + crypto.randomUUID().replace(/-/g,'').slice(0,16);
    }
    if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
      var arr = new Uint32Array(3);
      crypto.getRandomValues(arr);
      return 'spark_' + arr[0].toString(36) + arr[1].toString(36) + arr[2].toString(36) + '_' + Date.now().toString(36);
    }
    return 'spark_' + Math.random().toString(36).slice(2,10) + Math.random().toString(36).slice(2,10) + '_' + Date.now().toString(36);
  }

  function clamp(v, lo, hi) { return Math.min(hi, Math.max(lo, v)); }

  /* ── District + title derivation ────────────────────────────────────── */
  var DISTRICT_MAP = {
    leadership: 'south', creativity: 'east',   discipline: 'west',
    compassion: 'central', ambition: 'south',  power: 'south'
  };

  var TITLE_MAP = {
    leadership: 'The Vanguard',   creativity: 'The Artisan',
    discipline: 'The Warden',     compassion: 'The Keeper',
    ambition:   'The Challenger', power: 'The Enforcer'
  };

  var DISTRICT_NAMES = {
    north: 'North Meridian — Knowledge District',
    east:  'East Meridian — Creativity District',
    west:  'West Meridian — Discipline District',
    south: 'South Meridian — Power District',
    central: 'Central Meridian — The Crossroads'
  };

  function topTrait(traits) {
    return TRAIT_KEYS.slice().sort(function (a, b) { return traits[b] - traits[a]; })[0];
  }

  function deriveDistrict(traits) {
    return DISTRICT_MAP[topTrait(traits)] || 'central';
  }

  function deriveTitle(traits) {
    return TITLE_MAP[topTrait(traits)] || 'The Wanderer';
  }

  /* ── Domain → trait seeding ─────────────────────────────────────────── */
  var DOMAIN_SEEDS = {
    'Leadership':  { leadership:70, compassion:55 },
    'Creativity':  { creativity:70, ambition:60 },
    'Discipline':  { discipline:70, power:55 },
    'Analytical':  { discipline:65, leadership:55 },
    'Empathy':     { compassion:75, leadership:55 },
    'Ambition':    { ambition:75, power:60 },
    'Resilience':  { discipline:65, compassion:60 },
    'Innovation':  { creativity:68, ambition:62 },
    'Strategy':    { discipline:65, leadership:60 },
    'Charisma':    { leadership:68, creativity:55 },
    'Justice':     { compassion:65, discipline:60 },
    'Vision':      { creativity:60, ambition:65 }
  };

  function seedTraitsFromDomains(traits, domains) {
    if (!Array.isArray(domains)) return;
    domains.forEach(function (d) {
      var seed = DOMAIN_SEEDS[d];
      if (!seed) return;
      Object.keys(seed).forEach(function (k) {
        if (traits[k] !== undefined) traits[k] = clamp(seed[k], 0, 100);
      });
    });
  }

  /* ── Blank record factory ────────────────────────────────────────────── */
  function blankRecord(legacy) {
    var traits = { leadership:50, creativity:50, discipline:50, compassion:50, ambition:50, power:50 };
    var domains = (legacy && legacy.domains) || [];
    seedTraitsFromDomains(traits, domains);

    var rec = {
      character_id:    (legacy && legacy.id)              || uid(),
      name:            (legacy && legacy.name)            || 'Unknown Spark',
      cast:            (legacy && legacy.cast)            || 'Unclassified',
      spark_signature: (legacy && legacy.spark_signature) || '',
      domains:         domains,
      level: 1,
      xp:    0,
      traits: traits,
      reputation: { vanguard:0, archivists:0, shadow_market:0, crimson_forge:0, the_order:0 },
      inventory:   [],
      known_npcs:  [],
      world_events: [],
      canon: { tier1:[], tier2:[], tier3:[] },
      campaign: {
        chapter:   1,
        scene_id:  null,
        story_log: [],
        active:    false
      },
      gauntlet: {
        highest_floor: 0,
        total_runs:    0,
        current_floor: 0,
        active:        false
      },
      arena: { explored_timelines: [] },
      meridian: {
        starting_district: null,
        unlocked_districts: [],
        reputation_title: null
      },
      created_at: now(),
      updated_at: now()
    };

    rec.meridian.starting_district = deriveDistrict(rec.traits);
    rec.meridian.reputation_title  = deriveTitle(rec.traits);
    return rec;
  }

  /* ── Persistence ─────────────────────────────────────────────────────── */
  function load() {
    try {
      var raw = localStorage.getItem(RECORD_KEY);
      if (raw) return JSON.parse(raw);
    } catch (_) {}
    // Migrate from legacy Spark quiz keys
    var legacy = null;
    try { legacy = JSON.parse(localStorage.getItem(LEGACY_KEY) || localStorage.getItem(LEGACY_KEY2) || 'null'); } catch (_) {}
    if (legacy) {
      var rec = blankRecord(legacy);
      save(rec);
      return rec;
    }
    return null;
  }

  function save(rec) {
    rec.updated_at = now();
    // Ensure meridian titles stay current
    rec.meridian.starting_district = rec.meridian.starting_district || deriveDistrict(rec.traits);
    rec.meridian.reputation_title  = deriveTitle(rec.traits);
    localStorage.setItem(RECORD_KEY, JSON.stringify(rec));
    return rec;
  }

  function getOrCreate() { return load(); }

  function reset() { localStorage.removeItem(RECORD_KEY); }

  /* ── Trait / reputation mutation ─────────────────────────────────────── */
  function updateTraits(changes) {
    var rec = load();
    if (!rec) return null;
    Object.keys(changes).forEach(function (k) {
      if (rec.traits[k] !== undefined) {
        rec.traits[k] = clamp(rec.traits[k] + changes[k], 0, 100);
      }
    });
    return save(rec);
  }

  function updateReputation(changes) {
    var rec = load();
    if (!rec) return null;
    Object.keys(changes).forEach(function (k) {
      if (rec.reputation[k] !== undefined) {
        rec.reputation[k] = clamp(rec.reputation[k] + changes[k], -100, 100);
      }
    });
    return save(rec);
  }

  /* ── XP + levelling ──────────────────────────────────────────────────── */
  function xpThreshold(level) { return level * 120; }

  function addXP(amount, source, canonTier) {
    var rec = load();
    if (!rec) return null;
    rec.xp = (rec.xp || 0) + amount;
    while (rec.level < 20 && rec.xp >= xpThreshold(rec.level)) {
      rec.xp -= xpThreshold(rec.level);
      rec.level += 1;
      _logCanon(rec, { type:'level_up', level:rec.level, source:source||'unknown' }, canonTier||2);
    }
    if (source) _logCanon(rec, { type:'xp', amount:amount, source:source }, canonTier||2);
    return save(rec);
  }

  function xpProgress(rec) {
    var needed = xpThreshold(rec.level);
    return { current: rec.xp, needed: needed, pct: Math.round((rec.xp / needed) * 100) };
  }

  /* ── Canon events ────────────────────────────────────────────────────── */
  function _logCanon(rec, event, tier) {
    event.timestamp = now();
    var key = 'tier' + (tier || 1);
    if (!rec.canon[key]) rec.canon[key] = [];
    rec.canon[key].push(event);
    if (tier <= 2) {
      if (!rec.world_events) rec.world_events = [];
      rec.world_events.push(event);
    }
  }

  function addWorldEvent(description, canonTier) {
    var rec = load();
    if (!rec) return null;
    _logCanon(rec, { type:'event', description:description }, canonTier || 1);
    return save(rec);
  }

  /* ── Campaign helpers ────────────────────────────────────────────────── */
  function saveStoryEntry(role, text) {
    var rec = load();
    if (!rec) return null;
    if (!rec.campaign.story_log) rec.campaign.story_log = [];
    rec.campaign.story_log.push({ role:role, text:text, timestamp:now() });
    if (rec.campaign.story_log.length > 60) {
      rec.campaign.story_log = rec.campaign.story_log.slice(-60);
    }
    return save(rec);
  }

  function updateCampaign(patch) {
    var rec = load();
    if (!rec) return null;
    Object.assign(rec.campaign, patch);
    return save(rec);
  }

  /* ── Gauntlet helpers ────────────────────────────────────────────────── */
  function updateGauntlet(patch) {
    var rec = load();
    if (!rec) return null;
    Object.assign(rec.gauntlet, patch);
    if (patch.current_floor && patch.current_floor > (rec.gauntlet.highest_floor || 0)) {
      rec.gauntlet.highest_floor = patch.current_floor;
    }
    return save(rec);
  }

  /* ── Arena helpers ───────────────────────────────────────────────────── */
  function logArenaTimeline(description) {
    var rec = load();
    if (!rec) return null;
    if (!rec.arena.explored_timelines) rec.arena.explored_timelines = [];
    _logCanon(rec, { type:'arena', description:description }, 3);
    rec.arena.explored_timelines.push({ description:description, timestamp:now() });
    return save(rec);
  }

  /* ── Inventory helpers ───────────────────────────────────────────────── */
  function addItem(item) {
    var rec = load();
    if (!rec) return null;
    if (!rec.inventory) rec.inventory = [];
    rec.inventory.push(typeof item === 'string' ? { name:item, acquired:now() } : item);
    return save(rec);
  }

  function addNPC(name) {
    var rec = load();
    if (!rec) return null;
    if (!rec.known_npcs) rec.known_npcs = [];
    if (!rec.known_npcs.includes(name)) rec.known_npcs.push(name);
    return save(rec);
  }

  /* ── AI settings ─────────────────────────────────────────────────────── */
  // API key is stored in sessionStorage (cleared on tab close) to limit exposure.
  // Note: sessionStorage is still accessible to all JS on the same origin.
  // Users should avoid entering keys on shared or public computers.
  function getApiKey()        { return sessionStorage.getItem(API_KEY) || ''; }
  function setApiKey(k)       { if (k) { sessionStorage.setItem(API_KEY, k.trim()); } else { sessionStorage.removeItem(API_KEY); } }
  function getModel()         { return localStorage.getItem(MODEL_KEY) || 'gpt-4o-mini'; }
  function setModel(m)        { localStorage.setItem(MODEL_KEY, m); }

  /* ── Export ──────────────────────────────────────────────────────────── */
  root.SparkEngine = {
    // Data
    RECORD_KEY:      RECORD_KEY,
    TRAIT_KEYS:      TRAIT_KEYS,
    FACTION_KEYS:    FACTION_KEYS,
    DISTRICT_NAMES:  DISTRICT_NAMES,
    // CRUD
    load:            load,
    save:            save,
    getOrCreate:     getOrCreate,
    blankRecord:     blankRecord,
    reset:           reset,
    // Mutations
    updateTraits:    updateTraits,
    updateReputation:updateReputation,
    addXP:           addXP,
    xpProgress:      xpProgress,
    addWorldEvent:   addWorldEvent,
    // Campaign
    saveStoryEntry:  saveStoryEntry,
    updateCampaign:  updateCampaign,
    // Gauntlet
    updateGauntlet:  updateGauntlet,
    // Arena
    logArenaTimeline:logArenaTimeline,
    // Inventory
    addItem:         addItem,
    addNPC:          addNPC,
    // Derive
    deriveDistrict:  deriveDistrict,
    deriveTitle:     deriveTitle,
    topTrait:        topTrait,
    // Utilities
    esc:             esc,
    // AI settings
    getApiKey:       getApiKey,
    setApiKey:       setApiKey,
    getModel:        getModel,
    setModel:        setModel
  };

}(typeof window !== 'undefined' ? window : this));
