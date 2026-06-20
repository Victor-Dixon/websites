import test from 'node:test';
import assert from 'node:assert/strict';

import {
  adaptiveQuestionIds,
  adaptiveProgress,
  calculateDomainScores,
  manifestedDomains,
  buildCharacterSheet,
  buildComicProfile
} from '../../packages/scoring-engine/index.js';

test('adaptive quiz asks all domain questions then only manifested Mind sub-affinity block', () => {
  const responses = {};

  let ids = adaptiveQuestionIds(responses);
  assert.equal(ids.length, 36);
  assert.equal(ids[0], 1);
  assert.equal(ids[35], 36);

  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  ids = adaptiveQuestionIds(responses);
  assert.deepEqual(ids, [73, 74, 75, 76, 77, 78]);

  const progress = adaptiveProgress(responses);
  assert.equal(progress.phase, 'flavor');
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
});

test('manifested domains include signals at 25 percent gate', () => {
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

test('all-G v5 answers manifest Mind as solo T5', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const scores = calculateDomainScores(responses);
  const domains = manifestedDomains(scores);

  assert.equal(scores.mind, 39);
  assert.deepEqual(domains, ['mind']);
});

test('comic profile hides raw scores and exposes styled profile fields', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 73; i <= 78; i++) responses[i] = 'B';

  const sheet = buildCharacterSheet(responses);
  const profile = buildComicProfile(sheet);

  const serialized = JSON.stringify(profile);

  assert.equal(profile.sheet.version, 'spark-protocol-v5');
  assert.ok(profile.cover_line.includes('MIND'));
  assert.ok(profile.cover_line.includes('percentile'));
  assert.ok(profile.sections.some(line => line.includes('Primary Manifestation')));
  assert.equal(serialized.includes('"domain_scores"'), true);
});
