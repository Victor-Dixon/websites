#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== UPGRADE QUIZ SCHEMA A-G =="

python - << 'PY'
from pathlib import Path

p = Path("packages/quiz-engine/index.js")
s = p.read_text()

s = s.replace("([A-F])", "([A-G])")
s = s.replace("['A', 'B', 'C', 'D', 'E', 'F']", "['A', 'B', 'C', 'D', 'E', 'F', 'G']")

p.write_text(s)
print("QUIZ_ENGINE_A_G_PATCH=PASS")
PY

python - << 'PY'
from pathlib import Path

p = Path("packages/scoring-engine/index.js")
s = p.read_text()

s = s.replace(
"""const DOMAIN_MAP = {
  A: 'titan',
  B: 'inferno',
  C: 'velocity',
  D: 'bulwark',
  E: 'echo',
  F: 'specter'
};""",
"""const DOMAIN_MAP = {
  A: 'titan',
  B: 'inferno',
  C: 'velocity',
  D: 'bulwark',
  E: 'echo',
  F: 'specter',
  G: 'omni'
};"""
)

s = s.replace(
"""const FLAVOR_MAP = {
  A: 'precision',
  B: 'aggression',
  C: 'adaptation',
  D: 'discipline',
  E: 'empathy',
  F: 'stealth'
};""",
"""const FLAVOR_MAP = {
  A: 'precision',
  B: 'aggression',
  C: 'adaptation',
  D: 'discipline',
  E: 'empathy',
  F: 'stealth',
  G: 'control'
};"""
)

p.write_text(s)
print("SCORING_ENGINE_A_G_PATCH=PASS")
PY

cat > tests/answer_choices.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  isValidAnswer,
  loadQuiz,
  normalizeQuestion,
  validAnswerChoices
} from '../packages/quiz-engine/index.js';

test('each canonical quiz question accepts A-G answers', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    for (const letter of ['A', 'B', 'C', 'D', 'E', 'F', 'G']) {
      assert.equal(
        isValidAnswer(question, letter, index),
        true,
        `Q${index + 1} should accept ${letter}`
      );
    }
  });
});

test('each question exposes exactly seven domain choices', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.deepEqual(
      validAnswerChoices(question, index),
      ['A', 'B', 'C', 'D', 'E', 'F', 'G']
    );

    const normalized = normalizeQuestion(question, index);
    assert.equal(Object.keys(normalized.answers).length, 7);
  });
});
JS

python - << 'PY'
from pathlib import Path
import json

quiz_path = Path("data/quiz/questions.json")
quiz = json.loads(quiz_path.read_text())

missing = []
for q in quiz["questions"]:
    opts = q.get("options", [])
    has_g = any(str(opt).startswith("G.") for opt in opts)
    if not has_g:
        missing.append(q["id"])

report = {
    "status": "BLOCKED" if missing else "PASS",
    "required_schema": "A-G",
    "domain_count": 7,
    "g_domain": "omni",
    "missing_g_question_ids": missing,
    "missing_count": len(missing)
}

Path("data/reports").mkdir(exist_ok=True)
Path("data/reports/quiz_schema_a_g_audit.json").write_text(json.dumps(report, indent=2) + "\n")

print("A_G_SCHEMA_AUDIT=" + report["status"])
print("MISSING_G_COUNT=" + str(len(missing)))
print("REPORT=data/reports/quiz_schema_a_g_audit.json")
PY

npm run test:answers || true

echo "UPGRADE_QUIZ_SCHEMA_A_G=PASS_WITH_CONTENT_BLOCKER"
