#!/usr/bin/env bash
# Install Dream.OS websites VPS runtime helpers on Ubuntu 24.04.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

SECRETS_ENV="${1:-${DEFAULT_SECRETS_ENV}}"

echo "==> Dream.OS websites VPS install"
load_vps_env "${SECRETS_ENV}"

require_cmd python3

DIRS=(
  "${DREAMOS_ROOT}"
  "${DREAMOS_ROOT}/repos"
  "${DREAMOS_ROOT}/runtime"
  "${DASHBOARD_RUNTIME_DIR}"
  "${PUBLIC_SITE_ROOT}"
  "${DASHBOARD_SITE_DATA_DIR}"
  "${DASHBOARD_PUBLIC_DATA_DIR}"
  "${VPS_REPORTS_DIR}"
  "$(dirname "${SECRETS_ENV}")"
)

for dir in "${DIRS[@]}"; do
  if [[ ! -d "${dir}" ]]; then
    mkdir -p "${dir}"
    echo "created ${dir}"
  fi
done

chmod +x "${SCRIPT_DIR}/"*.sh

if [[ ! -f "${SECRETS_ENV}" ]]; then
  if [[ -f "${DEFAULT_ENV_EXAMPLE}" ]]; then
    cp "${DEFAULT_ENV_EXAMPLE}" "${SECRETS_ENV}"
    chmod 600 "${SECRETS_ENV}"
    echo "created ${SECRETS_ENV} from .env.example (edit paths before production use)"
  else
    echo "WARN: ${DEFAULT_ENV_EXAMPLE} missing; create ${SECRETS_ENV} manually"
  fi
else
  echo "secrets env already present: ${SECRETS_ENV}"
fi

cat <<EOF

Install complete.

Next steps:
  1. Edit ${SECRETS_ENV} with your VPS paths.
  2. Clone/sync websites repo to ${WEBSITES_REPO}.
  3. Run healthcheck:
       ${SCRIPT_DIR}/healthcheck.sh
  4. Optional local preview:
       ${SCRIPT_DIR}/preview.sh
  5. Export dashboard JSON:
       ${SCRIPT_DIR}/export_dashboard_inputs.sh

Hostinger GitHub Actions deploy is unchanged; this package is VPS-side only.
EOF
