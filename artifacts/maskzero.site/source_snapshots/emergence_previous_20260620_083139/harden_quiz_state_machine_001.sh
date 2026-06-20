#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== HARDEN QUIZ STATE MACHINE =="

mkdir -p packages/quiz-session tests/e2e

cat > packages/quiz-session/index.js << 'JS'
import {
  adaptiveProgress,
  currentAdaptiveQuestionIds
} from '../scoring-engine/index.js';

export function createSession(userId) {
  return {
    userId,
    cursor: 0,
    responses: {},
    updated_at: new Date().toISOString()
  };
}

export function normalizeSession(session, userId = 'unknown') {
  const clean = session && typeof session === 'object' ? session : createSession(userId);

  clean.userId = clean.userId || userId;
  clean.cursor = Number.isInteger(clean.cursor) ? clean.cursor : 0;
  clean.responses = clean.responses && typeof clean.responses === 'object' ? clean.responses : {};

  const ids = currentAdaptiveQuestionIds(clean.responses);
  if (clean.cursor < 0) clean.cursor = 0;
  if (clean.cursor >= ids.length) clean.cursor = ids.length - 1;

  clean.updated_at = new Date().toISOString();
  return clean;
}

export function activeIds(session) {
  return currentAdaptiveQuestionIds(session.responses);
}

export function activeQuestionId(session) {
  const ids = activeIds(session);
  const cursor = Math.min(Math.max(session.cursor, 0), ids.length - 1);
  return ids[cursor];
}

export function firstUnansweredCursor(session) {
  const ids = activeIds(session);
  const idx = ids.findIndex(id => session.responses[id] === undefined);
  return idx >= 0 ? idx : Math.max(0, ids.length - 1);
}

export function reduceQuizSession(session, action) {
  const next = normalizeSession(JSON.parse(JSON.stringify(session || createSession(action?.userId || 'unknown'))));

  if (!action || !action.type) {
    return next;
  }

  if (action.type === 'answer') {
    const qid = activeQuestionId(next);
    const answer = action.answer;

    if (!['A', 'B', 'C', 'D', 'E', 'F', 'G'].includes(answer)) {
      return next;
    }

    next.responses[qid] = answer;

    const idsAfter = activeIds(next);
    const unansweredAfter = idsAfter.findIndex(id => next.responses[id] === undefined);

    if (unansweredAfter >= 0) {
      next.cursor = unansweredAfter;
    } else {
      next.cursor = Math.max(0, idsAfter.length - 1);
    }

    next.updated_at = new Date().toISOString();
    return normalizeSession(next);
  }

  if (action.type === 'next') {
    const ids = activeIds(next);
    next.cursor = Math.min(next.cursor + 1, ids.length - 1);
    return normalizeSession(next);
  }

  if (action.type === 'prev') {
    next.cursor = Math.max(next.cursor - 1, 0);
    return normalizeSession(next);
  }

  if (action.type === 'unanswered') {
    next.cursor = firstUnansweredCursor(next);
    return normalizeSession(next);
  }

  if (action.type === 'reset') {
    return createSession(next.userId);
  }

  return next;
}

export function sessionProgress(session) {
  return adaptiveProgress(session.responses);
}

export function assertSessionInvariant(session) {
  const ids = activeIds(session);
  const progress = sessionProgress(session);

  if (session.cursor < 0 || session.cursor >= ids.length) {
    throw new Error(`cursor out of bounds: ${session.cursor}/${ids.length}`);
  }

  if (progress.answered + progress.remaining !== progress.total) {
    throw new Error('progress total mismatch');
  }

  for (const qid of Object.keys(session.responses).map(Number)) {
    if (!ids.includes(qid) && qid >= 37) {
      throw new Error(`irrelevant affinity response retained: ${qid}`);
    }
  }

  return true;
}
JS

cat > tests/e2e/quiz_session_state_machine.e2e.test.js << 'JS'
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
JS

python - << 'PY'
from pathlib import Path
import json

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:quiz-session"] = "node --test tests/e2e/quiz_session_state_machine.e2e.test.js"

mobile = scripts.get("test:mobile", "")
if "test:quiz-session" not in mobile:
    scripts["test:mobile"] = mobile + " && npm run test:quiz-session"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("QUIZ_SESSION_TEST_SCRIPT=PASS")
PY

npm run test:quiz-session

echo "HARDEN_QUIZ_STATE_MACHINE=PASS"
