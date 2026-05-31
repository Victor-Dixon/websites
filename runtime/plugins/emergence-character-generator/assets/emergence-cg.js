(function () {
  const form = document.getElementById('emergence-cg-form');
  const result = document.getElementById('emergence-cg-result');
  const flavorMount = document.getElementById('emergence-cg-flavor');
  const progressLabel = document.getElementById('ecg-progress-label');
  const progressFill = document.getElementById('ecg-progress-fill');

  let lastDomainAnswers = [];

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

  const flavorOptions = {
    29:{A:'Invulnerability',B:'Density Control',C:'Giant Size',D:'Elasticity',E:'Unstoppable Momentum',F:'Super Strength'},
    30:{A:'Super Strength',B:'Giant Size',C:'Elasticity',D:'Invulnerability',E:'Density Control',F:'Unstoppable Momentum'},
    31:{A:'Super Strength',B:'Invulnerability',C:'Giant Size',D:'Elasticity',E:'Unstoppable Momentum',F:'Density Control'},
    32:{A:'Invulnerability',B:'Density Control',C:'Elasticity',D:'Giant Size',E:'Unstoppable Momentum',F:'Super Strength'},
    33:{A:'Super Strength',B:'Giant Size',C:'Elasticity',D:'Unstoppable Momentum',E:'Invulnerability',F:'Density Control'},

    34:{A:'Super Speed',B:'Flight',C:'Enhanced Reflexes',D:'Danger Sense',E:'Wall-Crawling',F:'Vibration Control'},
    35:{A:'Enhanced Reflexes',B:'Flight',C:'Danger Sense',D:'Super Speed',E:'Wall-Crawling',F:'Vibration Control'},
    36:{A:'Super Speed',B:'Flight',C:'Enhanced Reflexes',D:'Danger Sense',E:'Wall-Crawling',F:'Vibration Control'},
    37:{A:'Super Speed',B:'Flight',C:'Enhanced Reflexes',D:'Danger Sense',E:'Wall-Crawling',F:'Vibration Control'},
    38:{A:'Super Speed',B:'Flight',C:'Enhanced Reflexes',D:'Danger Sense',E:'Wall-Crawling',F:'Vibration Control'},

    39:{A:'Pyrokinesis',B:'Cryokinesis',C:'Concussive Blasts',D:'Electrokinesis',E:'Sonic Scream',F:'Hydrokinesis'},
    40:{A:'Pyrokinesis',B:'Cryokinesis',C:'Concussive Blasts',D:'Sonic Scream',E:'Hydrokinesis',F:'Electrokinesis'},
    41:{A:'Concussive Blasts',B:'Pyrokinesis',C:'Cryokinesis',D:'Electrokinesis',E:'Sonic Scream',F:'Hydrokinesis'},
    42:{A:'Pyrokinesis',B:'Cryokinesis',C:'Concussive Blasts',D:'Electrokinesis',E:'Sonic Scream',F:'Hydrokinesis'},
    43:{A:'Pyrokinesis',B:'Concussive Blasts',C:'Cryokinesis',D:'Electrokinesis',E:'Sonic Scream',F:'Hydrokinesis'},

    44:{A:'Portal Creation',B:'Intangibility',C:'Invisibility',D:'Shrinking',E:'Enhanced Senses',F:'Teleportation'},
    45:{A:'Enhanced Senses',B:'Invisibility',C:'Teleportation',D:'Portal Creation',E:'Shrinking',F:'Intangibility'},
    46:{A:'Intangibility',B:'Teleportation',C:'Shrinking',D:'Invisibility',E:'Enhanced Senses',F:'Portal Creation'},
    47:{A:'Intangibility',B:'Shrinking',C:'Invisibility',D:'Portal Creation',E:'Enhanced Senses',F:'Teleportation'},
    48:{A:'Intangibility',B:'Shrinking',C:'Portal Creation',D:'Enhanced Senses',E:'Teleportation',F:'Invisibility'},

    49:{A:'Laser Light',B:'Energy Absorption',C:'Shadow Control',D:'Toxic Emission',E:'Void Grasp',F:'Hard Light'},
    50:{A:'Energy Absorption',B:'Toxic Emission',C:'Void Grasp',D:'Hard Light',E:'Laser Light',F:'Shadow Control'},
    51:{A:'Shadow Control',B:'Toxic Emission',C:'Void Grasp',D:'Hard Light',E:'Laser Light',F:'Energy Absorption'},
    52:{A:'Toxic Emission',B:'Void Grasp',C:'Hard Light',D:'Laser Light',E:'Energy Absorption',F:'Shadow Control'},
    53:{A:'Void Grasp',B:'Hard Light',C:'Laser Light',D:'Energy Absorption',E:'Shadow Control',F:'Toxic Emission'},

    54:{A:'Force Fields',B:'Healing Factor',C:'Gravity Control',D:'Magnetism',E:'Duplication',F:'Kinetic Manipulation'},
    55:{A:'Force Fields',B:'Healing Factor',C:'Gravity Control',D:'Kinetic Manipulation',E:'Magnetism',F:'Duplication'},
    56:{A:'Duplication',B:'Gravity Control',C:'Kinetic Manipulation',D:'Force Fields',E:'Magnetism',F:'Healing Factor'},
    57:{A:'Healing Factor',B:'Gravity Control',C:'Force Fields',D:'Magnetism',E:'Duplication',F:'Kinetic Manipulation'},
    58:{A:'Force Fields',B:'Duplication',C:'Healing Factor',D:'Gravity Control',E:'Kinetic Manipulation',F:'Magnetism'},

    59:{A:'Animal Form',B:'Nature Control',C:'Adaptive Biology',D:'Weather Control',E:'Pheromone Control',F:'Shapeshifting'},
    60:{A:'Adaptive Biology',B:'Weather Control',C:'Animal Form',D:'Pheromone Control',E:'Shapeshifting',F:'Nature Control'},
    61:{A:'Adaptive Biology',B:'Nature Control',C:'Animal Form',D:'Shapeshifting',E:'Pheromone Control',F:'Weather Control'},
    62:{A:'Nature Control',B:'Pheromone Control',C:'Animal Form',D:'Weather Control',E:'Adaptive Biology',F:'Shapeshifting'},
    63:{A:'Shapeshifting',B:'Nature Control',C:'Adaptive Biology',D:'Pheromone Control',E:'Animal Form',F:'Weather Control'},

    64:{A:'Telepathy',B:'Illusion',C:'Psychic Assault',D:'Psychic Defense',E:'Telekinesis',F:'Mind Control'},
    65:{A:'Telepathy',B:'Mind Control',C:'Psychic Defense',D:'Telekinesis',E:'Illusion',F:'Psychic Assault'},
    66:{A:'Psychic Assault',B:'Illusion',C:'Mind Control',D:'Telekinesis',E:'Telepathy',F:'Psychic Defense'},
    67:{A:'Mind Control',B:'Illusion',C:'Psychic Assault',D:'Telepathy',E:'Telekinesis',F:'Psychic Defense'},
    68:{A:'Mind Control',B:'Psychic Defense',C:'Illusion',D:'Telepathy',E:'Psychic Assault',F:'Telekinesis'}
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
      headers: {'Content-Type': 'application/json', 'X-WP-Nonce': EmergenceCG.nonce},
      body: JSON.stringify(body)
    });
    const payload = await response.json();
    if (!response.ok) throw new Error(payload.message || 'Generator failed');
    return payload;
  }

  function scoreCards(payload) {
    return Object.entries(payload.scores || {}).map(function (pair) {
      const tier = payload.tiers && payload.tiers[pair[0]] ? payload.tiers[pair[0]] : '?';
      return '<div class="ecg-card"><strong>' + esc(pair[0]) + '</strong><br>Score ' + esc(pair[1]) + ' · Tier ' + esc(tier) + '</div>';
    }).join('');
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
        const opts = flavorOptions[q] || {};
        return [
          '<fieldset class="ecg-question">',
          '<legend>Q' + q + ' — ' + esc(domain) + ' flavor choice</legend>',
          '<select name="' + q + '" required>',
          '<option value="">Choose one...</option>',
          '<option value="A">A — ' + esc(opts.A || 'Option A') + '</option>',
          '<option value="B">B — ' + esc(opts.B || 'Option B') + '</option>',
          '<option value="C">C — ' + esc(opts.C || 'Option C') + '</option>',
          '<option value="D">D — ' + esc(opts.D || 'Option D') + '</option>',
          '<option value="E">E — ' + esc(opts.E || 'Option E') + '</option>',
          '<option value="F">F — ' + esc(opts.F || 'Option F') + '</option>',
          '</select>',
          '</fieldset>'
        ].join('');
      }).join('');
      return '<div class="ecg-explainer"><h2>' + esc(domain) + ' Flavor Block</h2>' + fields + '</div>';
    }).join('');

    flavorMount.innerHTML = [
      '<form id="emergence-cg-flavor-form" class="ecg-form">',
      '<h2>Pass 2: Flavor Power Selection</h2>',
      '<p>Only manifested domains get flavor questions. The options now show the actual power labels they push toward.</p>',
      blocks,
      '<button type="submit">Generate Character Sheet</button>',
      '</form>'
    ].join('');

    const flavorForm = document.getElementById('emergence-cg-flavor-form');
    flavorForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      result.innerHTML += '<p>Building character sheet...</p>';

      const data = new FormData(flavorForm);
      const flavorAnswers = {};
      for (const [q, value] of data.entries()) flavorAnswers[q] = value;

      try {
        const finalPayload = await postGenerate({answers: lastDomainAnswers, flavor_answers: flavorAnswers});
        const sheet = finalPayload.character_sheet || {};
        const selectedPowers = sheet.selected_powers || [];

        result.innerHTML = [
          '<article class="ecg-character-sheet">',
          '<p class="ecg-kicker">Spark Profile</p>',
          '<h2>' + esc(sheet.title || 'Unnamed Spark') + '</h2>',
          '<p class="ecg-result-note">' + esc(sheet.summary || 'Profile summary unavailable.') + '</p>',
          '<div class="ecg-card-grid">',
          '<div class="ecg-card"><strong>Archetype</strong><br>' + esc(sheet.archetype || 'Unresolved') + '</div>',
          '<div class="ecg-card"><strong>Signature Line</strong><br>' + esc(sheet.signature_line || 'Unresolved') + '</div>',
          '<div class="ecg-card"><strong>Lead Domain</strong><br>' + esc(finalPayload.lead_domain || 'Unresolved') + '</div>',
          '<div class="ecg-card"><strong>Cast</strong><br>' + esc(finalPayload.cast || 'Unresolved') + '</div>',
          '</div>',
          '<h3>Selected Powers</h3>',
          '<p>' + selectedPowers.map(esc).join(', ') + '</p>',
          '<div class="ecg-card-grid">' + powerCards(finalPayload) + '</div>',
          '<h3>Battle Readiness</h3>',
          '<p>' + esc(sheet.battle_ready_note || 'Ready for next layer.') + '</p>',
          '<h3>Domain Scores</h3>',
          '<div class="ecg-card-grid">' + scoreCards(finalPayload) + '</div>',
          '</article>'
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
      const domainPayload = await postGenerate({answers: lastDomainAnswers});
      renderDomainResult(domainPayload);
      renderFlavorForm(domainPayload);
    } catch (error) {
      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
    }
  });
})();
