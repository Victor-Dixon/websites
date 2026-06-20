import test from 'node:test';
import assert from 'node:assert/strict';

import {
  domainFlavorRange,
  manifestedDomains,
  calculateDomainScores,
  currentAdaptiveQuestionIds,
  adaptiveProgress
} from '../../packages/scoring-engine/index.js';

test('adaptive engine starts at domain phase only', () => {
  const ids = currentAdaptiveQuestionIds({});
  assert.equal(ids.length, 36);
  assert.equal(ids[0], 1);
  assert.equal(ids[35], 36);
});

test('each v5 domain maps to a valid sub-affinity range', () => {
  const expected = {
    titan: [37, 42],
    velocity: [43, 48],
    energy: [49, 54],
    specter: [55, 60],
    omni: [61, 66],
    primal: [67, 72],
    mind: [73, 78]
  };

  for (const [domain, range] of Object.entries(expected)) {
    assert.deepEqual(domainFlavorRange(domain), range);
  }
});

test('G answers manifest Mind and expand to Mind block only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const scores = calculateDomainScores(responses);
  const domains = manifestedDomains(scores);
  const ids = currentAdaptiveQuestionIds(responses);

  assert.equal(scores.mind, 39);
  assert.deepEqual(domains, ['mind']);
  assert.deepEqual(ids, [73, 74, 75, 76, 77, 78]);
});

test('adaptive questions left updates as manifested block is answered', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  responses[73] = 'A';
  responses[74] = 'B';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 38);
  assert.equal(progress.remaining, 4);
  assert.deepEqual(progress.remaining_ids, [75, 76, 77, 78]);
});

test('adaptive quiz completes without asking irrelevant flavor blocks', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 73; i <= 78; i++) responses[i] = 'C';

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.includes(43), false);
  assert.equal(ids.includes(55), false);
  assert.equal(ids.includes(67), false);
  assert.equal(ids.length, 0);
  assert.equal(progress.complete, true);
  assert.equal(progress.remaining, 0);
});
