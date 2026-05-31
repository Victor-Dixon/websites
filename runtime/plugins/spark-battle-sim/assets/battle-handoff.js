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
      '<p class="sbs-handoff-note">Player-safe handoff loaded. Backend scoring remains hidden.</p>',
      '</section>'
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

    if (!payload) return;

    const root = document.querySelector('.spark-battle-sim, #spark-battle-sim, form, main, article, body');
    if (!root) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = renderPayload(payload);

    if (root === document.body) {
      document.body.insertBefore(wrapper.firstElementChild, document.body.firstChild);
    } else {
      root.parentNode.insertBefore(wrapper.firstElementChild, root);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
