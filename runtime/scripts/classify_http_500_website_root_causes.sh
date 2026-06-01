#!/usr/bin/env bash
set -euo pipefail

WEBSITES="${WEBSITES:-$HOME/projects/websites}"
ENV_DIR="$WEBSITES/runtime/env/hostinger/sites"
CLASSIFICATION_JSON="$WEBSITES/_reports/website_audit/website_inventory_classification_003.json"
REPORT_DIR="$WEBSITES/_reports/website_audit/http_500_root_causes"
ROLLUP="$REPORT_DIR/http_500_root_cause_rollup_004.md"
ROLLUP_JSON="$REPORT_DIR/http_500_root_cause_rollup_004.json"

mkdir -p "$REPORT_DIR"

mapfile -t SITES < <(python - "$CLASSIFICATION_JSON" << 'PY'
import json, sys
from pathlib import Path

data = json.loads(Path(sys.argv[1]).read_text())
for site in data.get("sites", []):
    if str(site.get("https_status")) == "500":
        print(site["site"])
PY
)

echo "# HTTP 500 Website Root Cause Rollup 004" > "$ROLLUP"
echo >> "$ROLLUP"
echo "Generated: $(date -Is)" >> "$ROLLUP"
echo >> "$ROLLUP"
echo "| Site | Root Cause | Recommendation | Report |" >> "$ROLLUP"
echo "|---|---|---|---|" >> "$ROLLUP"

printf '{\n  "generated_at": %s,\n  "sites": [\n' "$(python -c 'import json,datetime; print(json.dumps(datetime.datetime.now(datetime.timezone.utc).isoformat()))')" > "$ROLLUP_JSON"

first=1
count=0

for site in "${SITES[@]}"; do
  count=$((count + 1))
  echo
  echo "== AUDIT HTTP 500 SITE $site =="

  env_file="$ENV_DIR/$site.env"
  safe_site="$(echo "$site" | tr -c 'A-Za-z0-9._-' '_')"
  remote_capture="$REPORT_DIR/${safe_site}_remote_500_audit.txt"
  site_report="$REPORT_DIR/${safe_site}_500_root_cause.md"
  remote_script_local="$REPORT_DIR/${safe_site}_remote_audit.sh"
  remote_script="/tmp/${safe_site}_remote_audit.sh"

  test -f "$env_file"

  set +u
  HOSTINGER_USER=""
  HOSTINGER_HOST=""
  HOSTINGER_PORT=""
  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
  DOMAIN="$site"
  HOSTINGER_WP_ROOT=""
  set -a
  . "$env_file"
  set +a
  set -u

  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
  domain="${DOMAIN:-$site}"
  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"

  cat > "$remote_script_local" << 'REMOTE'
#!/usr/bin/env bash
set -euo pipefail

ROOT="$1"
DOMAIN="$2"

echo "DOMAIN=$DOMAIN"
echo "REMOTE_ROOT=$ROOT"

if [ ! -d "$ROOT" ]; then
  echo "ROOT_EXISTS=NO"
  exit 0
fi

echo "ROOT_EXISTS=YES"

cd "$ROOT"

echo "== ROOT_STAT =="
pwd
ls -la

echo "== COUNTS =="
echo "FILE_COUNT=$(find . -type f | wc -l | tr -d ' ')"
echo "DIR_COUNT=$(find . -type d | wc -l | tr -d ' ')"

echo "== PERMISSIONS =="
stat -c 'ROOT_MODE=%a ROOT_OWNER=%U ROOT_GROUP=%G' "$ROOT" 2>/dev/null || true
find . -maxdepth 1 -printf '%M %u %g %p\n' 2>/dev/null | sort || true

echo "== INDEX_FILES =="
for f in index.php index.html index.htm default.php default.html; do
  if [ -f "$f" ]; then
    echo "INDEX_PRESENT=$f"
    echo "INDEX_MODE=$(stat -c '%a' "$f" 2>/dev/null || true)"
    echo "INDEX_HEAD_BEGIN=$f"
    head -40 "$f" 2>/dev/null || true
    echo "INDEX_HEAD_END=$f"
  fi
done

echo "== HTACCESS =="
if [ -f .htaccess ]; then
  echo "HTACCESS_PRESENT=YES"
  echo "HTACCESS_MODE=$(stat -c '%a' .htaccess 2>/dev/null || true)"
  echo "HTACCESS_BEGIN"
  sed -n '1,160p' .htaccess 2>/dev/null || true
  echo "HTACCESS_END"
else
  echo "HTACCESS_PRESENT=NO"
fi

echo "== WORDPRESS_MARKERS =="
if [ -f wp-config.php ]; then echo "WP_CONFIG=YES"; else echo "WP_CONFIG=NO"; fi
if [ -d wp-content ]; then echo "WP_CONTENT=YES"; else echo "WP_CONTENT=NO"; fi
if [ -d wp-admin ]; then echo "WP_ADMIN=YES"; else echo "WP_ADMIN=NO"; fi
if [ -f wp-load.php ]; then echo "WP_LOAD=YES"; else echo "WP_LOAD=NO"; fi
if [ -f wp-config.php ]; then
  echo "WP_CONFIG_HEAD_BEGIN"
  grep -E "DB_NAME|DB_USER|WP_DEBUG|table_prefix|ABSPATH" wp-config.php 2>/dev/null | sed 's/password.*/password REDACTED/I' || true
  echo "WP_CONFIG_HEAD_END"
fi

echo "== PHP_MARKERS =="
find . -maxdepth 2 -type f \( -name '*.php' -o -name 'composer.json' -o -name '.user.ini' -o -name 'php.ini' \) | sort | head -100

echo "== COMMON_ERROR_LOGS =="
for log in error_log php_errorlog debug.log wp-content/debug.log logs/error.log; do
  if [ -f "$log" ]; then
    echo "LOG_PRESENT=$log"
    echo "LOG_TAIL_BEGIN=$log"
    tail -80 "$log" 2>/dev/null || true
    echo "LOG_TAIL_END=$log"
  fi
done

echo "== DOMAIN_LEVEL_LOG_SEARCH =="
for base in "$HOME/domains/$DOMAIN/logs" "$HOME/domains/$DOMAIN" "$HOME/logs"; do
  if [ -d "$base" ]; then
    echo "LOG_DIR=$base"
    find "$base" -maxdepth 2 -type f \( -iname '*error*' -o -iname '*.log' \) 2>/dev/null | head -20
  fi
done

echo "== TOP_FILES =="
find . -maxdepth 3 -type f | sort | head -160

echo "== TOP_DIRS =="
find . -maxdepth 3 -type d | sort | head -160

echo "REMOTE_500_AUDIT=PASS"
REMOTE

  chmod +x "$remote_script_local"

  scp -q -o LogLevel=ERROR -i "$key_file" -P "$HOSTINGER_PORT" "$remote_script_local" "$HOSTINGER_USER@$HOSTINGER_HOST:$remote_script"

  ssh -n -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "bash '$remote_script' '$remote_root' '$domain'; rm -f '$remote_script'" > "$remote_capture"

  python - "$site" "$remote_capture" "$site_report" << 'PY'
import json
import re
import sys
from pathlib import Path

site = sys.argv[1]
capture_path = Path(sys.argv[2])
report_path = Path(sys.argv[3])
text = capture_path.read_text(errors="replace")
lower = text.lower()

def has(marker):
    return marker.lower() in lower

root_cause = "unknown_500"
recommendation = "review_manually"

if "root_exists=no" in lower:
    root_cause = "missing_public_html_root"
    recommendation = "rebuild_or_recreate_site_root"
elif "index_present=" not in lower:
    root_cause = "missing_index_file"
    recommendation = "deploy_static_index_or_restore_application"
elif "wp_config=yes" in lower and ("wp_content=yes" in lower or "wp_load=yes" in lower):
    if "database" in lower and ("error" in lower or "connection" in lower):
        root_cause = "wordpress_database_or_config_error"
        recommendation = "repair_wp_config_database_or_restore_wordpress"
    elif "fatal error" in lower or "parse error" in lower:
        root_cause = "wordpress_php_fatal_error"
        recommendation = "inspect_php_error_log_disable_bad_plugin_or_theme"
    else:
        root_cause = "wordpress_install_returning_500"
        recommendation = "run_wp_cli_health_check_or_restore_wordpress"
elif "htaccess_present=yes" in lower and ("rewrite" in lower or "deny" in lower or "require all denied" in lower):
    root_cause = "possible_htaccess_or_routing_error"
    recommendation = "backup_and_replace_htaccess_with_safe_static_or_wordpress_rules"
elif "fatal error" in lower or "parse error" in lower or "uncaught" in lower:
    root_cause = "php_fatal_error"
    recommendation = "inspect_error_log_and_disable_faulting_php_code"
elif "index_present=index.php" in lower and "wp_config=no" in lower:
    root_cause = "custom_php_app_error_or_incomplete_php_site"
    recommendation = "archive_or_rebuild_unless_site_has_current_business_purpose"
elif "index_present=index.html" in lower:
    root_cause = "static_site_server_config_or_htaccess_error"
    recommendation = "repair_permissions_and_htaccess_or_redeploy_static_site"

# purpose guess
purpose = "unknown"
name = site.lower()
if "event" in name:
    purpose = "events_business_candidate"
elif "sip" in name or "queen" in name:
    purpose = "food_beverage_brand_candidate"
elif "trading" in name or "robot" in name:
    purpose = "trading_project_candidate"
elif "swarm" in name:
    purpose = "dreamos_swarm_brand_candidate"
elif "dream" in name:
    purpose = "dreamos_brand_candidate"
elif "aria" in name or "jet" in name:
    purpose = "travel_or_brand_candidate"
elif "secret" in name:
    purpose = "content_or_brand_candidate"

priority = "classify_purpose_before_repair"
if recommendation.startswith("repair") or "repair" in recommendation:
    priority = "safe_repair_candidate"
if recommendation.startswith("archive"):
    priority = "archive_candidate"
if root_cause in {"missing_index_file", "missing_public_html_root"}:
    priority = "rebuild_or_park_candidate"

result = {
    "site": site,
    "root_cause": root_cause,
    "recommendation": recommendation,
    "priority": priority,
    "intended_purpose_guess": purpose,
    "remote_capture": str(capture_path),
    "report": str(report_path),
}

lines = []
lines.append(f"# HTTP 500 Root Cause: {site}")
lines.append("")
lines.append("## Classification")
lines.append("")
lines.append(f"- Root cause: `{root_cause}`")
lines.append(f"- Recommendation: `{recommendation}`")
lines.append(f"- Priority: `{priority}`")
lines.append(f"- Intended purpose guess: `{purpose}`")
lines.append("")
lines.append("## Decision")
lines.append("")
if priority == "safe_repair_candidate":
    lines.append("- Safe next step: create a site-specific repair lane with backup first.")
elif priority == "rebuild_or_park_candidate":
    lines.append("- Safe next step: decide whether this domain has current business value. If yes, deploy a clean static placeholder. If no, park/archive.")
else:
    lines.append("- Safe next step: confirm intended purpose before changing files.")
lines.append("")
lines.append("## Remote Evidence")
lines.append("")
lines.append("```text")
lines.append(text[:30000])
lines.append("```")

report_path.write_text("\n".join(lines) + "\n")

print(json.dumps(result))
PY

  result_json="$(python - "$site_report" << 'PY'
import json, re, sys
from pathlib import Path
text = Path(sys.argv[1]).read_text()
site = re.search(r"# HTTP 500 Root Cause: (.+)", text).group(1)
root = re.search(r"Root cause: `([^`]+)`", text).group(1)
rec = re.search(r"Recommendation: `([^`]+)`", text).group(1)
prio = re.search(r"Priority: `([^`]+)`", text).group(1)
purpose = re.search(r"Intended purpose guess: `([^`]+)`", text).group(1)
print(json.dumps({"site": site, "root_cause": root, "recommendation": rec, "priority": prio, "purpose": purpose}))
PY
)"

  root_cause="$(python -c 'import json,sys; print(json.loads(sys.argv[1])["root_cause"])' "$result_json")"
  recommendation="$(python -c 'import json,sys; print(json.loads(sys.argv[1])["recommendation"])' "$result_json")"
  priority="$(python -c 'import json,sys; print(json.loads(sys.argv[1])["priority"])' "$result_json")"

  echo "SITE_ROOT_CAUSE=$site:$root_cause:$recommendation:$priority"
  echo "| $site | $root_cause | $recommendation | [report]($(basename "$site_report")) |" >> "$ROLLUP"

  if [ "$first" -eq 0 ]; then
    printf ',\n' >> "$ROLLUP_JSON"
  fi
  first=0

  python - "$ROLLUP_JSON" "$site" "$root_cause" "$recommendation" "$priority" "$site_report" "$remote_capture" << 'PY'
import json, sys
_, out, site, root, rec, prio, report, capture = sys.argv
obj = {
  "site": site,
  "root_cause": root,
  "recommendation": rec,
  "priority": prio,
  "report": report,
  "remote_capture": capture,
}
with open(out, "a", encoding="utf-8") as f:
    f.write(json.dumps(obj, indent=2))
PY

done

printf '\n  ],\n  "total_500_sites": %s\n}\n' "$count" >> "$ROLLUP_JSON"

echo
echo "HTTP_500_SITE_COUNT=$count"
echo "HTTP_500_ROOT_CAUSE_ROLLUP=$ROLLUP"
echo "HTTP_500_ROOT_CAUSE_JSON=$ROLLUP_JSON"
echo "HTTP_500_ROOT_CAUSE_AUDIT=PASS"
