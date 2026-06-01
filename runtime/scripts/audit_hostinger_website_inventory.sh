#!/usr/bin/env bash
set -euo pipefail

WEBSITES="${WEBSITES:-$HOME/projects/websites}"
ENV_DIR="$WEBSITES/runtime/env/hostinger/sites"
REPORT_DIR="$WEBSITES/_reports/website_audit"
ROLLUP="$REPORT_DIR/website_inventory_audit_rollup.md"
ROLLUP_JSON="$REPORT_DIR/website_inventory_audit_rollup.json"
AUDIT_TMP_DIR="$REPORT_DIR/tmp"

mkdir -p "$REPORT_DIR" "$AUDIT_TMP_DIR"

json_escape() {
  python -c 'import json,sys; print(json.dumps(sys.stdin.read()))'
}

html_title() {
  python - "$1" << 'PY'
import re, sys
html = sys.argv[1]
m = re.search(r"<title[^>]*>(.*?)</title>", html, re.I | re.S)
if not m:
    print("")
else:
    title = re.sub(r"\s+", " ", m.group(1)).strip()
    print(title[:180])
PY
}

classify_local_body() {
  python - "$1" << 'PY'
import re, sys
html = sys.argv[1].lower()
signals = []
if "wp-content" in html or "wp-json" in html:
    signals.append("wordpress")
if "<form" in html:
    signals.append("form")
if "stripe" in html or "paypal" in html or "checkout" in html:
    signals.append("commerce_signal")
if "google-site-verification" in html or "gtag(" in html or "googletagmanager" in html:
    signals.append("analytics_or_search_signal")
if not signals:
    signals.append("static_or_unknown")
print(",".join(signals))
PY
}

fetch_status_body() {
  local url="$1"
  local out_status="$2"
  local out_body="$3"

  set +e
  curl -LfsS --max-time 25 -A "DreamOS-WebsiteAudit/1.0" -w "%{http_code}" -o "$out_body" "$url" > "$out_status" 2>"$out_body.err"
  local code=$?
  set -e

  if [ "$code" -ne 0 ]; then
    if grep -q "requested URL returned error" "$out_body.err"; then
      curl -LsS --max-time 25 -A "DreamOS-WebsiteAudit/1.0" -w "%{http_code}" -o "$out_body" "$url" > "$out_status" 2>/dev/null || echo "000" > "$out_status"
    else
      echo "000" > "$out_status"
    fi
  fi
}

echo "# Website Inventory Audit Rollup" > "$ROLLUP"
echo >> "$ROLLUP"
echo "Generated: $(date -Is)" >> "$ROLLUP"
echo >> "$ROLLUP"
echo "| Site | SSH | Type | HTTPS | HTTP | Root | Report |" >> "$ROLLUP"
echo "|---|---|---|---:|---:|---|---|" >> "$ROLLUP"

printf '{\n  "generated_at": %s,\n  "sites": [\n' "$(date -Is | json_escape)" > "$ROLLUP_JSON"

first_json=1
total=0
pass_count=0

shopt -s nullglob
for env_file in "$ENV_DIR"/*.env; do
  site="$(basename "$env_file" .env)"
  total=$((total + 1))
  safe_site="$(echo "$site" | tr -c 'A-Za-z0-9._-' '_')"
  site_report="$REPORT_DIR/${safe_site}_audit.md"
  remote_report="$AUDIT_TMP_DIR/${safe_site}_remote_audit.txt"
  body_https="$AUDIT_TMP_DIR/${safe_site}_https.html"
  body_http="$AUDIT_TMP_DIR/${safe_site}_http.html"
  status_https="$AUDIT_TMP_DIR/${safe_site}_https.status"
  status_http="$AUDIT_TMP_DIR/${safe_site}_http.status"

  echo
  echo "== AUDIT SITE $site =="

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

  ssh_status="FAIL"
  app_type="unknown"
  remote_summary="REMOTE_NOT_READ"
  wordpress="no"
  static_index="no"
  file_count="0"
  dir_count="0"
  root_writable="no"

  if [ -n "${HOSTINGER_USER:-}" ] && [ -n "${HOSTINGER_HOST:-}" ] && [ -n "${HOSTINGER_PORT:-}" ] && [ -f "$key_file" ]; then
    if ssh -n -o BatchMode=yes -o ConnectTimeout=15 -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "bash -s" > "$remote_report" 2>"$remote_report.err" << REMOTE
set -euo pipefail
ROOT="$remote_root"
echo "REMOTE_ROOT=\$ROOT"
if [ -d "\$ROOT" ]; then echo "ROOT_EXISTS=PASS"; else echo "ROOT_EXISTS=FAIL"; exit 0; fi
if [ -w "\$ROOT" ]; then echo "ROOT_WRITABLE=PASS"; else echo "ROOT_WRITABLE=FAIL"; fi
cd "\$ROOT"
echo "PWD=\$(pwd)"
echo "FILE_COUNT=\$(find . -type f | wc -l | tr -d ' ')"
echo "DIR_COUNT=\$(find . -type d | wc -l | tr -d ' ')"
if [ -f index.html ]; then echo "STATIC_INDEX=YES"; else echo "STATIC_INDEX=NO"; fi
if [ -f wp-config.php ] || [ -d wp-content ]; then echo "WORDPRESS=YES"; else echo "WORDPRESS=NO"; fi
if [ -f .htaccess ]; then echo "HTACCESS=YES"; else echo "HTACCESS=NO"; fi
echo "TOP_FILES_BEGIN"
find . -maxdepth 2 -type f | sort | head -80
echo "TOP_FILES_END"
echo "TOP_DIRS_BEGIN"
find . -maxdepth 2 -type d | sort | head -80
echo "TOP_DIRS_END"
REMOTE
    then
      ssh_status="PASS"
      root_writable="$(grep -E '^ROOT_WRITABLE=' "$remote_report" | cut -d= -f2 | tr '[:upper:]' '[:lower:]' || true)"
      file_count="$(grep -E '^FILE_COUNT=' "$remote_report" | cut -d= -f2 || echo 0)"
      dir_count="$(grep -E '^DIR_COUNT=' "$remote_report" | cut -d= -f2 || echo 0)"
      static_index="$(grep -E '^STATIC_INDEX=' "$remote_report" | cut -d= -f2 | tr '[:upper:]' '[:lower:]' || echo no)"
      wordpress="$(grep -E '^WORDPRESS=' "$remote_report" | cut -d= -f2 | tr '[:upper:]' '[:lower:]' || echo no)"

      if [ "$wordpress" = "yes" ]; then
        app_type="wordpress"
      elif [ "$static_index" = "yes" ]; then
        app_type="static"
      else
        app_type="unknown"
      fi

      remote_summary="$(cat "$remote_report")"
    else
      remote_summary="$(cat "$remote_report.err" 2>/dev/null || true)"
    fi
  else
    remote_summary="ENV_OR_KEY_MISSING"
  fi

  fetch_status_body "https://$site/" "$status_https" "$body_https"
  fetch_status_body "http://$site/" "$status_http" "$body_http"

  https_status="$(cat "$status_https" 2>/dev/null || echo 000)"
  http_status="$(cat "$status_http" 2>/dev/null || echo 000)"

  https_body="$(head -c 200000 "$body_https" 2>/dev/null || true)"
  title="$(html_title "$https_body")"
  body_signals="$(classify_local_body "$https_body")"

  if [ "$ssh_status" = "PASS" ] && [ "$root_writable" = "pass" ]; then
    pass_count=$((pass_count + 1))
  fi

  {
    echo "# Website Audit: $site"
    echo
    echo "Generated: $(date -Is)"
    echo
    echo "## Summary"
    echo
    echo "| Field | Value |"
    echo "|---|---|"
    echo "| Domain | $site |"
    echo "| SSH Access | $ssh_status |"
    echo "| Remote Root | \`$remote_root\` |"
    echo "| Root Writable | $root_writable |"
    echo "| App Type | $app_type |"
    echo "| HTTPS Status | $https_status |"
    echo "| HTTP Status | $http_status |"
    echo "| Homepage Title | $title |"
    echo "| Body Signals | $body_signals |"
    echo "| File Count | $file_count |"
    echo "| Dir Count | $dir_count |"
    echo
    echo "## Remote Inventory"
    echo
    echo '```text'
    printf '%s\n' "$remote_summary"
    echo '```'
    echo
    echo "## Public Checks"
    echo
    echo "- HTTPS: https://$site/ => $https_status"
    echo "- HTTP: http://$site/ => $http_status"
    echo
    echo "## Findings"
    echo
    if [ "$https_status" = "200" ]; then
      echo "- Homepage is publicly reachable over HTTPS."
    else
      echo "- Homepage HTTPS is not clean 200. Investigate DNS, SSL, permissions, app routing, or Hostinger config."
    fi

    if [ "$ssh_status" = "PASS" ] && [ "$root_writable" = "pass" ]; then
      echo "- SSH root access is usable for deployment."
    else
      echo "- SSH/root write access needs attention before deploy."
    fi

    if [ "$app_type" = "wordpress" ]; then
      echo "- Site appears to be WordPress."
    elif [ "$app_type" = "static" ]; then
      echo "- Site appears to be a static site."
    else
      echo "- Site type is unknown or incomplete."
    fi

    echo
    echo "## Recommended Next Action"
    echo
    if [ "$https_status" != "200" ]; then
      echo "- Run a serving repair or app-specific health check."
    elif [ "$app_type" = "unknown" ]; then
      echo "- Classify intended purpose and decide whether to park, rebuild, or deploy."
    else
      echo "- Keep in registry; no emergency action required."
    fi
  } > "$site_report"

  echo "SITE_REPORT=$site_report"
  echo "| $site | $ssh_status | $app_type | $https_status | $http_status | \`$remote_root\` | [report]($(basename "$site_report")) |" >> "$ROLLUP"

  if [ "$first_json" -eq 0 ]; then
    printf ',\n' >> "$ROLLUP_JSON"
  fi
  first_json=0

  python - "$ROLLUP_JSON" "$site" "$ssh_status" "$app_type" "$https_status" "$http_status" "$remote_root" "$root_writable" "$file_count" "$dir_count" "$title" "$body_signals" "$site_report" << 'PY'
import json, sys
_, rollup, site, ssh_status, app_type, https_status, http_status, root, writable, files, dirs, title, signals, report = sys.argv
obj = {
  "site": site,
  "ssh": ssh_status,
  "type": app_type,
  "https_status": https_status,
  "http_status": http_status,
  "remote_root": root,
  "root_writable": writable,
  "file_count": int(files) if files.isdigit() else 0,
  "dir_count": int(dirs) if dirs.isdigit() else 0,
  "title": title,
  "signals": signals.split(",") if signals else [],
  "report": report,
}
with open(rollup, "a", encoding="utf-8") as f:
    f.write(json.dumps(obj, indent=2))
PY

done

printf '\n  ],\n  "total_sites": %s,\n  "ssh_writable_pass": %s\n}\n' "$total" "$pass_count" >> "$ROLLUP_JSON"

{
  echo
  echo "## Totals"
  echo
  echo "- Sites audited: $total"
  echo "- SSH writable pass: $pass_count"
} >> "$ROLLUP"

echo
echo "WEBSITE_AUDIT_TOTAL=$total"
echo "WEBSITE_AUDIT_SSH_WRITABLE_PASS=$pass_count"
echo "WEBSITE_AUDIT_ROLLUP=$ROLLUP"
echo "WEBSITE_AUDIT_JSON=$ROLLUP_JSON"
echo "WEBSITE_INVENTORY_AUDIT=PASS"
