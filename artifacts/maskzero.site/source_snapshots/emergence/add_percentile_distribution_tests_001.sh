#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD PERCENTILE DISTRIBUTION TESTS =="

cat > tests/scoring/percentile_distribution.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  buildCharacterSheet,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

const CHOICES = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

function seededChoice(seedState) {
  seedState.value = (seedState.value * 1103515245 + 12345) % 2147483648;
  return CHOICES[seedState.value % CHOICES.length];
}

function simulateRun(seed) {
  const responses = {};
  const seedState = { value: seed };
  let safety = 0;

  while (!adaptiveProgress(responses).complete) {
    safety++;
    assert.ok(safety < 100, 'simulation should not freeze');

    const ids = currentAdaptiveQuestionIds(responses);
    const nextId = ids.find(id => responses[id] === undefined);
    assert.ok(nextId, 'next question should exist');

    responses[nextId] = seededChoice(seedState);
  }

  return buildCharacterSheet({
    userId: `sim-${seed}`,
    username: `Sim${seed}`,
    responses
  });
}

test('simulated percentile distribution stays commercially sane', () => {
  const sheets = [];

  for (let seed = 1; seed <= 300; seed++) {
    sheets.push(simulateRun(seed));
  }

  const percentiles = sheets.map(sheet => sheet.percentile);
  const average = percentiles.reduce((sum, value) => sum + value, 0) / percentiles.length;
  const max = Math.max(...percentiles);
  const min = Math.min(...percentiles);

  assert.ok(average >= 80, `average percentile too low: ${average}`);
  assert.ok(average <= 85, `average percentile too high: ${average}`);
  assert.ok(max <= 93, `max percentile too high: ${max}`);
  assert.ok(min >= 77, `min percentile too low: ${min}`);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:percentile-distribution"] = "node --test tests/scoring/percentile_distribution.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:answers && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord && npm run test:adaptive-full && npm run test:stress && npm run test:percentile && npm run test:percentile-distribution"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PERCENTILE_DISTRIBUTION_SCRIPT=PASS")
PY

npm run test:percentile-distribution
