import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

test('discord adaptive model starts with 36 domain questions', () => {
  const responses = {};
  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 36);
  assert.equal(progress.total, 36);
  assert.equal(progress.remaining, 36);
});

test('discord adaptive model expands to manifested Mind block after G manifests', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 6);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
});

test('discord adaptive model completes after manifested block only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  for (const id of currentAdaptiveQuestionIds(responses).filter(id => id > 36)) {
    responses[id] = 'G';
  }

  const progress = adaptiveProgress(responses);

  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 42);
  assert.equal(progress.remaining, 0);
  assert.equal(progress.complete, true);
});
