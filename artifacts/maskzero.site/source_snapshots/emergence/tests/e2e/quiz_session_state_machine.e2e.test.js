import assert from 'node:assert/strict';
import test from 'node:test';

import {
  activeQuestionId,
  assertSessionInvariant,
  createSession,
  reduceQuizSession,
  sessionProgress
} from '../../packages/quiz-session/index.js';

const CHOICES = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

function runUntilComplete(pattern) {
  let session = createSession('stress');
  let safety = 0;

  while (!sessionProgress(session).complete) {
    safety++;
    assert.ok(safety < 200, 'session should not freeze');

    const answer = pattern(session, safety);
    const before = activeQuestionId(session);

    session = reduceQuizSession(session, {
      type: 'answer',
      answer
    });

    assertSessionInvariant(session);

    const progress = sessionProgress(session);
    assert.ok(
      session.responses[before] !== undefined,
      `answered qid should persist: ${before}`
    );

    assert.ok(progress.remaining >= 0);
  }

  return session;
}

test('single-path all choices complete without freezing', () => {
  for (const answer of CHOICES) {
    const session = runUntilComplete(() => answer);
    const progress = sessionProgress(session);

    assert.equal(progress.complete, true);
    assert.ok(progress.total >= 42);
    assert.ok(progress.total <= 78);
  }
});

test('all domain/affinity pair choices complete without freezing', () => {
  for (const domainAnswer of CHOICES) {
    for (const affinityAnswer of CHOICES) {
      const session = runUntilComplete((session) => {
        const qid = activeQuestionId(session);
        return qid <= 36 ? domainAnswer : affinityAnswer;
      });

      assert.equal(sessionProgress(session).complete, true);
    }
  }
});

test('nav spam mixed with answers cannot desync cursor/progress', () => {
  let session = createSession('nav-spam');

  for (let i = 0; i < 300; i++) {
    const op = i % 5;

    if (op === 0) session = reduceQuizSession(session, { type: 'next' });
    if (op === 1) session = reduceQuizSession(session, { type: 'prev' });
    if (op === 2) session = reduceQuizSession(session, { type: 'unanswered' });
    if (op === 3 || op === 4) {
      session = reduceQuizSession(session, {
        type: 'answer',
        answer: CHOICES[i % CHOICES.length]
      });
    }

    assertSessionInvariant(session);

    if (sessionProgress(session).complete) break;
  }

  assert.ok(Object.keys(session.responses).length > 0);
});

test('deterministic random click sequences complete without freeze', () => {
  let seed = 987654321;

  function nextInt() {
    seed = (seed * 1103515245 + 12345) % 2147483648;
    return seed;
  }

  for (let run = 0; run < 100; run++) {
    let session = createSession(`random-${run}`);
    let safety = 0;

    while (!sessionProgress(session).complete) {
      safety++;
      assert.ok(safety < 250, `run ${run} froze`);

      const roll = nextInt() % 10;

      if (roll <= 6) {
        session = reduceQuizSession(session, {
          type: 'answer',
          answer: CHOICES[nextInt() % CHOICES.length]
        });
      } else if (roll === 7) {
        session = reduceQuizSession(session, { type: 'next' });
      } else if (roll === 8) {
        session = reduceQuizSession(session, { type: 'prev' });
      } else {
        session = reduceQuizSession(session, { type: 'unanswered' });
      }

      assertSessionInvariant(session);
    }

    assert.equal(sessionProgress(session).complete, true);
  }
});
