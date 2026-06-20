import assert from 'node:assert/strict';
import test from 'node:test';

import {
  buildCharacterSheet,
  calculatePercentile
} from '../../packages/scoring-engine/index.js';

import {
  createSession,
  currentQid,
  flatResponses,
  progress,
  reduceSession
} from '../../packages/quiz-button-session/index.js';

const CHOICES = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

function answerRun(domainAnswer, affinityAnswer) {
  let session = createSession({ userId: '123456789' });
  let safety = 0;

  while (!progress(session).complete) {
    safety++;
    assert.ok(safety < 120, 'button reducer should not freeze');

    const qid = currentQid(session);
    const answer = qid <= 36 ? domainAnswer : affinityAnswer;

    const result = reduceSession(session, {
      type: 'answer',
      qid,
      answer,
      viewVersion: session.viewVersion,
      userTail: '456789'
    });

    assert.ok(['answered', 'complete', 'stale_view', 'already_answered', 'wrong_question', 'nav', 'refresh'].includes(result.outcome));
    session = result.session;
  }

  const responses = flatResponses(session);
  const sheet = buildCharacterSheet({
    userId: '123456789',
    username: 'StressUser',
    responses
  });

  return { session, responses, sheet, progress: progress(session) };
}

test('percentile formula is v5 bounded and deterministic', () => {
  assert.equal(calculatePercentile(0), 70);
  assert.ok(calculatePercentile(1) >= 70);
  assert.ok(calculatePercentile(36) <= 100);
});

test('all single-domain A-G runs complete through button reducer', () => {
  for (const domainAnswer of CHOICES) {
    const { progress: p, sheet } = answerRun(domainAnswer, 'G');

    assert.equal(p.complete, true);
    assert.equal(p.remaining, 0);
    assert.ok(p.total >= 42);
    assert.ok(sheet.percentile >= 70);
    assert.ok(sheet.percentile <= 100);
  }
});

test('all domain and affinity A-G pair combinations complete through button reducer', () => {
  let runs = 0;

  for (const domainAnswer of CHOICES) {
    for (const affinityAnswer of CHOICES) {
      const { progress: p, sheet } = answerRun(domainAnswer, affinityAnswer);

      assert.equal(p.complete, true);
      assert.equal(p.remaining, 0);
      assert.ok(p.total >= 42);
      assert.ok(sheet.percentile >= 70);
      assert.ok(sheet.percentile <= 100);

      runs++;
    }
  }

  assert.equal(runs, 49);
});
