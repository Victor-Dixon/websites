#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-.}"
if [ "$ROOT" = "." ] && [ ! -f "$ROOT/index.html" ] && [ -f "$ROOT/_deploy/weareswarm/index.html" ]; then
  SITE_ROOT="$ROOT/_deploy/weareswarm"
else
  SITE_ROOT="$ROOT"
fi
OUT="$ROOT/data/reports/weareswarm_online_audit_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$OUT"

echo "== WEARESWARM.ONLINE STATIC AUDIT =="
echo "ROOT=$ROOT"
echo "SITE_ROOT=$SITE_ROOT"
echo "OUT=$OUT"

PAGES=(
  "index.html"
  "focus/index.html"
  "projects/index.html"
  "tasks/index.html"
  "feed/index.html"
  "skill-tree/index.html"
  "kids-planner/index.html"
  "dreamos-services/index.html"
)

DATA_FILES=(
  "data/planner/all_tasks.json"
  "data/planner/kids_tasks.json"
  "data/planner/projects_full.json"
  "data/planner/projects_board_enriched.json"
  "data/planner/next_lane.json"
  "data/planner/skill_tree.json"
  "data/planner/closeouts.json"
)

REPORT="$OUT/audit.md"
JSON_REPORT="$OUT/audit.json"

{
  echo "# WeAreSwarm.online Audit"
  echo
  echo "Generated: $(date -Is)"
  echo "Site root: $SITE_ROOT"
  echo
  echo "## Pages"
} > "$REPORT"

printf '{\n  "generated": "%s",\n  "site_root": "%s",\n  "pages": [\n' "$(date -Is)" "$SITE_ROOT" > "$JSON_REPORT"

first=1
for page in "${PAGES[@]}"; do
  path="$SITE_ROOT/$page"
  status="MISSING"
  loading_count=0
  empty_count=0
  bytes=0
  if [ -f "$path" ]; then
    status="FOUND"
    loading_count="$( (rg -io "Loading" "$path" || true) | wc -l | tr -d ' ')"
    empty_count="$( (rg -io "No .*published|No tasks|—" "$path" || true) | wc -l | tr -d ' ')"
    bytes="$(wc -c < "$path" | tr -d ' ')"
  fi
  echo "- $page: $status bytes=$bytes loading_tokens=$loading_count empty_markers=$empty_count" >> "$REPORT"
  [ "$first" -eq 0 ] && printf ',\n' >> "$JSON_REPORT"
  first=0
  printf '    {"page":"%s","status":"%s","bytes":%s,"loading_tokens":%s,"empty_markers":%s}' \
    "$page" "$status" "$bytes" "$loading_count" "$empty_count" >> "$JSON_REPORT"
done

printf '\n  ],\n  "data_files": [\n' >> "$JSON_REPORT"

{
  echo
  echo "## Data Files"
} >> "$REPORT"

first=1
for file in "${DATA_FILES[@]}"; do
  path="$SITE_ROOT/$file"
  status="MISSING"
  bytes=0
  item_count="null"
  if [ -f "$path" ]; then
    status="FOUND"
    bytes="$(wc -c < "$path" | tr -d ' ')"
    item_count="$(python - "$path" <<'PY'
import json, sys
p = sys.argv[1]
try:
    data = json.load(open(p, encoding="utf-8"))
    if isinstance(data, list):
        print(len(data))
    elif isinstance(data, dict):
        for key in ("tasks", "items", "projects", "nodes", "closeouts", "approved_tasks"):
            if isinstance(data.get(key), list):
                print(len(data[key]))
                break
        else:
            print(len(data.keys()))
except Exception:
    print("parse_error")
PY
)"
  fi
  echo "- $file: $status bytes=$bytes item_count=$item_count" >> "$REPORT"
  [ "$first" -eq 0 ] && printf ',\n' >> "$JSON_REPORT"
  first=0
  printf '    {"file":"%s","status":"%s","bytes":%s,"item_count":"%s"}' \
    "$file" "$status" "$bytes" "$item_count" >> "$JSON_REPORT"
done

printf '\n  ]\n}\n' >> "$JSON_REPORT"

{
  echo
  echo "## Frontend Data References"
  rg --no-filename --only-matching -e '(/?data/[^") ]+\.json)' "$SITE_ROOT" \
    --glob '*.html' --glob '*.js' --glob '*.jsx' --glob '*.ts' --glob '*.tsx' 2>/dev/null \
    | sort -u \
    | sed 's/^/- /' || true
  echo
  echo "## Placeholder Hotspots"
  rg -n "Loading|No tasks match|No closeouts published|Loading feed|Loading planner board|Loading capability map" \
    "$SITE_ROOT" \
    --glob '*.html' --glob '*.js' --glob '*.jsx' --glob '*.ts' --glob '*.tsx' 2>/dev/null \
    | head -200 || true
} >> "$REPORT"

echo
echo "== AUDIT COMPLETE =="
echo "REPORT=$REPORT"
echo "JSON=$JSON_REPORT"
