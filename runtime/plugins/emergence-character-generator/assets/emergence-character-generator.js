(function () {
  const form = document.getElementById('emergence-cg-form');
  const result = document.getElementById('emergence-cg-result');
  const flavorMount = document.getElementById('emergence-cg-flavor');
  const progressLabel = document.getElementById('ecg-progress-label');
  const progressFill = document.getElementById('ecg-progress-fill');

  let lastDomainAnswers = [];
  let pendingFinalPayload = null;

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

  function compileVisualMotifs(powers) {
    const motifMap = {
      'Laser Light': 'focused radiant beams, prism-cut highlights, bright edge lighting, precise luminous blasts',
      'Hard Light': 'translucent geometric armor plates, solid-light shields, constructed weapons, angular glowing structures',
      'Energy Absorption': 'energy sinking into the suit, glowing intake seams, impact halos being absorbed into the body',
      'Shadow Control': 'controlled shadow tendrils, negative-space cloak shapes, dark environmental distortion',
      'Toxic Emission': 'hazardous mist aura, corrosive color accents, controlled vapor trails around hands and shoulders',
      'Void Grasp': 'black-violet pressure fields, gravity-like hand effects, collapsing space distortion',

      'Telekinesis': 'floating debris, invisible force pressure, objects orbiting the body, bent metal suspended midair',
      'Telepathy': 'subtle psychic halo, glowing eyes, thought-wave distortions around the head',
      'Psychic Defense': 'crystalline mental shield geometry, calm focused expression, transparent protective aura',
      'Mind Control': 'hypnotic ring motifs, commanding gaze, subtle psychic thread effects',

      'Super Strength': 'dense heroic stance, reinforced gloves, cracked ground, heavy silhouette, compressed force',
      'Invulnerability': 'impact sparks breaking around the body, armored posture, unbroken forward stance',
      'Density Control': 'heavy gravity posture, dense body outline, pressure cracks beneath the feet',
      'Giant Size': 'towering scale cues, low camera angle, massive shoulders, environmental scale contrast',
      'Elasticity': 'dynamic stretched limbs, flexible silhouette, motion arcs',
      'Unstoppable Momentum': 'forward-driving pose, shockwave trail, motion pressure',

      'Flight': 'wind lift, upward body angle, cape-or-coat motion, airborne energy trail',
      'Danger Sense': 'alert posture, reactive motion lines, precognitive visual echoes',
      'Super Speed': 'speed trails, blurred afterimages, lightning-like motion streaks',
      'Phase Shift': 'partially intangible body edges, passing-through distortion',
      'Invisibility': 'refracted outline, fading edges, partial transparency transition',
      'Portal Step': 'threshold ring, spatial doorway, stepping through warped light',

      'Pyrokinesis': 'controlled flame halo, ember lighting, heat distortion, fire wrapped around hands',
      'Concussive Blasts': 'impact shock rings, compressed blast energy, explosive hand projection',
      'Kinetic Manipulation': 'motion vectors, redirected force trails, impact arcs',
      'Force Fields': 'transparent barrier planes, protective geometry, glowing shield curvature',
      'Gravity Control': 'floating rubble, bent light, heavy pressure field, distorted horizon',
      'Magnetism': 'metal fragments orbiting, magnetic field arcs, armored metal accents',

      'Shapeshifting': 'adaptive costume panels, shifting silhouette edges, organic transformation hints',
      'Animal Communication': 'subtle animal-spirit silhouettes, instinctive nature aura',
      'Plant Control': 'living vine motifs, botanical energy, growth wrapping around costume',
      'Elemental Adaptation': 'mixed natural elements orbiting the body',
      'Healing Factor': 'regenerative glow, restored fabric/skin effects, life-energy pulse',
      'Beast Form': 'feral silhouette cues, clawed gauntlets, predatory posture',

      'Technopathy': 'holographic circuitry, machine-light interface, data glyphs without text',
      'Probability Shift': 'lucky distortion, improbable debris paths, chance-wave visual arcs',
      'Force Multiplication': 'echoed silhouettes, duplicated strike trails, multiplied impact forms',
      'Matter Reshape': 'objects reforming around the hands, material transformation effects',
      'Time Sense': 'clockless time distortion, layered motion ghosts, temporal shimmer',
      'Spatial Fold': 'folded background geometry, compressed space lines, impossible perspective'
    };

    const motifs = (powers || []).map(function (p) {
      return motifMap[p.power] || (p.power + ' expressed visually through original aura, posture, costume details, and environmental effects');
    });

    return motifs.length ? motifs.join('; ') : 'latent power shown through aura, posture, costume symbolism, and environmental tension';
  }

  function inferCombatRole(payload) {
    const powers = payload.powers || [];
    const names = powers.map(function (p) { return p.power; }).join(' ').toLowerCase();

    if (powers.length > 2) return 'multi-vector combatant';
    if (/shield|defense|invulnerability|force field|healing|regeneration/.test(names)) return 'defensive anchor';
    if (/blast|fire|laser|strength|toxic|void|shadow|concussive/.test(names)) return 'frontline striker';
    if (/telepathy|telekinesis|control|gravity|magnetism|hard light|matter|time|spatial/.test(names)) return 'battlefield controller';
    if (/speed|flight|danger|invisibility|portal|phase/.test(names)) return 'mobile infiltrator';

    return powers.length > 1 ? 'hybrid specialist' : 'focused specialist';
  }

  function compilePlayerDesignDirection(name, cosmetics) {
    cosmetics = cosmetics || {};

    const toneMap = {
      heroic: 'hopeful heroic presence, upright posture, controlled power, aspirational energy',
      ominous: 'darker dramatic presence, intimidating stillness, controlled menace, heavy shadows',
      mythic: 'larger-than-life mythic presence, iconic silhouette, symbolic aura, legendary reveal',
      street: 'grounded street-level hero presence, practical costume details, urban intensity'
    };

    const buildMap = {
      lean: 'lean athletic build, fast silhouette, agile posture',
      powerful: 'powerful muscular build, strong shoulders, grounded heroic stance',
      compact: 'compact fighter build, dense posture, coiled energy',
      tall: 'tall imposing build, long silhouette, commanding presence',
      elegant: 'elegant refined build, graceful posture, controlled movement',
      system: 'system-chosen body build that best matches the generated powers and identity'
    };

    const costumeMap = {
      sleek: 'sleek fitted suit, clean silhouette, precise seam lines, minimal armor',
      armored: 'layered armor panels, reinforced gauntlets, protective boots, strong chest structure',
      mystical: 'ritual-like costume geometry, symbolic fabric layers, luminous trim, mythic silhouette',
      tactical: 'practical combat suit, utility seams, protective fabric, grounded heroic details',
      regal: 'regal heroic costume, mantle-like silhouette, ceremonial armor/fabric balance',
      balanced: 'hybrid superhero costume with fabric, armor accents, gloves, boots, and a unique chest symbol'
    };

    const personalityMap = {
      calm: 'calm controlled expression, quiet confidence, restrained power',
      fierce: 'fierce determined expression, aggressive battle readiness, explosive presence',
      mysterious: 'mysterious unreadable expression, shadowed eyes, hidden intent',
      noble: 'noble protective expression, leader-like posture, guardian energy',
      playful: 'confident playful expression, mischievous energy, stylish swagger',
      haunted: 'haunted intense expression, emotional weight, survival-driven presence'
    };

    const showcaseMap = {
      subtle: 'subtle ability showcase, powers hinted through aura and costume details',
      active: 'active ability showcase, visible power effects around hands/body/background',
      dramatic: 'dramatic ability showcase, cinematic energy surge and strong environmental reaction',
      restrained: 'restrained ability showcase, controlled effects with disciplined intensity'
    };

    return [
      'visual tone: ' + (toneMap[cosmetics.tone] || toneMap.heroic),
      'body/build type: ' + (buildMap[cosmetics.build] || buildMap.system),
      'costume type: ' + (costumeMap[cosmetics.costume] || costumeMap.balanced),
      'mask direction: ' + (cosmetics.mask || 'system-chosen mask treatment'),
      'personality presentation: ' + (personalityMap[cosmetics.personality] || personalityMap.calm),
      'ability showcase intensity: ' + (showcaseMap[cosmetics.showcase] || showcaseMap.active),
      'image framing: ' + (cosmetics.frame || 'three-quarter portrait'),
      'unique chest symbol inspired by the name "' + name + '", not a franchise logo'
    ].join('; ');
  }

  function compilePremiumPortraitPrompt(payload, name, cosmetics) {
    cosmetics = cosmetics || {};

    const sheet = payload.character_sheet || {};
    const powers = payload.powers || [];
    const powerNames = powers.map(function (p) { return p.power; }).filter(Boolean);
    const visualMotifs = compileVisualMotifs(powers);
    const title = name || sheet.title || 'Unnamed Spark';
    const archetype = sheet.archetype || 'emergent superhero archetype';
    const cast = payload.cast || 'unclassified Spark';
    const role = inferCombatRole(payload);
    const profileShape = payload.profile_shape || 'identity-driven power profile';
    const playerDesign = compilePlayerDesignDirection(title, cosmetics);

    return [
      'Create a premium original superhero character portrait for a new hero named "' + title + '".',
      'FULL BODY REVEAL STANDARD: show the complete character from head to toe, full costume visible, heroic stance, no cropped portrait, no bust-only portrait, no face-only portrait.',
      'This character comes from a deterministic psychological power system, not a wish-list creator. The design should feel like the powers emerged from personality, pressure, instinct, and identity.',
      'STYLE: premium American superhero comic-book aesthetic, bold inked linework, high-end painted comic cover finish, dramatic cinematic lighting, strong rim light, dynamic shadow shapes, heroic costume design, cover-art composition, mythic but grounded.',
      'CHARACTER IDENTITY: name "' + title + '"; archetype "' + archetype + '"; cast type "' + cast + '"; combat role "' + role + '"; profile shape "' + profileShape + '".',
      'POWERS TO VISUALLY SHOWCASE: ' + (powerNames.length ? powerNames.join(', ') : 'latent unresolved abilities') + '.',
      'CUSTOM COSTUME DIRECTION: ' + (tone.costume || 'system-designed heroic costume') + '.',
      'CUSTOM PERSONALITY / ATTITUDE: ' + (tone.personality || 'system-interpreted heroic personality') + '.',
      'COMPOSITION: full-body reveal, complete head-to-toe superhero design, readable silhouette, costume and abilities visible in one image.',
      'ABILITY VISUALIZATION: ' + visualMotifs + '.',
      'PLAYER DESIGN DIRECTION: ' + playerDesign + '.',
      'POSE: battle-ready reveal pose, intense expression, controlled power, designed like the first official dossier image.',
      'COSTUME DESIGN: original superhero costume, no existing franchise logos, no copied characters, layered materials, believable seams, gloves, boots, torso structure, one memorable silhouette feature.',
      'BACKGROUND: abstract battle-ready energy field, dramatic atmosphere, subtle environmental distortion, no copyrighted settings.',
      'DOSSIER CONSISTENCY: this is the official premium hero image for the generated Spark profile.',
      'DO NOT INCLUDE: text labels, UI elements, stat tables, watermarks, raw scores, domain names, manifest thresholds, backend terms, debug output, questionnaire references, Marvel, DC, Spider-Man, Batman, Superman, X-Men, Avengers, Justice League, or any existing character likeness.'
    ].join(' ');
  }


  function renderTotalityObservation(finalPayload) {
    pendingFinalPayload = finalPayload;

    const previewPayload = Object.assign({}, finalPayload, {
      character_sheet: Object.assign({}, finalPayload.character_sheet || {}, {
        title: 'Awaiting Name',
        summary: 'The Spark pattern is complete. Totality Observation gives it identity.'
      })
    });

    result.innerHTML = [
      '<div class="ecg-generated-portrait ecg-preview-portrait" data-render="deterministic-svg-preview">',
      buildSparkPortraitSvg(previewPayload),
      '</div>',
      '<section class="ecg-profile-panel ecg-profile-wide ecg-totality-panel">',
      '<p class="ecg-kicker">Totality Observation</p>',
      '<h2>Name the Spark</h2>',
      '<p>The system has enough signal to see the full pattern. Give the Spark a name or alias before the final dossier is born.</p>',
      '<form id="emergence-totality-form" class="ecg-totality-form">',
      '<label for="emergence-spark-name"><strong>Character name / alias</strong></label>',
      '<input id="emergence-spark-name" name="spark_name" type="text" minlength="2" maxlength="64" required placeholder="Example: The Prism Warden">',

      '<div class="ecg-cosmetic-grid">',
      '<label>Visual Tone<select id="emergence-tone-style"><option value="heroic">Heroic</option><option value="ominous">Ominous</option><option value="mythic">Mythic</option><option value="street">Street-level</option></select></label>',
      '<label>Build Type<select id="emergence-build-style"><option value="system">System-chosen</option><option value="lean">Lean athletic</option><option value="powerful">Powerful</option><option value="compact">Compact fighter</option><option value="tall">Tall imposing</option><option value="elegant">Elegant refined</option></select></label>',
      '<label>Costume Concept<input id="emergence-costume-style" type="text" maxlength="160" placeholder="Example: armored hooded suit, sleek tactical jacket, cosmic cape, cracked gold mask"></label>',
      '<label>Mask<select id="emergence-mask-style"><option value="system-chosen mask treatment">System-chosen</option><option value="masked face, original mask shape">Masked</option><option value="unmasked face, clear expression">Unmasked</option><option value="partial mask or visor">Partial mask / visor</option></select></label>',
      '<label>Personality / Attitude<input id="emergence-personality-style" type="text" maxlength="120" placeholder="Example: stoic protector, cocky street hero, haunted survivor, noble guardian"></label>',
      '<label>Ability Showcase<select id="emergence-showcase-style"><option value="active">Active effects</option><option value="subtle">Subtle hints</option><option value="dramatic">Dramatic surge</option><option value="restrained">Restrained control</option></select></label>',
      '</div>',
      '<button type="button" data-ecg-action="create-final-dossier">Create Final Dossier</button>',
      '</form>',
      '</section>'
    ].join('');

    flavorMount.dataset.phase = 'totality_observation';
    flavorMount.innerHTML = '';

    const totalityForm = document.getElementById('emergence-totality-form');
    totalityForm.addEventListener('submit', function (event) {
    if (event && event.preventDefault) { event.preventDefault(); }
      event.preventDefault();
      const nameInput = document.getElementById('emergence-spark-name');
      const sparkName = String(nameInput.value || '').trim();

      if (sparkName.length < 2) {
        nameInput.focus();
        return;
      }

      const cosmetics = {
        tone: document.getElementById('emergence-tone-style').value,
        build: document.getElementById('emergence-build-style').value,
        costume: (document.getElementById('emergence-costume-style').value || 'system-designed heroic costume'),
        mask: document.getElementById('emergence-mask-style').value,
        personality: (document.getElementById('emergence-personality-style').value || 'system-interpreted heroic personality'),
        showcase: document.getElementById('emergence-showcase-style').value,
      };

      const namedPayload = Object.assign({}, pendingFinalPayload, {
        character_sheet: Object.assign({}, pendingFinalPayload.character_sheet || {}, {
          title: sparkName
        }),
        spark_name: sparkName,
        cosmetic_direction: cosmetics,
        premium_portrait_prompt: compilePremiumPortraitPrompt(pendingFinalPayload, sparkName, cosmetics)
      });

      renderCharacterProfile(namedPayload);
    });

    const premiumButton = document.getElementById('ecg-generate-premium-image');
    if (premiumButton) {
      premiumButton.addEventListener('click', async function () {
        premiumButton.disabled = true;
        premiumButton.textContent = 'Checking provider…';
        try {
          const providerResult = await requestPremiumHeroImage(finalPayload);
          renderPremiumImageProviderResult(providerResult);
        } catch (error) {
          renderPremiumImageProviderResult({status: 'error', message: 'Provider request failed.'});
        } finally {
          premiumButton.disabled = false;
          premiumButton.textContent = 'Generate Premium Hero Image';
        }
      });
    }


    const battleButton = document.getElementById('ecg-export-to-battle');
    if (battleButton) {
      battleButton.addEventListener('click', function () {
        const status = document.getElementById('ecg-battle-handoff-status');
        try {
          const payload = storeBattleHandoffPayload(finalPayload);
          if (status) {
            status.textContent = 'Spark exported: ' + (payload.spark_name || payload.title) + '. Opening Battle Simulator...';
          }
          window.location.href = '/battles/?spark_handoff=1';
        } catch (error) {
          if (status) {
            status.textContent = 'Export failed: unsafe payload blocked.';
          }
        }
      });
    }

    result.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  async function requestPremiumHeroImage(payload) {
    const prompt = payload.premium_portrait_prompt || '';
    const sparkName = payload.spark_name || (payload.character_sheet && payload.character_sheet.title) || '';

    const response = await fetch('/wp-json/emergence/v1/portrait', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        spark_name: sparkName,
        premium_portrait_prompt: prompt
      })
    });

    return await response.json();
  }

  function renderPremiumImageProviderResult(data) {
    const mount = document.getElementById('ecg-premium-image-provider-result');
    if (!mount) return;

    if (!data || data.status === 'error') {
      mount.innerHTML = '<p class="ecg-provider-error">Premium portrait request failed. The SVG fallback remains available.</p>';
      return;
    }

    if (data.image_url) {
      mount.innerHTML = [
        '<figure class="ecg-premium-generated-image">',
        '<img src="' + esc(data.image_url) + '" alt="' + esc(data.spark_name || 'Generated Spark portrait') + '">',
        '<figcaption>Premium hero portrait generated.</figcaption>',
        '</figure>'
      ].join('');
      return;
    }

    mount.innerHTML = [
      '<div class="ecg-provider-fallback">',
      '<p><strong>Premium image provider:</strong> ' + esc(data.status || 'prompt-only') + '</p>',
      '<p>' + esc(data.message || 'Prompt-only fallback is active. SVG card remains available.') + '</p>',
      '</div>'
    ].join('');
  }


  function buildBattleHandoffPayload(finalPayload) {
    const sheet = finalPayload.character_sheet || {};
    const powers = (finalPayload.powers || []).map(function (power) {
      return {
        domain: power.domain || '',
        power: power.power || '',
        lead: !!power.lead
      };
    });

    return {
      version: 1,
      source: 'emergence-character-generator',
      created_at: new Date().toISOString(),
      spark_name: finalPayload.spark_name || sheet.title || 'Unnamed Spark',
      title: sheet.title || finalPayload.spark_name || 'Unnamed Spark',
      archetype: sheet.archetype || '',
      summary: sheet.summary || '',
      cast: finalPayload.cast || '',
      profile_shape: finalPayload.profile_shape || '',
      selected_powers: powers,
      battle_ready_note: sheet.battle_ready_note || 'This Spark is ready for battle simulation.',
      visual_prompt_present: !!finalPayload.premium_portrait_prompt
    };
  }

  function storeBattleHandoffPayload(finalPayload) {
    const payload = buildBattleHandoffPayload(finalPayload);

    const forbiddenKeys = [
      'scores',
      'tiers',
      'manifest_threshold',
      'flavor_vectors',
      'spark_signature',
      'combat_capability',
      'provisional_spark_signature',
      'provisional_combat_capability',
      'debug',
      'raw',
      'roll',
      'odds'
    ];

    const serialized = JSON.stringify(payload);
    forbiddenKeys.forEach(function (key) {
      if (serialized.indexOf(key) !== -1) {
        throw new Error('Unsafe battle handoff payload contains hidden key: ' + key);
      }
    });

    window.localStorage.setItem('emergence_spark_battle_handoff_v1', serialized);
    return payload;
  }

  function renderBattleHandoffCTA(finalPayload) {
    return [
      '<section class="ecg-profile-panel ecg-battle-handoff-panel">',
      '<p class="ecg-kicker">Battle Ready</p>',
      '<h3>Use this Spark in Battle Simulator</h3>',
      '<p>Export a player-safe version of this dossier into the battle simulator. Backend scoring stays hidden.</p>',
      '<button type="button" id="ecg-export-to-battle">Use this Spark in Battle Simulator</button>',
      '<p id="ecg-battle-handoff-status" class="ecg-battle-handoff-status" aria-live="polite"></p>',
      '</section>'
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

      '<section class="ecg-profile-panel ecg-profile-wide ecg-premium-prompt-panel">',
      '<h3>Premium Hero Portrait Prompt</h3>',
      '<p>This prompt is ready for premium image generation after naming. It avoids franchise names, raw scores, and hidden routing.</p>',
      '<textarea readonly class="ecg-premium-prompt">' + esc(finalPayload.premium_portrait_prompt || compilePremiumPortraitPrompt(finalPayload, tone.codename)) + '</textarea>',
      '<div class="ecg-premium-provider-actions">',
      '<button type="button" id="ecg-generate-premium-image">Generate Premium Hero Image</button>',
      '</div>',
      '<div id="ecg-premium-image-provider-result" class="ecg-premium-image-provider-result" aria-live="polite"></div>',
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
    const previewPayload = Object.assign({}, payload, {
      character_sheet: {
        title: 'Unobserved Spark',
        archetype: 'Pre-Dossier Signal',
        summary: 'The system has resolved enough of your Spark pattern to generate a visual preview. The full dossier remains locked until Totality Observation.',
        signature_line: 'Signature forming · Combat profile pending'
      },
      powers: [],
      cast: payload.cast || 'Unclassified Spark'
    });

    result.innerHTML = [
      '<div class="ecg-generated-portrait ecg-preview-portrait" data-render="deterministic-svg-preview">',
      buildSparkPortraitSvg(previewPayload),
      '</div>',
      '<section class="ecg-profile-panel ecg-profile-wide">',
      '<h2>Pass 1 Complete: Spark Preview Formed</h2>',
      '<p class="ecg-result-note">The system has unlocked a private set of follow-up questions. The routing stays hidden until your final dossier is born.</p>',
      '<p>Your free SVG preview is a temporary signal card, not the final superhero image.</p>',
      '</section>'
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
        renderTotalityObservation(finalPayload);
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



/* DreamOS Public Spark Recovery Blocker Suppression
 * Public UX must not expose hardening/debug blocker internals.
 * Full diagnostics belong in the future AI-generated response/debug area.
 */
(function () {
  "use strict";

  if (window.__DreamOSPublicSparkRecoveryBlockerSuppression) return;
  window.__DreamOSPublicSparkRecoveryBlockerSuppression = true;

  function scrubTextNode(node) {
    if (!node || !node.nodeValue) return;
    var text = node.nodeValue;
    var next = text
      .replace(/SPARK PROTOCOL RECOVERY MODE/gi, "SPARK PROTOCOL")
      .replace(/Spark Protocol Recovery Mode/gi, "Spark Protocol")
      .replace(/The full generator interface did not mount cleanly, so this fail-open path keeps the Spark Protocol usable while the frontend is repaired\./gi, "Generate your Spark profile, then carry it into the battle loop.")
      .replace(/Reason:\s*Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
      .replace(/Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
      .replace(/Unsafe public demo hardening payload blocked:\s*answers/gi, "")
      .replace(/Generate Diagnostic Spark/gi, "Generate Your Spark");

    if (next !== text) node.nodeValue = next;
  }

  function scrubElement(el) {
    if (!el || el.nodeType !== 1) return;

    var text = (el.textContent || "").toLowerCase();
    if (
      text.indexOf("unsafe public demo hardening payload blocked") !== -1 ||
      text.indexOf("full generator interface did not mount cleanly") !== -1 ||
      text.indexOf("spark protocol recovery mode") !== -1
    ) {
      el.setAttribute("data-dreamos-public-recovery-scrubbed", "1");
    }

    if ((el.textContent || "").trim() === "Generate Diagnostic Spark") {
      el.textContent = "Generate Your Spark";
    }
  }

  function scrub(root) {
    root = root || document.body;
    if (!root) return;

    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
    var nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach(scrubTextNode);

    Array.prototype.forEach.call(root.querySelectorAll("*"), scrubElement);
  }

  function boot() {
    scrub(document.body);
    window.setTimeout(function () { scrub(document.body); }, 250);
    window.setTimeout(function () { scrub(document.body); }, 1000);
    window.setTimeout(function () { scrub(document.body); }, 2500);

    new MutationObserver(function (mutations) {
      mutations.forEach(function (m) {
        Array.prototype.forEach.call(m.addedNodes || [], function (node) {
          if (node.nodeType === 3) scrubTextNode(node);
          if (node.nodeType === 1) scrub(node);
        });
      });
    }).observe(document.body, { childList: true, subtree: true, characterData: true });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();


/* DreamOS Canonical Quiz-Gated Final Dossier
 * One public dossier button only.
 * It must not build from fallback/default answers before the Spark quiz is completed.
 */
(function () {
  "use strict";

  if (window.__DreamOSCanonicalQuizGatedFinalDossier) return;
  window.__DreamOSCanonicalQuizGatedFinalDossier = true;

  var REQUIRED = 28;
  var ANSWERS = ["A","B","C","D","E","F","G","H"];

  function root() {
    return document.querySelector("#emergence-character-generator, .emergence-character-generator, .ecg-shell, .ecg-app, .ecg-wrap, [data-emergence-character-generator]") || document.body;
  }

  function endpoint() {
    if (window.EmergenceCG && window.EmergenceCG.endpoint) return window.EmergenceCG.endpoint;
    return "/wp-json/emergence/v1/generate";
  }

  function hideDuplicates(scope) {
    scope = scope || document;
    var selectors = [
      "[data-dreamos-floating-dossier-fab]",
      "[data-dreamos-guaranteed-final-dossier-button]",
      "[data-dreamos-guaranteed-final-dossier]",
      "[data-ecg-action='create-final-dossier']",
      ".dreamos-guaranteed-dossier-button",
      ".dreamos-floating-dossier-fab"
    ];

    selectors.forEach(function (sel) {
      Array.prototype.forEach.call(scope.querySelectorAll(sel), function (el) {
        if (el && !el.hasAttribute("data-dreamos-canonical-final-dossier")) {
          el.setAttribute("data-dreamos-duplicate-dossier-hidden", "1");
          el.setAttribute("aria-hidden", "true");
          el.tabIndex = -1;
        }
      });
    });

    Array.prototype.forEach.call(scope.querySelectorAll("button, a"), function (el) {
      if (!el || el.hasAttribute("data-dreamos-canonical-final-dossier")) return;
      var text = (el.textContent || "").replace(/\s+/g, " ").trim().toLowerCase();
      if (
        text === "create final dossier" ||
        text === "build final dossier" ||
        text === "rebuild final dossier" ||
        text.indexOf("final dossier") !== -1
      ) {
        el.setAttribute("data-dreamos-duplicate-dossier-hidden", "1");
        el.setAttribute("aria-hidden", "true");
        el.tabIndex = -1;
      }
    });
  }

  function findQuestionKey(el) {
    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
    for (var i = 0; i < attrs.length; i++) {
      var val = el.getAttribute(attrs[i]) || "";
      var m = val.match(/(?:question|answer|q)[-_ ]?(\d{1,2})/i) || val.match(/^(\d{1,2})$/);
      if (m) {
        var n = parseInt(m[1], 10);
        if (n >= 1 && n <= REQUIRED) return String(n);
      }
    }

    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
    if (wrap) {
      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
      var m2 = String(val2).match(/(\d{1,2})/);
      if (m2) {
        var n2 = parseInt(m2[1], 10);
        if (n2 >= 1 && n2 <= REQUIRED) return String(n2);
      }
    }
    return "";
  }

  function normalizeAnswer(v) {
    v = String(v || "").trim().toUpperCase();
    if (!v) return "";
    v = v.substring(0, 1);
    return ANSWERS.indexOf(v) !== -1 ? v : "";
  }

  function collectRealAnswers(scope) {
    scope = scope || root();
    var answers = {};

    Array.prototype.forEach.call(scope.querySelectorAll("input, select, textarea, button[aria-pressed='true'], [role='radio'][aria-checked='true']"), function (el) {
      if (!el || el.disabled) return;

      var tag = (el.tagName || "").toLowerCase();
      var type = (el.getAttribute("type") || "").toLowerCase();

      if ((type === "radio" || type === "checkbox") && !el.checked) return;

      var q = findQuestionKey(el);
      if (!q) return;

      var raw = "";
      if (tag === "select" || tag === "textarea" || tag === "input") raw = el.value;
      else raw = el.getAttribute("data-answer") || el.getAttribute("value") || el.textContent;

      var val = normalizeAnswer(raw);
      if (val) answers[q] = val;
    });

    return answers;
  }

  function countAnswers(answers) {
    var count = 0;
    for (var i = 1; i <= REQUIRED; i++) {
      if (answers[String(i)]) count++;
    }
    return count;
  }

  function panel(scope) {
    scope = scope || root();
    var p = scope.querySelector(".dreamos-canonical-dossier-panel");
    if (!p) {
      p = document.createElement("section");
      p.className = "dreamos-canonical-dossier-panel";
      p.setAttribute("aria-live", "polite");
      scope.appendChild(p);
    }
    return p;
  }

  function renderIncomplete(scope, count) {
    panel(scope).innerHTML =
      '<div class="dreamos-dossier-warning">' +
      '<strong>Complete the Spark quiz first.</strong>' +
      '<p>You have answered ' + count + ' of ' + REQUIRED + ' required questions. The final dossier will unlock after the quiz is complete.</p>' +
      '</div>';
  }

  function renderLoading(scope) {
    panel(scope).innerHTML =
      '<div class="dreamos-dossier-loading">' +
      '<strong>Building Final Spark Dossier...</strong>' +
      '<p>Your completed quiz answers are being resolved through the Spark Protocol.</p>' +
      '</div>';
  }

  function renderDossier(scope, payload) {
    var manifested = Array.isArray(payload.manifested) ? payload.manifested.join(", ") : "Unclassified";
    var lead = payload.lead_domain || "Unclassified";
    var cast = payload.cast || "Unclassified Spark";
    var sig = payload.spark_signature || payload.provisional_spark_signature || "Pending";
    var combat = payload.combat_capability || payload.provisional_combat_capability || "Pending";
    var shape = payload.profile_shape || "Spark profile generated.";

    panel(scope).innerHTML =
      '<section class="dreamos-final-dossier-card">' +
      '<p class="dreamos-kicker">Final Spark Dossier</p>' +
      '<h2>Generated Spark</h2>' +
      '<div class="dreamos-dossier-grid">' +
      '<div><strong>Lead Domain</strong><span>' + lead + '</span></div>' +
      '<div><strong>Cast</strong><span>' + cast + '</span></div>' +
      '<div><strong>Spark Signature</strong><span>' + sig + '</span></div>' +
      '<div><strong>Combat Capability</strong><span>' + combat + '</span></div>' +
      '</div>' +
      '<p><strong>Manifested Domains:</strong> ' + manifested + '</p>' +
      '<p><strong>Profile Shape:</strong> ' + shape + '</p>' +
      '<p class="dreamos-dossier-actions"><a href="/battles/">Enter Battles</a></p>' +
      '<details><summary>Raw Spark Data</summary><pre>' + JSON.stringify(payload, null, 2) + '</pre></details>' +
      '</section>';
  }

  async function build(scope) {
    scope = scope || root();
    var answers = collectRealAnswers(scope);
    var count = countAnswers(answers);

    if (count < REQUIRED) {
      renderIncomplete(scope, count);
      return;
    }

    renderLoading(scope);

    var res = await fetch(endpoint(), {
      method: "POST",
      credentials: "same-origin",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({
        answers: answers,
        source: "dreamos-canonical-quiz-gated-dossier"
      })
    });

    var payload = await res.json();
    if (!res.ok) throw new Error(payload && payload.message ? payload.message : "Spark generation failed");
    renderDossier(scope, payload);
  }

  function ensureButton() {
    var r = root();
    if (!r) return;

    hideDuplicates(document);

    var existing = document.querySelector("[data-dreamos-canonical-final-dossier='1']");
    if (!existing) {
      var wrap = document.createElement("div");
      wrap.className = "dreamos-canonical-dossier-action";

      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "dreamos-canonical-dossier-button";
      btn.setAttribute("data-dreamos-canonical-final-dossier", "1");
      btn.textContent = "Build Final Dossier";

      wrap.appendChild(btn);
      r.appendChild(wrap);
      existing = btn;
    }

    var answers = collectRealAnswers(r);
    var count = countAnswers(answers);

    existing.setAttribute("data-answer-count", String(count));
    existing.setAttribute("data-required-count", String(REQUIRED));
    existing.textContent = count >= REQUIRED ? "Build Final Dossier" : "Complete Quiz First (" + count + "/" + REQUIRED + ")";
  }

  document.addEventListener("click", function (ev) {
    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-canonical-final-dossier='1']") : null;
    if (!btn) return;
    ev.preventDefault();
    ev.stopPropagation();
    build(root()).catch(function (err) {
      panel(root()).innerHTML =
        '<div class="dreamos-dossier-warning"><strong>Dossier build failed.</strong><p>' +
        String(err && err.message ? err.message : err) +
        '</p></div>';
    });
  }, true);

  document.addEventListener("change", ensureButton, true);
  document.addEventListener("input", ensureButton, true);

  function boot() {
    ensureButton();
    setTimeout(ensureButton, 250);
    setTimeout(ensureButton, 1000);
    setTimeout(ensureButton, 2500);

    new MutationObserver(function () {
      ensureButton();
    }).observe(document.body, {childList: true, subtree: true});
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();

/* DreamOS Final Dossier End-Only Gate
 * The final dossier button should not float and should not appear before quiz completion.
 */
(function () {
  "use strict";

  if (window.__DreamOSFinalDossierEndOnlyGate) return;
  window.__DreamOSFinalDossierEndOnlyGate = true;

  var REQUIRED = 28;
  var ANSWERS = ["A","B","C","D","E","F","G","H"];

  function appRoot() {
    return document.querySelector("#emergence-character-generator, .emergence-character-generator, .ecg-shell, .ecg-app, .ecg-wrap, [data-emergence-character-generator]") || document.body;
  }

  function endpoint() {
    return (window.EmergenceCG && window.EmergenceCG.endpoint) || "/wp-json/emergence/v1/generate";
  }

  function killFloatingAndLegacy() {
    var selectors = [
      "[data-dreamos-floating-dossier-fab]",
      ".dreamos-floating-dossier-fab",
      "[data-dreamos-guaranteed-final-dossier]",
      "[data-dreamos-guaranteed-final-dossier-button]",
      ".dreamos-guaranteed-dossier-button",
      "[data-ecg-action='create-final-dossier']"
    ];

    selectors.forEach(function (sel) {
      Array.prototype.forEach.call(document.querySelectorAll(sel), function (el) {
        if (!el.hasAttribute("data-dreamos-end-only-final-dossier")) {
          el.setAttribute("data-dreamos-retired-dossier-control", "1");
          el.setAttribute("aria-hidden", "true");
          el.tabIndex = -1;
        }
      });
    });

    Array.prototype.forEach.call(document.querySelectorAll("button, a"), function (el) {
      if (!el || el.hasAttribute("data-dreamos-end-only-final-dossier")) return;
      var text = (el.textContent || "").replace(/\s+/g, " ").trim().toLowerCase();
      if (
        text === "create final dossier" ||
        text === "build final dossier" ||
        text === "rebuild final dossier" ||
        text.indexOf("complete quiz first") !== -1 ||
        text.indexOf("final dossier") !== -1
      ) {
        el.setAttribute("data-dreamos-retired-dossier-control", "1");
        el.setAttribute("aria-hidden", "true");
        el.tabIndex = -1;
      }
    });
  }

  function findQuestionKey(el) {
    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
    for (var i = 0; i < attrs.length; i++) {
      var val = el.getAttribute(attrs[i]) || "";
      var m = val.match(/(?:question|answer|q)[-_ ]?(\d{1,2})/i) || val.match(/^(\d{1,2})$/);
      if (m) {
        var n = parseInt(m[1], 10);
        if (n >= 1 && n <= REQUIRED) return String(n);
      }
    }

    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
    if (wrap) {
      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
      var m2 = String(val2).match(/(\d{1,2})/);
      if (m2) {
        var n2 = parseInt(m2[1], 10);
        if (n2 >= 1 && n2 <= REQUIRED) return String(n2);
      }
    }
    return "";
  }

  function normalizeAnswer(v) {
    v = String(v || "").trim().toUpperCase();
    if (!v) return "";
    v = v.substring(0, 1);
    return ANSWERS.indexOf(v) !== -1 ? v : "";
  }

  function collectAnswers(scope) {
    scope = scope || appRoot();
    var answers = {};

    Array.prototype.forEach.call(scope.querySelectorAll("input, select, textarea, button[aria-pressed='true'], [role='radio'][aria-checked='true']"), function (el) {
      if (!el || el.disabled) return;
      var tag = (el.tagName || "").toLowerCase();
      var type = (el.getAttribute("type") || "").toLowerCase();

      if ((type === "radio" || type === "checkbox") && !el.checked) return;

      var q = findQuestionKey(el);
      if (!q) return;

      var raw = "";
      if (tag === "select" || tag === "textarea" || tag === "input") raw = el.value;
      else raw = el.getAttribute("data-answer") || el.getAttribute("value") || el.textContent;

      var val = normalizeAnswer(raw);
      if (val) answers[q] = val;
    });

    return answers;
  }

  function answerCount(answers) {
    var count = 0;
    for (var i = 1; i <= REQUIRED; i++) {
      if (answers[String(i)]) count++;
    }
    return count;
  }

  function removeEndButton() {
    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-end-only-final-dossier-wrap]"), function (el) {
      el.remove();
    });
  }

  function panel(scope) {
    scope = scope || appRoot();
    var p = scope.querySelector(".dreamos-end-only-dossier-panel");
    if (!p) {
      p = document.createElement("section");
      p.className = "dreamos-end-only-dossier-panel";
      p.setAttribute("aria-live", "polite");
      scope.appendChild(p);
    }
    return p;
  }

  function renderLoading(scope) {
    panel(scope).innerHTML =
      '<div class="dreamos-dossier-loading"><strong>Building Final Spark Dossier...</strong><p>Your completed quiz answers are being resolved through the Spark Protocol.</p></div>';
  }

  function renderDossier(scope, payload) {
    var manifested = Array.isArray(payload.manifested) ? payload.manifested.join(", ") : "Unclassified";
    var lead = payload.lead_domain || "Unclassified";
    var cast = payload.cast || "Unclassified Spark";
    var sig = payload.spark_signature || payload.provisional_spark_signature || "Pending";
    var combat = payload.combat_capability || payload.provisional_combat_capability || "Pending";
    var shape = payload.profile_shape || "Spark profile generated.";

    panel(scope).innerHTML =
      '<section class="dreamos-final-dossier-card">' +
      '<p class="dreamos-kicker">Final Spark Dossier</p>' +
      '<h2>Generated Spark</h2>' +
      '<div class="dreamos-dossier-grid">' +
      '<div><strong>Lead Domain</strong><span>' + lead + '</span></div>' +
      '<div><strong>Cast</strong><span>' + cast + '</span></div>' +
      '<div><strong>Spark Signature</strong><span>' + sig + '</span></div>' +
      '<div><strong>Combat Capability</strong><span>' + combat + '</span></div>' +
      '</div>' +
      '<p><strong>Manifested Domains:</strong> ' + manifested + '</p>' +
      '<p><strong>Profile Shape:</strong> ' + shape + '</p>' +
      '<p class="dreamos-dossier-actions"><a href="/battles/">Enter Battles</a></p>' +
      '<details><summary>Raw Spark Data</summary><pre>' + JSON.stringify(payload, null, 2) + '</pre></details>' +
      '</section>';
  }

  async function build(scope) {
    scope = scope || appRoot();
    var answers = collectAnswers(scope);
    var count = answerCount(answers);

    if (count < REQUIRED) {
      removeEndButton();
      return;
    }

    renderLoading(scope);

    var res = await fetch(endpoint(), {
      method: "POST",
      credentials: "same-origin",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({
        answers: answers,
        source: "dreamos-end-only-final-dossier"
      })
    });

    var payload = await res.json();
    if (!res.ok) throw new Error(payload && payload.message ? payload.message : "Spark generation failed");
    renderDossier(scope, payload);
  }

  function ensureEndOnlyButton() {
    var r = appRoot();
    if (!r) return;

    killFloatingAndLegacy();

    var answers = collectAnswers(r);
    var count = answerCount(answers);

    if (count < REQUIRED) {
      removeEndButton();
      return;
    }

    if (document.querySelector("[data-dreamos-end-only-final-dossier='1']")) return;

    var wrap = document.createElement("div");
    wrap.className = "dreamos-end-only-dossier-action";
    wrap.setAttribute("data-dreamos-end-only-final-dossier-wrap", "1");

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "dreamos-end-only-dossier-button";
    btn.setAttribute("data-dreamos-end-only-final-dossier", "1");
    btn.textContent = "Build Final Dossier";

    wrap.appendChild(btn);
    r.appendChild(wrap);
  }

  document.addEventListener("click", function (ev) {
    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-end-only-final-dossier='1']") : null;
    if (!btn) return;
    ev.preventDefault();
    ev.stopPropagation();

    build(appRoot()).catch(function (err) {
      panel(appRoot()).innerHTML =
        '<div class="dreamos-dossier-warning"><strong>Dossier build failed.</strong><p>' +
        String(err && err.message ? err.message : err) +
        '</p></div>';
    });
  }, true);

  document.addEventListener("input", ensureEndOnlyButton, true);
  document.addEventListener("change", ensureEndOnlyButton, true);

  function boot() {
    ensureEndOnlyButton();
    setTimeout(ensureEndOnlyButton, 250);
    setTimeout(ensureEndOnlyButton, 1000);
    setTimeout(ensureEndOnlyButton, 2500);
    new MutationObserver(ensureEndOnlyButton).observe(document.body, {childList: true, subtree: true});
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();

/* DreamOS Spark Two-Pass Dossier Gate
 * Final dossier appears only after the currently visible required quiz phase is complete.
 * Hidden/locked flavor sections do not count and should not create fake blank space.
 */
(function () {
  "use strict";

  if (window.__DreamOSSparkTwoPassDossierGate) return;
  window.__DreamOSSparkTwoPassDossierGate = true;

  var ANSWERS = ["A","B","C","D","E","F","G","H"];

  function appRoot() {
    return document.querySelector("#emergence-character-generator, .emergence-character-generator, .ecg-shell, .ecg-app, .ecg-wrap, [data-emergence-character-generator]") || document.body;
  }

  function isVisible(el) {
    if (!el) return false;
    if (el.closest("[hidden], [aria-hidden='true'], [data-phase='locked'], [data-locked='1'], .is-hidden, .hidden")) return false;
    var cs = window.getComputedStyle(el);
    if (cs.display === "none" || cs.visibility === "hidden" || cs.opacity === "0") return false;
    var rect = el.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
  }

  function endpoint() {
    return (window.EmergenceCG && window.EmergenceCG.endpoint) || "/wp-json/emergence/v1/generate";
  }

  function retireOldDossierControls() {
    var selectors = [
      "[data-dreamos-floating-dossier-fab]",
      ".dreamos-floating-dossier-fab",
      "[data-dreamos-guaranteed-final-dossier]",
      "[data-dreamos-guaranteed-final-dossier-button]",
      ".dreamos-guaranteed-dossier-button",
      "[data-dreamos-canonical-final-dossier]",
      "[data-dreamos-end-only-final-dossier]",
      "[data-ecg-action='create-final-dossier']"
    ];

    selectors.forEach(function (sel) {
      Array.prototype.forEach.call(document.querySelectorAll(sel), function (el) {
        if (!el.hasAttribute("data-dreamos-two-pass-final-dossier")) {
          el.setAttribute("data-dreamos-retired-dossier-control", "1");
          el.setAttribute("aria-hidden", "true");
          el.tabIndex = -1;
        }
      });
    });
  }

  function normalizeAnswer(v) {
    v = String(v || "").trim().toUpperCase();
    if (!v) return "";
    v = v.substring(0, 1);
    return ANSWERS.indexOf(v) !== -1 ? v : "";
  }

  function questionNumberFrom(el) {
    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
    for (var i = 0; i < attrs.length; i++) {
      var val = el.getAttribute(attrs[i]) || "";
      var m = val.match(/(?:question|answer|flavor|q)[-_ ]?(\d{1,2})/i) || val.match(/^(\d{1,2})$/);
      if (m) return String(parseInt(m[1], 10));
    }

    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question'], [id*='flavor'], [class*='flavor']");
    if (wrap) {
      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
      var m2 = String(val2).match(/(\d{1,2})/);
      if (m2) return String(parseInt(m2[1], 10));
    }

    return "";
  }

  function visibleQuestionGroups(scope) {
    scope = scope || appRoot();
    var groups = {};

    Array.prototype.forEach.call(scope.querySelectorAll("input, select, textarea, button[aria-pressed], [role='radio']"), function (el) {
      if (!isVisible(el) || el.disabled) return;

      var q = questionNumberFrom(el);
      if (!q) return;

      if (!groups[q]) groups[q] = [];
      groups[q].push(el);
    });

    return groups;
  }

  function collectVisibleAnswers(scope) {
    scope = scope || appRoot();
    var answers = {};
    var groups = visibleQuestionGroups(scope);

    Object.keys(groups).forEach(function (q) {
      groups[q].forEach(function (el) {
        var tag = (el.tagName || "").toLowerCase();
        var type = (el.getAttribute("type") || "").toLowerCase();

        if ((type === "radio" || type === "checkbox") && !el.checked) return;

        var raw = "";
        if (tag === "select" || tag === "textarea" || tag === "input") raw = el.value;
        else if (el.getAttribute("aria-pressed") === "true" || el.getAttribute("aria-checked") === "true") raw = el.getAttribute("data-answer") || el.getAttribute("value") || el.textContent;

        var val = normalizeAnswer(raw);
        if (val) answers[q] = val;
      });
    });

    return answers;
  }

  function phaseState(scope) {
    var groups = visibleQuestionGroups(scope);
    var answers = collectVisibleAnswers(scope);
    var required = Object.keys(groups).length;
    var answered = Object.keys(answers).filter(function (q) { return !!answers[q]; }).length;

    return {
      required: required,
      answered: answered,
      complete: required > 0 && answered >= required,
      answers: answers
    };
  }

  function removeTwoPassButton() {
    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-two-pass-final-dossier-wrap]"), function (el) {
      el.remove();
    });
  }

  function panel(scope) {
    scope = scope || appRoot();
    var p = scope.querySelector(".dreamos-two-pass-dossier-panel");
    if (!p) {
      p = document.createElement("section");
      p.className = "dreamos-two-pass-dossier-panel";
      p.setAttribute("aria-live", "polite");
      scope.appendChild(p);
    }
    return p;
  }

  function renderLoading(scope) {
    panel(scope).innerHTML =
      '<div class="dreamos-dossier-loading"><strong>Building Final Spark Dossier...</strong><p>Your completed Spark phase is being resolved.</p></div>';
  }

  function renderDossier(scope, payload) {
    var manifested = Array.isArray(payload.manifested) ? payload.manifested.join(", ") : "Unclassified";
    var powers = Array.isArray(payload.powers) && payload.powers.length
      ? payload.powers.map(function (p) { return p.name || p.id || String(p); }).join(", ")
      : "Pending final flavor pass";

    panel(scope).innerHTML =
      '<section class="dreamos-final-dossier-card">' +
      '<p class="dreamos-kicker">Final Spark Dossier</p>' +
      '<h2>Generated Spark</h2>' +
      '<div class="dreamos-dossier-grid">' +
      '<div><strong>Lead Domain</strong><span>' + (payload.lead_domain || "Unclassified") + '</span></div>' +
      '<div><strong>Cast</strong><span>' + (payload.cast || "Unclassified Spark") + '</span></div>' +
      '<div><strong>Spark Signature</strong><span>' + (payload.spark_signature || payload.provisional_spark_signature || "Pending") + '</span></div>' +
      '<div><strong>Combat Capability</strong><span>' + (payload.combat_capability || payload.provisional_combat_capability || "Pending") + '</span></div>' +
      '</div>' +
      '<p><strong>Manifested Domains:</strong> ' + manifested + '</p>' +
      '<p><strong>Powers:</strong> ' + powers + '</p>' +
      '<p><strong>Profile Shape:</strong> ' + (payload.profile_shape || "Spark profile generated.") + '</p>' +
      '<p class="dreamos-dossier-actions"><a href="/battles/">Enter Battles</a></p>' +
      '<details><summary>Raw Spark Data</summary><pre>' + JSON.stringify(payload, null, 2) + '</pre></details>' +
      '</section>';
  }

  async function build(scope) {
    scope = scope || appRoot();
    var state = phaseState(scope);

    if (!state.complete) {
      removeTwoPassButton();
      return;
    }

    renderLoading(scope);

    var res = await fetch(endpoint(), {
      method: "POST",
      credentials: "same-origin",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({
        answers: state.answers,
        source: "dreamos-two-pass-final-dossier"
      })
    });

    var payload = await res.json();
    if (!res.ok) throw new Error(payload && payload.message ? payload.message : "Spark generation failed");
    renderDossier(scope, payload);
  }

  function ensureButton() {
    var r = appRoot();
    if (!r) return;

    retireOldDossierControls();

    var state = phaseState(r);

    if (!state.complete) {
      removeTwoPassButton();
      return;
    }

    if (document.querySelector("[data-dreamos-two-pass-final-dossier='1']")) return;

    var wrap = document.createElement("div");
    wrap.className = "dreamos-two-pass-dossier-action";
    wrap.setAttribute("data-dreamos-two-pass-final-dossier-wrap", "1");

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "dreamos-two-pass-dossier-button";
    btn.setAttribute("data-dreamos-two-pass-final-dossier", "1");
    btn.textContent = "Build Final Dossier";

    wrap.appendChild(btn);
    r.appendChild(wrap);
  }

  document.addEventListener("click", function (ev) {
    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-two-pass-final-dossier='1']") : null;
    if (!btn) return;

    ev.preventDefault();
    ev.stopPropagation();

    build(appRoot()).catch(function (err) {
      panel(appRoot()).innerHTML =
        '<div class="dreamos-dossier-warning"><strong>Dossier build failed.</strong><p>' +
        String(err && err.message ? err.message : err) +
        '</p></div>';
    });
  }, true);

  document.addEventListener("input", ensureButton, true);
  document.addEventListener("change", ensureButton, true);
  document.addEventListener("click", function () { setTimeout(ensureButton, 50); }, true);

  function boot() {
    ensureButton();
    setTimeout(ensureButton, 250);
    setTimeout(ensureButton, 1000);
    setTimeout(ensureButton, 2500);
    new MutationObserver(function(){ window.clearTimeout(window.__dreamosDossierObserverTimer); window.__dreamosDossierObserverTimer = window.setTimeout(ensureButton, 150); }).observe(document.body, {childList: true, subtree: true});
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();


/* DreamOS Spark Quiz Freeze Observer Fix
 * Passive safety shim: prevent dossier helper loops from stalling mobile quiz rendering.
 */
(function () {
  "use strict";

  if (window.__DreamOSSparkQuizFreezeObserverFix) return;
  window.__DreamOSSparkQuizFreezeObserverFix = true;

  var lastRun = 0;

  function safeSweep() {
    var now = Date.now();
    if (now - lastRun < 250) return;
    lastRun = now;

    // CSS handles hiding. JS only removes impossible floating controls once.
    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-floating-dossier-fab], .dreamos-floating-dossier-fab"), function (el) {
      if (el && el.parentNode) el.parentNode.removeChild(el);
    });
  }

  function boot() {
    safeSweep();
    setTimeout(safeSweep, 500);
    setTimeout(safeSweep, 1500);
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot, { once: true });
  else boot();
})();


/* DreamOS Spark Q11 Renderer Fix
 * Repairs visible question cards that have title text but no mounted select/options.
 * Keeps the quiz moving without fabricating answers.
 */
(function () {
  "use strict";

  if (window.__DreamOSSparkQ11RendererFix) return;
  window.__DreamOSSparkQ11RendererFix = true;

  var LETTERS = ["A","B","C","D","E","F","G","H"];

  function getQuestionBank() {
    var cg = window.EmergenceCG || {};
    var qb = cg.question_bank || {};
    return qb.domain_questions || qb.questions || [];
  }

  function normalizeQuestionList() {
    var list = getQuestionBank();
    if (!Array.isArray(list)) return [];

    return list.map(function (q, idx) {
      var num = parseInt(q.q || q.id || q.number || (idx + 1), 10);
      var text = q.question || q.prompt || q.text || "";
      var opts = q.options || q.answers || {};
      return { num: num, text: text, options: opts };
    }).filter(function (q) {
      return q.num && q.text && q.options;
    });
  }

  function optionEntries(options) {
    if (Array.isArray(options)) {
      return options.map(function (v, i) {
        return [LETTERS[i] || String(i + 1), String(v || "")];
      }).filter(function (pair) { return pair[1]; });
    }

    return LETTERS.map(function (k) {
      return [k, String(options[k] || options[k.toLowerCase()] || "")];
    }).filter(function (pair) { return pair[1]; });
  }

  function findQuestionNumFromText(text) {
    var m = String(text || "").match(/\bQ\s*(\d{1,2})\b/i);
    return m ? parseInt(m[1], 10) : 0;
  }

  function isVisible(el) {
    if (!el) return false;
    var cs = window.getComputedStyle(el);
    if (cs.display === "none" || cs.visibility === "hidden") return false;
    var r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0;
  }

  function hasAnswerControl(card) {
    return !!card.querySelector("select option[value]:not([value='']), input[type='radio'], button[data-answer], [role='radio']");
  }

  function buildSelect(question) {
    var select = document.createElement("select");
    select.className = "dreamos-q11-repair-select";
    select.setAttribute("name", "question_" + question.num);
    select.setAttribute("data-question", String(question.num));
    select.setAttribute("aria-label", "Q" + question.num + " answer");

    var ph = document.createElement("option");
    ph.value = "";
    ph.textContent = "Choose one...";
    select.appendChild(ph);

    optionEntries(question.options).forEach(function (pair) {
      var opt = document.createElement("option");
      opt.value = pair[0];
      opt.textContent = pair[0] + ". " + pair[1];
      select.appendChild(opt);
    });

    return select;
  }

  function repairBlankCards() {
    var questions = normalizeQuestionList();
    if (!questions.length) return;

    var byNum = {};
    questions.forEach(function (q) {
      byNum[q.num] = q;
    });

    var candidates = Array.prototype.slice.call(document.querySelectorAll(
      ".ecg-question, .question, [data-question], [data-q], .ecg-card, .ecg-step, section, fieldset, div"
    ));

    candidates.forEach(function (card) {
      if (!isVisible(card)) return;
      if (card.getAttribute("data-dreamos-q11-renderer-repaired") === "1") return;
      if (hasAnswerControl(card)) return;

      var text = (card.textContent || "").replace(/\s+/g, " ").trim();
      if (!text) return;

      var qnum = findQuestionNumFromText(text);
      if (!qnum || !byNum[qnum]) return;

      // Target blank question cards only. Q11 is known failure, but this also fixes same renderer failure for Q12+.
      if (qnum < 1 || qnum > 28) return;

      var q = byNum[qnum];
      var entries = optionEntries(q.options);
      if (!entries.length) return;

      var controlWrap = document.createElement("div");
      controlWrap.className = "dreamos-q11-repair-control";
      controlWrap.appendChild(buildSelect(q));

      card.appendChild(controlWrap);
      card.setAttribute("data-dreamos-q11-renderer-repaired", "1");
      card.setAttribute("data-question", String(qnum));
    });
  }

  function boot() {
    repairBlankCards();
    setTimeout(repairBlankCards, 250);
    setTimeout(repairBlankCards, 750);
    setTimeout(repairBlankCards, 1500);

    var timer = null;
    var observer = new MutationObserver(function () {
      clearTimeout(timer);
      timer = setTimeout(repairBlankCards, 150);
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot, { once: true });
  else boot();
})();
