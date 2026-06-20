import assert from 'node:assert/strict';
import test from 'node:test';

import {
  assertInvariant,
  createSession,
  currentQid,
  progress,
  reduceSession
} from '../../packages/quiz-button-session/index.js';

test('stale view refreshes without mutation', () => {
  let s = createSession({ userId: '123456789' });
  let r = reduceSession(s, { type: 'answer', qid: currentQid(s), answer: 'A', viewVersion: 0, userTail: '456789' });
  s = r.session;

  const before = Object.keys(s.responses).length;
  r = reduceSession(s, { type: 'answer', qid: currentQid(s), answer: 'B', viewVersion: 0, userTail: '456789' });

  assert.equal(r.outcome, 'stale_view');
  assert.equal(Object.keys(r.session.responses).length, before);
});

test('wrong question is rejected', () => {
  const s = createSession({ userId: '123456789' });
  const r = reduceSession(s, { type: 'answer', qid: 18, answer: 'A', viewVersion: 0, userTail: '456789' });
  assert.equal(r.outcome, 'wrong_question');
});

test('all A-G paths complete', () => {
  for (const a of ['A','B','C','D','E','F','G']) {
    let s = createSession({ userId: '123456789' });
    let safety = 0;
    while (!progress(s).complete) {
      safety++;
      assert.ok(safety < 100);
      const qid = currentQid(s);
      const r = reduceSession(s, { type: 'answer', qid, answer: a, viewVersion: s.viewVersion, userTail: '456789' });
      s = r.session;
      assertInvariant(s);
    }
    assert.equal(progress(s).complete, true);
  }
});

test('random nav and stale clicks never corrupt state', () => {
  let s = createSession({ userId: '123456789' });
  let seed = 42;
  const choices = ['A','B','C','D','E','F','G'];

  function rnd() {
    seed = (seed * 1103515245 + 12345) % 2147483648;
    return seed;
  }

  for (let i = 0; i < 300 && !progress(s).complete; i++) {
    const roll = rnd() % 10;
    let event;

    if (roll <= 5) event = { type: 'answer', qid: currentQid(s), answer: choices[rnd() % 7], viewVersion: s.viewVersion, userTail: '456789' };
    else if (roll === 6) event = { type: 'answer', qid: currentQid(s), answer: choices[rnd() % 7], viewVersion: Math.max(0, s.viewVersion - 1), userTail: '456789' };
    else if (roll === 7) event = { type: 'next', viewVersion: s.viewVersion, userTail: '456789' };
    else if (roll === 8) event = { type: 'prev', viewVersion: s.viewVersion, userTail: '456789' };
    else event = { type: 'unanswered', viewVersion: s.viewVersion, userTail: '456789' };

    const r = reduceSession(s, event);
    s = r.session;
    assertInvariant(s);
  }

  assert.ok(Object.keys(s.responses).length > 0);
});
