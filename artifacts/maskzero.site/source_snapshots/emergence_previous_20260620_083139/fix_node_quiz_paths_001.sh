#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== FIX NODE QUIZ PATHS =="

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
data["type"] = "module"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PACKAGE_TYPE_MODULE=PASS")
PY

python - << 'PY'
from pathlib import Path

p = Path("packages/quiz-engine/index.js")
s = p.read_text()

s = s.replace(
"""import fs from 'node:fs';

export function loadQuiz(path = 'data/quiz/questions.json') {
  const raw = JSON.parse(fs.readFileSync(path, 'utf8'));""",
"""import fs from 'node:fs';
import path from 'node:path';

export function repoRoot() {
  return path.resolve(new URL('../..', import.meta.url).pathname);
}

export function resolveRepoPath(relativePath) {
  return path.join(repoRoot(), relativePath);
}

export function loadQuiz(relativePath = 'data/quiz/questions.json') {
  const raw = JSON.parse(fs.readFileSync(resolveRepoPath(relativePath), 'utf8'));"""
)

s = s.replace(
"""  fs.mkdirSync('data/state/quiz_results', { recursive: true });
  fs.writeFileSync(
    `data/state/quiz_results/${userId}.json`,""",
"""  const outDir = resolveRepoPath('data/state/quiz_results');
  fs.mkdirSync(outDir, { recursive: true });
  fs.writeFileSync(
    path.join(outDir, `${userId}.json`),"""
)

p.write_text(s)
print("QUIZ_ENGINE_PATHS_PATCHED=PASS")
PY

node -e "import('./packages/quiz-engine/index.js').then(m => { const q=m.loadQuiz(); console.log('QUIZ_LOAD=PASS'); console.log('QUESTIONS=' + q.questions.length); })"

echo "FIX_NODE_QUIZ_PATHS=PASS"
