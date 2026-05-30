#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DIST="$ROOT/_hostinger_build/dist"

FREERIDE_ZIP="$DIST/freerideinvestor-content-engine-0.1.0.zip"
TRADING_ZIP="$DIST/dreamos-trading-tools-0.1.0.zip"

: "${HOSTINGER_HOST:?missing HOSTINGER_HOST}"
: "${HOSTINGER_USER:?missing HOSTINGER_USER}"
: "${HOSTINGER_PORT:?missing HOSTINGER_PORT}"
: "${HOSTINGER_WP_PLUGINS_DIR:?missing HOSTINGER_WP_PLUGINS_DIR}"

REMOTE="${HOSTINGER_USER}@${HOSTINGER_HOST}"
STAMP="$(date +%Y%m%d_%H%M%S)"
REMOTE_TMP="/tmp/freeride_ci_deploy_${STAMP}"

echo "== BUILD OR VERIFY ZIP ARTIFACTS =="

if [ -x "$ROOT/runtime/scripts/build_hostinger_freeride_plugins.sh" ]; then
  "$ROOT/runtime/scripts/build_hostinger_freeride_plugins.sh"
elif [ -x "$ROOT/_hostinger_build/build.sh" ]; then
  "$ROOT/_hostinger_build/build.sh"
else
  echo "NO_BUILD_SCRIPT_FOUND=USING_EXISTING_DIST_ZIPS"
fi

test -f "$FREERIDE_ZIP"
test -f "$TRADING_ZIP"

echo "FREERIDE_ZIP=PASS"
echo "TRADING_ZIP=PASS"

echo "== REMOTE PREPARE =="
ssh -p "$HOSTINGER_PORT" "$REMOTE" "mkdir -p '$REMOTE_TMP'"

echo "== UPLOAD ZIPS =="
scp -P "$HOSTINGER_PORT" "$FREERIDE_ZIP" "$TRADING_ZIP" "$REMOTE:$REMOTE_TMP/"

echo "UPLOAD=PASS"

echo "== REMOTE INSTALL =="
ssh -p "$HOSTINGER_PORT" "$REMOTE" bash -s << REMOTEEOF
set -euo pipefail

REMOTE_TMP="$REMOTE_TMP"
PLUGINS_DIR="$HOSTINGER_WP_PLUGINS_DIR"

mkdir -p "\$REMOTE_TMP/unpacked"
cd "\$REMOTE_TMP"

unzip -oq "freerideinvestor-content-engine-0.1.0.zip" -d "\$REMOTE_TMP/unpacked"
unzip -oq "dreamos-trading-tools-0.1.0.zip" -d "\$REMOTE_TMP/unpacked"

mkdir -p "\$PLUGINS_DIR"
cp -a "\$REMOTE_TMP/unpacked/"* "\$PLUGINS_DIR/"

rm -rf "\$REMOTE_TMP"

echo "REMOTE_COPY=PASS"
echo "REMOTE_CLEANUP=PASS"
REMOTEEOF

echo "== CLOSEOUT =="
echo "HOSTINGER_FREERIDE_CI_DEPLOY=PASS"
