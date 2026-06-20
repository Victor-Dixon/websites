#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== SPARK PROTOCOL SCORING ENGINE =="

mkdir -p packages/scoring-engine tests/scoring

cat > packages/scoring-engine/index.js << 'JS'
const DOMAIN_MAP = {
  A: 'titan',
  B: 'inferno',
  C: 'velocity',
  D: 'bulwark',
  E: 'echo',
  F: 'specter'
};

const FLAVOR_MAP = {
  A: 'precision',
  B: 'aggression',
  C: 'adaptation',
  D: 'discipline',
  E: 'empathy',
  F: 'stealth'
};

export function calculateDomainScores(responses) {
  const scores = {};

  for (const domain of Object.values(DOMAIN_MAP)) {
    scores[domain] = 0;
  }

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);

    if (qnum >= 1 && qnum <= 36) {
      const domain = DOMAIN_MAP[answer];
      if (domain) {
        scores[domain] += 1;
      }
    }
  }

  return scores;
}

export function calculateFlavorVectors(responses) {
  const vectors = {};

  for (const flavor of Object.values(FLAVOR_MAP)) {
    vectors[flavor] = 0;
  }

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);

    if (qnum >= 37 && qnum <= 72) {
      const flavor = FLAVOR_MAP[answer];
      if (flavor) {
        vectors[flavor] += 1;
      }
    }
  }

  return vectors;
}

export function determinePrimaryDomain(scores) {
  return Object.entries(scores)
    .sort((a, b) => b[1] - a[1])[0]?.[0] || 'unknown';
}

export function determineTier(primaryScore) {
  if (primaryScore >= 28) return 'T5';
  if (primaryScore >= 22) return 'T4';
  if (primaryScore >= 16) return 'T3';
  if (primaryScore >= 10) return 'T2';
  return 'T1';
}

export function determineThreatClass(tier, flavorVectors) {
  const stealth = flavorVectors.stealth || 0;
  const aggression = flavorVectors.aggression || 0;

  if (tier === 'T5' && aggression >= 10) {
    return 'OMEGA';
  }

  if (tier === 'T5' || tier === 'T4') {
    return 'ALPHA';
  }

  if (stealth >= 12) {
    return 'SIGMA';
  }

  return 'STANDARD';
}

export function buildCharacterSheet({ userId, username, responses }) {
  const domain_scores = calculateDomainScores(responses);
  const flavor_vectors = calculateFlavorVectors(responses);

  const primary_domain = determinePrimaryDomain(domain_scores);
  const primary_score = domain_scores[primary_domain] || 0;

  const tier = determineTier(primary_score);
  const threat_class = determineThreatClass(tier, flavor_vectors);

  return {
    schema_version: 'spark_character_sheet_v1',
    locked: true,
    user_id: userId,
    username,
    generated_at: new Date().toISOString(),

    primary_domain,
    primary_score,
    tier,
    threat_class,

    domain_scores,
    flavor_vectors,

    manifestation: {
      codename: null,
      alignment: threat_class,
      descriptor: `${tier} ${primary_domain}`
    }
  };
}
JS

cat > tests/scoring/scoring_engine.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  calculateDomainScores,
  calculateFlavorVectors,
  determinePrimaryDomain,
  determineTier,
  determineThreatClass,
  buildCharacterSheet
} from '../../packages/scoring-engine/index.js';

test('domain scoring counts first 36 responses only', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'A';
  }

  const scores = calculateDomainScores(responses);

  assert.equal(scores.titan, 36);
  assert.equal(scores.velocity, 0);
});

test('flavor vectors count questions 37-72 only', () => {
  const responses = {};

  for (let i = 37; i <= 72; i++) {
    responses[i] = 'F';
  }

  const vectors = calculateFlavorVectors(responses);

  assert.equal(vectors.stealth, 36);
  assert.equal(vectors.precision, 0);
});

test('tier thresholds classify correctly', () => {
  assert.equal(determineTier(5), 'T1');
  assert.equal(determineTier(10), 'T2');
  assert.equal(determineTier(16), 'T3');
  assert.equal(determineTier(22), 'T4');
  assert.equal(determineTier(28), 'T5');
});

test('threat class resolves correctly', () => {
  assert.equal(
    determineThreatClass('T5', { aggression: 12 }),
    'OMEGA'
  );

  assert.equal(
    determineThreatClass('T4', { aggression: 2 }),
    'ALPHA'
  );

  assert.equal(
    determineThreatClass('T2', { stealth: 14 }),
    'SIGMA'
  );

  assert.equal(
    determineThreatClass('T2', { stealth: 1 }),
    'STANDARD'
  );
});

test('full character sheet builds deterministically', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'C';
  }

  for (let i = 37; i <= 72; i++) {
    responses[i] = 'F';
  }

  const sheet = buildCharacterSheet({
    userId: '123',
    username: 'Victor',
    responses
  });

  assert.equal(sheet.locked, true);
  assert.equal(sheet.primary_domain, 'velocity');
  assert.equal(sheet.primary_score, 36);
  assert.equal(sheet.tier, 'T5');
  assert.equal(sheet.threat_class, 'ALPHA');

  assert.equal(sheet.domain_scores.velocity, 36);
  assert.equal(sheet.flavor_vectors.stealth, 36);

  assert.equal(
    sheet.manifestation.descriptor,
    'T5 velocity'
  );
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())

scripts = data.setdefault("scripts", {})
scripts["test:scoring"] = "node --test tests/scoring/*.test.js"
scripts["test:all"] = "node --test tests/*.test.js tests/e2e/*.test.js tests/scoring/*.test.js"

pkg.write_text(json.dumps(data, indent=2) + "\n")

print("PACKAGE_SCORING_TESTS=PASS")
PY

npm run test:scoring
npm run test:all

echo "SPARK_PROTOCOL_SCORING_ENGINE=PASS"
