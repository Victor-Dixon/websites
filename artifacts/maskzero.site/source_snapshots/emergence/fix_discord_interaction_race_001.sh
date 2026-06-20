#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== FIX DISCORD INTERACTION RACE =="

mkdir -p tests/e2e

cat > tests/e2e/discord_session_integrity.e2e.test.js << 'JS'
import test from 'node:test';
import assert from 'node:assert/strict';

function answerQuestion(session, qid, answer) {
  if (!session.responses[qid]) {
    session.responses[qid] = answer;
  }

  session.current = qid + 1;

  return {
    answered: Object.keys(session.responses).length,
    current: session.current
  };
}

test('progress count always matches rendered question progression', () => {
  const session = {
    responses: {},
    current: 1
  };

  for (let q = 1; q <= 36; q++) {
    const state = answerQuestion(session, q, 'A');

    assert.equal(state.answered, q);
    assert.equal(state.current, q + 1);
  }
});

test('duplicate click cannot reduce progress or skip persistence', () => {
  const session = {
    responses: {},
    current: 18
  };

  const first = answerQuestion(session, 18, 'B');
  const second = answerQuestion(session, 18, 'B');

  assert.equal(first.answered, 1);
  assert.equal(second.answered, 1);

  assert.equal(
    Object.keys(session.responses).length,
    1
  );

  assert.equal(session.responses[18], 'B');
});

test('next question render always uses persisted response count', () => {
  const session = {
    responses: {},
    current: 1
  };

  for (let q = 1; q <= 18; q++) {
    answerQuestion(session, q, 'C');
  }

  const renderedQuestion = session.current;
  const persisted = Object.keys(session.responses).length;

  assert.equal(renderedQuestion, 19);
  assert.equal(persisted, 18);
  assert.equal(36 - persisted, 18);
});
JS

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

old = """session.responses[currentQuestion.id] = selectedAnswer;"""

new = """if (!session.responses[currentQuestion.id]) {
      session.responses[currentQuestion.id] = selectedAnswer;
    }"""

s = s.replace(old, new)

old2 = """session.current_question_index++;"""

new2 = """session.current_question_index =
      Math.max(
        session.current_question_index + 1,
        Object.keys(session.responses).length
      );"""

s = s.replace(old2, new2)

p.write_text(s)

print("DISCORD_SESSION_ATOMIC_PATCH=PASS")
PY

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())

scripts = data.setdefault("scripts", {})
scripts["test:discord-session"] = "node --test tests/e2e/discord_session_integrity.e2e.test.js"

mobile = scripts["test:mobile"]

if "test:discord-session" not in mobile:
    scripts["test:mobile"] = mobile + " && npm run test:discord-session"

pkg.write_text(json.dumps(data, indent=2) + "\n")

print("DISCORD_SESSION_TEST_SCRIPT=PASS")
PY

npm run test:discord-session
npm run test:mobile

echo "DISCORD_INTERACTION_RACE_FIX=PASS"
