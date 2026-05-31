(function () {
  const form = document.getElementById('emergence-cg-form');
  const result = document.getElementById('emergence-cg-result');

  if (!form || !result || !window.EmergenceCG) return;

  function esc(value) {
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    result.innerHTML = '<p>Generating Spark...</p>';

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

      const powers = payload.powers.map(function (p) {
        return '<div class="ecg-card"><strong>' + esc(p.power) + '</strong><br>' +
          esc(p.domain) + ' · Tier ' + esc(p.tier) + (p.lead ? ' · Lead' : '') + '</div>';
      }).join('');

      result.innerHTML = [
        '<h2>Your Spark</h2>',
        '<div class="ecg-card-grid">',
        '<div class="ecg-card"><strong>Signature</strong><br>' + esc(payload.spark_signature) + '</div>',
        '<div class="ecg-card"><strong>Combat Capability</strong><br>' + esc(payload.combat_capability) + '</div>',
        '<div class="ecg-card"><strong>Threat Class</strong><br>' + esc(payload.threat_class) + '</div>',
        '<div class="ecg-card"><strong>Cast</strong><br>' + esc(payload.cast) + '</div>',
        '</div>',
        '<h3>Manifested Domains</h3>',
        '<p>' + payload.manifested.map(esc).join(', ') + '</p>',
        '<h3>Domain Scores</h3>',
        '<p>' + Object.entries(payload.scores || {}).map(function (pair) { return esc(pair[0]) + ': ' + esc(pair[1]); }).join(' · ') + '</p>',
        '<h3>Powers</h3>',
        '<div class="ecg-card-grid">' + powers + '</div>'
      ].join('');
    } catch (error) {
      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
    }
  });
})();
