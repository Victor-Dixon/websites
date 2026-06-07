#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-$HOME/projects/websites}"
DOMAIN="${2:-crosbyultimateevents.com}"
ENV_FILE="$ROOT/runtime/env/hostinger/sites/$DOMAIN.env"
REMOTE_ROOT="/home/u996867598/domains/$DOMAIN/public_html"
OUT_DIR="$ROOT/data/exports/crosbyultimateevents"
STAMP="$(date +%Y%m%d_%H%M%S)"
OUT="$OUT_DIR/event_inquiries_$STAMP.csv"

mkdir -p "$OUT_DIR"
cd "$ROOT"

if [ ! -f "$ENV_FILE" ]; then
  echo "ENV_FILE=MISSING:$ENV_FILE"
  exit 2
fi

set -a
. "$ENV_FILE"
set +a

SSH_HOST="${HOSTINGER_HOST:?missing HOSTINGER_HOST}"
SSH_USER="${HOSTINGER_USER:?missing HOSTINGER_USER}"
SSH_PORT="${HOSTINGER_PORT:-65002}"
SSH_KEY="${HOSTINGER_SSH_PRIVATE_KEY_FILE:-}"

if [ -n "$SSH_KEY" ] && [ -f "$SSH_KEY" ]; then
  SCP_BASE=(scp -i "$SSH_KEY" -P "$SSH_PORT")
else
  SCP_BASE=(scp -P "$SSH_PORT")
fi

"${SCP_BASE[@]}" "$SSH_USER@$SSH_HOST:$REMOTE_ROOT/.private/leads/event_inquiries.csv" "$OUT"

echo "EXPORT=PASS"
echo "OUT=$OUT"
wc -l "$OUT"
