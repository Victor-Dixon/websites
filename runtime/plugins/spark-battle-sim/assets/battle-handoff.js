(function () {
  'use strict';

  if (window.__SparkBattleOneCardArena) return;
  window.__SparkBattleOneCardArena = true;

  const STORAGE_KEY = 'emergence_spark_battle_handoff_v1';
  const LOCAL_SINGLE_KEY = 'dreamos.singleSparkCharacter.v1';
  const LEGACY_SAVED_KEY = 'dreamos.savedSparkCharacters.v1';
  const FORBIDDEN = [
    'scores',
    'tiers',
    'manifest_threshold',
    'flavor_vectors',
    'debug',
    'showwork',
    'roll',
    'odds',
    'raw'
  ];
  const AI_ROSTER = [
    {
      id: 'ai-null-prince',
      name: 'Null Prince',
      archetype: 'Shadow AI Rival',
      summary: 'A blackout duelist that waits for the lights to fail.',
      selected_powers: [{power: 'Shadow Step'}, {power: 'Knife-Edge Teleport'}]
    },
    {
      id: 'ai-iron-seraph',
      name: 'Iron Seraph',
      archetype: 'Force AI Rival',
      summary: 'A plated guardian that turns impact into returning pressure.',
      selected_powers: [{power: 'Gravity Pin'}, {power: 'Impact Mirror'}]
    },
    {
      id: 'ai-wild-crown',
      name: 'Wild Crown',
      archetype: 'Primal AI Rival',
      summary: 'A predator-minded tactician using roots, fear, and timing.',
      selected_powers: [{power: 'Thorn Lock'}, {power: 'Predator Pulse'}]
    }
  ];
  const ATTACKS = {
    mind: ['Synapse Split', 'Crown Fracture', 'Memory Bleed', 'Thought Lock'],
    energy: ['Arc Lance', 'Flash Burn', 'Thunder Pulse', 'Solar Cut'],
    titan: ['Meteor Elbow', 'Anchor Slam', 'Bonebreaker Charge', 'Pressure Wall'],
    velocity: ['Blink Rush', 'Afterimage Cut', 'Velocity Hook', 'Zero-G Feint'],
    specter: ['Shadow Step', 'Blindspot Pierce', 'Silent Drop', 'Phantom Counter'],
    primal: ['Thorn Cage', 'Predator Howl', 'Root Snare', 'Venom Bloom'],
    omni: ['Gravity Crush', 'Impact Mirror', 'Barrier Burst', 'Vector Snap'],
    duality: ['Eclipse Cut', 'Radiant Cage', 'Void Grasp', 'Hard-Light Crash'],
    default: ['Spark Strike', 'Domain Burst', 'Signature Blow', 'Overdrive Hit']
  };

  const state = {
    character: null,
    aiIndex: 0,
    playerHp: 100,
    aiHp: 100,
    round: 0,
    resolving: false,
    finished: false,
    feed: []
  };

  function esc(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function rejectUnsafe(raw) {
    const text = typeof raw === 'string' ? raw : JSON.stringify(raw || {});
    FORBIDDEN.forEach(function (key) {
      if (text.indexOf(key) !== -1) {
        throw new Error('Unsafe battle payload blocked: ' + key);
      }
    });
  }

  function powers(character) {
    if (!character) return [];
    if (Array.isArray(character.selected_powers)) {
      return character.selected_powers.map(function (power) {
        return typeof power === 'string' ? power : (power.power || power.name || '');
      }).filter(Boolean);
    }
    if (Array.isArray(character.powers)) {
      return character.powers.map(function (power) {
        return typeof power === 'string' ? power : (power.power || power.name || '');
      }).filter(Boolean);
    }
    return [];
  }

  function title(character) {
    return character.spark_name || character.title || character.name || character.lead_domain || 'Unnamed Spark';
  }

  function archetype(character) {
    return character.archetype || character.cast || character.lead_domain || 'Spark Card';
  }

  function normalize(character) {
    if (!character || typeof character !== 'object') return null;
    return {
      version: 1,
      source: character.source || 'emergence-character-generator',
      spark_name: title(character),
      title: title(character),
      archetype: archetype(character),
      summary: character.summary || character.profile_shape || '',
      cast: character.cast || '',
      profile_shape: character.profile_shape || '',
      selected_powers: powers(character).map(function (power) {
        return {power: power};
      })
    };
  }

  function domainKey(character) {
    const text = [
      title(character),
      archetype(character),
      character.summary || '',
      powers(character).join(' ')
    ].join(' ').toLowerCase();
    if (/mind|telepathy|psychic|thought|will|illusion/.test(text)) return 'mind';
    if (/energy|fire|flame|electric|lightning|sonic|ice|beam/.test(text)) return 'energy';
    if (/titan|strength|invulner|density|giant|elastic|momentum/.test(text)) return 'titan';
    if (/velocity|speed|flight|reflex|wall|vibration/.test(text)) return 'velocity';
    if (/specter|teleport|portal|phase|invis|shadow|sense/.test(text)) return 'specter';
    if (/primal|nature|animal|weather|venom|toxic|wild/.test(text)) return 'primal';
    if (/omni|force|gravity|shield|barrier|metal|magnet|heal/.test(text)) return 'omni';
    if (/duality|light|void|eclipse|hard light|laser/.test(text)) return 'duality';
    return 'default';
  }

  function attackName(character) {
    const bank = ATTACKS[domainKey(character)] || ATTACKS.default;
    return bank[Math.floor(Math.random() * bank.length)];
  }

  function damage(attacker, defender) {
    const attackWeight = 14 + powers(attacker).length * 4 + title(attacker).length % 8;
    const defenseWeight = powers(defender).length * 2 + title(defender).length % 5;
    return Math.max(8, Math.min(34, attackWeight + Math.floor(Math.random() * 14) - defenseWeight));
  }

  function ai() {
    return AI_ROSTER[state.aiIndex % AI_ROSTER.length];
  }

  function readLocalCharacter() {
    const raw = window.localStorage.getItem(STORAGE_KEY) || window.localStorage.getItem(LOCAL_SINGLE_KEY);
    if (raw) {
      rejectUnsafe(raw);
      return normalize(JSON.parse(raw));
    }

    const legacy = JSON.parse(window.localStorage.getItem(LEGACY_SAVED_KEY) || '[]');
    if (Array.isArray(legacy) && legacy.length) {
      return normalize(legacy[0]);
    }

    return null;
  }

  function queryValue(name) {
    return new URLSearchParams(window.location.search || '').get(name) || '';
  }

  async function loadSharedCharacterFromUrl() {
    const record = queryValue('character_record');
    if (record) {
      const response = await fetch('/wp-json/emergence/v1/characters/' + encodeURIComponent(record), {
        method: 'GET',
        headers: {'Accept': 'application/json'}
      });
      const data = await response.json();
      if (response.ok && data.status === 'loaded' && data.character) {
        rejectUnsafe(data.character);
        const character = normalize(data.character);
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(character));
        return character;
      }
    }

    const token = queryValue('spark_token');
    if (token) {
      const response = await fetch('/wp-json/emergence/v1/spark-token/' + encodeURIComponent(token), {
        method: 'GET',
        headers: {'Accept': 'application/json'}
      });
      const data = await response.json();
      if (response.ok && data.status === 'loaded' && data.spark) {
        rejectUnsafe(data.spark);
        const character = normalize(data.spark);
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(character));
        return character;
      }
    }

    return null;
  }

  async function loadCharacter(shell) {
    const shared = await loadSharedCharacterFromUrl();
    if (shared) return shared;

    const isLoggedIn = shell.getAttribute('data-logged-in') === '1';
    const endpoint = shell.getAttribute('data-account-endpoint') || '/wp-json/emergence/v1/characters/me';
    const nonce = shell.getAttribute('data-account-nonce') || '';

    if (isLoggedIn) {
      const response = await fetch(endpoint, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {'Accept': 'application/json', 'X-WP-Nonce': nonce}
      });
      const data = await response.json();
      if (response.ok && data.status === 'loaded' && data.character) {
        rejectUnsafe(data.character);
        const character = normalize(data.character);
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(character));
        return character;
      }
    }

    return readLocalCharacter();
  }

  function resetBattle() {
    state.playerHp = 100;
    state.aiHp = 100;
    state.round = 0;
    state.resolving = false;
    state.finished = false;
    state.feed = [];
  }

  function card(character, role) {
    const p = powers(character);
    return [
      '<article class="sbs-snap-card sbs-domain-' + esc(domainKey(character)) + '">',
      '<p class="sbs-card-role">' + esc(role) + '</p>',
      '<div class="sbs-card-portrait"><span>' + esc(title(character).slice(0, 2).toUpperCase()) + '</span></div>',
      '<h3>' + esc(title(character)) + '</h3>',
      '<p>' + esc(archetype(character)) + '</p>',
      '<ul>' + (p.length ? p.slice(0, 3).map(function (power) { return '<li>' + esc(power) + '</li>'; }).join('') : '<li>Latent Spark</li>') + '</ul>',
      '</article>'
    ].join('');
  }

  function health(label, hp, side) {
    return [
      '<div class="sbs-health sbs-health-' + esc(side) + '">',
      '<span>' + esc(label) + '</span>',
      '<strong>' + Math.max(0, hp) + '/100</strong>',
      '<div><i style="width:' + Math.max(0, hp) + '%"></i></div>',
      '</div>'
    ].join('');
  }

  function render(shell) {
    const loginUrl = shell.getAttribute('data-login-url') || '/wp-login.php';
    const registerUrl = shell.getAttribute('data-register-url') || loginUrl;

    if (!state.character) {
      shell.innerHTML = [
        '<section class="sbs-empty-account">',
        '<p class="sbs-kicker">What-If Arena</p>',
        '<h2>Create or sign in to load your one Spark card</h2>',
        '<p>Your account keeps one character so you do not have to redo the Spark questions after leaving the site.</p>',
        '<div class="sbs-actions">',
        '<a href="' + esc(loginUrl) + '">Sign in</a>',
        '<a href="' + esc(registerUrl) + '">Create account</a>',
        '<a href="/spark-generator/">Generate Spark</a>',
        '</div>',
        '<p class="sbs-note">Google/GitHub sign-in can be enabled on the WordPress login screen with the site OAuth provider.</p>',
        '</section>'
      ].join('');
      return;
    }

    const rival = ai();
    const buttonText = state.resolving ? 'AI Countering...' : (state.finished ? 'Reset Fight' : (state.round ? 'Next Attack' : 'Battle AI'));
    shell.innerHTML = [
      '<section class="sbs-snap-arena">',
      '<div class="sbs-arena-head">',
      '<p class="sbs-kicker">What-If Arena</p>',
      '<h2>Your card vs AI</h2>',
      '<p>One saved Spark character loads here. Tap Battle AI to trade animated attacks.</p>',
      '</div>',
      '<div class="sbs-fx-layer" aria-hidden="true"></div>',
      '<div class="sbs-board">',
      '<div>' + card(state.character, 'Your Card') + health(title(state.character), state.playerHp, 'player') + '</div>',
      '<div class="sbs-versus">VS</div>',
      '<div>' + card(rival, 'AI Rival') + health(title(rival), state.aiHp, 'ai') + '</div>',
      '</div>',
      '<div class="sbs-actions sbs-battle-actions">',
      '<button type="button" data-sbs-battle ' + (state.resolving ? 'disabled' : '') + '>' + esc(buttonText) + '</button>',
      '<button type="button" data-sbs-shuffle>Shuffle AI</button>',
      '<button type="button" data-sbs-reset>Reset</button>',
      '</div>',
      '<div class="sbs-feed" aria-live="polite">' + state.feed.map(function (item) { return '<p>' + item + '</p>'; }).join('') + '</div>',
      '</section>'
    ].join('');
  }

  function addFeed(html) {
    state.feed.unshift(html);
    state.feed = state.feed.slice(0, 5);
  }

  function pop(shell, amount, side, label) {
    const layer = shell.querySelector('.sbs-fx-layer');
    if (!layer) return;
    const el = document.createElement('div');
    el.className = 'sbs-damage-pop sbs-damage-' + side;
    el.style.left = (18 + Math.random() * 64) + '%';
    el.style.top = (18 + Math.random() * 58) + '%';
    el.innerHTML = '-' + amount + '<small>' + esc(label) + '</small>';
    layer.appendChild(el);
    setTimeout(function () { el.remove(); }, 1300);
  }

  function burst(shell, character) {
    const layer = shell.querySelector('.sbs-fx-layer');
    if (!layer) return;
    const key = domainKey(character);
    const el = document.createElement('div');
    el.className = 'sbs-power-burst sbs-burst-' + key;
    el.innerHTML = key === 'mind'
      ? '<div class="sbs-mind-core"><span></span><span></span><span></span></div><b>Mind Fatality</b>'
      : '<div class="sbs-energy-core">' + esc(key.toUpperCase()) + '</div><b>' + esc(key) + ' attack</b>';
    layer.appendChild(el);
    setTimeout(function () { el.remove(); }, 1200);
  }

  function exchange(shell) {
    if (!state.character || state.resolving) return;
    if (state.finished) {
      resetBattle();
      render(shell);
      return;
    }

    state.resolving = true;
    state.round += 1;
    const rival = ai();
    const playerAttack = attackName(state.character);
    const playerDamage = damage(state.character, rival);
    state.aiHp = Math.max(0, state.aiHp - playerDamage);
    addFeed('<strong>' + esc(title(state.character)) + '</strong> uses ' + esc(playerAttack) + ' for <strong>' + playerDamage + '</strong> damage.');
    render(shell);
    pop(shell, playerDamage, 'ai', playerAttack);
    burst(shell, state.character);

    if (state.aiHp <= 0) {
      state.finished = true;
      state.resolving = false;
      addFeed('<strong>' + esc(title(state.character)) + '</strong> wins the projection.');
      setTimeout(function () { render(shell); }, 700);
      return;
    }

    setTimeout(function () {
      const aiAttack = attackName(rival);
      const aiDamage = damage(rival, state.character);
      state.playerHp = Math.max(0, state.playerHp - aiDamage);
      state.finished = state.playerHp <= 0;
      state.resolving = false;
      addFeed('<strong>' + esc(title(rival)) + '</strong> counters with ' + esc(aiAttack) + ' for <strong>' + aiDamage + '</strong> damage.' + (state.finished ? ' AI wins the projection.' : ''));
      render(shell);
      pop(shell, aiDamage, 'player', aiAttack);
      burst(shell, rival);
    }, 900);
  }

  function bind(shell) {
    shell.addEventListener('click', function (event) {
      if (event.target.closest('[data-sbs-battle]')) {
        exchange(shell);
      } else if (event.target.closest('[data-sbs-shuffle]')) {
        state.aiIndex += 1;
        resetBattle();
        render(shell);
      } else if (event.target.closest('[data-sbs-reset]')) {
        resetBattle();
        render(shell);
      }
    });
  }

  function mountShell(shell) {
    bind(shell);
    loadCharacter(shell)
      .then(function (character) {
        state.character = character;
        resetBattle();
        render(shell);
      })
      .catch(function () {
        state.character = readLocalCharacter();
        resetBattle();
        render(shell);
      });
  }

  function mount() {
    const shells = Array.prototype.slice.call(document.querySelectorAll('[data-spark-battle-arena="1"]'));
    if (!shells.length) return;
    shells.forEach(mountShell);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
