#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SECRET_ENV="$HOME/.config/dreamos/hostinger_freeride_github_secrets.env"

COLLECT_ROOT="$ROOT/collected/hostinger/wordpress"
REPORT_DIR="$ROOT/_reports"
REPORT="$REPORT_DIR/hostinger_custom_asset_collection_045.md"
RAW_LIST="$REPORT_DIR/hostinger_custom_asset_candidates_045.txt"
HASHES="$REPORT_DIR/hostinger_custom_asset_hashes_045.txt"
MANIFEST="$ROOT/runtime/deploy/hostinger_custom_asset_collection_manifest.yaml"

mkdir -p "$COLLECT_ROOT" "$REPORT_DIR" "$ROOT/runtime/deploy"

echo "== VERIFY LOCAL INPUTS =="
test -f "$SECRET_ENV"

set -a
. "$SECRET_ENV"
set +a

KEY_FILE="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
test -f "$KEY_FILE"

echo "SECRET_ENV_EXISTS=PASS"
echo "PRIVATE_KEY_EXISTS=PASS"

echo ""
echo "== DISCOVER CUSTOM CANDIDATES =="
ssh -i "$KEY_FILE" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "
set -e
base='/home/$HOSTINGER_USER/domains'
echo HOSTINGER_SSH_LOGIN=PASS >&2

for domain_dir in \"\$base\"/*; do
  [ -d \"\$domain_dir\" ] || continue
  domain=\$(basename \"\$domain_dir\")

  plugins_dir=\"\$domain_dir/public_html/wp-content/plugins\"
  themes_dir=\"\$domain_dir/public_html/wp-content/themes\"

  if [ -d \"\$plugins_dir\" ]; then
    for item in \"\$plugins_dir\"/*; do
      [ -d \"\$item\" ] || continue
      slug=\$(basename \"\$item\")
      low=\$(printf '%s' \"\$slug\" | tr '[:upper:]' '[:lower:]')
      case \"\$low\" in
        *dreamos*|*freeride*|*trading*|*robot*|*automator*|*setup*|*swarm*|*dadudekc*)
          printf 'plugin|%s|%s|%s\n' \"\$domain\" \"\$slug\" \"\$item\"
          ;;
      esac
    done
  fi

  if [ -d \"\$themes_dir\" ]; then
    for item in \"\$themes_dir\"/*; do
      [ -d \"\$item\" ] || continue
      slug=\$(basename \"\$item\")
      low=\$(printf '%s' \"\$slug\" | tr '[:upper:]' '[:lower:]')
      case \"\$low\" in
        *dreamos*|*freeride*|*trading*|*robot*|*automator*|*setup*|*swarm*|*dadudekc*)
          printf 'theme|%s|%s|%s\n' \"\$domain\" \"\$slug\" \"\$item\"
          ;;
      esac
    done
  fi
done
" | tee "$RAW_LIST"

echo "CANDIDATE_LIST_WRITTEN=PASS $RAW_LIST"

if [ ! -s "$RAW_LIST" ]; then
  echo "CUSTOM_CANDIDATES=NONE"
  exit 1
fi

echo ""
echo "== COLLECT CANDIDATES =="
while IFS='|' read -r kind domain slug remote_path; do
  [ -n "$kind" ] || continue

  dest="$COLLECT_ROOT/domains/$domain/${kind}s/$slug"
  mkdir -p "$(dirname "$dest")"

  echo "COLLECT kind=$kind domain=$domain slug=$slug"

  rm -rf "$dest"
  mkdir -p "$dest"

  ssh -i "$KEY_FILE" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" \
    "cd '$remote_path' && tar -czf - ." | tar -xzf - -C "$dest"

  test -d "$dest"
  echo "COLLECTED=$dest"
done < "$RAW_LIST"

echo "COLLECTION_COPY=PASS"

echo ""
echo "== HASH COLLECTED FILES =="
cd "$ROOT"
find collected/hostinger/wordpress -type f -print0 | sort -z | xargs -0 sha256sum > "$HASHES"
test -s "$HASHES"
echo "HASHES_WRITTEN=PASS $HASHES"

echo ""
echo "== WRITE COLLECTION MANIFEST =="
python - "$RAW_LIST" "$HASHES" "$MANIFEST" << 'PY'
from pathlib import Path
import sys
from collections import defaultdict

raw_list = Path(sys.argv[1])
hashes = Path(sys.argv[2])
manifest = Path(sys.argv[3])

rows = []
for line in raw_list.read_text(errors="replace").splitlines():
    parts = line.split("|", 3)
    if len(parts) == 4:
        rows.append(parts)

hash_lines = hashes.read_text(errors="replace").splitlines()

lines = []
lines.append("version: 1")
lines.append("name: hostinger_custom_asset_collection_manifest")
lines.append("purpose: salvage custom Hostinger WordPress plugins/themes before Dream.OS redeploy workflows")
lines.append("collection_root: collected/hostinger/wordpress")
lines.append("deploy_enabled: false")
lines.append("promotion_required: true")
lines.append("assets:")

for kind, domain, slug, remote_path in sorted(rows):
    local_path = f"collected/hostinger/wordpress/domains/{domain}/{kind}s/{slug}"
    lines.append(f"  - kind: {kind}")
    lines.append(f"    domain: {domain}")
    lines.append(f"    slug: {slug}")
    lines.append(f"    remote_path: {remote_path}")
    lines.append(f"    local_path: {local_path}")
    lines.append("    classification: custom_candidate")
    lines.append("    collected: true")
    lines.append("    deploy_enabled: false")
    lines.append("    promotion_status: review_required")

lines.append("")
lines.append("hash_file: _reports/hostinger_custom_asset_hashes_045.txt")
lines.append("proof:")
lines.append("  - ssh_login")
lines.append("  - candidate_list")
lines.append("  - local_copy")
lines.append("  - sha256_hashes")
lines.append("  - no_remote_mutation")

manifest.write_text("\n".join(lines) + "\n")
PY

test -f "$MANIFEST"
echo "MANIFEST_WRITTEN=PASS $MANIFEST"

echo ""
echo "== WRITE REPORT =="
{
  echo "# Hostinger Custom Asset Collection 045"
  echo ""
  echo "## Purpose"
  echo ""
  echo "Collect custom-candidate WordPress plugins/themes before Dream.OS redeploy or theme replacement workflows."
  echo ""
  echo "## Safety"
  echo ""
  echo "- No deploy performed."
  echo "- No remote files modified."
  echo "- All collected assets require review before promotion."
  echo ""
  echo "## Candidate List"
  echo ""
  echo '```text'
  cat "$RAW_LIST"
  echo '```'
  echo ""
  echo "## Outputs"
  echo ""
  echo "- \`$MANIFEST\`"
  echo "- \`$HASHES\`"
  echo "- \`$RAW_LIST\`"
  echo "- \`$COLLECT_ROOT\`"
} > "$REPORT"

echo "REPORT_WRITTEN=PASS $REPORT"

echo ""
echo "== VERIFY =="
test -s "$RAW_LIST"
test -s "$HASHES"
test -f "$MANIFEST"
test -f "$REPORT"
test -d "$COLLECT_ROOT"
echo "CUSTOM_ASSET_COLLECTION_VERIFY=PASS"

echo ""
echo "== CLOSEOUT =="
echo "HOSTINGER_CUSTOM_ASSET_COLLECTION=PASS"
echo "COLLECT_ROOT=$COLLECT_ROOT"
echo "MANIFEST=$MANIFEST"
echo "REPORT=$REPORT"
echo "HASHES=$HASHES"
