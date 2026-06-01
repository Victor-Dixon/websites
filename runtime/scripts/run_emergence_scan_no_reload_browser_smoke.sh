#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

if [ "$(uname -o 2>/dev/null || true)" = "Android" ]; then
  echo "PLAYWRIGHT_PLATFORM=ENV_BLOCKED_ANDROID"
  echo "Run this on Linux/WSL/macOS/Windows/GitHub Actions. Termux cannot run Playwright Chromium."
  exit 12
fi

if ! node -e "require('playwright')" >/dev/null 2>&1; then
  npm install --no-save playwright
fi

npx playwright install chromium
node runtime/scripts/smoke_emergence_scan_no_reload_browser.mjs
