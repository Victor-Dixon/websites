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
    29:{A:'Let nothing move you.',B:'Become heavier than the threat.',C:'Take up more space.',D:'Bend without breaking.',E:'Keep advancing.',F:'Answer force with force.'},
    30:{A:'Break the obstacle.',B:'Rise above it.',C:'Stretch past the limit.',D:'Absorb the impact.',E:'Anchor yourself.',F:'Become unstoppable.'},
    31:{A:'Overpower the lock.',B:'Ignore the damage.',C:'Tower over the field.',D:'Reach farther.',E:'Crash through.',F:'Condense your will.'},
    32:{A:'Refuse the wound.',B:'Hold your shape.',C:'Flex around danger.',D:'Grow into the answer.',E:'Never lose momentum.',F:'Push back harder.'},
    33:{A:'Lift the impossible.',B:'Become enormous.',C:'Slip the bind.',D:'Keep charging.',E:'Take the hit.',F:'Stand immovable.'},

    34:{A:'Arrive before fear does.',B:'Leave the ground behind.',C:'React before thought.',D:'Feel the danger early.',E:'Cling to impossible angles.',F:'Blur through resistance.'},
    35:{A:'Let instinct fire first.',B:'Rise out of reach.',C:'Sense the wrongness.',D:'Cross the gap instantly.',E:'Use the wall as a road.',F:'Shake through the lock.'},
    36:{A:'Move like a rumor.',B:'Take the sky route.',C:'Trust your nerves.',D:'Notice the trap.',E:'Hold the vertical path.',F:'Vibrate past the barrier.'},
    37:{A:'Turn distance into nothing.',B:'Lift away from the fight.',C:'Beat the strike by a breath.',D:'Hear danger before it speaks.',E:'Find purchase anywhere.',F:'Phase through pressure.'},
    38:{A:'Be gone before impact.',B:'Float above the answer.',C:'Let reflex decide.',D:'Respect the warning.',E:'Stick the landing anywhere.',F:'Shake loose.'},

    39:{A:'Let heat answer.',B:'Make cold precise.',C:'Release a focused hit.',D:'Call the current.',E:'Weaponize sound.',F:'Move with the tide.'},
    40:{A:'Burn the path open.',B:'Freeze the moment.',C:'Strike from the core.',D:'Shatter with voice.',E:'Flow around the line.',F:'Charge the air.'},
    41:{A:'Hit clean and direct.',B:'Ignite the pressure.',C:'Lower the temperature.',D:'Arc through the target.',E:'Break rhythm with sound.',F:'Pull water into motion.'},
    42:{A:'Turn anger into flame.',B:'Make stillness dangerous.',C:'Push energy outward.',D:'Let lightning choose.',E:'Scream the field apart.',F:'Drown the opening.'},
    43:{A:'Spark the fuse.',B:'Punch the air itself.',C:'Lock everything in frost.',D:'Conduct the storm.',E:'Make sound physical.',F:'Shape the current.'},

    44:{A:'Open a way through.',B:'Let the strike pass.',C:'Disappear from sight.',D:'Become smaller than the problem.',E:'Notice what others miss.',F:'Step elsewhere.'},
    45:{A:'Hear the hidden movement.',B:'Leave no visible target.',C:'Change location instantly.',D:'Create an exit.',E:'Shrink the risk.',F:'Pass through.'},
    46:{A:'Become untouchable.',B:'Choose a new position.',C:'Reduce your profile.',D:'Leave the eye behind.',E:'Read the room sharply.',F:'Fold distance.'},
    47:{A:'Let matter fail to hold you.',B:'Make yourself hard to catch.',C:'Fade from the obvious view.',D:'Cut a doorway.',E:'Track the unseen.',F:'Blink away.'},
    48:{A:'Slip through the hit.',B:'Become overlooked.',C:'Open the threshold.',D:'Sense the hidden path.',E:'Move without crossing.',F:'Erase your outline.'},

    49:{A:'Choose the bright edge.',B:'Take in what is thrown at you.',C:'Answer with darkness.',D:'Poison the opening.',E:'Reach into the absence.',F:'Shape light into form.'},
    50:{A:'Feed on the impact.',B:'Let the wound contaminate.',C:'Grip the empty place.',D:'Build the solid illusion.',E:'Cut with brightness.',F:'Hide inside shadow.'},
    51:{A:'Let darkness obey.',B:'Make danger infectious.',C:'Pull from the void.',D:'Make light solid.',E:'Focus the beam.',F:'Drink the force.'},
    52:{A:'Make the air unsafe.',B:'Use the gap between things.',C:'Construct from radiance.',D:'Draw the sharp line.',E:'Turn attacks into fuel.',F:'Move the dark.'},
    53:{A:'Reach where nothing should be.',B:'Hold the light still.',C:'Burn a precise path.',D:'Absorb the answer.',E:'Let shadow take shape.',F:'Leave poison behind.'},

    54:{A:'Raise the barrier.',B:'Repair the damage.',C:'Pull the field downward.',D:'Draw metal and motion.',E:'Become more than one.',F:'Redirect the hit.'},
    55:{A:'Protect the boundary.',B:'Close the wound.',C:'Change the weight of things.',D:'Steal the motion.',E:'Command attraction.',F:'Split your presence.'},
    56:{A:'Multiply the problem.',B:'Bend the fall.',C:'Turn force aside.',D:'Wall off the danger.',E:'Pull the weapon away.',F:'Recover faster.'},
    57:{A:'Heal through the cost.',B:'Make gravity speak.',C:'Hold the line with a shield.',D:'Call the metal home.',E:'Divide your attention into bodies.',F:'Catch the impact.'},
    58:{A:'Put a wall between worlds.',B:'Become many angles.',C:'Regrow the lost ground.',D:'Shift the center of mass.',E:'Turn movement into control.',F:'Command the field.'},

    59:{A:'Wear the beast.',B:'Ask the wild for help.',C:'Change to survive.',D:'Let the sky answer.',E:'Influence the living room.',F:'Become something else.'},
    60:{A:'Adapt under pressure.',B:'Call the weather down.',C:'Take the animal path.',D:'Change the mood of the crowd.',E:'Alter your body.',F:'Grow through the earth.'},
    61:{A:'Evolve on contact.',B:'Let roots decide.',C:'Borrow the predator.',D:'Rewrite your shape.',E:'Sway the instinct.',F:'Move the storm.'},
    62:{A:'Let nature reclaim the fight.',B:'Command attraction and alarm.',C:'Take a creature form.',D:'Turn climate into leverage.',E:'Survive by changing.',F:'Shift your skin.'},
    63:{A:'Become the answer.',B:'Let the green world rise.',C:'Mutate past the limit.',D:'Change what others feel.',E:'Run with animal memory.',F:'Summon weather.'},

    64:{A:'Hear the thought.',B:'Make the false image real enough.',C:'Attack the mind directly.',D:'Shield the self.',E:'Move without touching.',F:'Guide another will.'},
    65:{A:'Listen beneath words.',B:'Take the wheel gently.',C:'Defend the inner gate.',D:'Lift with thought.',E:'Show them what is not there.',F:'Strike behind the eyes.'},
    66:{A:'Pressure the psyche.',B:'Bend perception.',C:'Command the decision.',D:'Move matter with focus.',E:'Reach mind to mind.',F:'Hold your mind intact.'},
    67:{A:'Turn choice into suggestion.',B:'Build the illusion.',C:'Hit the unseen self.',D:'Read the signal.',E:'Push the object from afar.',F:'Keep the psychic wall.'},
    68:{A:'Influence the hand.',B:'Guard the mind.',C:'Rewrite the scene.',D:'Speak without sound.',E:'Strike through thought.',F:'Lift the impossible silently.'}
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
      '<p>Only manifested domains get flavor questions. The options are disguised, but each one still pushes toward a specific power.</p>',
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
