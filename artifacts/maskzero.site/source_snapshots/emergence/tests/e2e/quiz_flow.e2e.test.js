import assert from 'node:assert/strict';
import test from 'node:test';

import {
  buildBasicResults,
  loadQuiz,
  normalizeQuestion
} from '../../packages/quiz-engine/index.js';

import {
  interpretQuizResult
} from '../../packages/aegis-interpreter/index.js';

test('e2e quiz flow: answer 72 questions, lock result, create AEGIS readout', async () => {
  const quiz = loadQuiz();
  const responses = {};

  quiz.questions.forEach((question, index) => {
    const normalized = normalizeQuestion(question, index);
    const letters = Object.keys(normalized.answers);

    assert.deepEqual(letters, ['A', 'B', 'C', 'D', 'E', 'F', 'G']);

    responses[normalized.id] = letters[index % letters.length];
  });

  const results = buildBasicResults(quiz, responses);

  assert.equal(results.total_answered, 72);
  assert.equal(results.total_questions, 72);
  assert.equal(results.locked, true);

  const aegisPacket = await interpretQuizResult({
    quiz,
    responses,
    results
  });

  assert.equal(aegisPacket.provider, 'mock');
  assert.equal(typeof aegisPacket.prompt, 'string');
  assert.ok(aegisPacket.prompt.includes('AEGIS Classification Services'));
  assert.equal(typeof aegisPacket.interpretation.classification_title, 'string');
  assert.equal(typeof aegisPacket.interpretation.summary, 'string');
  assert.equal(typeof aegisPacket.interpretation.primary_signal, 'string');
  assert.equal(typeof aegisPacket.interpretation.risk_note, 'string');
  assert.equal(typeof aegisPacket.interpretation.next_step, 'string');
});
