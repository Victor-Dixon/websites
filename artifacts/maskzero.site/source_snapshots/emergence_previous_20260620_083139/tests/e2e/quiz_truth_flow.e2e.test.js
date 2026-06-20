import assert from 'node:assert/strict';
import test from 'node:test';

import {
  createSession,
  answerCurrentQuestion,
  getCurrentQuestion,
  isComplete
} from '../../packages/quiz-button-session/index.js';

test('quiz truth flow completes without freeze for single-path answers', () => {
  const session = createSession({ userId: 'u1', username: 'DemoUser' });

  const seen = new Set();
  let guard = 0;

  while (!isComplete(session.state) && guard < 200) {
    const q = getCurrentQuestion(session.state);
    assert.ok(q, 'current question must exist');
    assert.ok(!seen.has(`${q.id}:${guard}`) || guard < 80, 'should progress without looping forever');
    answerCurrentQuestion(session.state, 'G');
    guard += 1;
  }

  assert.equal(isComplete(session.state), true);
  assert.ok(guard >= 36);
  assert.ok(guard <= 78);
});

test('quiz truth flow persists answered qids across adaptive transition', () => {
  const session = createSession({ userId: 'u2', username: 'TransitionUser' });

  for (let i = 0; i < 36; i++) {
    const q = getCurrentQuestion(session.state);
    assert.ok(q, 'domain question missing');
    answerCurrentQuestion(session.state, 'G');
  }

  const after36 = Object.keys(session.state.responses || {}).map(Number);
  assert.ok(after36.includes(36), 'domain answer 36 should persist');

  const q37 = getCurrentQuestion(session.state);
  assert.ok(q37, 'adaptive question should exist after 36');
  answerCurrentQuestion(session.state, 'G');

  const answered = Object.keys(session.state.responses || {}).map(Number);
  assert.ok(answered.includes(q37.id), `answered qid should persist: ${q37.id}`);
});
