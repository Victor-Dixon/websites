import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  buildCharacterSheet,
  calculatePercentile,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

function canonicalTier(sheet) {
  if (Number.isInteger(sheet.tier)) return sheet.tier;
  if (Number.isInteger(sheet.highestTier)) return sheet.highestTier;
  if (Number.isInteger(sheet.primaryTier)) return sheet.primaryTier;
  return sheet?.domains?.[0]?.tier
    || sheet?.manifestedDomains?.[0]?.tier
    || sheet?.character?.domains?.[0]?.tier
    || null;
}

function canonicalPowers(sheet) {
  return sheet.powers
    || sheet.manifestedPowers
    || sheet?.character?.powers
    || [];
}

function canonicalPercentile(sheet) {
  return sheet.percentile
    || sheet.percentileScore
    || sheet?.character?.percentile
    || null;
}


test('domain-only phase never completes before 36 answers', () => {
  const responses = {};
  for (let i = 1; i <= 35; i++) responses[i] = { answer: 'G' };

  const progress = adaptiveProgress(responses);

  assert.equal(progress.complete, false);
  assert.equal(progress.answered, 35);
  assert.equal(progress.remaining, 1);
  assert.equal(progress.total, 36);
});

test('after 36 domain answers, adaptive block adds exactly the manifested block', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = { answer: 'G' };

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 6);
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
});

test('completed adaptive run produces valid locked sheet math', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = { answer: 'G' };

  for (const id of currentAdaptiveQuestionIds(responses)) {
    if (id > 36) responses[id] = { answer: 'G' };
  }

  const progress = adaptiveProgress(responses);
  const sheet = buildCharacterSheet(responses);

  assert.equal(progress.complete, true);
  assert.equal(progress.remaining, 0);
  assert.ok(sheet.percentile >= 70);
  assert.ok(sheet.percentile <= 100);
  assert.ok(sheet.primary_domain || sheet.manifested_domains?.length > 0);
  assert.ok(canonicalTier(sheet));
  assert.ok(sheet.threat_class);
});

test('percentile remains bounded across score range', () => {
  for (let score = 0; score <= 100; score++) {
    const pct = calculatePercentile(score);
    assert.ok(pct >= 70);
    assert.ok(pct <= 100);
  }
});
