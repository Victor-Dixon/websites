(function () {
  const form = document.getElementById('emergence-cg-form');
  const result = document.getElementById('emergence-cg-result');
  const flavorMount = document.getElementById('emergence-cg-flavor');
  const progressLabel = document.getElementById('ecg-progress-label');
  const progressFill = document.getElementById('ecg-progress-fill');

  let lastDomainAnswers = [];
  let lastDomainPayload = null;

  const flavorBlocks = {
    Titan: [29, 30, 31, 32, 33],
    Velocity: [34, 35, 36, 37, 38],
    Energy: [39, 40, 41, 42, 43],
    Specter: [44, 45, 46, 47, 48],
    Duality: [49, 50, 51, 52, 53],
    Omni: [54, 55, 56, 57, 58],
    Primal: [59, 60, 61, 62, 63],
    Mind: [64, 65, 66, 67, 68]
  };

  if (!form || !result || !window.EmergenceCG) return;

  function esc(value) {
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  function updateProgress() {
    const selects = Array.from(form.querySelectorAll('select'));
    const answered = selects.filter((select) => select.value).length;
    if (progressLabel) progressLabel.textContent = answered + ' / ' + selects.length + ' answered';
    if (progressFill) progressFill.style.width = ((answered / selects.length) * 100) + '%';
  }

  async function postGenerate(body) {
    const response = await fetch(EmergenceCG.endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': EmergenceCG.nonce
      },
      body: JSON.stringify(body)
    });

    const payload = await response.json();
    if (!response.ok) throw new Error(payload.message || 'Generator failed');
    return payload;
  }

  function scoreCards(payload) {
    return Object.entries(payload.scores || {})
      .map(function (pair) {
        const tier = payload.tiers && payload.tiers[pair[0]] ? payload.tiers[pair[0]] : '?';
        return '<div class="ecg-card"><strong>' + esc(pair[0]) + '</strong><br>Score ' + esc(pair[1]) + ' · Tier ' + esc(tier) + '</div>';
      })
      .join('');
  }

  function powerCards(payload) {
    return (payload.powers || []).map(function (p) {
      return '<div class="ecg-card"><strong>' + esc(p.power) + '</strong><br>' +
        esc(p.domain) + ' · Tier ' + esc(p.tier) + ' · ' + esc(p.selection) + '</div>';
    }).join('');
  }

  function renderDomainResult(payload) {
    result.innerHTML = [
      '<h2>Your Spark Type Scan</h2>',
      '<p class="ecg-result-note">Pass 1 is complete. No powers have been selected yet.</p>',
      '<div class="ecg-card-grid">',
      '<div class="ecg-card"><strong>Lead Domain</strong><br>' + esc(payload.lead_domain || 'Unresolved') + '</div>',
      '<div class="ecg-card"><strong>Profile Shape</strong><br>' + esc(payload.profile_shape || 'Unresolved') + '</div>',
      '<div class="ecg-card"><strong>Provisional Signature</strong><br>' + esc(payload.provisional_spark_signature) + '</div>',
      '<div class="ecg-card"><strong>Provisional Combat</strong><br>' + esc(payload.provisional_combat_capability) + '</div>',
      '<div class="ecg-card"><strong>Power Selection</strong><br>Locked until flavor pass</div>',
      '</div>',
      '<h3>Manifested Domains</h3>',
      '<p>' + (payload.manifested || []).map(esc).join(', ') + '</p>',
      '<p><strong>Manifest threshold:</strong> ' + esc(payload.manifest_threshold) + '</p>',
      '<h3>Domain Scores</h3>',
      '<div class="ecg-card-grid">' + scoreCards(payload) + '</div>'
    ].join('');
  }

  function renderFlavorForm(payload) {
    const manifested = payload.manifested || [];
    const blocks = manifested.map(function (domain) {
      const qs = flavorBlocks[domain] || [];
      const fields = qs.map(function (q) {
        return [
          '<fieldset class="ecg-question">',
          '<legend>Q' + q + ' — ' + esc(domain) + ' flavor choice</legend>',
          '<select name="' + q + '" required>',
          '<option value="">Choose one...</option>',
          '<option value="A">A</option>',
          '<option value="B">B</option>',
          '<option value="C">C</option>',
          '<option value="D">D</option>',
          '<option value="E">E</option>',
          '<option value="F">F</option>',
          '</select>',
          '</fieldset>'
        ].join('');
      }).join('');

      return '<div class="ecg-explainer"><h2>' + esc(domain) + ' Flavor Block</h2>' + fields + '</div>';
    }).join('');

    flavorMount.innerHTML = [
      '<form id="emergence-cg-flavor-form" class="ecg-form">',
      '<h2>Pass 2: Flavor Power Selection</h2>',
      '<p>Only manifested domains get flavor questions. This keeps powers constrained to the type scan.</p>',
      blocks,
      '<button type="submit">Select Powers</button>',
      '</form>'
    ].join('');

    const flavorForm = document.getElementById('emergence-cg-flavor-form');
    flavorForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      result.innerHTML += '<p>Running flavor pass...</p>';

      const data = new FormData(flavorForm);
      const flavorAnswers = {};
      for (const [q, value] of data.entries()) flavorAnswers[q] = value;

      try {
        const finalPayload = await postGenerate({
          answers: lastDomainAnswers,
          flavor_answers: flavorAnswers
        });

        result.innerHTML = [
          '<h2>Your Spark Profile</h2>',
          '<p class="ecg-result-note">Pass 2 is complete. Powers were selected only from manifested domains.</p>',
          '<div class="ecg-card-grid">',
          '<div class="ecg-card"><strong>Lead Domain</strong><br>' + esc(finalPayload.lead_domain || 'Unresolved') + '</div>',
          '<div class="ecg-card"><strong>Spark Signature</strong><br>' + esc(finalPayload.spark_signature) + '</div>',
          '<div class="ecg-card"><strong>Combat Capability</strong><br>' + esc(finalPayload.combat_capability) + '</div>',
          '<div class="ecg-card"><strong>Cast</strong><br>' + esc(finalPayload.cast) + '</div>',
          '</div>',
          '<h3>Manifested Domains</h3>',
          '<p>' + (finalPayload.manifested || []).map(esc).join(', ') + '</p>',
          '<h3>Selected Powers</h3>',
          '<div class="ecg-card-grid">' + powerCards(finalPayload) + '</div>',
          '<h3>Domain Scores</h3>',
          '<div class="ecg-card-grid">' + scoreCards(finalPayload) + '</div>'
        ].join('');

        flavorMount.innerHTML = '';
      } catch (error) {
        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
      }
    });
  }

  form.addEventListener('change', updateProgress);
  updateProgress();

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    result.innerHTML = '<p>Running domain typing pass...</p>';
    flavorMount.innerHTML = '';

    const data = new FormData(form);
    lastDomainAnswers = [];
    for (const [, value] of data.entries()) lastDomainAnswers.push(value);

    try {
      lastDomainPayload = await postGenerate({ answers: lastDomainAnswers });
      renderDomainResult(lastDomainPayload);
      renderFlavorForm(lastDomainPayload);
    } catch (error) {
      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
    }
  });
})();
