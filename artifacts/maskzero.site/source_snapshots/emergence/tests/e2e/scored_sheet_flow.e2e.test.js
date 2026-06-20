import assert from 'node:assert/strict';
import test from 'node:test';

import {
  loadQuiz,
  normalizeQuestion,
  buildBasicResults
} from '../../packages/quiz-engine/index.js';

import {
  buildCharacterSheet
} from '../../packages/scoring-engine/index.js';

test('e2e scored sheet flow builds locked deterministic sheet after 72 answers', () => {
  const quiz = loadQuiz();
  const responses = {};

  quiz.questions.forEach((question, index) => {
    const normalized = normalizeQuestion(question, index);
    responses[normalized.id] = index < 36 ? 'C' : 'F';
  });

  const results = buildBasicResults(quiz, responses);
  assert.equal(results.locked, true);

  const sheet = buildCharacterSheet({
    userId: '150',
    username: 'TheEmergenceTest',
    responses
  });

  assert.equal(sheet.schema_version, 'spark_character_sheet_v1');
  assert.equal(sheet.locked, true);
  assert.equal(sheet.primary_domain, 'velocity');
  assert.equal(sheet.primary_score, 36);
  assert.equal(sheet.tier, 'T5');
  assert.equal(sheet.threat_class, 'ALPHA');
  assert.equal(sheet.domain_scores.velocity, 36);
  assert.equal(sheet.flavor_vectors.stealth, 36);
});
