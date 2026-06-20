import test from 'node:test';
import assert from 'node:assert/strict';

import {
  calculateDomainScores,
  calculateFlavorVectors,
  determinePrimaryDomain,
  manifestedDomains,
  determineTier,
  calculatePercentile,
  determineThreatClass,
  buildCharacterSheet
} from '../../packages/scoring-engine/index.js';

test('v5 domain scoring counts weighted first 36 responses only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  responses[37] = 'A';
  responses[78] = 'A';

  const scores = calculateDomainScores(responses);

  assert.equal(scores.mind, 39);
  assert.equal(scores.omni, 0);
});

test('v5 flavor vectors count questions 37-78 including Mind block', () => {
  const responses = {};
  for (let i = 73; i <= 78; i++) responses[i] = 'A';
  responses[1] = 'G';

  const vectors = calculateFlavorVectors(responses);
  const mindTotal = Object.entries(vectors)
    .filter(([key]) => key.startsWith('mind.'))
    .reduce((sum, [, value]) => sum + value, 0);

  assert.equal(mindTotal, 6);
  assert.equal(vectors['mind.telepathy'], 1);
  assert.equal(vectors['mind.mind_control'], 1);
  assert.equal(vectors['mind.telekinesis'], 1);
  assert.equal(vectors['mind.illusion'], 1);
  assert.equal(vectors['mind.psychic_assault'], 1);
  assert.equal(vectors['mind.psychic_defense'], 1);
});

test('v5 tier thresholds classify correctly', () => {
  assert.equal(determineTier(0), 0);
  assert.equal(determineTier(1), 1);
  assert.equal(determineTier(8), 2);
  assert.equal(determineTier(16), 3);
  assert.equal(determineTier(24), 4);
  assert.equal(determineTier(35), 5);
  assert.equal(determineTier(39), 5);
});

test('v5 manifestation gate honors 25 percent threshold and ties', () => {
  assert.deepEqual(
    manifestedDomains({
      titan: 36,
      velocity: 9,
      energy: 8,
      specter: 0,
      omni: 0,
      primal: 0,
      mind: 0
    }),
    ['titan', 'velocity']
  );

  assert.deepEqual(
    determinePrimaryDomain({
      titan: 10,
      velocity: 10,
      energy: 2,
      specter: 0,
      omni: 0,
      primal: 0,
      mind: 0
    }),
    ['titan', 'velocity']
  );
});

test('v5 percentile formula uses compressed tier inputs', () => {
  assert.equal(
    calculatePercentile({
      highestTier: 4,
      secondHighestTier: 2,
      powerCountTerm: 3
    }),
    85
  );
});

test('v5 strategic threat detects Mind Control', () => {
  assert.equal(
    determineThreatClass(3, { 'mind.mind_control': 1 }),
    'Strategic Threat'
  );
});

test('v5 character sheet builds deterministic packet', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 73; i <= 78; i++) responses[i] = 'B';

  const sheet = buildCharacterSheet(responses);

  assert.equal(sheet.version, 'spark-protocol-v5');
  assert.equal(sheet.domain_scores.mind, 39);
  assert.equal(sheet.tier, 5);
  assert.equal(sheet.primary_domain, 'mind');
  assert.deepEqual(sheet.manifested_domains, ['mind']);
  assert.equal(sheet.character_packet.ruleset, 'Spark Protocol v5');
  assert.equal(sheet.character_packet.domains[0].name, 'mind');
});
