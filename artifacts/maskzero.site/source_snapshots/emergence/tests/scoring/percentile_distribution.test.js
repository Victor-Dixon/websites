import test from 'node:test';
import assert from 'node:assert/strict';

import { calculatePercentile } from '../../packages/scoring-engine/index.js';

test('simulated v5 percentile distribution stays commercially sane', () => {
  const values = [
    calculatePercentile({ highestTier: 3, secondHighestTier: 2, powerCountTerm: 2 }),
    calculatePercentile({ highestTier: 4, secondHighestTier: 1, powerCountTerm: 1 }),
    calculatePercentile({ highestTier: 4, secondHighestTier: 2, powerCountTerm: 3 }),
    calculatePercentile({ highestTier: 5, secondHighestTier: 0, powerCountTerm: 1 })
  ];

  const avg = values.reduce((a, b) => a + b, 0) / values.length;

  assert.ok(avg >= 79, `average percentile too low: ${avg}`);
  assert.ok(avg <= 92, `average percentile too high: ${avg}`);
});
