#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== FIX QUIZ CONTRACT RED =="

python - << 'PY'
import json
from pathlib import Path

src = Path("apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json")
dst = Path("data/quiz/questions.json")
dst.parent.mkdir(parents=True, exist_ok=True)

data = json.loads(src.read_text())

data["form_id"] = data.get("form_id") or "SPARK-72"
data["version"] = data.get("version") or "MVP-2026-05-23"
data["title"] = data.get("title") or "Spark Protocol: The Emergence Classification Quiz"
data["preamble"] = data.get("preamble") or "Answer honestly. The system reads your psychology, not your wish list."
data["instructions"] = data.get("instructions") or "Select the response that fits you best."
data["scoring"] = data.get("scoring") or {
    "mode": "spark_protocol_domain_flavor_v1_pending_engine",
    "domain_questions": "1-36",
    "flavor_questions": "37-72",
    "notes": "Scoring-map implementation is a separate lane."
}

dst.write_text(json.dumps(data, indent=2, ensure_ascii=False) + "\n")
print("CANONICAL_QUIZ_NORMALIZED=PASS")
print(f"QUESTIONS={len(data['questions'])}")
PY

cat > packages/quiz-engine/index.js << 'JS'
import fs from 'node:fs';
import path from 'node:path';

export function repoRoot() {
  return path.resolve(new URL('../..', import.meta.url).pathname);
}

export function resolveRepoPath(relativePath) {
  return path.join(repoRoot(), relativePath);
}

export function loadQuiz(relativePath = 'data/quiz/questions.json') {
  const raw = JSON.parse(fs.readFileSync(resolveRepoPath(relativePath), 'utf8'));

  if (!raw.questions || !Array.isArray(raw.questions)) {
    throw new Error('Invalid quiz: missing questions[]');
  }

  return raw;
}

export function normalizeQuestion(q, idx) {
  let answers = q.answers || {};

  if ((!answers || Object.keys(answers).length === 0) && Array.isArray(q.options)) {
    answers = {};

    for (const option of q.options) {
      const match = String(option).match(/^([A-F])\.\s*(.*)$/);

      if (match) {
        answers[match[1]] = match[2];
      }
    }
  }

  return {
    id: q.id ?? idx + 1,
    question: q.question || q.text || '',
    answers
  };
}

export function buildBasicResults(quiz, responses) {
  const counts = {};

  for (const value of Object.values(responses)) {
    counts[value] = (counts[value] || 0) + 1;
  }

  const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
  const primary = sorted[0]?.[0] || 'UNKNOWN';

  return {
    total_answered: Object.keys(responses).length,
    total_questions: quiz.questions.length,
    counts,
    primary,
    locked: Object.keys(responses).length === quiz.questions.length
  };
}

export function saveQuizResult(userId, payload) {
  const outDir = resolveRepoPath('data/state/quiz_results');
  fs.mkdirSync(outDir, { recursive: true });

  fs.writeFileSync(
    path.join(outDir, `${userId}.json`),
    JSON.stringify(payload, null, 2) + '\n'
  );
}
JS

npm run test:quiz

echo "FIX_QUIZ_CONTRACT_RED=PASS"
