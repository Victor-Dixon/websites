import test from 'node:test';
import assert from 'node:assert/strict';

import {
  adaptiveProgress,
  adaptiveQuestionsRemaining
} from '../../packages/scoring-engine/index.js';

test('before domain phase completes, total remains 36 and questions left updates', () => {
  const responses = { 1: 'G', 2: 'G' };
  const progress = adaptiveProgress(responses);

  assert.equal(progress.phase, 'domain');
  assert.equal(progress.total, 36);
  assert.equal(progress.answered, 2);
  assert.equal(progress.remaining, 34);
  assert.equal(progress.remaining_ids[0], 3);
});

test('after domain phase completes, total expands only to manifested Mind sub-affinity block', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.phase, 'flavor');
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
  assert.deepEqual(progress.remaining_ids, [73, 74, 75, 76, 77, 78]);
});

test('adaptive progress completes after manifested sub-affinity block answered', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 73; i <= 78; i++) responses[i] = 'A';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.phase, 'complete');
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 42);
  assert.equal(progress.remaining, 0);
  assert.equal(progress.complete, true);
});

test('questions left excludes irrelevant sub-affinity blocks', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const remaining = adaptiveQuestionsRemaining(responses);

  assert.equal(remaining.includes(61), false);
  assert.equal(remaining.includes(67), false);
  assert.equal(remaining.includes(73), true);
});
