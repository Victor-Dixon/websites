import assert from 'node:assert/strict';
import test from 'node:test';

import {
  bumpViewVersion,
  createSession,
  isStaleView,
  reduceQuizSession
} from '../../packages/quiz-session/index.js';

test('view version increments after mutation', () => {
  let session = createSession('v');
  assert.equal(session.view_version, 0);

  session = reduceQuizSession(session, { type: 'answer', answer: 'A' });
  bumpViewVersion(session);

  assert.equal(session.view_version, 1);
});

test('stale view is detected and can be refreshed without mutation', () => {
  const session = createSession('v');
  bumpViewVersion(session);
  bumpViewVersion(session);

  assert.equal(isStaleView(session, 0), true);
  assert.equal(isStaleView(session, 2), false);
});
