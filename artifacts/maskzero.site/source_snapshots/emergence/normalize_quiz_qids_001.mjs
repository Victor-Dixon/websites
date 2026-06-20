import fs from 'fs';

const path = 'packages/quiz-button-session/index.js';

let s = fs.readFileSync(path, 'utf8');

s = s.replaceAll(
  'Object.keys(session.responses)',
  'Object.keys(session.responses).map(Number)'
);

s = s.replaceAll(
  'Object.keys(next.responses)',
  'Object.keys(next.responses).map(Number)'
);

s = s.replaceAll(
  'valid.has(qid)',
  'valid.has(Number(qid))'
);

s = s.replaceAll(
  'next.responses[id]',
  'next.responses[String(id)]'
);

s = s.replaceAll(
  'session.responses[id]',
  'session.responses[String(id)]'
);

s = s.replaceAll(
  'next.responses[qid]',
  'next.responses[String(qid)]'
);

s = s.replaceAll(
  'session.responses[qid]',
  'session.responses[String(qid)]'
);

s = s.replaceAll(
  'delete session.responses[key]',
  'delete session.responses[String(key)]'
);

s = s.replaceAll(
  'delete next.responses[key]',
  'delete next.responses[String(key)]'
);

fs.writeFileSync(path, s);

console.log('QID_NORMALIZATION_PATCH=PASS');
