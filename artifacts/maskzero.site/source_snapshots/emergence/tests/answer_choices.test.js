import assert from 'node:assert/strict';
import test from 'node:test';

import {
  ANSWER_CHOICES,
  isValidAnswer,
  loadQuiz,
  normalizeQuestion,
  validAnswerChoices
} from '../packages/quiz-engine/index.js';

test('each canonical quiz question accepts A-G answers', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    for (const letter of ANSWER_CHOICES) {
      assert.equal(isValidAnswer(question, letter, index), true, `Q${index + 1} should accept ${letter}`);
    }
  });
});

test('each question exposes exactly seven domain choices', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.deepEqual(validAnswerChoices(question, index), ANSWER_CHOICES);
    assert.equal(Object.keys(normalizeQuestion(question, index).answers).length, 7);
  });
});
