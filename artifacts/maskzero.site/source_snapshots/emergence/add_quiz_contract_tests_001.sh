#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD QUIZ CONTRACT TESTS =="

mkdir -p tests

cat > tests/quiz_contract.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  loadQuiz,
  normalizeQuestion,
  buildBasicResults
} from '../packages/quiz-engine/index.js';

test('canonical quiz loads with 72 questions', () => {
  const quiz = loadQuiz();

  assert.equal(quiz.form_id, 'SPARK-72');
  assert.equal(quiz.version, 'MVP-2026-05-23');
  assert.equal(Array.isArray(quiz.questions), true);
  assert.equal(quiz.questions.length, 72);
});

test('canonical quiz has required metadata', () => {
  const quiz = loadQuiz();

  assert.equal(typeof quiz.title, 'string');
  assert.ok(quiz.title.includes('Spark Protocol'));
  assert.equal(typeof quiz.preamble, 'string');
  assert.equal(typeof quiz.instructions, 'string');
  assert.equal(typeof quiz.scoring, 'object');
  assert.equal(quiz.scoring.domain_questions, '1-36');
  assert.equal(quiz.scoring.flavor_questions, '37-72');
});

test('every question has id, text, and six A-F options', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.equal(question.id, index + 1);
    assert.equal(typeof question.question, 'string');
    assert.ok(question.question.length > 10);

    const normalized = normalizeQuestion(question, index);
    assert.deepEqual(Object.keys(normalized.answers), ['A', 'B', 'C', 'D', 'E', 'F']);

    for (const answer of Object.values(normalized.answers)) {
      assert.equal(typeof answer, 'string');
      assert.ok(answer.length > 3);
    }
  });
});

test('domain and flavor question ranges are structurally valid', () => {
  const quiz = loadQuiz();

  const domain = quiz.questions.slice(0, 36);
  const flavor = quiz.questions.slice(36, 72);

  assert.equal(domain[0].id, 1);
  assert.equal(domain.at(-1).id, 36);
  assert.equal(flavor[0].id, 37);
  assert.equal(flavor.at(-1).id, 72);
});

test('basic results locks only after all 72 answers exist', () => {
  const quiz = loadQuiz();

  const partial = {};
  for (let i = 1; i <= 71; i++) partial[i] = 'A';

  const partialResults = buildBasicResults(quiz, partial);
  assert.equal(partialResults.locked, false);
  assert.equal(partialResults.total_answered, 71);

  const complete = {};
  for (let i = 1; i <= 72; i++) complete[i] = i <= 36 ? 'A' : 'F';

  const completeResults = buildBasicResults(quiz, complete);
  assert.equal(completeResults.locked, true);
  assert.equal(completeResults.total_answered, 72);
  assert.equal(completeResults.counts.A, 36);
  assert.equal(completeResults.counts.F, 36);
});

test('normalizer supports legacy answers object and canonical options array', () => {
  const optionsQuestion = {
    id: 1,
    question: 'Example?',
    options: [
      'A. Alpha',
      'B. Beta',
      'C. Gamma',
      'D. Delta',
      'E. Epsilon',
      'F. Zeta'
    ]
  };

  assert.deepEqual(normalizeQuestion(optionsQuestion, 0).answers, {
    A: 'Alpha',
    B: 'Beta',
    C: 'Gamma',
    D: 'Delta',
    E: 'Epsilon',
    F: 'Zeta'
  });

  const legacyQuestion = {
    id: 1,
    question: 'Example?',
    answers: {
      A: 'Alpha',
      B: 'Beta',
      C: 'Gamma',
      D: 'Delta',
      E: 'Epsilon',
      F: 'Zeta'
    }
  };

  assert.deepEqual(normalizeQuestion(legacyQuestion, 0).answers, legacyQuestion.answers);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test"] = "node --test tests/*.test.js"
scripts["test:quiz"] = "node --test tests/quiz_contract.test.js"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PACKAGE_TEST_SCRIPTS=PASS")
PY

npm test

echo "QUIZ_CONTRACT_TESTS=PASS"
