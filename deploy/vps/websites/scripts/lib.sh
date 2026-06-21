#!/usr/bin/env bash
# Shared helpers for Dream.OS websites VPS scripts.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
VPS_PACKAGE_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
REPO_ROOT="$(cd "${VPS_PACKAGE_ROOT}/../../.." && pwd)"

DEFAULT_SECRETS_ENV="/opt/dreamos/secrets/websites.env"
DEFAULT_ENV_EXAMPLE="${VPS_PACKAGE_ROOT}/.env.example"

load_vps_env() {
  local secrets_env="${1:-${DEFAULT_SECRETS_ENV}}"

  if [[ -f "${DEFAULT_ENV_EXAMPLE}" ]]; then
    # shellcheck disable=SC1090
    set -a
    source "${DEFAULT_ENV_EXAMPLE}"
    set +a
  fi

  if [[ -f "${secrets_env}" ]]; then
    # shellcheck disable=SC1090
    set -a
    source "${secrets_env}"
    set +a
  fi

  : "${DREAMOS_ROOT:=/opt/dreamos}"
  : "${WEBSITES_REPO:=${REPO_ROOT}}"
  : "${DASHBOARD_RUNTIME_DIR:=${DREAMOS_ROOT}/runtime/dashboard}"
  : "${SITE_PREVIEW_PORT:=8080}"
  : "${PUBLIC_SITE_ROOT:=/var/www/dreamos-sites}"
  : "${DASHBOARD_SITE_DATA_DIR:=${PUBLIC_SITE_ROOT}/weareswarm.online/data/planner}"
  : "${DASHBOARD_PUBLIC_DATA_DIR:=${PUBLIC_SITE_ROOT}/data/dashboard}"
  : "${VPS_REPORTS_DIR:=${WEBSITES_REPO}/reports/vps}"
}

require_cmd() {
  local cmd="$1"
  if ! command -v "${cmd}" >/dev/null 2>&1; then
    echo "ERROR: required command not found: ${cmd}" >&2
    exit 1
  fi
}

validate_json_file() {
  local file="$1"
  python3 - <<'PY' "${file}"
import json
import sys
from pathlib import Path

path = Path(sys.argv[1])
try:
    json.loads(path.read_text(encoding="utf-8"))
except Exception as exc:
    print(f"invalid JSON: {path}: {exc}", file=sys.stderr)
    raise SystemExit(1)
PY
}

is_secret_key() {
  local key="$1"
  local lower
  lower="$(printf '%s' "${key}" | tr '[:upper:]' '[:lower:]')"
  case "${lower}" in
    *password*|*secret*|*token*|*api_key*|*apikey*|*private_key*|*webhook*|*credential*)
      return 0
      ;;
  esac
  return 1
}

sanitize_json_for_public() {
  local src="$1"
  local dest="$2"
  python3 - <<'PY' "${src}" "${dest}"
import json
import sys
from pathlib import Path

SECRET_HINTS = (
    "password", "secret", "token", "api_key", "apikey",
    "private_key", "webhook", "credential", "authorization",
)

def is_secret_key(key: str) -> bool:
    lower = key.lower()
    return any(hint in lower for hint in SECRET_HINTS)

def scrub(value):
    if isinstance(value, dict):
        cleaned = {}
        for key, item in value.items():
            if is_secret_key(str(key)):
                continue
            cleaned[key] = scrub(item)
        return cleaned
    if isinstance(value, list):
        return [scrub(item) for item in value]
    return value

src = Path(sys.argv[1])
dest = Path(sys.argv[2])
data = json.loads(src.read_text(encoding="utf-8"))
dest.parent.mkdir(parents=True, exist_ok=True)
dest.write_text(json.dumps(scrub(data), indent=2, sort_keys=True) + "\n", encoding="utf-8")
PY
}

timestamp_utc() {
  date -u +"%Y%m%dT%H%M%SZ"
}
