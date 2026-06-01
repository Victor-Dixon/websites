#!/usr/bin/env bash
set -euo pipefail

WEBSITES="${WEBSITES:-$HOME/projects/websites}"
ENV_DIR="$WEBSITES/runtime/env/hostinger/sites"

echo "== HOSTINGER ACCESS PREFLIGHT =="
test -d "$ENV_DIR"

shopt -s nullglob
envs=("$ENV_DIR"/*.env)

if [ "${#envs[@]}" -eq 0 ]; then
  echo "NO_HOSTINGER_SITE_ENVS_FOUND"
  exit 1
fi

overall=0

for env_file in "${envs[@]}"; do
  site="$(basename "$env_file" .env)"
  echo
  echo "== SITE $site =="

  set +u
  HOSTINGER_USER=""
  HOSTINGER_HOST=""
  HOSTINGER_PORT=""
  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
  DOMAIN="$site"
  HOSTINGER_WP_ROOT=""
  set -a
  # shellcheck disable=SC1090
  . "$env_file"
  set +a
  set -u

  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
  domain="${DOMAIN:-$site}"
  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"

  if [ -z "${HOSTINGER_USER:-}" ] || [ -z "${HOSTINGER_HOST:-}" ] || [ -z "${HOSTINGER_PORT:-}" ] || [ -z "${HOSTINGER_SSH_PRIVATE_KEY_FILE:-}" ]; then
    echo "ENV_FIELDS=FAIL site=$site"
    overall=1
    continue
  fi

  if [ ! -f "$key_file" ]; then
    echo "KEY_FILE=FAIL site=$site key=$key_file"
    overall=1
    continue
  fi

  echo "ENV_FIELDS=PASS"
  echo "KEY_FILE=PASS"
  echo "REMOTE_ROOT=$remote_root"

  if ssh -n -o BatchMode=yes -o ConnectTimeout=15 -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "test -d '$remote_root' && test -w '$remote_root' && echo SSH_ROOT_WRITABLE=PASS"; then
    echo "SITE_ACCESS=PASS site=$site"
  else
    echo "SITE_ACCESS=FAIL site=$site"
    overall=1
  fi
done

if [ "$overall" -eq 0 ]; then
  echo
  echo "HOSTINGER_ACCESS_PREFLIGHT=PASS"
else
  echo
  echo "HOSTINGER_ACCESS_PREFLIGHT=FAIL"
fi

exit "$overall"
