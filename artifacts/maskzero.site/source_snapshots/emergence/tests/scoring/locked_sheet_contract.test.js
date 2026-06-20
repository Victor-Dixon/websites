import assert from 'node:assert/strict';
import test from 'node:test';

import { buildCharacterSheet, currentAdaptiveQuestionIds } from '../../packages/scoring-engine/index.js';

function buildAdaptiveResponses() {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = { answer: 'G' };
  for (const id of currentAdaptiveQuestionIds(responses)) {
    if (id > 36) responses[id] = { answer: 'G' };
  }
  return responses;
}

test('locked sheet contract emits canonical fields', () => {
  const responses = buildAdaptiveResponses();
  const sheet = buildCharacterSheet({ userId: 'truth', username: 'TruthUser', responses });

  assert.ok(sheet, 'sheet missing');
  assert.ok(sheet.primary_domain || sheet.manifested_domains?.length > 0, 'missing manifestation');
  assert.ok(sheet.tier !== undefined && sheet.tier !== null, 'missing tier');
  assert.ok(sheet.tier >= 1, 'tier must be at least 1 for manifested build');
  assert.ok(sheet.percentile >= 70 && sheet.percentile <= 100, 'percentile out of range');
  assert.ok(sheet.threat_class, 'missing threat class');

  assert.ok(sheet.character_packet, 'missing character_packet');
  assert.equal(
    sheet.character_packet.schema_version || sheet.character_packet.version,
    'spark_character_sheet_v1'
  );
});

test('locked sheet domains carry non-empty tier values', () => {
  const responses = buildAdaptiveResponses();
  const sheet = buildCharacterSheet({ userId: 'truth2', username: 'TruthUser2', responses });

  const domains = sheet.character_packet?.domains || [];
  assert.ok(domains.length > 0, 'packet domains missing');
  for (const domain of domains) {
    assert.ok(domain.tier >= 1, `domain tier missing for ${domain.name}`);
  }
});
