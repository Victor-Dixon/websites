#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADAPTIVE QUIZ + COMIC PROFILE =="

cat >> packages/scoring-engine/index.js << 'JS'

export function manifestedDomains(domainScores) {
  const max = Math.max(...Object.values(domainScores));
  return Object.entries(domainScores)
    .filter(([, score]) => score === max || score >= Math.ceil(max * 0.75))
    .map(([domain]) => domain);
}

export function domainFlavorRange(domain) {
  const ranges = {
    titan: [37, 42],
    velocity: [43, 48],
    inferno: [49, 54],
    specter: [55, 60],
    omni: [61, 66],
    echo: [67, 72],
    bulwark: [37, 42]
  };

  return ranges[domain] || [37, 72];
}

export function adaptiveQuestionIds(responses) {
  const domainScores = calculateDomainScores(responses);
  const domains = manifestedDomains(domainScores);

  const ids = new Set();

  for (let i = 1; i <= 36; i++) ids.add(i);

  for (const domain of domains) {
    const [start, end] = domainFlavorRange(domain);
    for (let i = start; i <= end; i++) ids.add(i);
  }

  return [...ids].sort((a, b) => a - b);
}

export function buildComicProfile(sheet) {
  return {
    title: `${sheet.username}: Classified Emergence Profile`,
    subtitle: `Manifestation Class ${sheet.tier}`,
    cover_line: `A ${sheet.primary_domain.toUpperCase()}-type emergence with ${sheet.threat_class} threat-band behavior.`,
    stat_blocks: [
      `Primary Manifestation: ${sheet.primary_domain.toUpperCase()}`,
      `Power Tier: ${sheet.tier}`,
      `Threat Band: ${sheet.threat_class}`,
      `AEGIS Lock: IMMUTABLE`
    ],
    back_matter: [
      'Origin Signal: psychological resonance pattern confirmed.',
      'Protocol Note: raw scoring matrix sealed to prevent profile gaming.',
      'Battle Eligibility: approved after codename registration.',
      'Reader Advisory: this subject may evolve narratively, but the locked sheet does not drift.'
    ]
  };
}
JS

cat > tests/scoring/adaptive_quiz.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveQuestionIds,
  buildCharacterSheet,
  buildComicProfile,
  calculateDomainScores,
  manifestedDomains
} from '../../packages/scoring-engine/index.js';

test('adaptive quiz asks all domain questions but only manifested sub-affinity block', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'C';

  const ids = adaptiveQuestionIds(responses);

  assert.equal(ids.includes(1), true);
  assert.equal(ids.includes(36), true);
  assert.equal(ids.includes(43), true);
  assert.equal(ids.includes(48), true);
  assert.equal(ids.includes(49), false);
  assert.equal(ids.includes(72), false);
});

test('manifested domains include co-primary signals at 75 percent gate', () => {
  const responses = {};
  for (let i = 1; i <= 27; i++) responses[i] = 'C';
  for (let i = 28; i <= 36; i++) responses[i] = 'A';

  const scores = calculateDomainScores(responses);
  const domains = manifestedDomains(scores);

  assert.deepEqual(domains, ['velocity']);
});

test('comic profile hides raw scores and exposes styled profile fields', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'C';
  for (let i = 37; i <= 72; i++) responses[i] = 'F';

  const sheet = buildCharacterSheet({
    userId: '1',
    username: 'TestSubject',
    responses
  });

  const profile = buildComicProfile(sheet);

  assert.ok(profile.title.includes('TestSubject'));
  assert.ok(profile.subtitle.includes('T5'));
  assert.ok(profile.cover_line.includes('VELOCITY'));
  assert.equal(profile.stat_blocks.some(line => line.includes('36')), false);
  assert.equal(profile.back_matter.some(line => line.includes('sealed')), true);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:adaptive"] = "node --test tests/scoring/adaptive_quiz.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:e2e && npm run test:scoring && npm run test:adaptive"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("ADAPTIVE_TEST_SCRIPT=PASS")
PY

npm run test:adaptive
npm run test:mobile

echo "ADAPTIVE_QUIZ_COMIC_PROFILE=PASS"
