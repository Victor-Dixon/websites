import {
  createSession,
  currentQid,
  progress,
  reduceSession
} from './packages/quiz-button-session/index.js';

const session = createSession({ userId: '123456789' });

let s = session;
let lastKey = null;

for (let step = 1; step <= 140; step++) {
  const p = progress(s);
  const qid = currentQid(s);

  const key = [
    s.phase,
    s.cursor,
    qid,
    p.answered,
    p.total,
    p.remaining
  ].join(':');

  console.log(JSON.stringify({
    step,
    phase: s.phase,
    cursor: s.cursor,
    qid,
    answered: p.answered,
    total: p.total,
    remaining: p.remaining,
    complete: p.complete,
    activeQueueLength: s.activeQueue?.length,
    viewVersion: s.viewVersion
  }));

  if (key === lastKey) {
    console.log('LOOP_DETECTED=' + key);
    process.exit(1);
  }

  lastKey = key;

  if (p.complete) {
    console.log('COMPLETE=PASS');
    process.exit(0);
  }

  const result = reduceSession(s, {
    type: 'answer',
    qid,
    answer: 'A',
    viewVersion: s.viewVersion,
    userTail: '456789'
  });

  console.log('OUTCOME=' + result.outcome);

  s = result.session;
}

console.log('SAFETY_BREAK');
process.exit(1);
