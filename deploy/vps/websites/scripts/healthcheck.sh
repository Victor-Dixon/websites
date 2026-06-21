#!/usr/bin/env bash
# Verify websites repo and VPS runtime readiness (no Hostinger/Windows/Termux dependency).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

SECRETS_ENV="${1:-${DEFAULT_SECRETS_ENV}}"
load_vps_env "${SECRETS_ENV}"

require_cmd python3

PASS=0
FAIL=0
WARN=0

pass() { echo "PASS: $*"; PASS=$((PASS + 1)); }
fail() { echo "FAIL: $*" >&2; FAIL=$((FAIL + 1)); }
warn() { echo "WARN: $*"; WARN=$((WARN + 1)); }

REPORT_DIR="${VPS_REPORTS_DIR}"
mkdir -p "${REPORT_DIR}"
REPORT_FILE="${REPORT_DIR}/healthcheck_$(timestamp_utc).json"

echo "==> websites VPS healthcheck"
echo "repo: ${WEBSITES_REPO}"

# --- repo structure ---
REQUIRED_PATHS=(
  "${WEBSITES_REPO}/ops/deployment/sites.yml"
  "${WEBSITES_REPO}/deploy/vps/websites/.env.example"
  "${WEBSITES_REPO}/websites"
  "${WEBSITES_REPO}/public"
)

for path in "${REQUIRED_PATHS[@]}"; do
  if [[ -e "${path}" ]]; then
    pass "repo path exists: ${path}"
  else
    fail "missing repo path: ${path}"
  fi
done

# --- expected site folders from sites.yml ---
if python3 - <<'PY' "${WEBSITES_REPO}/ops/deployment/sites.yml"
import sys
from pathlib import Path

try:
    import yaml
except ImportError:
    print("WARN")
    raise SystemExit(2)

sites_yml = Path(sys.argv[1])
repo = sites_yml.parents[2]
data = yaml.safe_load(sites_yml.read_text(encoding="utf-8")) or {}
sites = data.get("sites", {})
missing = []
for name, cfg in sites.items():
    if not cfg.get("enabled", True):
        continue
    rel = cfg.get("path")
    if not rel:
        continue
    site_path = repo / rel
    if not site_path.exists():
        missing.append(str(site_path))

if missing:
    for item in missing[:10]:
        print(f"WARN: enabled site path missing: {item}")
    if len(missing) > 10:
        print(f"WARN: ... and {len(missing) - 10} more missing site paths")
    raise SystemExit(1)

print(f"PASS: all enabled site folders present ({len(sites)} registered)")
PY
then
  pass "enabled site folders from sites.yml"
elif [[ $? -eq 2 ]]; then
  warn "PyYAML not installed; skipping sites.yml site folder checks"
else
  warn "some enabled site folders missing (see output above)"
fi

# --- no committed secrets in tracked deploy/vps + public JSON ---
if python3 - <<'PY' "${WEBSITES_REPO}"
import json
import re
import sys
from pathlib import Path

repo = Path(sys.argv[1])
scan_roots = [
    repo / "deploy" / "vps" / "websites",
    repo / "public",
]

secret_patterns = [
    re.compile(r"sk-[A-Za-z0-9]{10,}"),
    re.compile(r"(?i)(api[_-]?key|password|secret|token)\s*[:=]\s*['\"]?[A-Za-z0-9_\-./]{8,}"),
    re.compile(r"-----BEGIN (RSA |EC )?PRIVATE KEY-----"),
]

failures = []
for root in scan_roots:
    if not root.exists():
        continue
    for path in root.rglob("*"):
        if not path.is_file():
            continue
        if path.suffix in {".png", ".jpg", ".jpeg", ".gif", ".webp", ".zip"}:
            continue
        try:
            text = path.read_text(encoding="utf-8", errors="ignore")
        except OSError:
            continue
        for pattern in secret_patterns:
            if pattern.search(text):
                failures.append(f"{path}: matched {pattern.pattern}")

if failures:
    for item in failures[:5]:
        print(f"FAIL: possible secret in {item}")
    if len(failures) > 5:
        print(f"FAIL: ... and {len(failures) - 5} more secret pattern matches")
    raise SystemExit(1)

print("PASS: no obvious secret patterns in deploy/vps or public/")
PY
then
  pass "secret pattern scan clean"
else
  fail "secret pattern scan failed"
fi

# --- validate public JSON ---
if compgen -G "${WEBSITES_REPO}/public/*.json" >/dev/null; then
  for json_file in "${WEBSITES_REPO}"/public/*.json; do
    if validate_json_file "${json_file}"; then
      pass "valid JSON: ${json_file}"
    else
      fail "invalid JSON: ${json_file}"
    fi
  done
else
  warn "no JSON files in ${WEBSITES_REPO}/public"
fi

# --- broken symlinks under websites/ (sample) ---
python3 - <<'PY' "${WEBSITES_REPO}/websites"
import sys
from pathlib import Path

root = Path(sys.argv[1])
broken = []
checked = 0
for path in root.rglob("*"):
    if path.is_symlink():
        checked += 1
        if not path.exists():
            broken.append(str(path))
    if checked > 5000:
        break

if broken:
    for item in broken[:5]:
        print(f"WARN: broken symlink: {item}")
    if len(broken) > 5:
        print(f"WARN: ... and {len(broken) - 5} more broken symlinks")
else:
    print("PASS: no broken symlinks detected in websites/ sample scan")
PY

# --- VPS scripts must not require Windows/Termux paths ---
if python3 - <<'PY' "${SCRIPT_DIR}"
import re
import sys
from pathlib import Path

script_dir = Path(sys.argv[1])
bad = []
win_drive = "D"
patterns = [
    re.compile(win_drive + r":[/\\]", re.I),
    re.compile(r"/data/data/com" + ".termux", re.I),
    re.compile(r"com" + ".termux", re.I),
]
for path in script_dir.glob("*.sh"):
    if path.name == "healthcheck.sh":
        continue
    text = path.read_text(encoding="utf-8")
    for pattern in patterns:
        if pattern.search(text):
            bad.append(f"{path.name}: {pattern.pattern}")

if bad:
    for item in bad:
        print(f"FAIL: hardcoded local path in VPS script: {item}")
    raise SystemExit(1)
print("PASS: VPS scripts have no Windows/Termux hardcoded paths")
PY
then
  pass "VPS scripts path-neutral"
else
  fail "VPS scripts contain hardcoded local paths"
fi

# --- dashboard runtime dir configurable ---
if [[ -d "${DASHBOARD_RUNTIME_DIR}" ]]; then
  pass "dashboard runtime dir exists: ${DASHBOARD_RUNTIME_DIR}"
else
  warn "dashboard runtime dir missing (create or set DASHBOARD_RUNTIME_DIR): ${DASHBOARD_RUNTIME_DIR}"
fi

# --- preview root resolvable ---
PREVIEW_ROOT="${SITE_PREVIEW_ROOT:-${PUBLIC_SITE_ROOT}}"
if [[ -d "${PREVIEW_ROOT}" ]]; then
  pass "preview root exists: ${PREVIEW_ROOT}"
elif [[ -d "${WEBSITES_REPO}/public" ]]; then
  pass "fallback preview root available: ${WEBSITES_REPO}/public"
else
  fail "no preview root: ${PREVIEW_ROOT} or ${WEBSITES_REPO}/public"
fi

# --- static preview can validate target root ---
python3 -m http.server --help >/dev/null 2>&1 && pass "python3 http.server available for preview" || fail "python3 http.server unavailable"

python3 - <<'PY' "${REPORT_FILE}" "${PASS}" "${FAIL}" "${WARN}" "${WEBSITES_REPO}" "${DASHBOARD_RUNTIME_DIR}" "${PUBLIC_SITE_ROOT}"
import json
import sys
from pathlib import Path

report = {
    "schema": "websites.vps.healthcheck.v1",
    "pass": int(sys.argv[2]),
    "fail": int(sys.argv[3]),
    "warn": int(sys.argv[4]),
    "websites_repo": sys.argv[5],
    "dashboard_runtime_dir": sys.argv[6],
    "public_site_root": sys.argv[7],
}
Path(sys.argv[1]).write_text(json.dumps(report, indent=2) + "\n", encoding="utf-8")
PY

echo
echo "Summary: pass=${PASS} fail=${FAIL} warn=${WARN}"
echo "Report: ${REPORT_FILE}"

if [[ "${FAIL}" -gt 0 ]]; then
  exit 1
fi
