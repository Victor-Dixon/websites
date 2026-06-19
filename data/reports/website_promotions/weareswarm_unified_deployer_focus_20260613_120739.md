# WeAreSwarm Unified Deployer Focus

Generated: 20260613_120739

## Repo
/data/data/com.termux/files/home/projects/websites

## Git Status
?? data/reports/website_promotions/weareswarm_unified_deployer_focus_20260613_120710.md
?? data/reports/website_promotions/weareswarm_unified_deployer_focus_20260613_120739.md

## Hostinger Site Env Files
```
total 59
drwx------. 2 u0_a477 u0_a477 3452 May 31 05:29 .
drwx------. 3 u0_a477 u0_a477 3452 May 31 05:28 ..
-rw-------. 1 u0_a477 u0_a477  499 May 31 05:28 ariajet.site.env
-rw-------. 1 u0_a477 u0_a477  547 May 31 05:28 crosbyultimateevents.com.env
-rw-------. 1 u0_a477 u0_a477  499 May 31 05:29 dadudekc.com.env
-rw-------. 1 u0_a477 u0_a477  503 May 31 05:29 dadudekc.site.env
-rw-------. 1 u0_a477 u0_a477  539 May 31 05:29 digitaldreamscape.site.env
-rw-------. 1 u0_a477 u0_a477  531 May 31 05:29 freerideinvestor.com.env
-rw-------. 1 u0_a477 u0_a477  527 May 31 05:29 houstonsipqueen.com.env
-rw-------. 1 u0_a477 u0_a477  527 May 31 05:29 prismblossom.online.env
-rw-------. 1 u0_a477 u0_a477  527 May 31 05:29 southwestsecret.com.env
-rw-------. 1 u0_a477 u0_a477  531 May 31 05:29 tradingrobotplug.com.env
-rw-------. 1 u0_a477 u0_a477  519 May 31 05:29 weareswarm.online.env
-rw-------. 1 u0_a477 u0_a477  511 May 31 05:29 weareswarm.site.env
-rw-------. 1 u0_a477 u0_a477  503 May 31 05:29 xthunder.site.env
```

## WeAreSwarm Env Summary
```
### runtime/env/hostinger/sites/weareswarm.online.env
GH_REPO='Victor-Dixon/websites'
HOSTINGER_HOST='157.173.214.121'
HOSTINGER_USER=***REDACTED***
HOSTINGER_PORT='65002'
DOMAIN='weareswarm.online'
HOSTINGER_WP_ROOT='/home/u996867598/domains/weareswarm.online/public_html'
HOSTINGER_WP_PLUGINS_DIR='/home/u996867598/domains/weareswarm.online/public_html/wp-content/plugins'
HOSTINGER_WP_THEMES_DIR='/home/u996867598/domains/weareswarm.online/public_html/wp-content/themes'
HOSTINGER_SSH_PRIVATE_KEY_FILE='/data/data/com.termux/files/home/.ssh/hostinger_freeride_deploy_key'

### runtime/env/hostinger/sites/weareswarm.site.env
GH_REPO='Victor-Dixon/websites'
HOSTINGER_HOST='157.173.214.121'
HOSTINGER_USER=***REDACTED***
HOSTINGER_PORT='65002'
DOMAIN='weareswarm.site'
HOSTINGER_WP_ROOT='/home/u996867598/domains/weareswarm.site/public_html'
HOSTINGER_WP_PLUGINS_DIR='/home/u996867598/domains/weareswarm.site/public_html/wp-content/plugins'
HOSTINGER_WP_THEMES_DIR='/home/u996867598/domains/weareswarm.site/public_html/wp-content/themes'
HOSTINGER_SSH_PRIVATE_KEY_FILE='/data/data/com.termux/files/home/.ssh/hostinger_freeride_deploy_key'

```

## Package Scripts
```
no package.json
```

## Deploy Tool Candidates
```
```

## WeAreSwarm Source / Deploy Tree
```
```

## Unified / SFTP / Public HTML References
```
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:10:: "${HOSTINGER_HOST:?missing HOSTINGER_HOST}"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:11:: "${HOSTINGER_USER:?missing HOSTINGER_USER}"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:12:: "${HOSTINGER_PORT:?missing HOSTINGER_PORT}"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:13:: "${HOSTINGER_WP_PLUGINS_DIR:?missing HOSTINGER_WP_PLUGINS_DIR}"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:15:REMOTE="${HOSTINGER_USER}@${HOSTINGER_HOST}"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:36:ssh -p "$HOSTINGER_PORT" "$REMOTE" "mkdir -p '$REMOTE_TMP'"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:39:scp -P "$HOSTINGER_PORT" "$FREERIDE_ZIP" "$TRADING_ZIP" "$REMOTE:$REMOTE_TMP/"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:44:ssh -p "$HOSTINGER_PORT" "$REMOTE" bash -s << REMOTEEOF
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:48:PLUGINS_DIR="$HOSTINGER_WP_PLUGINS_DIR"
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh:66:echo "HOSTINGER_FREERIDE_CI_DEPLOY=PASS"
runtime/scripts/collect_hostinger_custom_assets_045.sh:23:KEY_FILE="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
runtime/scripts/collect_hostinger_custom_assets_045.sh:31:ssh -i "$KEY_FILE" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "
runtime/scripts/collect_hostinger_custom_assets_045.sh:33:base='/home/$HOSTINGER_USER/domains'
runtime/scripts/collect_hostinger_custom_assets_045.sh:34:echo HOSTINGER_SSH_LOGIN=PASS >&2
runtime/scripts/collect_hostinger_custom_assets_045.sh:40:  plugins_dir=\"\$domain_dir/public_html/wp-content/plugins\"
runtime/scripts/collect_hostinger_custom_assets_045.sh:41:  themes_dir=\"\$domain_dir/public_html/wp-content/themes\"
runtime/scripts/collect_hostinger_custom_assets_045.sh:91:  ssh -n -i "$KEY_FILE" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" \
runtime/scripts/collect_hostinger_custom_assets_045.sh:129:lines.append("purpose: salvage custom Hostinger WordPress plugins/themes before Dream.OS redeploy workflows")
runtime/scripts/collect_hostinger_custom_assets_045.sh:165:  echo "# Hostinger Custom Asset Collection 045"
runtime/scripts/collect_hostinger_custom_assets_045.sh:204:echo "HOSTINGER_CUSTOM_ASSET_COLLECTION=PASS"
runtime/scripts/hostinger_wp_manager.py:49:    key_file = expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"])
runtime/scripts/hostinger_wp_manager.py:57:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_wp_manager.py:58:        f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}',
runtime/scripts/hostinger_wp_manager.py:63:    if env.get("HOSTINGER_WP_ROOT"):
runtime/scripts/hostinger_wp_manager.py:64:        return env["HOSTINGER_WP_ROOT"]
runtime/scripts/hostinger_wp_manager.py:66:    return f"/home/{env['HOSTINGER_USER']}/domains/{env['DOMAIN']}/public_html"
runtime/scripts/hostinger_wp_manager.py:101:    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")
runtime/scripts/hostinger_wp_manager.py:240:    parser = argparse.ArgumentParser(description="Dream.OS Hostinger WordPress manager")
runtime/scripts/hostinger_wp_manager.py:265:            "HOSTINGER_HOST",
runtime/scripts/hostinger_wp_manager.py:266:            "HOSTINGER_USER",
runtime/scripts/hostinger_wp_manager.py:267:            "HOSTINGER_PORT",
runtime/scripts/hostinger_wp_manager.py:268:            "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/export_crosby_event_inquiries.sh:7:REMOTE_ROOT="/home/u996867598/domains/$DOMAIN/public_html"
runtime/scripts/export_crosby_event_inquiries.sh:24:SSH_HOST="${HOSTINGER_HOST:?missing HOSTINGER_HOST}"
runtime/scripts/export_crosby_event_inquiries.sh:25:SSH_USER="${HOSTINGER_USER:?missing HOSTINGER_USER}"
runtime/scripts/export_crosby_event_inquiries.sh:26:SSH_PORT="${HOSTINGER_PORT:-65002}"
runtime/scripts/export_crosby_event_inquiries.sh:27:SSH_KEY="${HOSTINGER_SSH_PRIVATE_KEY_FILE:-}"
runtime/scripts/hostinger_access_preflight.sh:14:  echo "NO_HOSTINGER_SITE_ENVS_FOUND"
runtime/scripts/hostinger_access_preflight.sh:26:  HOSTINGER_USER=""
runtime/scripts/hostinger_access_preflight.sh:27:  HOSTINGER_HOST=""
runtime/scripts/hostinger_access_preflight.sh:28:  HOSTINGER_PORT=""
runtime/scripts/hostinger_access_preflight.sh:29:  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
runtime/scripts/hostinger_access_preflight.sh:31:  HOSTINGER_WP_ROOT=""
runtime/scripts/hostinger_access_preflight.sh:38:  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
runtime/scripts/hostinger_access_preflight.sh:40:  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"
runtime/scripts/hostinger_access_preflight.sh:42:  if [ -z "${HOSTINGER_USER:-}" ] || [ -z "${HOSTINGER_HOST:-}" ] || [ -z "${HOSTINGER_PORT:-}" ] || [ -z "${HOSTINGER_SSH_PRIVATE_KEY_FILE:-}" ]; then
runtime/scripts/hostinger_access_preflight.sh:58:  if ssh -n -o BatchMode=yes -o ConnectTimeout=15 -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "test -d '$remote_root' && test -w '$remote_root' && echo SSH_ROOT_WRITABLE=PASS"; then
runtime/scripts/hostinger_access_preflight.sh:68:  echo "HOSTINGER_ACCESS_PREFLIGHT=PASS"
runtime/scripts/hostinger_access_preflight.sh:71:  echo "HOSTINGER_ACCESS_PREFLIGHT=FAIL"
runtime/scripts/audit_hostinger_website_inventory.sh:97:  HOSTINGER_USER=""
runtime/scripts/audit_hostinger_website_inventory.sh:98:  HOSTINGER_HOST=""
runtime/scripts/audit_hostinger_website_inventory.sh:99:  HOSTINGER_PORT=""
runtime/scripts/audit_hostinger_website_inventory.sh:100:  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
runtime/scripts/audit_hostinger_website_inventory.sh:102:  HOSTINGER_WP_ROOT=""
runtime/scripts/audit_hostinger_website_inventory.sh:108:  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
runtime/scripts/audit_hostinger_website_inventory.sh:110:  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"
runtime/scripts/audit_hostinger_website_inventory.sh:121:  if [ -n "${HOSTINGER_USER:-}" ] && [ -n "${HOSTINGER_HOST:-}" ] && [ -n "${HOSTINGER_PORT:-}" ] && [ -f "$key_file" ]; then
runtime/scripts/audit_hostinger_website_inventory.sh:122:    if ssh -n -o BatchMode=yes -o ConnectTimeout=15 -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "bash -s" > "$remote_report" 2>"$remote_report.err" << REMOTE
runtime/scripts/audit_hostinger_website_inventory.sh:217:      echo "- Homepage HTTPS is not clean 200. Investigate DNS, SSL, permissions, app routing, or Hostinger config."
runtime/scripts/classify_http_500_website_root_causes.sh:51:  HOSTINGER_USER=""
runtime/scripts/classify_http_500_website_root_causes.sh:52:  HOSTINGER_HOST=""
runtime/scripts/classify_http_500_website_root_causes.sh:53:  HOSTINGER_PORT=""
runtime/scripts/classify_http_500_website_root_causes.sh:54:  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
runtime/scripts/classify_http_500_website_root_causes.sh:56:  HOSTINGER_WP_ROOT=""
runtime/scripts/classify_http_500_website_root_causes.sh:62:  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
runtime/scripts/classify_http_500_website_root_causes.sh:64:  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"
runtime/scripts/classify_http_500_website_root_causes.sh:162:  scp -q -o LogLevel=ERROR -i "$key_file" -P "$HOSTINGER_PORT" "$remote_script_local" "$HOSTINGER_USER@$HOSTINGER_HOST:$remote_script"
runtime/scripts/classify_http_500_website_root_causes.sh:164:  ssh -n -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "bash '$remote_script' '$remote_root' '$domain'; rm -f '$remote_script'" > "$remote_capture"
runtime/scripts/classify_http_500_website_root_causes.sh:185:    root_cause = "missing_public_html_root"
runtime/scripts/classify_http_500_website_root_causes.sh:236:if root_cause in {"missing_index_file", "missing_public_html_root"}:
runtime/scripts/hostinger_deploy_target_guard.py:42:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_deploy_target_guard.py:44:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_deploy_target_guard.py:49:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
runtime/scripts/hostinger_deploy_target_guard.py:79:  root="$d/public_html"
runtime/scripts/hostinger_deploy_target_guard.py:162:    parser = argparse.ArgumentParser(description="Hostinger deploy target guard")
runtime/scripts/hostinger_deploy_target_guard.py:171:        "HOSTINGER_HOST",
runtime/scripts/hostinger_deploy_target_guard.py:172:        "HOSTINGER_USER",
runtime/scripts/hostinger_deploy_target_guard.py:173:        "HOSTINGER_PORT",
runtime/scripts/hostinger_deploy_target_guard.py:174:        "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/hostinger_static_deploy_guarded.py:36:def run_unified_fallback(domain: str, dry_run: bool, reason: str) -> None:
runtime/scripts/hostinger_static_deploy_guarded.py:37:    if os.environ.get("HOSTINGER_STATIC_DEPLOY_DISABLE_UNIFIED_FALLBACK") == "1":
runtime/scripts/hostinger_static_deploy_guarded.py:42:        "ops/deployment/unified_deployer.py",
runtime/scripts/hostinger_static_deploy_guarded.py:63:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_static_deploy_guarded.py:65:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_static_deploy_guarded.py:70:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
runtime/scripts/hostinger_static_deploy_guarded.py:80:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_static_deploy_guarded.py:82:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_static_deploy_guarded.py:88:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}:{remote_path}",
runtime/scripts/hostinger_static_deploy_guarded.py:109:    return f"/home/{user}/domains/{domain}/public_html"
runtime/scripts/hostinger_static_deploy_guarded.py:132:    parser = argparse.ArgumentParser(description="Guarded static Hostinger deploy")
runtime/scripts/hostinger_static_deploy_guarded.py:144:        run_unified_fallback(args.domain, args.dry_run, fallback.reason)
runtime/scripts/hostinger_static_deploy_guarded.py:147:        "HOSTINGER_HOST",
runtime/scripts/hostinger_static_deploy_guarded.py:148:        "HOSTINGER_USER",
runtime/scripts/hostinger_static_deploy_guarded.py:149:        "HOSTINGER_PORT",
runtime/scripts/hostinger_static_deploy_guarded.py:150:        "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/hostinger_static_deploy_guarded.py:154:            run_unified_fallback(args.domain, args.dry_run, f"MISSING_ENV={key}")
runtime/scripts/hostinger_static_deploy_guarded.py:156:    key_file = Path(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"].replace("$HOME", str(Path.home())))
runtime/scripts/hostinger_static_deploy_guarded.py:158:        run_unified_fallback(args.domain, args.dry_run, f"SSH_KEY_MISSING={key_file}")
runtime/scripts/hostinger_static_deploy_guarded.py:173:    remote_root = remote_root_for(args.domain, env["HOSTINGER_USER"])
runtime/scripts/hostinger_static_deploy_guarded.py:202:    print("GUARDED_STATIC_HOSTINGER_DEPLOY=PASS")
runtime/scripts/dreamos_site_deployer.py:5:One Dream.OS website deployer for Hostinger-style sites.
runtime/scripts/dreamos_site_deployer.py:9:- static: upload HTML to public_html/<remote-rel>
runtime/scripts/dreamos_site_deployer.py:13:- HOSTINGER_SSH_PRIVATE_KEY_FILE
runtime/scripts/dreamos_site_deployer.py:14:- HOSTINGER_USER
runtime/scripts/dreamos_site_deployer.py:15:- HOSTINGER_HOST
runtime/scripts/dreamos_site_deployer.py:16:- HOSTINGER_PORT
runtime/scripts/dreamos_site_deployer.py:19:- HOSTINGER_WP_ROOT
runtime/scripts/dreamos_site_deployer.py:35:    "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/dreamos_site_deployer.py:36:    "HOSTINGER_USER",
runtime/scripts/dreamos_site_deployer.py:37:    "HOSTINGER_HOST",
runtime/scripts/dreamos_site_deployer.py:38:    "HOSTINGER_PORT",
runtime/scripts/dreamos_site_deployer.py:84:        "-i", expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
runtime/scripts/dreamos_site_deployer.py:85:        "-p", env["HOSTINGER_PORT"],
runtime/scripts/dreamos_site_deployer.py:86:        f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}',
runtime/scripts/dreamos_site_deployer.py:94:        "-P", env["HOSTINGER_PORT"],
runtime/scripts/dreamos_site_deployer.py:95:        "-i", expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
runtime/scripts/dreamos_site_deployer.py:100:    if env.get("HOSTINGER_WP_ROOT"):
runtime/scripts/dreamos_site_deployer.py:101:        root = env["HOSTINGER_WP_ROOT"]
runtime/scripts/dreamos_site_deployer.py:105:    return f"/home/{env['HOSTINGER_USER']}/domains/{domain}/public_html"
runtime/scripts/dreamos_site_deployer.py:165:    run(scp_base(env) + [str(source), f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}:{remote_file}'])
runtime/scripts/dreamos_site_deployer.py:266:    ap = argparse.ArgumentParser(description="Dream.OS streamlined Hostinger website deployer")
runtime/scripts/dreamos_site_deployer.py:290:    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")
runtime/scripts/build_weareswarm_project_board_001.py:328:    out = root / "runtime" / "content" / "weareswarm.site" / "data" / "project-board.generated.json"
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:50:        expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:52:        env["HOSTINGER_PORT"],
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:57:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:69:        expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:71:        env["HOSTINGER_PORT"],
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:77:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}:{remote_path}",
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:83:    for key in ["HOSTINGER_HOST", "HOSTINGER_USER", "HOSTINGER_PORT", "HOSTINGER_SSH_PRIVATE_KEY_FILE"]:
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:89:    configured = env.get("HOSTINGER_WP_ROOT", "")
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:92:    return f"/home/{env['HOSTINGER_USER']}/domains/{domain}/public_html"
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:124:    backup_dir = f"/home/{env['HOSTINGER_USER']}/domains/{domain}/dreamos_backups/static_fallback_{stamp}"
runtime/scripts/repair_crosbyultimateevents_static_fallback.py:247:        print(f"DRY_RUN=PASS backup=/home/{env['HOSTINGER_USER']}/domains/{args.domain}/dreamos_backups/static_fallback_{stamp}")
runtime/scripts/sync_weareswarm_planner_status_001.py:149:    out = root / "runtime" / "content" / "weareswarm.site" / "data" / "swarm-status.generated.json"
runtime/deploy/hostinger_sites_manifest.yaml:3:purpose: Dream.OS Hostinger WordPress deployment control surface
runtime/deploy/hostinger_sites_manifest.yaml:5:  host_secret: HOSTINGER_HOST
runtime/deploy/hostinger_sites_manifest.yaml:6:  user_secret: HOSTINGER_USER
runtime/deploy/hostinger_sites_manifest.yaml:7:  port_secret: HOSTINGER_PORT
runtime/deploy/hostinger_sites_manifest.yaml:8:  private_key_secret: HOSTINGER_SSH_PRIVATE_KEY
runtime/deploy/hostinger_sites_manifest.yaml:20:    wp_root: '/home/u996867598/domains/ariajet.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:21:    plugins_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:22:    themes_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:154:    wp_root: '/home/u996867598/domains/crosbyultimateevents.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:155:    plugins_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:156:    themes_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:218:    wp_root: '/home/u996867598/domains/dadudekc.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:219:    plugins_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:220:    themes_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:297:    wp_root: '/home/u996867598/domains/maskzero.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:298:    plugins_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:299:    themes_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:327:        theme_name: 'Hostinger AI theme'
runtime/deploy/hostinger_sites_manifest.yaml:351:    wp_root: '/home/u996867598/domains/digitaldreamscape.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:352:    plugins_dir: '/home/u996867598/domains/digitaldreamscape.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:353:    themes_dir: '/home/u996867598/domains/digitaldreamscape.site/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:395:    wp_root: '/home/u996867598/domains/freerideinvestor.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:396:    plugins_dir: '/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:397:    themes_dir: '/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:489:    wp_root: '/home/u996867598/domains/houstonsipqueen.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:490:    plugins_dir: '/home/u996867598/domains/houstonsipqueen.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:491:    themes_dir: '/home/u996867598/domains/houstonsipqueen.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:548:    wp_root: '/home/u996867598/domains/prismblossom.online/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:549:    plugins_dir: '/home/u996867598/domains/prismblossom.online/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:550:    themes_dir: '/home/u996867598/domains/prismblossom.online/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:652:    wp_root: '/home/u996867598/domains/southwestsecret.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:653:    plugins_dir: '/home/u996867598/domains/southwestsecret.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:654:    themes_dir: '/home/u996867598/domains/southwestsecret.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:746:    wp_root: '/home/u996867598/domains/tradingrobotplug.com/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:747:    plugins_dir: '/home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:748:    themes_dir: '/home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:832:    domain: 'weareswarm.online'
runtime/deploy/hostinger_sites_manifest.yaml:835:    wp_root: '/home/u996867598/domains/weareswarm.online/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:836:    plugins_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:837:    themes_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:926:    domain: 'weareswarm.site'
runtime/deploy/hostinger_sites_manifest.yaml:929:    wp_root: '/home/u996867598/domains/weareswarm.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:930:    plugins_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:931:    themes_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:983:    wp_root: '/home/u996867598/domains/xthunder.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:984:    plugins_dir: '/home/u996867598/domains/xthunder.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:985:    themes_dir: '/home/u996867598/domains/xthunder.site/public_html/wp-content/themes'
runtime/deploy/hostinger_sites_manifest.yaml:1018:        theme_name: 'Hostinger AI theme'
runtime/deploy/hostinger_plugin_registry.yaml:24:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:36:      - weareswarm.site
runtime/deploy/hostinger_plugin_registry.yaml:62:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:84:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:85:      - weareswarm.site
runtime/deploy/hostinger_plugin_registry.yaml:102:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:103:      - weareswarm.site
runtime/deploy/hostinger_plugin_registry.yaml:120:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:121:      - weareswarm.site
runtime/deploy/hostinger_plugin_registry.yaml:149:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:164:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:205:      - weareswarm.online
runtime/deploy/hostinger_plugin_registry.yaml:226:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:60:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:193:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:200:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:227:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:228:      - weareswarm.site
runtime/deploy/hostinger_theme_registry.yaml:244:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:245:      - weareswarm.site
runtime/deploy/hostinger_theme_registry.yaml:261:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:262:      - weareswarm.site
runtime/deploy/hostinger_theme_registry.yaml:269:      - weareswarm.online
runtime/deploy/hostinger_theme_registry.yaml:275:      - weareswarm.site
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:3:purpose: salvage custom Hostinger WordPress plugins/themes before Dream.OS redeploy workflows
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:11:    remote_path: /home/u996867598/domains/ariajet.site/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:20:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/dreamos-trading-tools
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:29:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-content-engine
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:38:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-setup
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:47:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/trading-plans-automator
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:56:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:65:    remote_path: /home/u996867598/domains/southwestsecret.com/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:74:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:83:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/plugins/trp-paper-trading-stats
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:92:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/plugins/trp-swarm-status
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:99:    domain: weareswarm.site
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:101:    remote_path: /home/u996867598/domains/weareswarm.site/public_html/wp-content/plugins/dreamos-swarm-status
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:102:    local_path: collected/hostinger/wordpress/domains/weareswarm.site/plugins/dreamos-swarm-status
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:110:    remote_path: /home/u996867598/domains/dadudekc.com/public_html/wp-content/themes/dadudekc
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:119:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:128:    remote_path: /home/u996867598/domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-v2
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:137:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/themes/swarm-theme
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:146:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/themes/tradingrobot-theme
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:155:    remote_path: /home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/themes/tradingrobotplug-theme
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:162:    domain: weareswarm.online
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:164:    remote_path: /home/u996867598/domains/weareswarm.online/public_html/wp-content/themes/swarm
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:165:    local_path: collected/hostinger/wordpress/domains/weareswarm.online/themes/swarm
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:171:    domain: weareswarm.online
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:173:    remote_path: /home/u996867598/domains/weareswarm.online/public_html/wp-content/themes/swarm-theme
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:174:    local_path: collected/hostinger/wordpress/domains/weareswarm.online/themes/swarm-theme
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:180:    domain: weareswarm.online
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:182:    remote_path: /home/u996867598/domains/weareswarm.online/public_html/wp-content/themes/weareswarm
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:183:    local_path: collected/hostinger/wordpress/domains/weareswarm.online/themes/weareswarm
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:189:    domain: weareswarm.site
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:191:    remote_path: /home/u996867598/domains/weareswarm.site/public_html/wp-content/themes/weareswarm-dreamos
runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:192:    local_path: collected/hostinger/wordpress/domains/weareswarm.site/themes/weareswarm-dreamos
runtime/deploy/theme_redeployment_policy.yaml:9:      Collected Hostinger themes came from earlier website structure experiments.
runtime/deploy/hostinger_connected_sites.yaml:8:    wp_root: '/home/u996867598/domains/ariajet.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:9:    plugins_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:10:    themes_dir: '/home/u996867598/domains/ariajet.site/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:11:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:21:    wp_root: '/home/u996867598/domains/crosbyultimateevents.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:22:    plugins_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:23:    themes_dir: '/home/u996867598/domains/crosbyultimateevents.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:24:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:34:    wp_root: '/home/u996867598/domains/dadudekc.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:35:    plugins_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:36:    themes_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:37:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:47:    wp_root: '/home/u996867598/domains/maskzero.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:48:    plugins_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:49:    themes_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:50:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:60:    wp_root: '/home/u996867598/domains/digitaldreamscape.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:61:    plugins_dir: '/home/u996867598/domains/digitaldreamscape.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:62:    themes_dir: '/home/u996867598/domains/digitaldreamscape.site/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:63:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:73:    wp_root: '/home/u996867598/domains/freerideinvestor.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:74:    plugins_dir: '/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:75:    themes_dir: '/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:76:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:86:    wp_root: '/home/u996867598/domains/houstonsipqueen.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:87:    plugins_dir: '/home/u996867598/domains/houstonsipqueen.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:88:    themes_dir: '/home/u996867598/domains/houstonsipqueen.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:89:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:99:    wp_root: '/home/u996867598/domains/prismblossom.online/public_html'
runtime/deploy/hostinger_connected_sites.yaml:100:    plugins_dir: '/home/u996867598/domains/prismblossom.online/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:101:    themes_dir: '/home/u996867598/domains/prismblossom.online/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:102:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:112:    wp_root: '/home/u996867598/domains/southwestsecret.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:113:    plugins_dir: '/home/u996867598/domains/southwestsecret.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:114:    themes_dir: '/home/u996867598/domains/southwestsecret.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:115:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:125:    wp_root: '/home/u996867598/domains/tradingrobotplug.com/public_html'
runtime/deploy/hostinger_connected_sites.yaml:126:    plugins_dir: '/home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:127:    themes_dir: '/home/u996867598/domains/tradingrobotplug.com/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:128:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:136:    domain: 'weareswarm.online'
runtime/deploy/hostinger_connected_sites.yaml:137:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/weareswarm.online.env'
runtime/deploy/hostinger_connected_sites.yaml:138:    wp_root: '/home/u996867598/domains/weareswarm.online/public_html'
runtime/deploy/hostinger_connected_sites.yaml:139:    plugins_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:140:    themes_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:141:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:149:    domain: 'weareswarm.site'
runtime/deploy/hostinger_connected_sites.yaml:150:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/weareswarm.site.env'
runtime/deploy/hostinger_connected_sites.yaml:151:    wp_root: '/home/u996867598/domains/weareswarm.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:152:    plugins_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:153:    themes_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:154:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_connected_sites.yaml:164:    wp_root: '/home/u996867598/domains/xthunder.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:165:    plugins_dir: '/home/u996867598/domains/xthunder.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:166:    themes_dir: '/home/u996867598/domains/xthunder.site/public_html/wp-content/themes'
runtime/deploy/hostinger_connected_sites.yaml:167:    public_html: 'PUBLIC_HTML=PASS'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:110:    domain: 'weareswarm.online'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:111:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/weareswarm.online.env'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:120:    domain: 'weareswarm.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:121:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/weareswarm.site.env'
```

## Decision Needed

- Identify the deploy script that consumes runtime/env/hostinger/sites/weareswarm.online.env.
- Identify whether _deploy/weareswarm.online is generated output or canonical static source.
- Roadmap patch only happens after that.
