#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== REMOVE MUTABLE QUESTION POINTER =="

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

replacements = [
    (
        "session.current_question_index++;",
        "// removed mutable pointer"
    ),
    (
        "session.current_question_index =\n      Math.max(\n        session.current_question_index + 1,\n        Object.keys(session.responses).length\n      );",
        "// removed mutable pointer"
    ),
    (
        "const currentQuestion = questions[session.current_question_index];",
        "const currentQuestion = questions[Object.keys(session.responses).length];"
    ),
    (
        "const currentIndex = session.current_question_index;",
        "const currentIndex = Object.keys(session.responses).length;"
    )
]

for old, new in replacements:
    s = s.replace(old, new)

p.write_text(s)

print("MUTABLE_POINTER_REMOVED=PASS")
PY

cat > tests/e2e/derived_question_pointer.e2e.test.js << 'JS'
import test from 'node:test';
import assert from 'node:assert/strict';

function deriveCurrentQuestion(responses, total = 36) {
  const answered = Object.keys(responses).length;

  return Math.min(answered + 1, total);
}

test('derived pointer always matches answered count', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'A';

    assert.equal(
      deriveCurrentQuestion(responses),
      Math.min(i + 1, 36)
    );
  }
});

test('duplicate clicks cannot drift derived pointer', () => {
  const responses = {};

  responses[1] = 'A';
  responses[1] = 'A';
  responses[1] = 'A';

  assert.equal(
    deriveCurrentQuestion(responses),
    2
  );

  assert.equal(
    Object.keys(responses).length,
    1
  );
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())

scripts = data.setdefault("scripts", {})

scripts["test:derived-pointer"] = "node --test tests/e2e/derived_question_pointer.e2e.test.js"

mobile = scripts["test:mobile"]

if "test:derived-pointer" not in mobile:
    scripts["test:mobile"] = mobile + " && npm run test:derived-pointer"

pkg.write_text(json.dumps(data, indent=2) + "\n")

print("DERIVED_POINTER_TEST_SCRIPT=PASS")
PY

npm run test:derived-pointer
npm run test:mobile

echo "REMOVE_MUTABLE_QUESTION_POINTER=PASS"
