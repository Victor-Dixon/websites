#!/usr/bin/env bash
set -euo pipefail

DIR="$(cd "$(dirname "$0")" && pwd)"
PORT="${PORT:-8789}"
URL="http://127.0.0.1:$PORT"
LOG="${HOME}/.cache/weareswarm_motion_shell_001.log"
PIDFILE="${HOME}/.cache/weareswarm_motion_shell_001.pid"

cd "$DIR"

if [ -f "$PIDFILE" ]; then
  OLD_PID="$(cat "$PIDFILE" 2>/dev/null || true)"
  if [ -n "$OLD_PID" ] && kill -0 "$OLD_PID" 2>/dev/null; then
    kill "$OLD_PID" 2>/dev/null || true
    sleep .3
  fi
fi

nohup python -m http.server "$PORT" --bind 127.0.0.1 > "$LOG" 2>&1 &
PID="$!"
echo "$PID" > "$PIDFILE"

sleep 1

echo "Serving WeAreSwarm motion mockup at $URL"
echo "PID=$PID"
echo "LOG=$LOG"

if command -v termux-open-url >/dev/null 2>&1; then
  termux-open-url "$URL" || true
elif command -v xdg-open >/dev/null 2>&1; then
  xdg-open "$URL" >/dev/null 2>&1 || true
else
  echo "Open manually: $URL"
fi
