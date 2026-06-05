#!/usr/bin/env bash
# Run on PHONE / Termux (Linux) — not Windows.
# Phone master is canonical; this applies desktop xThunder salvage on top.
set -euo pipefail

ROOT="${1:-$HOME/projects/websites}"
PATCH_REL="data/reports/git_history_repair/xthunder_phone_canonical_salvage.patch"
STAMP="$(date +%Y%m%d_%H%M%S)"
REPORT="data/reports/git_history_repair/phone_canonical_merge_$STAMP.md"

cd "$ROOT"

echo "== TARGET =="
pwd

echo "== VERIFY LINUX =="
uname -s | grep -qi linux

echo "== FETCH =="
git fetch origin

echo "== PHONE CANONICAL BASE =="
git checkout master
git reset --hard origin/master
git log -1 --oneline

echo "== APPLY XTHUNDER SALVAGE PATCH =="
test -s "$PATCH_REL"
git apply --check "$PATCH_REL"
git apply "$PATCH_REL"

echo "== VERIFY XTHUNDER =="
test -f sites/production/websites/xthunder.site/index.html
test -f sites/production/websites/xthunder.site/assets/js/main.js
grep -q "global chat" sites/production/websites/xthunder.site/index.html || grep -q "storm" sites/production/websites/xthunder.site/index.html

{
  echo "# Phone-canonical merge report"
  echo
  echo "generated=$STAMP"
  echo "base=$(git rev-parse origin/master)"
  echo "head=$(git log -1 --oneline)"
  echo
  echo "## Strategy"
  echo "origin/master (phone) wins history; xThunder paths salvaged from desktop patch."
} > "$REPORT"

git add -A
git status --short | head -40

if git diff --cached --quiet; then
  echo "NO_CHANGES_TO_COMMIT=1"
else
  git commit -m "Salvage xthunder.site onto phone-canonical master"
fi

echo "== PUSH =="
git push origin master

echo "REPORT=$REPORT"
echo "STATUS=PASS"
