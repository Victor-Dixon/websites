# Dream.OS Website Deployer Contract

Generated: 20260613_120826

## Repo
/data/data/com.termux/files/home/projects/websites

## Git Status
?? data/reports/website_promotions/dreamos_site_deployer_contract_20260613_120826.md
?? data/reports/website_promotions/weareswarm_unified_deployer_focus_20260613_120710.md
?? data/reports/website_promotions/weareswarm_unified_deployer_focus_20260613_120739.md

## Deployer Help: dreamos_site_deployer.py
```
usage: dreamos_site_deployer.py [-h] --env ENV --domain DOMAIN --source SOURCE
                                [--mode {auto,static,wp-page}]
                                [--remote-rel REMOTE_REL] [--title TITLE]
                                [--slug SLUG] [--status STATUS]
                                [--verify-url VERIFY_URL]

Dream.OS streamlined Hostinger website deployer

options:
  -h, --help            show this help message and exit
  --env ENV
  --domain DOMAIN
  --source SOURCE
  --mode {auto,static,wp-page}
  --remote-rel REMOTE_REL
  --title TITLE
  --slug SLUG
  --status STATUS
  --verify-url VERIFY_URL
```

## Deployer Help: hostinger_static_deploy_guarded.py
```
usage: hostinger_static_deploy_guarded.py [-h] [--env ENV] --domain DOMAIN
                                          --local-root LOCAL_ROOT --file FILE
                                          --verify-url VERIFY_URL [--dry-run]

Guarded static Hostinger deploy

options:
  -h, --help            show this help message and exit
  --env ENV
  --domain DOMAIN
  --local-root LOCAL_ROOT
  --file FILE           File path under local-root; repeatable
  --verify-url VERIFY_URL
  --dry-run
```

## Deployer Help: hostinger_deploy_target_guard.py
```
usage: hostinger_deploy_target_guard.py [-h] [--env ENV] [--domain DOMAIN]
                                        [--expect {wordpress,static,partial_or_php,empty_or_placeholder,missing}]
                                        [--json]

Hostinger deploy target guard

options:
  -h, --help            show this help message and exit
  --env ENV
  --domain DOMAIN       Exact domain to validate
  --expect {wordpress,static,partial_or_php,empty_or_placeholder,missing}
  --json
```

## Hostinger Manifest: WeAreSwarm / MaskZero Sections
```
286-        deploy_enabled: false
287-      - slug: 'twentytwentythree'
288-        theme_name: 'Twenty Twenty-Three'
289-        classification: wordpress_default
290-        collect_enabled: false
291-        deploy_enabled: false
292-
293-  maskzero_site:
294:    domain: 'maskzero.site'
295-    platform: wordpress
296-    wordpress_detected: true
297:    wp_root: '/home/u996867598/domains/maskzero.site/public_html'
298:    plugins_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins'
299:    themes_dir: '/home/u996867598/domains/maskzero.site/public_html/wp-content/themes'
300-    deploy_enabled: false
301-    plugin_deploy_enabled: false
302-    theme_deploy_enabled: false
303-    deploy_mode: manual_review_required
304-    plugins:
305-      - slug: 'hostinger'
306-        classification: vendor
307-        main_file: 'index.php'
308-        collect_enabled: false
309-        deploy_enabled: false
310-      - slug: 'hostinger-easy-onboarding'
311-        classification: vendor
312-        main_file: 'index.php'
313-        collect_enabled: false
314-        deploy_enabled: false
315-      - slug: 'hostinger-reach'
316-        classification: vendor
317-        main_file: 'hostinger-reach.php'
318-        collect_enabled: false
319-        deploy_enabled: false
320-      - slug: 'litespeed-cache'
321-        classification: vendor
322-        main_file: 'guest.vary.php'
323-        collect_enabled: false
324-        deploy_enabled: false
325-    themes:
326-      - slug: 'hostinger-ai-theme'
327-        theme_name: 'Hostinger AI theme'
328-        classification: unknown_review_required
329-        collect_enabled: false
330-        deploy_enabled: false
331-      - slug: 'twentytwentyfive'
332-        theme_name: 'Twenty Twenty-Five'
333-        classification: wordpress_default
334-        collect_enabled: false
335-        deploy_enabled: false
336-      - slug: 'twentytwentyfour'
337-        theme_name: 'Twenty Twenty-Four'
338-        classification: wordpress_default
339-        collect_enabled: false
340-        deploy_enabled: false
341-      - slug: 'twentytwentythree'
342-        theme_name: 'Twenty Twenty-Three'
343-        classification: wordpress_default
344-        collect_enabled: false
--
824-        deploy_enabled: false
825-      - slug: 'twentytwentythree'
826-        theme_name: 'Twenty Twenty-Three'
827-        classification: wordpress_default
828-        collect_enabled: false
829-        deploy_enabled: false
830-
831-  weareswarm_online:
832:    domain: 'weareswarm.online'
833-    platform: wordpress
834-    wordpress_detected: true
835:    wp_root: '/home/u996867598/domains/weareswarm.online/public_html'
836:    plugins_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/plugins'
837:    themes_dir: '/home/u996867598/domains/weareswarm.online/public_html/wp-content/themes'
838-    deploy_enabled: false
839-    plugin_deploy_enabled: false
840-    theme_deploy_enabled: false
841-    deploy_mode: manual_review_required
842-    plugins:
843-      - slug: 'all-in-one-seo-pack'
844-        classification: unknown_review_required
845-        main_file: 'all_in_one_seo_pack.php'
846-        collect_enabled: false
847-        deploy_enabled: false
848-      - slug: 'google-site-kit'
849-        classification: unknown_review_required
850-        main_file: 'google-site-kit.php'
851-        collect_enabled: false
852-        deploy_enabled: false
853-      - slug: 'hostinger'
854-        classification: vendor
855-        main_file: 'index.php'
856-        collect_enabled: false
857-        deploy_enabled: false
858-      - slug: 'hostinger-easy-onboarding'
859-        classification: vendor
860-        main_file: 'index.php'
861-        collect_enabled: false
862-        deploy_enabled: false
863-      - slug: 'hostinger-reach'
864-        classification: vendor
865-        main_file: 'hostinger-reach.php'
866-        collect_enabled: false
867-        deploy_enabled: false
868-      - slug: 'litespeed-cache'
869-        classification: vendor
870-        main_file: 'guest.vary.php'
871-        collect_enabled: false
872-        deploy_enabled: false
873-      - slug: 'sureforms'
874-        classification: unknown_review_required
875-        main_file: 'sureforms.php'
876-        collect_enabled: false
877-        deploy_enabled: false
878-      - slug: 'ultimate-addons-for-gutenberg'
879-        classification: unknown_review_required
880-        main_file: 'ultimate-addons-for-gutenberg.php'
881-        collect_enabled: false
882-        deploy_enabled: false
--
918-        deploy_enabled: false
919-      - slug: 'weareswarm'
920-        theme_name: ''
921-        classification: custom_candidate
922-        collect_enabled: false
923-        deploy_enabled: false
924-
925-  weareswarm_site:
926:    domain: 'weareswarm.site'
927-    platform: wordpress
928-    wordpress_detected: true
929:    wp_root: '/home/u996867598/domains/weareswarm.site/public_html'
930:    plugins_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/plugins'
931:    themes_dir: '/home/u996867598/domains/weareswarm.site/public_html/wp-content/themes'
932-    deploy_enabled: false
933-    plugin_deploy_enabled: false
934-    theme_deploy_enabled: false
935-    deploy_mode: manual_review_required
936-    plugins:
937-      - slug: 'dreamos-swarm-status'
938-        classification: custom_candidate
939-        main_file: 'dreamos-swarm-status.php'
940-        collect_enabled: false
941-        deploy_enabled: false
942-      - slug: 'hostinger'
943-        classification: vendor
944-        main_file: 'index.php'
945-        collect_enabled: false
946-        deploy_enabled: false
947-      - slug: 'hostinger-easy-onboarding'
948-        classification: vendor
949-        main_file: 'index.php'
950-        collect_enabled: false
951-        deploy_enabled: false
952-      - slug: 'hostinger-reach'
953-        classification: vendor
954-        main_file: 'hostinger-reach.php'
955-        collect_enabled: false
956-        deploy_enabled: false
957-    themes:
958-      - slug: 'twentytwentyfive'
959-        theme_name: 'Twenty Twenty-Five'
960-        classification: wordpress_default
961-        collect_enabled: false
962-        deploy_enabled: false
963-      - slug: 'twentytwentyfour'
964-        theme_name: 'Twenty Twenty-Four'
965-        classification: wordpress_default
966-        collect_enabled: false
967-        deploy_enabled: false
968-      - slug: 'twentytwentythree'
969-        theme_name: 'Twenty Twenty-Three'
970-        classification: wordpress_default
971-        collect_enabled: false
972-        deploy_enabled: false
973-      - slug: 'weareswarm-dreamos'
974-        theme_name: 'WeAreSwarm DreamOS'
975-        classification: custom_candidate
976-        collect_enabled: false
```

## Site Env Files
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

### runtime/env/hostinger/sites/maskzero.site.env
missing

```

## Deploy Output Directories
```
### _deploy
_deploy/weareswarm.online/dreamos-services/index.html
_deploy/weareswarm.online/skill-tree/index.html
_deploy/weareswarm/assets/weareswarm-hydration.js
_deploy/weareswarm/crosbyultimateevents/index.html
_deploy/weareswarm/dreamos-services/deploy-manifest.json
_deploy/weareswarm/dreamos-services/index.html
_deploy/weareswarm/feed/index.html
_deploy/weareswarm/focus/index.html
_deploy/weareswarm/hub/index.html
_deploy/weareswarm/index.html
_deploy/weareswarm/kids-planner/index.html
_deploy/weareswarm/projects/index.html
_deploy/weareswarm/skill-tree/deploy-manifest.json
_deploy/weareswarm/skill-tree/index.html
_deploy/weareswarm/tasks/index.html

### _deploy/weareswarm.online
_deploy/weareswarm.online/dreamos-services/index.html
_deploy/weareswarm.online/skill-tree/index.html

### _deploy/weareswarm.site
missing

### _deploy/maskzero.site
missing

```

## Runtime Content Directories
```
### runtime/content
runtime/content/dadudekc.site/.htaccess
runtime/content/dadudekc.site/index.html
runtime/content/freerideinvestor.com/README.md
runtime/content/freerideinvestor.com/day-trade-planner.html
runtime/content/freerideinvestor.com/early-access.html
runtime/content/freerideinvestor.com/index.html
runtime/content/freerideinvestor.com/replay-proof.html
runtime/content/freerideinvestor.com/trading-journal.html
runtime/content/freerideinvestor.com/trading-journal/index.html
runtime/content/freerideinvestor/ai-trading-journal.html
runtime/content/freerideinvestor/data/tsla-command-center.json
runtime/content/freerideinvestor/sales-funnel.html
runtime/content/freerideinvestor/sales-funnel.md
runtime/content/freerideinvestor/tsla-command-center.html
runtime/content/maskzero.site/assets/js/spark-auth-nav.js
runtime/content/maskzero.site/battle-simulator.html
runtime/content/maskzero.site/battles.html
runtime/content/maskzero.site/battles/index.html
runtime/content/maskzero.site/character-generator.html
runtime/content/maskzero.site/client-preview.html
runtime/content/maskzero.site/home.html
runtime/content/maskzero.site/missions/index.html
runtime/content/maskzero.site/protocol.html
runtime/content/maskzero.site/spark-generator/index.html
runtime/content/maskzero.site/spark-os/index.html
runtime/content/maskzero.site/the-emergence.html
runtime/content/meridian/map-grid.json
runtime/content/meridian/missions.json
runtime/content/meridian/world.json
runtime/content/parked_domains/OWNER_APPROVAL_REQUIRED.md
runtime/content/parked_domains/ariajet.site/OWNER_APPROVAL_REQUIRED.md
runtime/content/parked_domains/ariajet.site/about/index.html
runtime/content/parked_domains/ariajet.site/achievements/index.html
runtime/content/parked_domains/ariajet.site/assets/README.md
runtime/content/parked_domains/ariajet.site/assets/style.css
runtime/content/parked_domains/ariajet.site/dev-log/index.html
runtime/content/parked_domains/ariajet.site/index.html
runtime/content/parked_domains/ariajet.site/projects/index.html
runtime/content/parked_domains/ariajet.site/roadmap/index.html
runtime/content/parked_domains/ariajet.site/updates/index.html
runtime/content/parked_domains/crosbyultimateevents.com/OWNER_APPROVAL_REQUIRED.md
runtime/content/parked_domains/crosbyultimateevents.com/assets/style.css
runtime/content/parked_domains/crosbyultimateevents.com/index.html
runtime/content/parked_domains/houstonsipqueen.com/OWNER_APPROVAL_REQUIRED.md
runtime/content/parked_domains/houstonsipqueen.com/assets/style.css
runtime/content/parked_domains/houstonsipqueen.com/index.html
runtime/content/parked_domains/parked_domain_static_placeholder_pack_006.json
runtime/content/parked_domains/southwestsecret.com/.htaccess
runtime/content/parked_domains/southwestsecret.com/OWNER_APPROVAL_REQUIRED.md
runtime/content/parked_domains/southwestsecret.com/assets/style.css
runtime/content/parked_domains/southwestsecret.com/index.html
runtime/content/weareswarm.site/PLANNER_BRIDGE.md
runtime/content/weareswarm.site/data/project-board.generated.json
runtime/content/weareswarm.site/data/swarm-status.generated.json

### runtime/content/weareswarm.online
missing

### runtime/content/weareswarm.site
runtime/content/weareswarm.site/PLANNER_BRIDGE.md
runtime/content/weareswarm.site/data/project-board.generated.json
runtime/content/weareswarm.site/data/swarm-status.generated.json

### runtime/content/maskzero.site
runtime/content/maskzero.site/assets/js/spark-auth-nav.js
runtime/content/maskzero.site/battle-simulator.html
runtime/content/maskzero.site/battles.html
runtime/content/maskzero.site/battles/index.html
runtime/content/maskzero.site/character-generator.html
runtime/content/maskzero.site/client-preview.html
runtime/content/maskzero.site/home.html
runtime/content/maskzero.site/missions/index.html
runtime/content/maskzero.site/protocol.html
runtime/content/maskzero.site/spark-generator/index.html
runtime/content/maskzero.site/spark-os/index.html
runtime/content/maskzero.site/the-emergence.html

```

## Existing Roadmap / Skill Tree / Feed Routes
```
```

## Decision

- If deployer accepts --domain and --source, patch _deploy/weareswarm.online/roadmap/index.html then deploy.
- If deployer consumes runtime/content, patch runtime/content source and regenerate _deploy.
- If site is WordPress theme-driven, patch collected/hostinger WordPress theme templates and deploy via WP manager/static deployer.
