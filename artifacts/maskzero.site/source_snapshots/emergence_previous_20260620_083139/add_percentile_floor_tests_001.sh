#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD PERCENTILE FLOOR TESTS =="

cat > tests/scoring/percentile_floor.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  buildCharacterSheet,
  percentileFromTier
} from '../../packages/scoring-engine/index.js';

test('percentile floor starts at 77 for classified emergence', () => {
  assert.equal(percentileFromTier('T1', 0), 77);
  assert.equal(percentileFromTier('T1', 1), 77.2);
  assert.equal(percentileFromTier('T1', 36), 84);
});

test('tier percentile bands are lore calibrated', () => {
  assert.equal(percentileFromTier('T2', 0), 85);
  assert.equal(percentileFromTier('T2', 36), 91);

  assert.equal(percentileFromTier('T3', 0), 92);
  assert.equal(percentileFromTier('T3', 36), 96);

  assert.equal(percentileFromTier('T4', 0), 97);
  assert.equal(percentileFromTier('T4', 36), 98);

  assert.equal(percentileFromTier('T5', 0), 99);
  assert.equal(percentileFromTier('T5', 36), 99);
});

test('character sheet never emits sub-77 percentile', () => {
  const responses = {};

  responses[1] = 'A';
  for (let i = 2; i <= 36; i++) responses[i] = 'B';
  for (let i = 49; i <= 54; i++) responses[i] = 'F';

  const sheet = buildCharacterSheet({
    userId: 'floor-test',
    username: 'FloorTest',
    responses
  });

  assert.ok(sheet.percentile >= 77);
});
JS

python - << 'PY'
from pathlib import Path
import json

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:percentile"] = "node --test tests/scoring/percentile_floor.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:answers && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord && npm run test:adaptive-full && npm run test:stress && npm run test:percentile"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PERCENTILE_TEST_SCRIPT=PASS")
PY

npm run test:percentile
