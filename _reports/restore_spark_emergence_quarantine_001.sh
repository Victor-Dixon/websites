#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-$PWD}"
cd "$ROOT"

QUARANTINE="_reports/backups/spark_emergence_quarantine_001"
MOVED_LIST="_reports/spark_emergence_quarantine_moved_001.txt"

test -d "$QUARANTINE"
test -f "$MOVED_LIST"

while IFS= read -r path; do
  [ -n "$path" ] || continue
  src="$QUARANTINE/$path"
  if [ ! -e "$src" ]; then
    echo "SKIP_MISSING_QUARANTINE=$src"
    continue
  fi
  mkdir -p "$(dirname "$path")"
  mv "$src" "$path"
  echo "RESTORED=$path"
done < "$MOVED_LIST"

echo "RESTORE_SPARK_EMERGENCE_QUARANTINE=PASS"
