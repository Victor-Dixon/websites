#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD ADAPTIVE QUESTIONS LEFT TESTS =="

cat >> packages/scoring-engine/index.js << 'JS'

export function currentAdaptiveQuestionIds(responses) {
  const answeredDomainCount = Object.keys(responses)
    .map(Number)
    .filter(qid => qid >= 1 && qid <= 36)
    .length;

  if (answeredDomainCount < 36) {
    return Array.from({ length: 36 }, (_, i) => i + 1);
  }

  return adaptiveQuestionIds(responses);
}

export function adaptiveQuestionsRemaining(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const answered = new Set(Object.keys(responses).map(Number));

  return ids.filter(id => !answered.has(id));
}

export function adaptiveProgress(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const remaining = adaptiveQuestionsRemaining(responses);

  return {
    total: ids.length,
    answered: ids.length - remaining.length,
    remaining: remaining.length,
    remaining_ids: remaining,
    complete: remaining.length === 0
  };
}
JS

cat > tests/scoring/adaptive_progress.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  adaptiveQuestionsRemaining,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

test('before domain phase completes, total remains 36 and questions left updates', () => {
  const responses = {};

  for (let i = 1; i <= 10; i++) responses[i] = 'C';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.total, 36);
  assert.equal(progress.answered, 10);
  assert.equal(progress.remaining, 26);
  assert.equal(progress.remaining_ids[0], 11);
  assert.equal(progress.complete, false);
});

test('after domain phase completes, total expands only to manifested sub-affinity block', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) responses[i] = 'C';

  const ids = currentAdaptiveQuestionIds(responses);
  const remaining = adaptiveQuestionsRemaining(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 42);
  assert.equal(ids.includes(43), true);
  assert.equal(ids.includes(48), true);
  assert.equal(ids.includes(49), false);

  assert.deepEqual(remaining, [43, 44, 45, 46, 47, 48]);
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
  assert.equal(progress.complete, false);
});

test('adaptive progress completes after manifested sub-affinity block answered', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) responses[i] = 'C';
  for (let i = 43; i <= 48; i++) responses[i] = 'F';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 42);
  assert.equal(progress.remaining, 0);
  assert.deepEqual(progress.remaining_ids, []);
  assert.equal(progress.complete, true);
});

test('questions left excludes irrelevant sub-affinity blocks', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) responses[i] = 'A';

  const ids = currentAdaptiveQuestionIds(responses);

  assert.equal(ids.includes(37), true);
  assert.equal(ids.includes(42), true);
  assert.equal(ids.includes(43), false);
  assert.equal(ids.includes(72), false);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:adaptive-progress"] = "node --test tests/scoring/adaptive_progress.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("ADAPTIVE_PROGRESS_TEST_SCRIPT=PASS")
PY

npm run test:adaptive-progress
npm run test:mobile

echo "ADAPTIVE_QUESTIONS_LEFT_TESTS=PASS"
