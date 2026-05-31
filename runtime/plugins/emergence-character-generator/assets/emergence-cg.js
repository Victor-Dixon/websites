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

  function stableHash(input) {
    let h = 2166136261;
    const str = String(input);
    for (let i = 0; i < str.length; i++) {
      h ^= str.charCodeAt(i);
      h = Math.imul(h, 16777619);
    }
    return h >>> 0;
  }

  function svgText(value, max) {
    const clean = String(value || '').replace(/[<>&"']/g, '').trim();
    if (!max || clean.length <= max) return clean;
    return clean.slice(0, max - 1) + '…';
  }

  function buildSparkPortraitSvg(payload) {
    const sheet = payload.character_sheet || {};
    const powers = payload.powers || [];
    const title = sheet.title || 'Unnamed Spark';
    const archetype = sheet.archetype || 'Emergent Manifest';
    const signature = payload.spark_signature || payload.provisional_spark_signature || 0;
    const combat = payload.combat_capability || payload.provisional_combat_capability || 0;
    const cast = payload.cast || 'Unclassified Spark';

    const seedParts = [
      title,
      archetype,
      cast,
      signature,
      combat,
      powers.map((p) => p.power).join('|'),
      powers.map((p) => p.tier).join('|')
    ].join('::');

    const seed = stableHash(seedParts);
    const hue = seed % 360;
    const hue2 = (hue + 42 + (seed % 80)) % 360;
    const hue3 = (hue + 180) % 360;
    const aura = Math.max(28, Math.min(88, Number(signature) || 48));
    const frame = Math.max(24, Math.min(92, Number(combat) || 45));
    const glyphCount = Math.max(3, Math.min(9, powers.length + 4));
    const silhouetteVariant = seed % 4;
    const maskVariant = (seed >>> 3) % 5;
    const starOffset = seed % 97;

    const powerOne = powers[0] ? powers[0].power : 'Latent Spark';
    const powerTwo = powers[1] ? powers[1].power : 'Awaiting Battle Input';
    const badge = cast.includes('Solo') ? 'SOLO' : cast.includes('Wild') ? 'WILD' : 'SPARK';

    const glyphs = [];
    for (let i = 0; i < glyphCount; i++) {
      const angle = (Math.PI * 2 * i) / glyphCount + (seed % 13) / 10;
      const x = 210 + Math.cos(angle) * (118 + (seed % 19));
      const y = 236 + Math.sin(angle) * (118 + ((seed >>> 5) % 21));
      const r = 5 + ((seed >>> (i % 12)) & 5);
      glyphs.push('<circle cx="' + x.toFixed(1) + '" cy="' + y.toFixed(1) + '" r="' + r + '" class="ecg-svg-glyph"/>');
    }

    const shoulders =
      silhouetteVariant === 0 ? 'M125 397 C145 342 175 323 210 323 C245 323 276 342 295 397 Z' :
      silhouetteVariant === 1 ? 'M103 397 C130 336 166 316 210 326 C253 316 290 336 317 397 Z' :
      silhouetteVariant === 2 ? 'M116 397 C137 350 154 320 210 318 C266 320 283 350 304 397 Z' :
      'M96 397 C139 345 169 330 210 330 C251 330 282 345 324 397 Z';

    const mask =
      maskVariant === 0 ? '<path d="M176 196 C189 178 231 178 244 196 C238 222 225 237 210 237 C195 237 182 222 176 196 Z" class="ecg-svg-mask"/>' :
      maskVariant === 1 ? '<path d="M170 198 L210 178 L250 198 L236 230 L210 241 L184 230 Z" class="ecg-svg-mask"/>' :
      maskVariant === 2 ? '<path d="M176 190 C199 204 221 204 244 190 C239 226 226 243 210 243 C194 243 181 226 176 190 Z" class="ecg-svg-mask"/>' :
      maskVariant === 3 ? '<path d="M168 202 C181 177 239 177 252 202 L230 239 L190 239 Z" class="ecg-svg-mask"/>' :
      '<path d="M174 192 L246 192 L233 234 L210 246 L187 234 Z" class="ecg-svg-mask"/>';

    return [
      '<svg class="ecg-spark-svg" viewBox="0 0 420 620" role="img" aria-label="' + esc(title) + ' generated Spark portrait">',
      '<defs>',
      '<linearGradient id="sparkBg' + seed + '" x1="0%" y1="0%" x2="100%" y2="100%">',
      '<stop offset="0%" stop-color="hsl(' + hue + ' 78% 16%)"/>',
      '<stop offset="55%" stop-color="hsl(' + hue2 + ' 72% 20%)"/>',
      '<stop offset="100%" stop-color="hsl(' + hue3 + ' 66% 12%)"/>',
      '</linearGradient>',
      '<radialGradient id="sparkAura' + seed + '" cx="50%" cy="38%" r="48%">',
      '<stop offset="0%" stop-color="hsl(' + hue2 + ' 95% 72%)" stop-opacity=".95"/>',
      '<stop offset="48%" stop-color="hsl(' + hue + ' 90% 55%)" stop-opacity=".34"/>',
      '<stop offset="100%" stop-color="hsl(' + hue3 + ' 85% 40%)" stop-opacity="0"/>',
      '</radialGradient>',
      '</defs>',

      '<rect x="12" y="12" width="396" height="596" rx="28" fill="url(#sparkBg' + seed + ')" class="ecg-svg-frame"/>',
      '<rect x="26" y="26" width="368" height="568" rx="22" class="ecg-svg-inner-frame"/>',
      '<circle cx="210" cy="236" r="' + (120 + aura / 2).toFixed(0) + '" fill="url(#sparkAura' + seed + ')"/>',

      '<g class="ecg-svg-stars">',
      Array.from({ length: 30 }, (_, i) => {
        const x = 40 + ((i * 53 + starOffset) % 340);
        const y = 54 + ((i * 89 + starOffset * 2) % 470);
        const r = 1 + ((i + seed) % 3);
        return '<circle cx="' + x + '" cy="' + y + '" r="' + r + '"/>';
      }).join(''),
      '</g>',

      '<g class="ecg-svg-glyph-ring">' + glyphs.join('') + '</g>',

      '<path d="' + shoulders.slice(2) + '" class="ecg-svg-shoulders"/>',
      '<path d="M172 276 C178 246 194 230 210 230 C226 230 242 246 248 276 C241 306 226 324 210 324 C194 324 179 306 172 276 Z" class="ecg-svg-torso"/>',
      '<circle cx="210" cy="194" r="48" class="ecg-svg-head"/>',
      mask,
      '<path d="M178 202 C194 212 226 212 242 202" class="ecg-svg-eye-line"/>',

      '<rect x="42" y="42" width="92" height="28" rx="14" class="ecg-svg-badge"/>',
      '<text x="88" y="61" text-anchor="middle" class="ecg-svg-badge-text">' + svgText(badge, 8) + '</text>',

      '<text x="210" y="462" text-anchor="middle" class="ecg-svg-title">' + svgText(title, 26) + '</text>',
      '<text x="210" y="488" text-anchor="middle" class="ecg-svg-subtitle">' + svgText(archetype, 34) + '</text>',

      '<g class="ecg-svg-bars">',
      '<text x="62" y="526" class="ecg-svg-label">SIGNATURE</text>',
      '<rect x="154" y="513" width="198" height="12" rx="6" class="ecg-svg-bar-bg"/>',
      '<rect x="154" y="513" width="' + Math.max(18, Math.min(198, signature * 1.98)).toFixed(0) + '" height="12" rx="6" class="ecg-svg-bar-fill"/>',
      '<text x="62" y="552" class="ecg-svg-label">COMBAT</text>',
      '<rect x="154" y="539" width="198" height="12" rx="6" class="ecg-svg-bar-bg"/>',
      '<rect x="154" y="539" width="' + Math.max(18, Math.min(198, combat * 1.98)).toFixed(0) + '" height="12" rx="6" class="ecg-svg-bar-fill alt"/>',
      '</g>',

      '<text x="210" y="580" text-anchor="middle" class="ecg-svg-powerline">' + svgText(powerOne, 24) + ' · ' + svgText(powerTwo, 24) + '</text>',
      '</svg>'
    ].join('');
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
      '<div class="ecg-generated-portrait" data-render="deterministic-svg">',
      buildSparkPortraitSvg(finalPayload),
      '</div>',

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
