# Deploy Emergence Spark loop live verification

Generated: 2026-06-05T01:24:33-05:00

## Deploy status

```text
DEPLOY_STATUS=FAIL
LIVE_STATUS=FAIL
```

## Deploy tooling

```text
ops/deployment/simple_wordpress_deployer.py
ops/deployment/unified_deployer.py
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh
runtime/scripts/dreamos_site_deployer.py
runtime/scripts/hostinger_deploy_target_guard.py
runtime/scripts/hostinger_static_deploy_guarded.py
runtime/scripts/validate_website_deploy_modes.py

ops/deployment/simple_wordpress_deployer.py:42:    host = os.getenv("HOSTINGER_HOST")
ops/deployment/simple_wordpress_deployer.py:43:    username = os.getenv("HOSTINGER_USER")
ops/deployment/simple_wordpress_deployer.py:44:    password = os.getenv("HOSTINGER_PASS")
ops/deployment/simple_wordpress_deployer.py:45:    port = int(os.getenv("HOSTINGER_PORT", "65002"))
ops/deployment/simple_wordpress_deployer.py:166:        host = os.getenv("HOSTINGER_HOST")
ops/deployment/simple_wordpress_deployer.py:167:        username = os.getenv("HOSTINGER_USER")
ops/deployment/simple_wordpress_deployer.py:168:        password = os.getenv("HOSTINGER_PASS")
ops/deployment/simple_wordpress_deployer.py:169:        port = int(os.getenv("HOSTINGER_PORT", "65002"))
ops/deployment/simple_wordpress_deployer.py:200:            print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
ops/deployment/simple_wordpress_deployer.py:201:            print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
ops/deployment/simple_wordpress_deployer.py:202:            print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
ops/deployment/simple_wordpress_deployer.py:203:            print(f"      - HOSTINGER_PORT: {port if port else '❌ Missing (default: 65002)'}")
ops/deployment/simple_wordpress_deployer.py:217:            print("   💡 Solution: Set HOSTINGER_* environment variables in .env file or add credentials to site config")
ops/deployment/simple_wordpress_deployer.py:355:            host = os.getenv("HOSTINGER_HOST")
ops/deployment/simple_wordpress_deployer.py:356:            username = os.getenv("HOSTINGER_USER")
ops/deployment/simple_wordpress_deployer.py:357:            password = os.getenv("HOSTINGER_PASS")
ops/deployment/simple_wordpress_deployer.py:358:            port = int(os.getenv("HOSTINGER_PORT", "65002"))  # Hostinger uses 65002
ops/deployment/simple_wordpress_deployer.py:377:                print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
ops/deployment/simple_wordpress_deployer.py:378:                print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
ops/deployment/simple_wordpress_deployer.py:379:                print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
ops/deployment/sites.yml:109:  dadudekc.site:
ops/deployment/sites.yml:110:    path: websites/dadudekc.site
ops/deployment/sites.yml:112:    verify_url: https://dadudekc.site/.well-known/deploy.json
ops/deployment/sites.yml:129:    notes: "Static global-chat game landing; SFTP via unified_deployer"
ops/deployment/unified_deployer.py:10:    python ops/deployment/unified_deployer.py --all              # Deploy all sites
ops/deployment/unified_deployer.py:11:    python ops/deployment/unified_deployer.py --site <domain>     # Deploy single site
ops/deployment/unified_deployer.py:12:    python ops/deployment/unified_deployer.py --all --dry-run    # Test without deploying
ops/deployment/unified_deployer.py:28:    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
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
runtime/scripts/collect_hostinger_custom_assets_045.sh:91:  ssh -n -i "$KEY_FILE" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" \
runtime/scripts/collect_hostinger_custom_assets_045.sh:204:echo "HOSTINGER_CUSTOM_ASSET_COLLECTION=PASS"
runtime/scripts/hostinger_wp_manager.py:49:    key_file = expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"])
runtime/scripts/hostinger_wp_manager.py:57:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_wp_manager.py:58:        f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}',
runtime/scripts/hostinger_wp_manager.py:63:    if env.get("HOSTINGER_WP_ROOT"):
runtime/scripts/hostinger_wp_manager.py:64:        return env["HOSTINGER_WP_ROOT"]
runtime/scripts/hostinger_wp_manager.py:66:    return f"/home/{env['HOSTINGER_USER']}/domains/{env['DOMAIN']}/public_html"
runtime/scripts/hostinger_wp_manager.py:101:    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")
runtime/scripts/hostinger_wp_manager.py:265:            "HOSTINGER_HOST",
runtime/scripts/hostinger_wp_manager.py:266:            "HOSTINGER_USER",
runtime/scripts/hostinger_wp_manager.py:267:            "HOSTINGER_PORT",
runtime/scripts/hostinger_wp_manager.py:268:            "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/smoke_emergence_character_generator_public_path.py:11:SITE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_character_generator_public_path.py:12:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_character_generator_public_path.py:13:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_flavor_unlock_routing.py:10:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_flavor_unlock_routing.py:11:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_flavor_unlock_routing.py:12:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_character_profile_display.py:7:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_character_profile_display.py:8:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_character_profile_display.py:9:CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
runtime/scripts/smoke_emergence_character_profile_display.py:10:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_generated_spark_portrait_card.py:7:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_generated_spark_portrait_card.py:8:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_generated_spark_portrait_card.py:9:CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
runtime/scripts/smoke_emergence_generated_spark_portrait_card.py:10:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_totality_observation_portrait_prompt.py:7:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_totality_observation_portrait_prompt.py:8:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_totality_observation_portrait_prompt.py:9:CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
runtime/scripts/smoke_emergence_totality_observation_portrait_prompt.py:10:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_premium_portrait_prompt.py:7:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_premium_portrait_prompt.py:8:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_premium_portrait_prompt.py:9:CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
runtime/scripts/smoke_emergence_premium_portrait_prompt.py:10:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
runtime/scripts/smoke_emergence_premium_hero_image_provider.py:8:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_premium_hero_image_provider.py:9:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_premium_hero_image_provider.py:10:CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
runtime/scripts/smoke_emergence_premium_hero_image_provider.py:11:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/portrait"
runtime/scripts/smoke_emergence_openai_premium_image_provider.py:9:PAGE_URL = "https://dadudekc.site/character-generator/"
runtime/scripts/smoke_emergence_openai_premium_image_provider.py:10:JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
runtime/scripts/smoke_emergence_openai_premium_image_provider.py:11:REST_URL = "https://dadudekc.site/wp-json/emergence/v1/portrait"
runtime/scripts/smoke_emergence_full_handoff_browser.py:9:CHARACTER_URL = "https://dadudekc.site/character-generator/?dreamos_smoke=100"
runtime/scripts/smoke_emergence_full_handoff_browser.py:10:BATTLE_URL = "https://dadudekc.site/battles/?spark_handoff=1&dreamos_smoke=100"
runtime/scripts/smoke_emergence_full_handoff_browser.py:11:CUSTOM_BATTLE_URL = "https://dadudekc.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=100"
runtime/scripts/smoke_emergence_token_handoff_browser.py:10:CREATE_TOKEN_URL = "https://dadudekc.site/wp-json/emergence/v1/spark-token?dreamos_smoke=102"
runtime/scripts/smoke_emergence_token_handoff_browser.py:11:LOAD_TOKEN_BASE = "https://dadudekc.site/wp-json/emergence/v1/spark-token/"
runtime/scripts/smoke_emergence_token_handoff_browser.py:12:CUSTOM_BATTLE_URL = "https://dadudekc.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=102"
runtime/scripts/smoke_emergence_token_handoff_browser.py:13:BATTLE_PAGE_BASE = "https://dadudekc.site/battles/?dreamos_smoke=102&spark_token="
runtime/scripts/smoke_emergence_saved_character_browser.py:10:SAVE_RECORD_URL = "https://dadudekc.site/wp-json/emergence/v1/characters?dreamos_smoke=104"
runtime/scripts/smoke_emergence_saved_character_browser.py:11:LOAD_RECORD_BASE = "https://dadudekc.site/wp-json/emergence/v1/characters/"
runtime/scripts/smoke_emergence_saved_character_browser.py:12:CUSTOM_BATTLE_URL = "https://dadudekc.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=104"
runtime/scripts/smoke_emergence_saved_character_browser.py:13:CHARACTER_PAGE_BASE = "https://dadudekc.site/character-generator/?dreamos_smoke=104&character_record="
runtime/scripts/smoke_emergence_saved_character_browser.py:14:BATTLE_PAGE_BASE = "https://dadudekc.site/battles/?dreamos_smoke=104&character_record="
runtime/scripts/smoke_emergence_scan_no_reload_browser.mjs:22:const URL = 'https://dadudekc.site/character-generator/?dreamos_smoke=112';
runtime/scripts/report_emergence_conversion_funnel.py:15:SUMMARY_URL = "https://dadudekc.site/wp-json/emergence/v1/events/summary?dreamos_smoke=114"
runtime/scripts/hostinger_access_preflight.sh:7:echo "== HOSTINGER ACCESS PREFLIGHT =="
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
runtime/scripts/audit_hostinger_website_inventory.sh:133:if [ -f wp-config.php ] || [ -d wp-content ]; then echo "WORDPRESS=YES"; else echo "WORDPRESS=NO"; fi
runtime/scripts/audit_hostinger_website_inventory.sh:148:      wordpress="$(grep -E '^WORDPRESS=' "$remote_report" | cut -d= -f2 | tr '[:upper:]' '[:lower:]' || echo no)"
runtime/scripts/classify_http_500_website_root_causes.sh:51:  HOSTINGER_USER=""
runtime/scripts/classify_http_500_website_root_causes.sh:52:  HOSTINGER_HOST=""
runtime/scripts/classify_http_500_website_root_causes.sh:53:  HOSTINGER_PORT=""
runtime/scripts/classify_http_500_website_root_causes.sh:54:  HOSTINGER_SSH_PRIVATE_KEY_FILE=""
runtime/scripts/classify_http_500_website_root_causes.sh:56:  HOSTINGER_WP_ROOT=""
runtime/scripts/classify_http_500_website_root_causes.sh:62:  key_file="${HOSTINGER_SSH_PRIVATE_KEY_FILE/#\$HOME/$HOME}"
runtime/scripts/classify_http_500_website_root_causes.sh:64:  remote_root="${HOSTINGER_WP_ROOT:-/home/$HOSTINGER_USER/domains/$domain/public_html}"
runtime/scripts/classify_http_500_website_root_causes.sh:119:echo "== WORDPRESS_MARKERS =="
runtime/scripts/classify_http_500_website_root_causes.sh:162:  scp -q -o LogLevel=ERROR -i "$key_file" -P "$HOSTINGER_PORT" "$remote_script_local" "$HOSTINGER_USER@$HOSTINGER_HOST:$remote_script"
runtime/scripts/classify_http_500_website_root_causes.sh:164:  ssh -n -o LogLevel=ERROR -i "$key_file" -p "$HOSTINGER_PORT" "$HOSTINGER_USER@$HOSTINGER_HOST" "bash '$remote_script' '$remote_root' '$domain'; rm -f '$remote_script'" > "$remote_capture"
runtime/scripts/hostinger_deploy_target_guard.py:42:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_deploy_target_guard.py:44:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_deploy_target_guard.py:49:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
runtime/scripts/hostinger_deploy_target_guard.py:171:        "HOSTINGER_HOST",
runtime/scripts/hostinger_deploy_target_guard.py:172:        "HOSTINGER_USER",
runtime/scripts/hostinger_deploy_target_guard.py:173:        "HOSTINGER_PORT",
runtime/scripts/hostinger_deploy_target_guard.py:174:        "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/hostinger_static_deploy_guarded.py:30:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_static_deploy_guarded.py:32:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_static_deploy_guarded.py:37:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
runtime/scripts/hostinger_static_deploy_guarded.py:47:        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
runtime/scripts/hostinger_static_deploy_guarded.py:49:        env["HOSTINGER_PORT"],
runtime/scripts/hostinger_static_deploy_guarded.py:55:        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}:{remote_path}",
runtime/scripts/hostinger_static_deploy_guarded.py:110:        "HOSTINGER_HOST",
runtime/scripts/hostinger_static_deploy_guarded.py:111:        "HOSTINGER_USER",
runtime/scripts/hostinger_static_deploy_guarded.py:112:        "HOSTINGER_PORT",
runtime/scripts/hostinger_static_deploy_guarded.py:113:        "HOSTINGER_SSH_PRIVATE_KEY_FILE",
runtime/scripts/hostinger_static_deploy_guarded.py:132:    remote_root = remote_root_for(args.domain, env["HOSTINGER_USER"])
runtime/scripts/hostinger_static_deploy_guarded.py:161:    print("GUARDED_STATIC_HOSTINGER_DEPLOY=PASS")
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
runtime/scripts/dreamos_site_deployer.py:290:    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")
runtime/scripts/dreamos_site_deployer.py:293:    print(f"WORDPRESS_DETECTED={'PASS' if wp_present else 'NO'}")
runtime/deploy/hostinger_sites_manifest.yaml:5:  host_secret: HOSTINGER_HOST
runtime/deploy/hostinger_sites_manifest.yaml:6:  user_secret: HOSTINGER_USER
runtime/deploy/hostinger_sites_manifest.yaml:7:  port_secret: HOSTINGER_PORT
runtime/deploy/hostinger_sites_manifest.yaml:8:  private_key_secret: HOSTINGER_SSH_PRIVATE_KEY
runtime/deploy/hostinger_sites_manifest.yaml:293:  dadudekc_site:
runtime/deploy/hostinger_sites_manifest.yaml:294:    domain: 'dadudekc.site'
runtime/deploy/hostinger_sites_manifest.yaml:297:    wp_root: '/home/u996867598/domains/dadudekc.site/public_html'
runtime/deploy/hostinger_sites_manifest.yaml:298:    plugins_dir: '/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_sites_manifest.yaml:299:    themes_dir: '/home/u996867598/domains/dadudekc.site/public_html/wp-content/themes'
runtime/deploy/hostinger_plugin_registry.yaml:77:      - dadudekc.site
runtime/deploy/hostinger_plugin_registry.yaml:95:      - dadudekc.site
runtime/deploy/hostinger_plugin_registry.yaml:113:      - dadudekc.site
runtime/deploy/hostinger_plugin_registry.yaml:143:      - dadudekc.site
runtime/deploy/hostinger_theme_registry.yaml:126:      - dadudekc.site
runtime/deploy/hostinger_theme_registry.yaml:221:      - dadudekc.site
runtime/deploy/hostinger_theme_registry.yaml:238:      - dadudekc.site
runtime/deploy/hostinger_theme_registry.yaml:255:      - dadudekc.site
runtime/deploy/hostinger_connected_sites.yaml:44:  dadudekc_site:
runtime/deploy/hostinger_connected_sites.yaml:45:    domain: 'dadudekc.site'
runtime/deploy/hostinger_connected_sites.yaml:46:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.site.env'
runtime/deploy/hostinger_connected_sites.yaml:47:    wp_root: '/home/u996867598/domains/dadudekc.site/public_html'
runtime/deploy/hostinger_connected_sites.yaml:48:    plugins_dir: '/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins'
runtime/deploy/hostinger_connected_sites.yaml:49:    themes_dir: '/home/u996867598/domains/dadudekc.site/public_html/wp-content/themes'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:39:  dadudekc_site:
runtime/deploy/hostinger_manager_smoke_matrix.yaml:40:    domain: 'dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:41:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.site.env'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:44:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:45:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:54:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:55:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:64:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:65:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:74:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:75:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:84:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:85:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:94:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:95:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:104:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:105:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:114:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:115:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:124:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:125:    home: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:134:    siteurl: 'https://dadudekc.site'
runtime/deploy/hostinger_manager_smoke_matrix.yaml:135:    home: 'https://dadudekc.site'
runtime/deploy/sites/dadudekc.site/spark_protocol_product_map.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml:2:name: dadudekc_site_client_theme_update_manifest
runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml:11:  wp_root: /home/u996867598/domains/dadudekc.site/public_html
runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml:12:  backup_dir: /home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/dadudekc_site_intake_20260531_061437
runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml:14:  manager_profile: runtime/env/hostinger/sites/dadudekc.site.env
runtime/deploy/sites/dadudekc.site/spark_preview_pages_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_public_site_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/wp_public_routing_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/wp_public_routing_manifest.yaml:5:backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/routing_fix_20260531_071847'
runtime/deploy/sites/dadudekc.site/emergence_character_generator_demo_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_character_generator_demo_manifest.yaml:8:  remote: /home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator
runtime/deploy/sites/dadudekc.site/emergence_character_generator_demo_manifest.yaml:13:  url: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_character_generator_demo_manifest.yaml:21:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/character_generator_demo_20260531_073205'
runtime/deploy/sites/dadudekc.site/emergence_homepage_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/spark_protocol_character_generation_port_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/spark_protocol_character_generation_port_manifest.yaml:20:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/spark_protocol_generation_port_20260531_074202'
runtime/deploy/sites/dadudekc.site/emergence_domain_pass_plugin_syntax_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_domain_pass_plugin_syntax_manifest.yaml:21:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/domain_pass_syntax_repair_20260531_082636'
runtime/deploy/sites/dadudekc.site/emergence_flavor_power_selection_pass_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_flavor_power_selection_pass_manifest.yaml:18:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/flavor_power_selection_20260531_082938'
runtime/deploy/sites/dadudekc.site/emergence_character_sheet_output_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_generator_answer_labels_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_generator_answer_labels_manifest.yaml:16:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/answer_labels_082b_20260531_084136'
runtime/deploy/sites/dadudekc.site/emergence_disguised_answer_labels_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_disguised_answer_labels_manifest.yaml:17:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/disguised_answer_labels_20260531_085000'
runtime/deploy/sites/dadudekc.site/emergence_protocol_v85_question_bank_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_protocol_v85_question_bank_manifest.yaml:20:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/protocol_question_bank_084b_20260531_092917'
runtime/deploy/sites/dadudekc.site/emergence_flavor_phase_transition_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_flavor_phase_transition_manifest.yaml:18:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/flavor_phase_transition_085_20260531_101334'
runtime/deploy/sites/dadudekc.site/emergence_public_scoring_privacy_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_public_scoring_privacy_manifest.yaml:17:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/public_scoring_privacy_086_20260531_101501'
runtime/deploy/sites/dadudekc.site/emergence_cg_public_path_smoke_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_cg_public_path_smoke_manifest.yaml:6:target_url: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_flavor_privacy_unlock_routing_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_character_profile_display_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_generated_spark_portrait_card_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_generated_spark_portrait_card_manifest.yaml:20:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/generated_spark_portrait_090_20260531_110641'
runtime/deploy/sites/dadudekc.site/emergence_totality_observation_portrait_prompt_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_totality_observation_portrait_prompt_manifest.yaml:21:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/totality_observation_091_20260531_113235'
runtime/deploy/sites/dadudekc.site/emergence_premium_portrait_prompt_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_premium_portrait_prompt_manifest.yaml:34:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/premium_prompt_092_20260531_113806'
runtime/deploy/sites/dadudekc.site/emergence_premium_hero_image_provider_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_openai_premium_image_provider_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_openai_premium_image_provider_manifest.yaml:31:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/openai_provider_094d_20260531_115948'
runtime/deploy/sites/dadudekc.site/emergence_hostinger_image_env_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_openai_provider_error_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/spark_battle_sim_hostinger_install_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/spark_battle_sim_hostinger_install_manifest.yaml:6:url: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/spark_battle_sim_hostinger_install_manifest.yaml:17:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/spark_battle_sim_install_097_20260531_125112'
runtime/deploy/sites/dadudekc.site/emergence_character_battle_handoff_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_character_battle_handoff_manifest.yaml:20:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/character_battle_handoff_098e_20260531_130001'
runtime/deploy/sites/dadudekc.site/emergence_character_battle_handoff_manifest.yaml:22:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_character_battle_handoff_manifest.yaml:23:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/custom_spark_battle_participant_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/custom_spark_battle_participant_manifest.yaml:21:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/custom_spark_battle_participant_099_20260531_130155'
runtime/deploy/sites/dadudekc.site/custom_spark_battle_participant_manifest.yaml:23:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_full_handoff_browser_smoke_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_full_handoff_browser_smoke_manifest.yaml:29:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_full_handoff_browser_smoke_manifest.yaml:30:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/shareable_spark_handoff_token_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/shareable_spark_handoff_token_manifest.yaml:28:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/shareable_spark_token_101_20260531_130546'
runtime/deploy/sites/dadudekc.site/shareable_spark_handoff_token_manifest.yaml:30:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/shareable_spark_handoff_token_manifest.yaml:31:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_token_handoff_browser_smoke_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_token_handoff_browser_smoke_manifest.yaml:27:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_token_handoff_browser_smoke_manifest.yaml:28:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_saved_character_records_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_saved_character_records_manifest.yaml:29:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/saved_character_records_103_20260531_131233'
runtime/deploy/sites/dadudekc.site/emergence_saved_character_records_manifest.yaml:31:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_saved_character_records_manifest.yaml:32:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_saved_character_browser_smoke_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_saved_character_browser_smoke_manifest.yaml:30:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_saved_character_browser_smoke_manifest.yaml:31:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_public_share_card_ui_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_public_share_card_ui_manifest.yaml:27:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/public_share_card_ui_105_20260531_131901'
runtime/deploy/sites/dadudekc.site/emergence_public_share_card_ui_manifest.yaml:29:  character_generator: https://dadudekc.site/character-generator/
runtime/deploy/sites/dadudekc.site/emergence_public_share_card_ui_manifest.yaml:30:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_battle_story_cinematics_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_battle_story_cinematics_manifest.yaml:24:  remote_backup_dir: '/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/battle_story_cinematics_106b_20260531_132151'
runtime/deploy/sites/dadudekc.site/emergence_battle_story_cinematics_manifest.yaml:26:  battles: https://dadudekc.site/battles/
runtime/deploy/sites/dadudekc.site/emergence_premium_portrait_design_controls_manifest.yaml:3:site: dadudekc.site
runtime/deploy/sites/dadudekc.site/emergence_premium_portrait_design_controls_manifest.yaml:29:  character_generator: https://dadudekc.site/character-generator/
```

## Deployer help

```text
--- ops/deployment/unified_deployer.py --help ---
usage: unified_deployer.py [-h] [--all] [--site SITE] [--dry-run]

Unified Website Deployer

options:
  -h, --help   show this help message and exit
  --all        Deploy to all websites
  --site SITE  Deploy to specific site (domain name)
  --dry-run    Test without deploying

--- ops/deployment/simple_wordpress_deployer.py --help ---
```

## Deploy attempt

```text
usage: unified_deployer.py [-h] [--all] [--site SITE] [--dry-run]
unified_deployer.py: error: unrecognized arguments: dadudekc.site
```

## Live DOM summary

```text
--- https://dadudekc.site/ ---
48829 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/deploy_emergence_spark_loop_live_001/root.html
<title>dadudekc.site
Generate Your Spark
What-If Arena
What-If Arena

--- https://dadudekc.site/spark-generator/ ---
176537 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/deploy_emergence_spark_loop_live_001/_spark-generator_.html
<title>Spark Generator &#8211; dadudekc.site
Character Record
character record
Character Record

--- https://dadudekc.site/character-generator/ ---
176959 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/deploy_emergence_spark_loop_live_001/_character-generator_.html
<title>Character Generator &#8211; dadudekc.site
Character Record
character record
Character Record

--- https://dadudekc.site/battles/ ---
40941 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/deploy_emergence_spark_loop_live_001/_battles_.html
<title>Battles &#8211; dadudekc.site
character record
Character record
character record
cinematic story

--- https://dadudekc.site/the-emergence/ ---
48829 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/deploy_emergence_spark_loop_live_001/_the-emergence_.html
<title>dadudekc.site
Generate Your Spark
What-If Arena
What-If Arena
```

## Result

Repo-side Spark Protocol loop is patched. Live verification status: `FAIL`.
