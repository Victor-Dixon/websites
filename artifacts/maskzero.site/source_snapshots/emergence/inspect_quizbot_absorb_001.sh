#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== QUIZBOT ABSORB INSPECTION =="

echo
echo "## repo files"
find apps -maxdepth 4 -type f | sort

echo
echo "## python quizbot key lines"
grep -RIn "questions\|quiz\|answer\|slash\|command\|sync\|json\|sqlite\|discord" apps/discord-quiz-bot \
  --exclude-dir='__pycache__' \
  --exclude='*.pyc' \
  | head -200 || true

echo
echo "## quiz data candidates"
find apps/discord-quiz-bot . -type f \( -name '*.json' -o -name '*.yaml' -o -name '*.yml' \) | sort

echo
echo "## node bot"
sed -n '1,220p' apps/discord-bot/src/index.js

echo
echo "QUIZBOT_ABSORB_INSPECTION=PASS"
