import assert from 'node:assert/strict';
import test from 'node:test';

import {
  ANSWER_CHOICES,
  loadQuiz,
  normalizeQuestion
} from '../packages/quiz-engine/index.js';

test('canonical quiz loads with 72 questions and A-G schema', () => {
  const quiz = loadQuiz();

  assert.equal(quiz.form_id, 'SPARK-72');
  assert.equal(quiz.version, 'MVP-2026-05-23');
  assert.equal(Array.isArray(quiz.questions), true);
  assert.equal(quiz.questions.length, 72);
  assert.deepEqual(quiz.scoring.answer_choices, ANSWER_CHOICES);
});

test('canonical quiz has required metadata', () => {
  const quiz = loadQuiz();

  assert.equal(typeof quiz.title, 'string');
  assert.ok(quiz.title.includes('Spark Protocol'));
  assert.equal(typeof quiz.preamble, 'string');
  assert.equal(typeof quiz.instructions, 'string');
  assert.equal(typeof quiz.scoring, 'object');
  assert.equal(quiz.scoring.domain_questions, '1-36');
});

test('every question has id, text, and seven A-G options', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.equal(question.id, index + 1);
    assert.equal(typeof question.question, 'string');
    assert.ok(question.question.length > 10);

    const normalized = normalizeQuestion(question, index);
    assert.deepEqual(Object.keys(normalized.answers), ANSWER_CHOICES);

    for (const answer of Object.values(normalized.answers)) {
      assert.equal(typeof answer, 'string');
      assert.ok(answer.length > 3);
    }
  });
});

test('domain and flavor question ranges are structurally valid', () => {
  const quiz = loadQuiz();

  const domain = quiz.questions.slice(0, 36);
  const flavor = quiz.questions.slice(36, 72);

  assert.equal(domain[0].id, 1);
  assert.equal(domain.at(-1).id, 36);
  assert.equal(flavor[0].id, 37);
  assert.equal(flavor.at(-1).id, 72);
});
