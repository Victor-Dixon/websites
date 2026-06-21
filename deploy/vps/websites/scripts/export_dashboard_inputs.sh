#!/usr/bin/env bash
# Copy and sanitize Dream.OS dashboard JSON into public site data folders.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

SECRETS_ENV="${1:-${DEFAULT_SECRETS_ENV}}"
load_vps_env "${SECRETS_ENV}"

require_cmd python3

RUNTIME_DIR="${DASHBOARD_RUNTIME_DIR}"
SITE_DATA_DIR="${DASHBOARD_SITE_DATA_DIR}"
PUBLIC_DATA_DIR="${DASHBOARD_PUBLIC_DATA_DIR}"
REPORT_DIR="${VPS_REPORTS_DIR}"
STAMP="$(timestamp_utc)"
REPORT_FILE="${REPORT_DIR}/export_dashboard_${STAMP}.json"

mkdir -p "${SITE_DATA_DIR}" "${PUBLIC_DATA_DIR}" "${REPORT_DIR}"

echo "==> Export dashboard inputs"
echo "source: ${RUNTIME_DIR}"
echo "site data: ${SITE_DATA_DIR}"
echo "public data: ${PUBLIC_DATA_DIR}"

if [[ ! -d "${RUNTIME_DIR}" ]]; then
  echo "ERROR: dashboard runtime dir missing: ${RUNTIME_DIR}" >&2
  exit 1
fi

COPIED=()
SKIPPED=()
FAILED=()

mapfile -t JSON_FILES < <(find "${RUNTIME_DIR}" -type f -name '*.json' 2>/dev/null || true)

if [[ ${#JSON_FILES[@]} -eq 0 ]]; then
  echo "WARN: no JSON files found under ${RUNTIME_DIR}"
fi

for src in "${JSON_FILES[@]}"; do
  [[ -f "${src}" ]] || continue
  base="$(basename "${src}")"

  if ! validate_json_file "${src}"; then
    FAILED+=("${src}")
    continue
  fi

  site_dest="${SITE_DATA_DIR}/${base}"
  public_dest="${PUBLIC_DATA_DIR}/${base}"

  if sanitize_json_for_public "${src}" "${site_dest}" && sanitize_json_for_public "${src}" "${public_dest}"; then
    COPIED+=("${base}")
  else
    FAILED+=("${src}")
  fi
done

# Normalize manifest metadata for VPS (no Windows/DreamVault drive letters in export)
MANIFEST="${SITE_DATA_DIR}/manifest.json"
if [[ -f "${MANIFEST}" ]]; then
  python3 - <<'PY' "${MANIFEST}" "${WEBSITES_REPO}" "${RUNTIME_DIR}"
import json
import sys
from pathlib import Path

manifest = Path(sys.argv[1])
repo = Path(sys.argv[2])
runtime = Path(sys.argv[3])
data = json.loads(manifest.read_text(encoding="utf-8"))
data["schema"] = data.get("schema", "weareswarm.online.planner_manifest.v1")
data["mode"] = "vps_export"
data["site_data_root"] = "data/planner"
data["dreamvault_planner_root"] = str(runtime)
data["dreamvault_emit_root"] = str(runtime)
data["refresh_command"] = f"{repo}/deploy/vps/websites/scripts/export_dashboard_inputs.sh"
data["public_safe"] = True
manifest.write_text(json.dumps(data, indent=2, sort_keys=True) + "\n", encoding="utf-8")
PY
fi

python3 - <<'PY' "${REPORT_FILE}" "${RUNTIME_DIR}" "${SITE_DATA_DIR}" "${PUBLIC_DATA_DIR}" "${#COPIED[@]}" "${#SKIPPED[@]}" "${#FAILED[@]}"
import json
import sys
from pathlib import Path

report = {
    "schema": "websites.vps.dashboard_export.v1",
    "source": sys.argv[2],
    "site_data_dir": sys.argv[3],
    "public_data_dir": sys.argv[4],
    "copied_count": int(sys.argv[5]),
    "skipped_count": int(sys.argv[6]),
    "failed_count": int(sys.argv[7]),
}
Path(sys.argv[1]).write_text(json.dumps(report, indent=2) + "\n", encoding="utf-8")
PY

echo "Copied: ${#COPIED[@]}  Skipped: ${#SKIPPED[@]}  Failed: ${#FAILED[@]}"
echo "Report: ${REPORT_FILE}"

if [[ ${#FAILED[@]} -gt 0 ]]; then
  printf 'Failed files:\n' >&2
  printf '  %s\n' "${FAILED[@]}" >&2
  exit 1
fi
