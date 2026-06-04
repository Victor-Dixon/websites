# Existing Website Registry Tooling Inspection

generated=2026-06-04T18:41:17-05:00

## Current production site configs
```text
/data/data/com.termux/files/home/projects/websites/sites/production/ariajet.site/site-config.json
{
  "domain": "ariajet.site",
  "type": "static_mvp",
  "status": "live",
  "purpose": "phone case showcase and limited phone repair request page",
  "remote_root": "/home/u996867598/domains/ariajet.site/public_html",
  "sections": [
    "phone cases",
    "repair proof",
    "limited repair services",
    "request form placeholder"
  ]
}
/data/data/com.termux/files/home/projects/websites/sites/production/tradingrobotplug.com/site-config.json
{
  "domain": "tradingrobotplug.com",
  "type": "static_trading_robot_plugin_lab",
  "status": "live",
  "purpose": "trading robot plugin showcase with strategy logic, proof logs, and risk controls",
  "remote_root": "/home/u996867598/domains/tradingrobotplug.com/public_html",
  "sections": [
    "platform",
    "trading robot plugins",
    "proof logs",
    "risk rules",
    "contact"
  ],
  "risk_positioning": "paper-first, proof-first, no profit guarantees"
}
/data/data/com.termux/files/home/projects/websites/sites/production/houstonsipqueen.com/site-config.json
{
  "domain": "houstonsipqueen.com",
  "type": "static_mobile_bartending_landing",
  "status": "live",
  "brand": "Houston Sip Queen",
  "palette": "Astros-inspired navy, orange, cream",
  "remote_root": "/home/u996867598/domains/houstonsipqueen.com/public_html",
  "compliance_positioning": "host provides alcohol; service provider does not sell alcohol directly through site"
}
```

## Existing Hostinger manifest
```yaml
version: 1
name: hostinger_sites_manifest
purpose: Dream.OS Hostinger WordPress deployment control surface
hostinger:
  host_secret: HOSTINGER_HOST
  user_secret: HOSTINGER_USER
  port_secret: HOSTINGER_PORT
  private_key_secret: HOSTINGER_SSH_PRIVATE_KEY
defaults:
  deploy_enabled: false
  plugin_deploy_enabled: false
  theme_deploy_enabled: false
  deploy_mode: manual_review_required
  proof_profile: hostinger_deploy_proof_profile
sites:
  ariajet_site:
    domain: 'ariajet.site'
    platform: wordpress
    wordpress_detected: true
    wp_root: '/home/u996867598/domains/ariajet.site/public_html'
    plugins_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins'
    themes_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/themes'
    deploy_enabled: false
    plugin_deploy_enabled: false
    theme_deploy_enabled: false
    deploy_mode: manual_review_required
    plugins:
      - slug: 'akismet'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'all-in-one-seo-pack'
        classification: unknown_review_required
        main_file: 'all_in_one_seo_pack.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'google-site-kit'
        classification: unknown_review_required
        main_file: 'google-site-kit.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger-easy-onboarding'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger-reach'
        classification: vendor
        main_file: 'hostinger-reach.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'kadence-starter-templates'
        classification: unknown_review_required
        main_file: 'class-kadence-starter-templates.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'litespeed-cache'
        classification: vendor
        main_file: 'guest.vary.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'sureforms'
        classification: unknown_review_required
        main_file: 'sureforms.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'tradingrobotplug-wordpress-plugin'
        classification: custom_candidate
        main_file: 'tradingrobotplug.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ultimate-addons-for-gutenberg'
        classification: unknown_review_required
        main_file: 'ultimate-addons-for-gutenberg.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'wpforms-lite'
        classification: unknown_review_required
        main_file: 'wpforms.php'
        collect_enabled: false
        deploy_enabled: false
    themes:
      - slug: 'ariajet'
        theme_name: 'riajet'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ariajet-cosmic'
        theme_name: 'AriaJet Cosmic'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ariajet-pink'
        theme_name: 'AriaJet Pink'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ariajet-studio'
        theme_name: 'AriaJet Studio'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ariajet-theme'
        theme_name: 'AriaJet Theme'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'ariajet-wp-theme'
        theme_name: ''
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'astra'
        theme_name: 'Astra'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'generatepress'
        theme_name: 'GeneratePress'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'kadence'
        theme_name: 'Kadence'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentyfive'
        theme_name: 'Twenty Twenty-Five'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentyfour'
        theme_name: 'Twenty Twenty-Four'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentythree'
        theme_name: 'Twenty Twenty-Three'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false

  crosbyultimateevents_com:
    domain: 'crosbyultimateevents.com'
    platform: wordpress
    wordpress_detected: true
    wp_root: '/home/u996867598/domains/crosbyultimateevents.com/public_html'
    plugins_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/plugins'
    themes_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/themes'
    deploy_enabled: false
    plugin_deploy_enabled: false
    theme_deploy_enabled: false
    deploy_mode: manual_review_required
    plugins:
      - slug: 'akismet'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'crosby-business-plan'
        classification: unknown_review_required
        main_file: 'debug_plugin.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger-easy-onboarding'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger-reach'
        classification: vendor
        main_file: 'hostinger-reach.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'litespeed-cache'
        classification: vendor
        main_file: 'guest.vary.php'
        collect_enabled: false
        deploy_enabled: false
    themes:
      - slug: 'crosbyultimateevents'
        theme_name: 'Crosby Ultimate Events'
        classification: unknown_review_required
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentyfive'
        theme_name: 'Twenty Twenty-Five'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentyfour'
        theme_name: 'Twenty Twenty-Four'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false
      - slug: 'twentytwentythree'
        theme_name: 'Twenty Twenty-Three'
        classification: wordpress_default
        collect_enabled: false
        deploy_enabled: false

  dadudekc_com:
    domain: 'dadudekc.com'
    platform: wordpress
    wordpress_detected: true
    wp_root: '/home/u996867598/domains/dadudekc.com/public_html'
    plugins_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/plugins'
    themes_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes'
    deploy_enabled: false
    plugin_deploy_enabled: false
    theme_deploy_enabled: false
    deploy_mode: manual_review_required
    plugins:
      - slug: 'akismet'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
      - slug: 'hostinger-easy-onboarding'
        classification: vendor
        main_file: 'index.php'
        collect_enabled: false
        deploy_enabled: false
```

## Website inventory script
```bash
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
```

## DreamVault Hostinger admin script
```bash
#!/usr/bin/env bash
set -euo pipefail
cd "$HOME/projects/DreamVault"

if [ -n "${DREAMOS_ENV_FILE:-}" ] && [ -f "$DREAMOS_ENV_FILE" ]; then
  set -a; . "$DREAMOS_ENV_FILE"; set +a
elif [ -f ".env.dreamos" ]; then
  set -a; . ".env.dreamos"; set +a
fi

REPORT="data/reports/hostinger_website_admin_025.md"
SITE_FILE="deploy/weareswarm_static/index.html"
TMPDIR_LOCAL="data/tmp/hostinger"
BODY="$TMPDIR_LOCAL/body.json"
HEADERS="$TMPDIR_LOCAL/headers.txt"

TOKEN="${HAPI_API_TOKEN:-${HOSTINGER_API_TOKEN:-${DREAMOS_HOSTINGER_API_TOKEN:-}}}"

mkdir -p data/reports "$TMPDIR_LOCAL"

{
  echo "# Hostinger Website Admin Report"
  echo
  echo "## Local Payload"
  test -s "$SITE_FILE"
  ls -lh "$SITE_FILE"

  echo
  echo "## Token Probe"
  if [ -n "$TOKEN" ]; then
    echo "PASS token loaded"
    printf "%s" "$TOKEN" | python -c 'import hashlib,sys; x=sys.stdin.read().strip(); print("TOKEN_LEN=%s" % len(x)); print("TOKEN_SHA256_12=%s" % hashlib.sha256(x.encode()).hexdigest()[:12])'
  else
    echo "FAIL token missing"
    echo "Expected: HAPI_API_TOKEN, HOSTINGER_API_TOKEN, or DREAMOS_HOSTINGER_API_TOKEN"
  fi

  echo
  echo "## Hostinger API Probe"
  if [ -n "$TOKEN" ]; then
    STATUS="$(
      curl -sS \
        -o "$BODY" \
        -D "$HEADERS" \
        -w "%{http_code}" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json" \
        "https://developers.hostinger.com/api/hosting/v1/websites?domain_name=weareswarm.site"
    )"

    echo "HTTP_STATUS=$STATUS"
    if [ "$STATUS" = "200" ]; then
      python -m json.tool "$BODY"
    else
      echo "BODY:"
      cat "$BODY"
    fi
  fi

  echo
  echo "## FTP/SFTP Deploy Readiness"
  missing=0
  for v in HOSTINGER_FTP_HOST HOSTINGER_FTP_USER HOSTINGER_FTP_PASS; do
    if [ -z "${!v:-}" ]; then
      echo "MISSING $v"
      missing=1
    else
      echo "PASS $v set"
    fi
  done
  echo "REMOTE_DIR=${HOSTINGER_FTP_REMOTE_DIR:-public_html}"

  echo
  echo "## Decision"
  echo "Hostinger API: account/site discovery."
  echo "FTP/SFTP: static file deployment to public_html/index.html."
} | tee "$REPORT"

echo
echo "== STATUS =="
git status --short
```

## DreamVault portfolio website admin
```python
#!/usr/bin/env python3
from __future__ import annotations

import os
import subprocess
import sys
from pathlib import Path

ROOT = Path.home() / "projects" / "DreamVault"
REGISTRY = ROOT / "data/registry/portfolio_sites.yaml"
REPORTS = ROOT / "data/reports"
TMP = ROOT / "data/tmp/portfolio_sites"

REPORTS.mkdir(parents=True, exist_ok=True)
TMP.mkdir(parents=True, exist_ok=True)

def load_env() -> None:
    env_file = ROOT / ".env.dreamos"
    if not env_file.exists():
        return
    for raw in env_file.read_text().splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        os.environ.setdefault(k.strip(), v.strip().strip("'").strip('"'))

def parse_registry() -> dict:
    # Minimal YAML parser for this simple registry shape. Avoids dependency drift.
    sites = {}
    current = None
    current_list_key = None
    for raw in REGISTRY.read_text().splitlines():
        if not raw.strip() or raw.strip().startswith("#"):
            continue
        if raw.startswith("  ") and not raw.startswith("    ") and raw.strip().endswith(":"):
            current = raw.strip()[:-1]
            sites[current] = {}
            current_list_key = None
            continue
        if current and raw.startswith("    ") and ": " in raw:
            k, v = raw.strip().split(": ", 1)
            sites[current][k] = v
            current_list_key = None
            continue
        if current and raw.startswith("    ") and raw.strip().endswith(":"):
            current_list_key = raw.strip()[:-1]
            sites[current][current_list_key] = []
            continue
        if current and current_list_key and raw.startswith("      - "):
            sites[current][current_list_key].append(raw.strip()[2:].strip())
    return sites

def sh(cmd: list[str], *, input_text: str | None = None) -> subprocess.CompletedProcess:
    return subprocess.run(cmd, input=input_text, text=True, cwd=ROOT, capture_output=True)

def main() -> int:
    load_env()
    sites = parse_registry()

    if len(sys.argv) < 2 or sys.argv[1] not in {"audit", "deploy"}:
        print("USAGE: run_portfolio_website_admin_027.py audit|deploy SITE_ID")
        print("SITES:", ", ".join(sorted(sites)))
        return 2

    mode = sys.argv[1]
    site_id = sys.argv[2] if len(sys.argv) > 2 else ""
    if site_id not in sites:
        print(f"UNKNOWN_SITE={site_id}")
        print("SITES:", ", ".join(sorted(sites)))
        return 2

    site = sites[site_id]
    report = REPORTS / f"portfolio_website_admin_027_{site_id}.md"
    local_file = ROOT / site["local_file"]

    lines = []
    lines.append(f"# Portfolio Website Admin Report: {site_id}")
    lines.append("")
    lines.append(f"- Domain: {site['domain']}")
    lines.append(f"- Local file: {site['local_file']}")
    lines.append(f"- Verify URL: {site['verify_url']}")
    lines.append("")

    if not local_file.exists() or local_file.stat().st_size == 0:
        lines.append("FAIL local payload missing")
        report.write_text("\n".join(lines) + "\n")
        print(report.read_text())
        return 3

    lines.append("## Local Payload")
    lines.append(f"PASS {site['local_file']} size={local_file.stat().st_size}")
    lines.append("")

    lines.append("## Env")
    missing = []
    for key_name in ("ftp_host_env", "ftp_user_env", "ftp_pass_env"):
        env_key = site[key_name]
        if os.environ.get(env_key):
            lines.append(f"PASS {env_key} set")
        else:
            lines.append(f"MISSING {env_key}")
            missing.append(env_key)
    lines.append("")

    if mode == "audit":
        lines.append("AUDIT_ONLY=PASS")
        report.write_text("\n".join(lines) + "\n")
        print(report.read_text())
        return 0 if not missing else 4

    if missing:
        lines.append("DEPLOY=BLOCKED_MISSING_ENV")
        report.write_text("\n".join(lines) + "\n")
        print(report.read_text())
        return 4

    host = os.environ[site["ftp_host_env"]].removeprefix("ftp://").removeprefix("ftps://").rstrip("/")
    user = os.environ[site["ftp_user_env"]]
    pw = os.environ[site["ftp_pass_env"]]
    remote_dir = site.get("remote_dir", "public_html")
    remote_file = site.get("remote_file", "index.html")
    ftp_url = f"ftp://{host}/{remote_dir}/{remote_file}"

    lines.append("## Deploy")
    lines.append(f"HOST={host}")
    lines.append(f"USER={user}")
    lines.append(f"REMOTE={remote_dir}/{remote_file}")
    report.write_text("\n".join(lines) + "\n")

    deploy = sh([
        "curl", "--fail", "--show-error", "--ftp-create-dirs",
        "-T", str(local_file),
        "--user", f"{user}:{pw}",
        ftp_url,
    ])

    with report.open("a") as f:
        f.write("\n")
        if deploy.returncode == 0:
            f.write("DEPLOY=PASS\n")
        else:
            f.write("DEPLOY=FAIL\n")
            f.write(deploy.stderr[-2000:] + "\n")
            print(report.read_text())
            return deploy.returncode

    html = sh(["curl", "-LfsS", site["verify_url"]])
    verify_ok = html.returncode == 0
    marker_results = []
    for marker in site.get("verify_markers", []):
        ok = marker in html.stdout
        marker_results.append((marker, ok))
        verify_ok = verify_ok and ok

    with report.open("a") as f:
        f.write("\n## Live Verify\n")
        f.write(f"HTTP_FETCH={'PASS' if html.returncode == 0 else 'FAIL'}\n")
        for marker, ok in marker_results:
            f.write(f"{'PASS' if ok else 'FAIL'} marker: {marker}\n")
        f.write(f"\nVERIFY={'PASS' if verify_ok else 'FAIL'}\n")

    print(report.read_text())
    return 0 if verify_ok else 5

if __name__ == "__main__":
    raise SystemExit(main())
```

## Domain registry
```json
{
    "version": 1,
    "purpose": "Canonical registry for Dream.OS website/domain/deploy surfaces.",
    "domains": [
        {
            "id": "dreamos_operator_dashboard",
            "name": "Dream.OS Operator Dashboard",
            "domain": null,
            "visibility": "private",
            "provider": "hostinger",
            "hosting_mode": "static_html",
            "source_repo": "DreamVault",
            "source_artifact": "data/reports/operator_dashboard/index.html",
            "deploy_target": "public_html/dreamos/operator-dashboard/index.html",
            "deploy_manifest": "runtime/deploy/hostinger_dashboard_manifest.json",
            "status": "staged",
            "design_status": "first_pass",
            "next_action": "confirm Hostinger deploy mechanism and public/private boundary"
        },
        {
            "id": "dadudekc_website",
            "name": "DaDudeKC Website",
            "domain": null,
            "visibility": "public_candidate",
            "provider": "unknown",
            "hosting_mode": "unknown",
            "source_repo": "DaDudeKC-Website",
            "source_artifact": null,
            "deploy_target": null,
            "deploy_manifest": null,
            "status": "inventory_candidate",
            "design_status": "unknown",
            "next_action": "inspect repo, domain, DNS, uptime, and design state"
        },
        {
            "id": "freerideinvestor_website",
            "name": "Freeride Investor Website",
            "domain": null,
            "visibility": "public_candidate",
            "provider": "unknown",
            "hosting_mode": "unknown",
            "source_repo": "FreerideinvestorWebsite",
            "source_artifact": null,
            "deploy_target": null,
            "deploy_manifest": null,
            "status": "inventory_candidate",
            "design_status": "unknown",
            "next_action": "inspect repo, domain, DNS, uptime, and design state"
        }
    ]
}
```

## Git status websites
```text
?? _reports/sitewide_deploy_registry/
```
