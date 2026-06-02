(function () {
  'use strict';

  const STORAGE_KEY = 'emergence_spark_battle_handoff_v1';
  const FORBIDDEN = [
    'scores',
    'tiers',
    'manifest_threshold',
    'flavor_vectors',
    'spark_signature',
    'combat_capability',
    'provisional_spark_signature',
    'provisional_combat_capability',
    'debug',
    'showwork',
    'roll',
    'odds'
  ];

  function esc(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function readPayload() {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) return null;

    FORBIDDEN.forEach(function (key) {
      if (raw.indexOf(key) !== -1) {
        throw new Error('Unsafe handoff payload blocked: ' + key);
      }
    });

    const payload = JSON.parse(raw);
    if (!payload || payload.version !== 1 || payload.source !== 'emergence-character-generator') {
      return null;
    }

    return payload;
  }

  function renderPayload(payload) {
    const powers = (payload.selected_powers || []).map(function (power) {
      return '<li>' + esc(power.power || 'Unknown ability') + '</li>';
    }).join('');

    return [
      '<section class="sbs-handoff-card" data-spark-handoff="1">',
      '<p class="sbs-handoff-kicker">Imported Spark</p>',
      '<h2>' + esc(payload.spark_name || payload.title || 'Unnamed Spark') + '</h2>',
      payload.archetype ? '<p><strong>' + esc(payload.archetype) + '</strong></p>' : '',
      payload.summary ? '<p>' + esc(payload.summary) + '</p>' : '',
      powers ? '<h3>Manifested Abilities</h3><ul>' + powers + '</ul>' : '',
      '<div class="sbs-custom-battle-controls">',
      '<label>Opponent <select id="sbs-custom-opponent">',
      '<option value="the-victor">The Victor</option>',
      '<option value="captain-cap-wilson">Captain Cap Wilson</option>',
      '</select></label>',
      '<button type="button" id="sbs-run-custom-spark-battle">Start Battle With This Spark</button>',
      '</div>',
      '<div id="sbs-custom-spark-battle-result" class="sbs-custom-spark-battle-result" aria-live="polite"></div>',
      '<p class="sbs-handoff-note">Player-safe handoff loaded. Backend scoring remains hidden.</p>',
      '</section>'
    ].join('');
  }

  async function runCustomBattle(payload) {
    const opponent = document.getElementById('sbs-custom-opponent');
    const result = document.getElementById('sbs-custom-spark-battle-result');

    if (!result) return;

    result.textContent = 'Resolving custom Spark battle...';

    const response = await fetch('/wp-json/spark-battle/v1/custom-battle', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        spark: payload,
        opponent: opponent ? opponent.value : 'the-victor'
      })
    });

    const data = await response.json();

    if (!response.ok || data.status !== 'resolved') {
      result.textContent = data.message || 'Battle could not be resolved.';
      return;
    }

    result.innerHTML = [
      '<h3>Winner: ' + esc(data.winner) + '</h3>',
      '<p><strong>Arena:</strong> ' + esc(data.arena) + '</p>',
      '<p>' + esc(data.story) + '</p>'
    ].join('');
  }

  function mount() {
    let payload = null;
    try {
      payload = readPayload();
    } catch (error) {
      console.warn('[SparkBattleSim] unsafe handoff ignored');
      return;
    }

    if (!payload || document.querySelector('[data-spark-handoff="1"]')) {
      return;
    }

    const root = document.querySelector('main, article, form, body');
    if (!root) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = renderPayload(payload);

    const card = wrapper.firstElementChild;
    if (root === document.body) {
      document.body.insertBefore(card, document.body.firstChild);
    } else {
      root.insertBefore(card, root.firstChild);
    }

    const button = document.getElementById('sbs-run-custom-spark-battle');
    if (button) {
      button.addEventListener('click', function () {
        runCustomBattle(payload);
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
