import test from 'node:test';
import assert from 'node:assert/strict';

import { calculatePercentile } from '../../packages/scoring-engine/index.js';

test('v5 percentile floor uses compressed formula', () => {
  assert.equal(
    calculatePercentile({ highestTier: 1, secondHighestTier: 0, powerCountTerm: 1 }),
    73.5
  );
});

test('v5 high but buildable percentile stays sane', () => {
  assert.equal(
    calculatePercentile({ highestTier: 5, secondHighestTier: 0, powerCountTerm: 1 }),
    83.5
  );
});
