#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD ADAPTIVE QUIZ STRESS + PERCENTILE TESTS =="

python - << 'PY'
from pathlib import Path

p = Path("packages/scoring-engine/index.js")
s = p.read_text()

insert = """
export function calculatePercentile(primaryScore, totalDomainQuestions = 36) {
  if (primaryScore <= 0) return 1;

  const raw = Math.round((primaryScore / totalDomainQuestions) * 100);

  if (raw < 1) return 1;
  if (raw > 99) return 99;

  return raw;
}
"""

if "export function calculatePercentile" not in s:
    marker = "export function buildCharacterSheet"
    s = s.replace(marker, insert + "\\n" + marker)

s = s.replace(
"""  const tier = determineTier(primaryScore);
  const threat_class = determineThreatClass(tier, flavor_vectors);""",
"""  const tier = determineTier(primaryScore);
  const percentile = calculatePercentile(primaryScore);
  const threat_class = determineThreatClass(tier, flavor_vectors);"""
)

s = s.replace(
"""    primary_score,
    tier,
    threat_class,""",
"""    primary_score,
    tier,
    percentile,
    threat_class,"""
)

p.write_text(s)
print("PERCENTILE_ENGINE_PATCH=PASS")
PY

cat > tests/e2e/adaptive_quiz_stress_percentile.e2e.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  loadQuiz,
  normalizeQuestion
} from '../../packages/quiz-engine/index.js';

import {
  adaptiveProgress,
  buildCharacterSheet,
  calculatePercentile,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

function simulateAdaptiveRun(domainAnswer, affinityAnswer) {
  const quiz = loadQuiz();
  const responses = {};
  let safety = 0;

  while (!adaptiveProgress(responses).complete) {
    safety++;
    assert.ok(safety < 100, 'adaptive quiz should not loop forever');

    const ids = currentAdaptiveQuestionIds(responses);
    const nextId = ids.find(id => responses[id] === undefined);

    assert.ok(nextId, 'next unanswered adaptive question should exist');

    const question = normalizeQuestion(quiz.questions[nextId - 1], nextId - 1);
    const valid = Object.keys(question.answers);

    assert.deepEqual(valid, ['A', 'B', 'C', 'D', 'E', 'F', 'G']);

    responses[nextId] = nextId <= 36 ? domainAnswer : affinityAnswer;
  }

  const progress = adaptiveProgress(responses);
  const sheet = buildCharacterSheet({
    userId: `stress-${domainAnswer}-${affinityAnswer}`,
    username: `Stress${domainAnswer}${affinityAnswer}`,
    responses
  });

  return { responses, progress, sheet };
}

test('percentile formula is bounded and deterministic', () => {
  assert.equal(calculatePercentile(0), 1);
  assert.equal(calculatePercentile(1), 3);
  assert.equal(calculatePercentile(18), 50);
  assert.equal(calculatePercentile(27), 75);
  assert.equal(calculatePercentile(36), 99);
  assert.equal(calculatePercentile(99), 99);
});

test('all single-domain A-G runs complete uninterrupted with correct percentile', () => {
  for (const domainAnswer of ['A', 'B', 'C', 'D', 'E', 'F', 'G']) {
    const { progress, sheet } = simulateAdaptiveRun(domainAnswer, 'G');

    assert.equal(progress.complete, true);
    assert.equal(progress.remaining, 0);
    assert.equal(progress.total, 42);

    assert.equal(sheet.primary_score, 36);
    assert.equal(sheet.percentile, 99);
    assert.equal(sheet.locked, true);
  }
});

test('all domain and affinity A-G pair combinations complete uninterrupted', () => {
  let runs = 0;

  for (const domainAnswer of ['A', 'B', 'C', 'D', 'E', 'F', 'G']) {
    for (const affinityAnswer of ['A', 'B', 'C', 'D', 'E', 'F', 'G']) {
      const { progress, sheet } = simulateAdaptiveRun(domainAnswer, affinityAnswer);

      assert.equal(progress.complete, true);
      assert.equal(progress.remaining, 0);
      assert.equal(progress.total, 42);
      assert.equal(sheet.primary_score, 36);
      assert.equal(sheet.percentile, 99);
      assert.equal(sheet.schema_version, 'spark_character_sheet_v1');

      runs++;
    }
  }

  assert.equal(runs, 49);
});

test('mixed answer run expands to co-primary blocks and percentile is correct', () => {
  const responses = {};

  for (let i = 1; i <= 18; i++) responses[i] = 'A';
  for (let i = 19; i <= 36; i++) responses[i] = 'G';

  let progress = adaptiveProgress(responses);

  assert.equal(progress.total, 48);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 12);

  for (const id of progress.remaining_ids) {
    responses[id] = 'C';
  }

  progress = adaptiveProgress(responses);

  const sheet = buildCharacterSheet({
    userId: 'mixed',
    username: 'MixedSubject',
    responses
  });

  assert.equal(progress.complete, true);
  assert.equal(progress.total, 48);
  assert.equal(sheet.primary_score, 18);
  assert.equal(sheet.percentile, 50);
});

test('deterministic seeded pseudo-random adaptive runs do not freeze', () => {
  const choices = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
  let seed = 1337;

  function nextChoice() {
    seed = (seed * 1103515245 + 12345) % 2147483648;
    return choices[seed % choices.length];
  }

  for (let run = 0; run < 100; run++) {
    const quiz = loadQuiz();
    const responses = {};
    let safety = 0;

    while (!adaptiveProgress(responses).complete) {
      safety++;
      assert.ok(safety < 100, `run ${run} should not freeze`);

      const ids = currentAdaptiveQuestionIds(responses);
      const nextId = ids.find(id => responses[id] === undefined);
      assert.ok(nextId, `run ${run} should have a next question`);

      const q = normalizeQuestion(quiz.questions[nextId - 1], nextId - 1);
      const answer = nextChoice();

      assert.ok(Object.keys(q.answers).includes(answer));
      responses[nextId] = answer;
    }

    const progress = adaptiveProgress(responses);
    const sheet = buildCharacterSheet({
      userId: `random-${run}`,
      username: `Random${run}`,
      responses
    });

    assert.equal(progress.complete, true);
    assert.ok(progress.total >= 42);
    assert.ok(progress.total <= 78);
    assert.ok(sheet.percentile >= 1);
    assert.ok(sheet.percentile <= 99);
  }
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:stress"] = "node --test tests/e2e/adaptive_quiz_stress_percentile.e2e.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:answers && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord && npm run test:adaptive-full && npm run test:stress"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("STRESS_TEST_SCRIPT=PASS")
PY

npm run test:stress
npm run test:mobile

echo "ADAPTIVE_STRESS_PERCENTILE_TESTS=PASS"
