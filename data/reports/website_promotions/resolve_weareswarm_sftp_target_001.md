# Resolve WeAreSwarm SFTP Target 001

- Generated: `2026-06-03T17:26:35`
- Status: `TARGET_REVIEW_READY`
- Artifact: `_deploy/weareswarm/dreamos-services/index.html`
- Desired route: `/dreamos-services/index.html`
- Match count: `303`
- Domain candidate count: `45`

## Guardrail

Do not upload to public root index.html. Create/use dreamos-services directory only.

## Recommendation

Review matched domain/config entries before upload; target must be route-only.

## Domain Candidates

### `runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml`
- Hit terms: `weareswarm, hostinger`
- L2: `name: hostinger_access_registry`
- L4: `env_dir: runtime/env/hostinger/sites`
- L5: `preflight_script: runtime/scripts/hostinger_access_preflight.sh`
- L6: `latest_report_txt: _reports/hostinger_access_preflight_002b.txt`
- L7: `latest_report_md: _reports/hostinger_access_preflight_002b.md`
- L20: `  - weareswarm.online`
- L21: `  - weareswarm.site`
- L24: `  - run_hostinger_access_preflight_before_multisite_deploys`

### `runtime/deploy/hostinger_manager_smoke_matrix.yaml`
- Hit terms: `weareswarm, hostinger`
- L2: `name: hostinger_manager_smoke_matrix`
- L11: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/ariajet.site.env'`
- L21: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/crosbyultimateevents.com.env'`
- L31: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.com.env'`
- L41: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.site.env'`
- L51: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/digitaldreamscape.site.env'`
- L61: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/freerideinvestor.com.env'`
- L71: `    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/houstonsipqueen.com.env'`

### `runtime/deploy/hostinger_plugin_registry.yaml`
- Hit terms: `weareswarm, hostinger`
- L2: `name: hostinger_plugin_registry`
- L24: `      - weareswarm.online`
- L36: `      - weareswarm.site`
- L62: `      - weareswarm.online`
- L69: `  hostinger:`
- L84: `      - weareswarm.online`
- L85: `      - weareswarm.site`
- L87: `  hostinger-easy-onboarding:`

### `runtime/deploy/hostinger_theme_registry.yaml`
- Hit terms: `weareswarm, hostinger`
- L2: `name: hostinger_theme_registry`
- L60: `      - weareswarm.online`
- L121: `  hostinger-ai-theme:`
- L193: `      - weareswarm.online`
- L200: `      - weareswarm.online`
- L227: `      - weareswarm.online`
- L228: `      - weareswarm.site`
- L244: `      - weareswarm.online`

### `runtime/config/website_deploy_modes.yaml`
- Hit terms: `weareswarm, hostinger, domains`
- L2: `# Purpose: prevent deploy drift across Hostinger domains.`
- L8: `domains:`
- L24: `    source_hint: _hostinger_build/plugins`
- L27: `  weareswarm.online:`
- L30: `    source_hint: runtime/content/weareswarm`
- L33: `  weareswarm.site:`
- L36: `    source_hint: runtime/content/weareswarm`
- L48: `    source_hint: _hostinger_build/dist/parked_domains`

### `data/reports/website_promotions/build_weareswarm_dreamos_services_funnel_001.md`
- Hit terms: `weareswarm, dreamos-services`
- L1: `# Build WeAreSwarm DreamOS Services Funnel 001`
- L5: `The promoted DaDudeKC service funnel now has a concrete WeAreSwarm route page.`
- L9: `- Host: `weareswarm``
- L10: `- Path: `/dreamos-services``
- L11: `- File: `routes/weareswarm/dreamos-services/index.html``

### `data/reports/website_promotions/commit_dadudekc_host_decision_reports_001.md`
- Hit terms: `weareswarm, dreamos-services`
- L9: `- Canonical host selected: weareswarm`
- L10: `- Canonical path selected: /dreamos-services`

### `data/reports/website_promotions/dadudekc_service_funnel_host_decision_001.md`
- Hit terms: `weareswarm, dreamos-services, hostinger`
- L5: `- Canonical host: `weareswarm``
- L6: `- Canonical path: `/dreamos-services``
- L11: `Host the DaDudeKC service funnel under the WeAreSwarm brand as the Dream.OS/webdev services offer page.`
- L17: `### `weareswarm``
- L20: `- Recommended path: `/dreamos-services``
- L29: `- Role: redirect to WeAreSwarm or founder proof page`
- L30: `- Reason: Good origin brand, but weaker as main agency brand than WeAreSwarm.`
- L109: `- `_hostinger_build/dreamos_site_deployer``

### `data/reports/website_promotions/dadudekc_service_funnel_promotion_001.md`
- Hit terms: `weareswarm, dreamos-services`
- L21: `- Host: `weareswarm``
- L22: `- Path: `/dreamos-services``
- L26: `- `weareswarm`: canonical service-funnel host`
- L45: `Turn `dadudekc-service-funnel` into a live `/dreamos-services` route with:`

### `data/reports/website_promotions/package_weareswarm_dreamos_services_deploy_artifact_001.md`
- Hit terms: `weareswarm, dreamos-services`
- L1: `# Package WeAreSwarm DreamOS Services Deploy Artifact 001`
- L5: `Packaged the WeAreSwarm DreamOS services route for live deployment.`
- L9: `- Package dir: `_deploy/weareswarm/dreamos-services``
- L10: `- Index: `_deploy/weareswarm/dreamos-services/index.html``
- L11: `- Manifest: `_deploy/weareswarm/dreamos-services/deploy-manifest.json``
- L12: `- Route: `weareswarm:/dreamos-services``
- L18: `Deploy as `/dreamos-services` route only. Do not overwrite live homepage.`

### `data/reports/website_promotions/probe_weareswarm_live_deploy_capability_001.md`
- Hit terms: `weareswarm, dreamos-services, hostinger, domains`
- L1: `# Probe WeAreSwarm Live Deploy Capability 001`
- L5: `- Source: `_deploy/weareswarm/dreamos-services/index.html``
- L6: `- Manifest: `_deploy/weareswarm/dreamos-services/deploy-manifest.json``
- L7: `- Target: `weareswarm:/dreamos-services/index.html``
- L22: `runtime/scripts/__pycache__/hostinger_deploy_target_guard.cpython-313.pyc`
- L23: `runtime/scripts/__pycache__/hostinger_static_deploy_guarded.cpython-313.pyc`
- L24: `runtime/scripts/__pycache__/hostinger_wp_manager.cpython-313.pyc`
- L26: `runtime/scripts/audit_hostinger_website_inventory.sh`

### `data/reports/website_promotions/push_websites_after_dadudekc_services_funnel_001.md`
- Hit terms: `weareswarm, dreamos-services`
- L5: `Pushed websites master after building WeAreSwarm DreamOS services funnel.`
- L9: `6f7bf2b Build WeAreSwarm DreamOS services funnel`
- L13: `- `routes/weareswarm/dreamos-services/index.html``
- L14: `- Canonical: `weareswarm:/dreamos-services``
- L25: `- WeAreSwarm service route built`

### `data/reports/website_promotions/select_weareswarm_live_upload_method_001.md`
- Hit terms: `weareswarm, dreamos-services, hostinger, domains`
- L1: `# Select WeAreSwarm Live Upload Method 001`
- L17: `- Source: `_deploy/weareswarm/dreamos-services/index.html``
- L18: `- Target: `weareswarm:/dreamos-services/index.html``
- L24: `- `runtime/scripts/__pycache__/hostinger_deploy_target_guard.cpython-313.pyc``
- L25: `- `runtime/scripts/__pycache__/hostinger_static_deploy_guarded.cpython-313.pyc``
- L26: `- `runtime/scripts/__pycache__/hostinger_wp_manager.cpython-313.pyc``
- L28: `- `runtime/scripts/audit_hostinger_website_inventory.sh``
- L29: `- `runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh``

### `data/reports/website_promotions/weareswarm_dreamos_services_build_001.json`
- Hit terms: `weareswarm, dreamos-services`
- L2: `  "id": "weareswarm_dreamos_services_build_001",`
- L4: `  "host": "weareswarm",`
- L5: `  "path": "/dreamos-services",`
- L6: `  "file": "routes/weareswarm/dreamos-services/index.html",`

### `data/reports/website_promotions/weareswarm_dreamos_services_deploy_target_001.json`
- Hit terms: `weareswarm, dreamos-services, hostinger`
- L4: `  "route": "routes/weareswarm/dreamos-services/index.html",`
- L5: `  "canonical_host": "weareswarm",`
- L6: `  "canonical_path": "/dreamos-services",`
- L10: `    "source_file": "routes/weareswarm/dreamos-services/index.html",`
- L11: `    "target_path": "weareswarm:/dreamos-services/index.html",`
- L31: `      "path": "_hostinger_build",`
- L38: `    "action": "copy route page into deploy package for live WeAreSwarm upload",`
- L40: `    "commit": "Package WeAreSwarm DreamOS services deploy artifact"`

### `data/reports/website_promotions/weareswarm_dreamos_services_deploy_target_001.md`
- Hit terms: `weareswarm, dreamos-services, hostinger`
- L1: `# WeAreSwarm DreamOS Services Deploy Target 001`
- L5: `- Canonical host: `weareswarm``
- L6: `- Canonical path: `/dreamos-services``
- L7: `- Source route: `routes/weareswarm/dreamos-services/index.html``
- L12: `- Source file: `routes/weareswarm/dreamos-services/index.html``
- L13: `- Target path: `weareswarm:/dreamos-services/index.html``
- L21: `- `_hostinger_build` exists=`True` files=`69``
- L26: `- ACTION: copy route page into deploy package for live WeAreSwarm upload`

### `data/reports/website_promotions/weareswarm_dreamos_services_route_001.json`
- Hit terms: `weareswarm, dreamos-services`
- L2: `  "id": "weareswarm_dreamos_services_route_001",`
- L4: `  "canonical_host": "weareswarm",`
- L5: `  "canonical_path": "/dreamos-services",`
- L13: `      "to": "weareswarm:/dreamos-services"`
- L17: `      "to": "weareswarm:/dreamos-services",`

### `data/reports/website_promotions/websites_dirty_bucket_split_001.md`
- Hit terms: `weareswarm, dreamos-services, hostinger`
- L9: `Once these buckets are closed, DaDudeKC can be promoted into websites/dadudekc-service-funnel and routed to weareswarm:/dreamos-services.`
- L13: `- `quarantine_hostinger_build_artifacts`: `6``
- L27: `- `quarantine_hostinger_build_artifacts``
- L37: `### `_hostinger_build/dreamos_site_deployer``
- L38: `- Source group: `hostinger_build_artifacts``
- L39: `- Lane: `quarantine_hostinger_build_artifacts``
- L41: `- Reason: Hostinger build outputs should not be committed into active source without review`
- L44: `### `_hostinger_build/emergence_comic_archive_preview_001``

### `_reports/guarded_static_hostinger_deploy_helper_001.txt`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L2: `ariajet.site	wordpress	/home/u996867598/domains/ariajet.site/public_html	1	1	1	1	0	1`
- L3: `crosbyultimateevents.com	wordpress	/home/u996867598/domains/crosbyultimateevents.com/public_html	1	1	1	1	0	1`
- L4: `dadudekc.com	wordpress	/home/u996867598/domains/dadudekc.com/public_html	1	1	1	1	0	1`
- L5: `dadudekc.site	wordpress	/home/u996867598/domains/dadudekc.site/public_html	1	1	1	1	0	1`
- L6: `digitaldreamscape.site	wordpress	/home/u996867598/domains/digitaldreamscape.site/public_html	1	1	1	1	0	1`
- L7: `freerideinvestor.com	static	/home/u996867598/domains/freerideinvestor.com/public_html	0	0	0	0	1	1`
- L8: `houstonsipqueen.com	wordpress	/home/u996867598/domains/houstonsipqueen.com/public_html	1	1	1	1	0	1`
- L9: `prismblossom.online	wordpress	/home/u996867598/domains/prismblossom.online/public_html	1	1	1	1	0	1`

### `_reports/hostinger_access_preflight_002b.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# Hostinger Access Preflight 002b`
- L5: `Verify Hostinger SSH/root access for all configured site env files before deploy lanes.`
- L10: `== HOSTINGER ACCESS PREFLIGHT ==`
- L15: `REMOTE_ROOT=/home/u996867598/domains/ariajet.site/public_html`
- L22: `REMOTE_ROOT=/home/u996867598/domains/crosbyultimateevents.com/public_html`
- L29: `REMOTE_ROOT=/home/u996867598/domains/dadudekc.com/public_html`
- L36: `REMOTE_ROOT=/home/u996867598/domains/dadudekc.site/public_html`
- L43: `REMOTE_ROOT=/home/u996867598/domains/digitaldreamscape.site/public_html`

### `_reports/hostinger_access_preflight_002b.txt`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `== HOSTINGER ACCESS PREFLIGHT ==`
- L6: `REMOTE_ROOT=/home/u996867598/domains/ariajet.site/public_html`
- L13: `REMOTE_ROOT=/home/u996867598/domains/crosbyultimateevents.com/public_html`
- L20: `REMOTE_ROOT=/home/u996867598/domains/dadudekc.com/public_html`
- L27: `REMOTE_ROOT=/home/u996867598/domains/dadudekc.site/public_html`
- L34: `REMOTE_ROOT=/home/u996867598/domains/digitaldreamscape.site/public_html`
- L41: `REMOTE_ROOT=/home/u996867598/domains/freerideinvestor.com/public_html`
- L48: `REMOTE_ROOT=/home/u996867598/domains/houstonsipqueen.com/public_html`

### `_reports/hostinger_custom_asset_candidates_045.txt`
- Hit terms: `weareswarm, public_html, domains`
- L1: `plugin|ariajet.site|tradingrobotplug-wordpress-plugin|/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin`
- L2: `theme|dadudekc.com|dadudekc|/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes/dadudekc`
- L3: `plugin|freerideinvestor.com|dreamos-trading-tools|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/dreamos-trading-tools`
- L4: `plugin|freerideinvestor.com|freerideinvestor-content-engine|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-content-engine`
- L5: `plugin|freerideinvestor.com|freerideinvestor-setup|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-setup`
- L6: `plugin|freerideinvestor.com|trading-plans-automator|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/trading-plans-automator`
- L7: `plugin|freerideinvestor.com|tradingrobotplug-wordpress-plugin|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin`
- L8: `theme|freerideinvestor.com|freerideinvestor-modern|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern`

### `_reports/hostinger_custom_asset_collection_045.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# Hostinger Custom Asset Collection 045`
- L16: `plugin|ariajet.site|tradingrobotplug-wordpress-plugin|/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin`
- L17: `theme|dadudekc.com|dadudekc|/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes/dadudekc`
- L18: `plugin|freerideinvestor.com|dreamos-trading-tools|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/dreamos-trading-tools`
- L19: `plugin|freerideinvestor.com|freerideinvestor-content-engine|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-content-engine`
- L20: `plugin|freerideinvestor.com|freerideinvestor-setup|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/freerideinvestor-setup`
- L21: `plugin|freerideinvestor.com|trading-plans-automator|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/trading-plans-automator`
- L22: `plugin|freerideinvestor.com|tradingrobotplug-wordpress-plugin|/home/u996867598/domains/freerideinvestor.com/public_html/wp-content/plugins/tradingrobotplug-wordpress-plugin`

### `_reports/hostinger_deploy_target_guard_001.txt`
- Hit terms: `weareswarm, public_html, domains`
- L2: `ariajet.site	wordpress	/home/u996867598/domains/ariajet.site/public_html	1	1	1	1	0	1`
- L3: `crosbyultimateevents.com	wordpress	/home/u996867598/domains/crosbyultimateevents.com/public_html	1	1	1	1	0	1`
- L4: `dadudekc.com	wordpress	/home/u996867598/domains/dadudekc.com/public_html	1	1	1	1	0	1`
- L5: `dadudekc.site	wordpress	/home/u996867598/domains/dadudekc.site/public_html	1	1	1	1	0	1`
- L6: `digitaldreamscape.site	wordpress	/home/u996867598/domains/digitaldreamscape.site/public_html	1	1	1	1	0	1`
- L7: `freerideinvestor.com	static	/home/u996867598/domains/freerideinvestor.com/public_html	0	0	0	0	1	1`
- L8: `houstonsipqueen.com	wordpress	/home/u996867598/domains/houstonsipqueen.com/public_html	1	1	1	1	0	1`
- L9: `prismblossom.online	wordpress	/home/u996867598/domains/prismblossom.online/public_html	1	1	1	1	0	1`

### `_reports/http_500_website_root_cause_audit_004.md`
- Hit terms: `weareswarm, public_html, domains`
- L4: `Inspect all HTTP 500 domains and classify root cause with recommendation.`
- L7: `- Selected HTTPS 500 domains from classification 003.`
- L8: `- Inspected public_html contents.`
- L47: `== AUDIT HTTP 500 SITE weareswarm.online ==`
- L48: `{"site": "weareswarm.online", "root_cause": "wordpress_install_returning_500", "recommendation": "run_wp_cli_health_check_or_restore_wordpress", "priority": "classify_purpose_before_repair", "intended_purpose_guess": "dreamos_swarm_brand_ca`
- L49: `SITE_ROOT_CAUSE=weareswarm.online:wordpress_install_returning_500:run_wp_cli_health_check_or_restore_wordpress:classify_purpose_before_repair`
- L51: `== AUDIT HTTP 500 SITE weareswarm.site ==`
- L52: `{"site": "weareswarm.site", "root_cause": "wordpress_install_returning_500", "recommendation": "run_wp_cli_health_check_or_restore_wordpress", "priority": "classify_purpose_before_repair", "intended_purpose_guess": "dreamos_swarm_brand_cand`

### `_reports/website_audit/domain_purpose_decision_matrix_005.md`
- Hit terms: `weareswarm`
- L48: `- weareswarm.online`
- L49: `- weareswarm.site`
- L65: `| weareswarm.online | 500 | dreamos_brand_candidate | Dream.OS swarm brand candidate | classify_before_repair | wordpress_install_returning_500 | choose primary swarm domain and park/archive duplicate |`
- L66: `| weareswarm.site | 500 | dreamos_brand_candidate_duplicate | Dream.OS swarm brand duplicate candidate | classify_before_repair | wordpress_install_returning_500 | compare against weareswarm.online and choose canonical |`

### `_reports/website_audit/http_500_root_causes/http_500_root_cause_audit_004.txt`
- Hit terms: `weareswarm`
- L29: `== AUDIT HTTP 500 SITE weareswarm.online ==`
- L30: `{"site": "weareswarm.online", "root_cause": "wordpress_install_returning_500", "recommendation": "run_wp_cli_health_check_or_restore_wordpress", "priority": "classify_purpose_before_repair", "intended_purpose_guess": "dreamos_swarm_brand_ca`
- L31: `SITE_ROOT_CAUSE=weareswarm.online:wordpress_install_returning_500:run_wp_cli_health_check_or_restore_wordpress:classify_purpose_before_repair`
- L33: `== AUDIT HTTP 500 SITE weareswarm.site ==`
- L34: `{"site": "weareswarm.site", "root_cause": "wordpress_install_returning_500", "recommendation": "run_wp_cli_health_check_or_restore_wordpress", "priority": "classify_purpose_before_repair", "intended_purpose_guess": "dreamos_swarm_brand_cand`
- L35: `SITE_ROOT_CAUSE=weareswarm.site:wordpress_install_returning_500:run_wp_cli_health_check_or_restore_wordpress:classify_purpose_before_repair`

### `_reports/website_audit/http_500_root_causes/http_500_root_cause_rollup_004.json`
- Hit terms: `weareswarm`
- L53: `  "site": "weareswarm.online",`
- L57: `  "report": "/data/data/com.termux/files/home/projects/websites/_reports/website_audit/http_500_root_causes/weareswarm.online__500_root_cause.md",`
- L58: `  "remote_capture": "/data/data/com.termux/files/home/projects/websites/_reports/website_audit/http_500_root_causes/weareswarm.online__remote_500_audit.txt"`
- L61: `  "site": "weareswarm.site",`
- L65: `  "report": "/data/data/com.termux/files/home/projects/websites/_reports/website_audit/http_500_root_causes/weareswarm.site__500_root_cause.md",`
- L66: `  "remote_capture": "/data/data/com.termux/files/home/projects/websites/_reports/website_audit/http_500_root_causes/weareswarm.site__remote_500_audit.txt"`

### `_reports/website_audit/http_500_root_causes/http_500_root_cause_rollup_004.md`
- Hit terms: `weareswarm`
- L13: `| weareswarm.online | wordpress_install_returning_500 | run_wp_cli_health_check_or_restore_wordpress | [report](weareswarm.online__500_root_cause.md) |`
- L14: `| weareswarm.site | wordpress_install_returning_500 | run_wp_cli_health_check_or_restore_wordpress | [report](weareswarm.site__500_root_cause.md) |`

### `_reports/website_audit/http_500_root_causes/weareswarm.online__500_root_cause.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# HTTP 500 Root Cause: weareswarm.online`
- L17: `DOMAIN=weareswarm.online`
- L18: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.online/public_html`
- L21: `/home/u996867598/domains/weareswarm.online/public_html`
- L35: `-rw-r--r--  1 u996867598 o1008028115   1950 Dec 22 19:06 WHAT_IS_WEARESWARM_SITE.md`
- L72: `-rw-r--r-- u996867598 o1008028115 ./WHAT_IS_WEARESWARM_SITE.md`
- L116: `        <link rel="icon" type="image/x-icon" href="https://hpanel.hostinger.com/favicons/hostinger.png">`

### `_reports/website_audit/http_500_root_causes/weareswarm.online__remote_500_audit.txt`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `DOMAIN=weareswarm.online`
- L2: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.online/public_html`
- L5: `/home/u996867598/domains/weareswarm.online/public_html`
- L19: `-rw-r--r--  1 u996867598 o1008028115   1950 Dec 22 19:06 WHAT_IS_WEARESWARM_SITE.md`
- L56: `-rw-r--r-- u996867598 o1008028115 ./WHAT_IS_WEARESWARM_SITE.md`
- L100: `        <link rel="icon" type="image/x-icon" href="https://hpanel.hostinger.com/favicons/hostinger.png">`

### `_reports/website_audit/http_500_root_causes/weareswarm.site__500_root_cause.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# HTTP 500 Root Cause: weareswarm.site`
- L17: `DOMAIN=weareswarm.site`
- L18: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.site/public_html`
- L21: `/home/u996867598/domains/weareswarm.site/public_html`
- L40: `drwxr-xr-x  3 u996867598 o1008028115  4096 May 10 16:00 public_html`
- L68: `drwxr-xr-x u996867598 o1008028115 ./public_html`
- L132: `        <link rel="icon" type="image/x-icon" href="https://hpanel.hostinger.com/favicons/hostinger.png">`

### `_reports/website_audit/http_500_root_causes/weareswarm.site__remote_500_audit.txt`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `DOMAIN=weareswarm.site`
- L2: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.site/public_html`
- L5: `/home/u996867598/domains/weareswarm.site/public_html`
- L24: `drwxr-xr-x  3 u996867598 o1008028115  4096 May 10 16:00 public_html`
- L52: `drwxr-xr-x u996867598 o1008028115 ./public_html`
- L116: `        <link rel="icon" type="image/x-icon" href="https://hpanel.hostinger.com/favicons/hostinger.png">`

### `_reports/website_audit/parked_domain_static_placeholder_pack_006.md`
- Hit terms: `weareswarm, hostinger, domains`
- L9: `| ariajet.site | park_or_rebuild_static | travel or brand candidate | `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/parked_domains/ariajet.site-placeholder-0.1.0.zip` |`
- L10: `| crosbyultimateevents.com | rebuild_static_or_repair_wordpress | events business candidate | `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/parked_domains/crosbyultimateevents.com-placeholder-0.1.0.zip` |`
- L11: `| houstonsipqueen.com | rebuild_static_or_repair_wordpress | food/beverage brand candidate | `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/parked_domains/houstonsipqueen.com-placeholder-0.1.0.zip` |`
- L12: `| southwestsecret.com | park_or_archive | content or brand candidate | `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/parked_domains/southwestsecret.com-placeholder-0.1.0.zip` |`
- L14: `## Excluded Domains`
- L24: `| weareswarm.online | not_placeholder_candidate |`
- L25: `| weareswarm.site | not_placeholder_candidate |`
- L32: `- Active product domains are excluded.`

### `_reports/website_audit/weareswarm.online__audit.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# Website Audit: weareswarm.online`
- L9: `| Domain | weareswarm.online |`
- L11: `| Remote Root | `/home/u996867598/domains/weareswarm.online/public_html` |`
- L29: `- HTTPS: https://weareswarm.online/ => 500`
- L30: `- HTTP: http://weareswarm.online/ => 500`
- L34: `- Homepage HTTPS is not clean 200. Investigate DNS, SSL, permissions, app routing, or Hostinger config.`

### `_reports/website_audit/weareswarm.site__audit.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L1: `# Website Audit: weareswarm.site`
- L9: `| Domain | weareswarm.site |`
- L11: `| Remote Root | `/home/u996867598/domains/weareswarm.site/public_html` |`
- L29: `- HTTPS: https://weareswarm.site/ => 500`
- L30: `- HTTP: http://weareswarm.site/ => 500`
- L34: `- Homepage HTTPS is not clean 200. Investigate DNS, SSL, permissions, app routing, or Hostinger config.`

### `_reports/website_audit/website_inventory_audit_001b.txt`
- Hit terms: `weareswarm`
- L35: `== AUDIT SITE weareswarm.online ==`
- L36: `SITE_REPORT=/data/data/com.termux/files/home/projects/websites/_reports/website_audit/weareswarm.online__audit.md`
- L38: `== AUDIT SITE weareswarm.site ==`
- L39: `SITE_REPORT=/data/data/com.termux/files/home/projects/websites/_reports/website_audit/weareswarm.site__audit.md`

### `_reports/website_audit/website_inventory_audit_rollup.json`
- Hit terms: `weareswarm, public_html, domains`
- L10: `  "remote_root": "/home/u996867598/domains/ariajet.site/public_html",`
- L26: `  "remote_root": "/home/u996867598/domains/crosbyultimateevents.com/public_html",`
- L42: `  "remote_root": "/home/u996867598/domains/dadudekc.com/public_html",`
- L58: `  "remote_root": "/home/u996867598/domains/dadudekc.site/public_html",`
- L75: `  "remote_root": "/home/u996867598/domains/digitaldreamscape.site/public_html",`
- L91: `  "remote_root": "/home/u996867598/domains/freerideinvestor.com/public_html",`
- L107: `  "remote_root": "/home/u996867598/domains/houstonsipqueen.com/public_html",`
- L123: `  "remote_root": "/home/u996867598/domains/prismblossom.online/public_html",`

### `_reports/website_audit/website_inventory_audit_rollup.md`
- Hit terms: `weareswarm, public_html, domains`
- L7: `| ariajet.site | PASS | unknown | 500 | 500 | `/home/u996867598/domains/ariajet.site/public_html` | [report](ariajet.site__audit.md) |`
- L8: `| crosbyultimateevents.com | PASS | unknown | 500 | 500 | `/home/u996867598/domains/crosbyultimateevents.com/public_html` | [report](crosbyultimateevents.com__audit.md) |`
- L9: `| dadudekc.com | PASS | unknown | 000 | 000 | `/home/u996867598/domains/dadudekc.com/public_html` | [report](dadudekc.com__audit.md) |`
- L10: `| dadudekc.site | PASS | unknown | 200 | 200 | `/home/u996867598/domains/dadudekc.site/public_html` | [report](dadudekc.site__audit.md) |`
- L11: `| digitaldreamscape.site | PASS | unknown | 500 | 500 | `/home/u996867598/domains/digitaldreamscape.site/public_html` | [report](digitaldreamscape.site__audit.md) |`
- L12: `| freerideinvestor.com | PASS | unknown | 200 | 200 | `/home/u996867598/domains/freerideinvestor.com/public_html` | [report](freerideinvestor.com__audit.md) |`
- L13: `| houstonsipqueen.com | PASS | unknown | 500 | 500 | `/home/u996867598/domains/houstonsipqueen.com/public_html` | [report](houstonsipqueen.com__audit.md) |`
- L14: `| prismblossom.online | PASS | unknown | 200 | 200 | `/home/u996867598/domains/prismblossom.online/public_html` | [report](prismblossom.online__audit.md) |`

### `_reports/website_audit/website_inventory_classification_002.json`
- Hit terms: `weareswarm, public_html, domains`
- L26: `      "weareswarm.online",`
- L27: `      "weareswarm.site",`
- L41: `      "remote_root": "/home/u996867598/domains/ariajet.site/public_html",`
- L65: `      "remote_root": "/home/u996867598/domains/crosbyultimateevents.com/public_html",`
- L89: `      "remote_root": "/home/u996867598/domains/dadudekc.com/public_html",`
- L113: `      "remote_root": "/home/u996867598/domains/dadudekc.site/public_html",`
- L137: `      "remote_root": "/home/u996867598/domains/digitaldreamscape.site/public_html",`
- L161: `      "remote_root": "/home/u996867598/domains/freerideinvestor.com/public_html",`

### `_reports/website_audit/website_inventory_classification_002.md`
- Hit terms: `weareswarm`
- L30: `- weareswarm.online`
- L31: `- weareswarm.site`
- L60: `| weareswarm.online | 500 | unknown | 0 | classify_purpose | access_ok, unknown_type, http_review, thin_or_parked, parked_or_placeholder |`
- L61: `| weareswarm.site | 500 | unknown | 0 | classify_purpose | access_ok, unknown_type, http_review, thin_or_parked, parked_or_placeholder |`

### `_reports/website_audit/website_inventory_classification_003.json`
- Hit terms: `weareswarm, public_html, domains`
- L23: `      "weareswarm.online",`
- L24: `      "weareswarm.site",`
- L41: `      "remote_root": "/home/u996867598/domains/ariajet.site/public_html",`
- L65: `      "remote_root": "/home/u996867598/domains/crosbyultimateevents.com/public_html",`
- L89: `      "remote_root": "/home/u996867598/domains/dadudekc.com/public_html",`
- L116: `      "remote_root": "/home/u996867598/domains/dadudekc.site/public_html",`
- L140: `      "remote_root": "/home/u996867598/domains/digitaldreamscape.site/public_html",`
- L164: `      "remote_root": "/home/u996867598/domains/freerideinvestor.com/public_html",`

### `_reports/website_audit/website_inventory_classification_003.md`
- Hit terms: `weareswarm`
- L31: `- weareswarm.online`
- L32: `- weareswarm.site`
- L65: `| weareswarm.online | 500 | unknown | classify_purpose | access_ok, unknown_type, http_review, thin_or_parked, parked_or_placeholder |  |`
- L66: `| weareswarm.site | 500 | unknown | classify_purpose | access_ok, unknown_type, http_review, thin_or_parked, parked_or_placeholder |  |`

### `_reports/website_deploy_mode_registry_001.txt`
- Hit terms: `weareswarm, public_html, domains`
- L2: `ariajet.site	wordpress	/home/u996867598/domains/ariajet.site/public_html	1	1	1	1	0	1`
- L3: `crosbyultimateevents.com	wordpress	/home/u996867598/domains/crosbyultimateevents.com/public_html	1	1	1	1	0	1`
- L4: `dadudekc.com	wordpress	/home/u996867598/domains/dadudekc.com/public_html	1	1	1	1	0	1`
- L5: `dadudekc.site	wordpress	/home/u996867598/domains/dadudekc.site/public_html	1	1	1	1	0	1`
- L6: `digitaldreamscape.site	wordpress	/home/u996867598/domains/digitaldreamscape.site/public_html	1	1	1	1	0	1`
- L7: `freerideinvestor.com	static	/home/u996867598/domains/freerideinvestor.com/public_html	0	0	0	0	1	1`
- L8: `houstonsipqueen.com	wordpress	/home/u996867598/domains/houstonsipqueen.com/public_html	1	1	1	1	0	1`
- L9: `prismblossom.online	wordpress	/home/u996867598/domains/prismblossom.online/public_html	1	1	1	1	0	1`

### `_reports/website_inventory_audit_001b.md`
- Hit terms: `weareswarm, hostinger, public_html, domains`
- L4: `Map and inventory current domains; audit all configured websites.`
- L8: `- Enumerated Hostinger site env files.`
- L10: `- Audited remote public_html file structure.`
- L53: `== AUDIT SITE weareswarm.online ==`
- L54: `SITE_REPORT=/data/data/com.termux/files/home/projects/websites/_reports/website_audit/weareswarm.online__audit.md`
- L56: `== AUDIT SITE weareswarm.site ==`
- L57: `SITE_REPORT=/data/data/com.termux/files/home/projects/websites/_reports/website_audit/weareswarm.site__audit.md`


## All Matches

- `runtime/deploy/custom_plugin_preservation_policy.yaml` terms=`hostinger`
- `runtime/deploy/freerideinvestor_revenue_showcase.yaml` terms=`hostinger`
- `runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml` terms=`weareswarm, hostinger`
- `runtime/deploy/hostinger/http_500_root_cause_audit_manifest.yaml` terms=`domains`
- `runtime/deploy/hostinger/parked_domain_static_placeholder_pack_manifest.yaml` terms=`hostinger, domains`
- `runtime/deploy/hostinger/website_inventory_audit_manifest.yaml` terms=`hostinger`
- `runtime/deploy/hostinger_connected_sites.yaml` terms=`weareswarm, hostinger, public_html, domains`
- `runtime/deploy/hostinger_custom_asset_collection_manifest.yaml` terms=`weareswarm, hostinger, public_html, domains`
- `runtime/deploy/hostinger_deploy_proof_profile.yaml` terms=`hostinger`
- `runtime/deploy/hostinger_manager_smoke_matrix.yaml` terms=`weareswarm, hostinger`
- `runtime/deploy/hostinger_plugin_registry.yaml` terms=`weareswarm, hostinger`
- `runtime/deploy/hostinger_sites_manifest.yaml` terms=`weareswarm, hostinger, public_html, domains`
- `runtime/deploy/hostinger_theme_registry.yaml` terms=`weareswarm, hostinger`
- `runtime/deploy/sites/dadudekc.site/client_theme_update_manifest.yaml` terms=`hostinger, public_html, domains`
- `runtime/deploy/sites/dadudekc.site/custom_spark_battle_participant_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_admin_event_dashboard_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_battle_story_cinematics_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_character_battle_handoff_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_character_generator_demo_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_disguised_answer_labels_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_domain_pass_plugin_syntax_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_flavor_phase_transition_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_flavor_power_selection_pass_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_generated_spark_portrait_card_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_generator_answer_labels_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_hostinger_image_env_manifest.yaml` terms=`hostinger`
- `runtime/deploy/sites/dadudekc.site/emergence_openai_premium_image_provider_manifest.yaml` terms=`hostinger, public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_portrait_prompt_preview_polish_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_premium_portrait_prompt_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_privacy_safe_event_tracking_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_protocol_v85_question_bank_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_public_demo_hardening_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_public_scoring_privacy_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_public_share_card_ui_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_saved_character_records_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_scan_submit_state_reset_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/emergence_totality_observation_portrait_prompt_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/shareable_spark_handoff_token_manifest.yaml` terms=`public_html, domains`
- `runtime/deploy/sites/dadudekc.site/spark_battle_sim_hostinger_install_manifest.yaml` terms=`hostinger, public_html, domains`
- `runtime/deploy/sites/dadudekc.site/spark_protocol_character_generation_port_manifest.yaml` terms=`public_html, domains`