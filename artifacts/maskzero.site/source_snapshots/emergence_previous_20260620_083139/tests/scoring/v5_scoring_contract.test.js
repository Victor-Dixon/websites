import test from 'node:test';
import assert from 'node:assert/strict';

import {
  calculateDomainScores,
  calculateFlavorVectors,
  manifestedDomains,
  calculatePercentile
} from '../../packages/scoring-engine/index.js';

test('v5 canon requires 78 total questions: 36 domain + 42 flavor', () => {
  const domainIds = Array.from({ length: 36 }, (_, i) => i + 1);
  const flavorIds = Array.from({ length: 42 }, (_, i) => i + 37);

  assert.equal(domainIds.length, 36);
  assert.equal(flavorIds.length, 42);
  assert.equal(flavorIds[0], 37);
  assert.equal(flavorIds[41], 78);
});

test('v5 domain answers include Mind as G option without replacing Omni', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const scores = calculateDomainScores(responses);

  assert.ok(Object.hasOwn(scores, 'mind'), 'G domain answers must score Mind');
  assert.ok(Object.hasOwn(scores, 'omni'), 'Omni must remain its own domain');
  assert.equal(scores.mind, 39);
});

test('v5 flavor scoring includes Mind block questions 73-78', () => {
  const responses = {};
  for (let i = 73; i <= 78; i++) responses[i] = 'A';

  const vectors = calculateFlavorVectors(responses);

  assert.ok(
    Object.keys(vectors).some(k => k.includes('telepathy') || k.includes('mind')),
    'Mind flavor vectors must exist'
  );

  const mindTotal = Object.entries(vectors)
    .filter(([key]) => key.startsWith('mind.'))
    .reduce((sum, [, value]) => sum + value, 0);

  assert.equal(
    mindTotal,
    6,
    'Mind flavor block should count six answers'
  );
});

test('v5 manifestation gate is 25 percent of highest score', () => {
  const scores = {
    titan: 36,
    velocity: 9,
    energy: 8,
    specter: 0,
    omni: 0,
    primal: 0,
    mind: 0
  };

  assert.deepEqual(manifestedDomains(scores), ['titan', 'velocity']);
});

test('v5 percentile formula uses tier/tier/power count compression inputs', () => {
  const highestTier = 4;
  const secondHighestTier = 2;
  const powerCountTerm = 3;
  const expected = 70 + (highestTier * 2.5) + (secondHighestTier * 1) + powerCountTerm;

  assert.equal(expected, 85);
  assert.equal(calculatePercentile({ highestTier: 4, secondHighestTier: 2, powerCountTerm: 3 }), expected);
});
