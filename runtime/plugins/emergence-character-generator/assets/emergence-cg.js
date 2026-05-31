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

  const questionBank = (window.EmergenceCG && window.EmergenceCG.question_bank) || {};
  const flavorQuestions = Array.isArray(questionBank.flavor_questions) ? questionBank.flavor_questions : [];

  if (!form || !result || !flavorMount || !window.EmergenceCG) {
    console.error('[EmergenceCG] bootstrap failed', {
      form: !!form,
      result: !!result,
      flavorMount: !!flavorMount,
      config: !!window.EmergenceCG
    });
    return;
  }

  function esc(value) {
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  function flavorQuestion(q) {
    return flavorQuestions.find((item) => Number(item.q) === Number(q)) || null;
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

    if (!response.ok) {
      throw new Error(payload.message || 'Generator failed');
    }

    return payload;
  }

  function debugSummary(payload) {
    const scores = payload.scores || {};
    const manifested = payload.manifested || [];
    return {
      phase: payload.phase || 'unknown',
      manifested: manifested,
      lead_domain: payload.lead_domain || null,
      score_keys: Object.keys(scores)
    };
  }

  function powerCards(payload) {
    return (payload.powers || []).map(function (p) {
      return '<div class="ecg-card"><strong>' + esc(p.power) + '</strong><br>' +
        esc(p.domain) + ' · Tier ' + esc(p.tier) + ' · ' + esc(p.selection) + '</div>';
    }).join('');
  }

  function buildProfileTone(payload) {
    const powers = payload.powers || [];
    const leadPower = powers.length ? powers[0].power : 'Latent Spark';
    const cast = payload.cast || 'Unclassified Spark';

    return {
      codename: (payload.character_sheet && payload.character_sheet.title) || (leadPower + ' Spark'),
      cast: cast,
      leadPower: leadPower,
      role: powers.length > 1 ? 'multi-vector combatant' : 'focused specialist',
      hook: 'Your Spark does not reveal itself as a wish list. It emerges from pressure, instinct, and the choices you made when the system stopped showing you the key.',
      fieldNote: 'This profile is ready for the next layer: arena conditions, matchup logic, and cinematic battle resolution.'
    };
  }

  function renderCharacterProfile(finalPayload) {
    const sheet = finalPayload.character_sheet || {};
    const powers = finalPayload.powers || [];
    const tone = buildProfileTone(finalPayload);
    const selectedPowers = sheet.selected_powers || powers.map(function (p) { return p.power; });

    const powerList = powers.map(function (p, index) {
      return [
        '<li>',
        '<span class="ecg-power-rank">Power ' + esc(index + 1) + '</span>',
        '<strong>' + esc(p.power) + '</strong>',
        '<small>Tier ' + esc(p.tier) + ' affinity</small>',
        '</li>'
      ].join('');
    }).join('');

    result.innerHTML = [
      '<article class="ecg-character-sheet ecg-profile-card">',
      '<div class="ecg-profile-hero">',
      '<p class="ecg-kicker">Spark Profile Generated</p>',
      '<h2>' + esc(tone.codename) + '</h2>',
      '<p class="ecg-profile-summary">' + esc(sheet.summary || tone.hook) + '</p>',
      '</div>',

      '<div class="ecg-profile-grid">',
      '<section class="ecg-profile-panel">',
      '<h3>Identity</h3>',
      '<p><strong>Archetype:</strong> ' + esc(sheet.archetype || 'Unresolved Manifest') + '</p>',
      '<p><strong>Cast:</strong> ' + esc(tone.cast) + '</p>',
      '<p><strong>Combat Role:</strong> ' + esc(tone.role) + '</p>',
      '</section>',

      '<section class="ecg-profile-panel">',
      '<h3>Combat Readiness</h3>',
      '<p>' + esc(sheet.signature_line || 'Signature unresolved') + '</p>',
      '<p>' + esc(sheet.battle_ready_note || tone.fieldNote) + '</p>',
      '</section>',
      '</div>',

      '<section class="ecg-profile-panel ecg-profile-wide">',
      '<h3>Manifested Abilities</h3>',
      powers.length ? '<ul class="ecg-power-list">' + powerList + '</ul>' : '<p>No public powers selected yet.</p>',
      '</section>',

      '<section class="ecg-profile-panel ecg-profile-wide">',
      '<h3>Story Hook</h3>',
      '<p>' + esc(tone.hook) + '</p>',
      '</section>',

      '<div class="ecg-profile-actions">',
      '<a href="/battle-simulator/" class="ecg-profile-cta">Use this Spark in Battle Simulator</a>',
      '<button type="button" class="ecg-secondary-action" onclick="window.location.reload()">Generate Another Spark</button>',
      '</div>',
      '</article>'
    ].join('');

    flavorMount.dataset.phase = 'complete';
    flavorMount.innerHTML = '';
    result.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  function renderDomainResult(payload) {
    result.innerHTML = [
      '<h2>Pass 1 Complete: Spark Type Scan</h2>',
      '<p class="ecg-result-note">Your manifested domains are resolved. No powers have been selected yet.</p>',
      '<div class="ecg-card-grid">',
      '<div class="ecg-card"><strong>Lead Domain</strong><br>' + esc(payload.lead_domain || 'Unresolved') + '</div>',
      '<div class="ecg-card"><strong>Profile Shape</strong><br>' + esc(payload.profile_shape || 'Unresolved') + '</div>',
      '<div class="ecg-card"><strong>Power Selection</strong><br>Locked until Pass 2</div>',
      '</div>',
      '<h3>Pass 2 Status</h3>',
      '<p>Your scan unlocked a private set of follow-up questions.</p>'
    ].join('');
  }

  function renderFlavorForm(payload) {
    const manifested = payload.manifested || [];

    if (!manifested.length) {
      flavorMount.dataset.phase = 'error';
      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 could not unlock</h2><p>No manifested domains were returned by the scan.</p></div>';
      return;
    }

    const blocks = manifested.map(function (domain, index) {
      const qs = flavorBlocks[domain] || [];

      const fields = qs.map(function (q) {
        const fq = flavorQuestion(q);
        const opts = (fq && fq.options) || {};
        const question = fq ? fq.question : (domain + ' flavor choice');

        return [
          '<fieldset class="ecg-question">',
          '<legend>Q' + q + ' — ' + esc(question) + '</legend>',
          '<select name="' + q + '" required>',
          '<option value="">Choose one...</option>',
          ['A','B','C','D','E','F'].map(function (letter) {
            if (!opts[letter]) return '';
            return '<option value="' + letter + '">' + letter + '. ' + esc(opts[letter]) + '</option>';
          }).join(''),
          '</select>',
          '</fieldset>'
        ].join('');
      }).join('');

      return '<div class="ecg-explainer ecg-flavor-block"><h2>Unlocked Flavor Block ' + esc(String(index + 1)) + '</h2>' + fields + '</div>';
    }).join('');

    flavorMount.dataset.phase = 'unlocked';
    flavorMount.innerHTML = [
      '<form id="emergence-cg-flavor-form" class="ecg-form ecg-pass-two-form">',
      '<h2>Pass 2 Unlocked: Flavor Power Selection</h2>',
      '<p><strong>Your follow-up questions are unlocked.</strong> Answer them without seeing which domains they came from.</p>',
      '<p class="ecg-result-note">Unlocked follow-up blocks are based on your scan. The domains stay hidden.</p>',
      blocks,
      '<button type="submit">Generate Character Sheet</button>',
      '</form>'
    ].join('');

    const flavorForm = document.getElementById('emergence-cg-flavor-form');

    if (!flavorForm) {
      console.error('[EmergenceCG] flavor form failed to mount');
      return;
    }

    flavorMount.scrollIntoView({behavior: 'smooth', block: 'start'});

    flavorForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      result.innerHTML += '<p>Building character sheet...</p>';

      const data = new FormData(flavorForm);
      const flavorAnswers = {};

      for (const [q, value] of data.entries()) {
        flavorAnswers[q] = value;
      }

      try {
        const finalPayload = await postGenerate({
          answers: lastDomainAnswers,
          flavor_answers: flavorAnswers
        });

        console.info('[EmergenceCG] flavor pass debug', debugSummary(finalPayload));
        renderCharacterProfile(finalPayload);
      } catch (error) {
        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
      }
    });
  }

  form.addEventListener('change', updateProgress);
  updateProgress();

  form.addEventListener('submit', async function (event) {
    event.preventDefault();

    result.innerHTML = '<p>Running Spark Type Scan...</p>';
    flavorMount.dataset.phase = 'loading';
    flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Preparing Pass 2...</h2><p>Manifested-domain flavor questions will appear here after the scan.</p></div>';

    const data = new FormData(form);
    lastDomainAnswers = [];

    for (const [, value] of data.entries()) {
      lastDomainAnswers.push(value);
    }

    try {
      const domainPayload = await postGenerate({answers: lastDomainAnswers});

      if (!domainPayload || domainPayload.phase !== 'domain_typing') {
        throw new Error('Domain scan did not return phase=domain_typing');
      }

      console.info('[EmergenceCG] domain pass debug', debugSummary(domainPayload));
      renderDomainResult(domainPayload);
      renderFlavorForm(domainPayload);
    } catch (error) {
      flavorMount.dataset.phase = 'error';
      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 failed to unlock</h2><p>' + esc(error.message) + '</p></div>';
      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
    }
  });
})();
