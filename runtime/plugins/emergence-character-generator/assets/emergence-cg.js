(function () {
  const form = document.getElementById('emergence-cg-form');
  const result = document.getElementById('emergence-cg-result');
  const progressLabel = document.getElementById('ecg-progress-label');
  const progressFill = document.getElementById('ecg-progress-fill');

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

  form.addEventListener('change', updateProgress);
  updateProgress();

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    result.innerHTML = '<p>Running domain typing pass...</p>';

    const data = new FormData(form);
    const answers = [];
    for (const [, value] of data.entries()) answers.push(value);

    try {
      const response = await fetch(EmergenceCG.endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': EmergenceCG.nonce
        },
        body: JSON.stringify({ answers })
      });

      const payload = await response.json();

      if (!response.ok) {
        throw new Error(payload.message || 'Generator failed');
      }

      const scores = Object.entries(payload.scores || {})
        .map(function (pair) {
          const tier = payload.tiers && payload.tiers[pair[0]] ? payload.tiers[pair[0]] : '?';
          return '<div class="ecg-card"><strong>' + esc(pair[0]) + '</strong><br>Score ' + esc(pair[1]) + ' · Tier ' + esc(tier) + '</div>';
        })
        .join('');

      result.innerHTML = [
        '<h2>Your Spark Type Scan</h2>',
        '<p class="ecg-result-note">This is deterministic for this answer set. Q1-Q28 scores your type. It does not select final powers yet.</p>',
        '<div class="ecg-card-grid">',
        '<div class="ecg-card"><strong>Lead Domain</strong><br>' + esc(payload.lead_domain || 'Unresolved') + '</div>',
        '<div class="ecg-card"><strong>Profile Shape</strong><br>' + esc(payload.profile_shape || 'Unresolved') + '</div>',
        '<div class="ecg-card"><strong>Provisional Signature</strong><br>' + esc(payload.provisional_spark_signature) + '</div>',
        '<div class="ecg-card"><strong>Provisional Combat</strong><br>' + esc(payload.provisional_combat_capability) + '</div>',
        '<div class="ecg-card"><strong>Cast</strong><br>' + esc(payload.cast) + '</div>',
        '<div class="ecg-card"><strong>Power Selection</strong><br>Locked until flavor pass</div>',
        '</div>',
        '<h3>Manifested Domains</h3>',
        '<p>' + (payload.manifested || []).map(esc).join(', ') + '</p>',
        '<p><strong>Manifest threshold:</strong> ' + esc(payload.manifest_threshold) + '</p>',
        '<h3>Domain Scores</h3>',
        '<div class="ecg-card-grid">' + scores + '</div>',
        '<h3>What happens next?</h3>',
        '<p>The next phase is Q29-Q68 flavor scoring. That pass selects actual powers/sub-affinities inside the manifested domains. This prevents the type scan from spraying too many abilities.</p>'
      ].join('');
    } catch (error) {
      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
    }
  });
})();
