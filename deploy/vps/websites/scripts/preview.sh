#!/usr/bin/env bash
# Serve a static site root locally for VPS validation (127.0.0.1 only).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

SECRETS_ENV="${1:-${DEFAULT_SECRETS_ENV}}"
load_vps_env "${SECRETS_ENV}"

PREVIEW_ROOT="${SITE_PREVIEW_ROOT:-${PUBLIC_SITE_ROOT}}"
if [[ ! -d "${PREVIEW_ROOT}" ]]; then
  PREVIEW_ROOT="${WEBSITES_REPO}/public"
fi

if [[ ! -d "${PREVIEW_ROOT}" ]]; then
  echo "ERROR: preview root not found: ${PREVIEW_ROOT}" >&2
  exit 1
fi

HOST="127.0.0.1"
PORT="${SITE_PREVIEW_PORT}"

echo "==> Static preview"
echo "root: ${PREVIEW_ROOT}"
echo "url:  http://${HOST}:${PORT}"
echo "Press Ctrl+C to stop."

cd "${PREVIEW_ROOT}"
exec python3 -m http.server "${PORT}" --bind "${HOST}"
