# Domain Purpose Decision Matrix 005

## Operating Policy

- Do not repair broken WordPress installs blindly.
- Confirm owner intent before spending time on repair.
- Prefer clean static rebuilds for simple business/brand sites.
- Back up remote root before any repair or rebuild.
- `dadudekc.com` is expired hold; do not repair until reacquired.

## Buckets

### keep_live

- maskzero.site
- freerideinvestor.com

### keep_live_pending_purpose

- prismblossom.online
- xthunder.site

### repair_wordpress_candidates

- crosbyultimateevents.com
- houstonsipqueen.com

### rebuild_static_candidates

- ariajet.site

### park_candidates

- southwestsecret.com

### archive_candidates

- none

### hold

- dadudekc.com

### classify_before_repair

- digitaldreamscape.site
- tradingrobotplug.com
- weareswarm.online
- weareswarm.site

## Decision Table

| Domain | HTTPS | Intent | Business Purpose | Recommended Action | Root Cause | Next Lane |
|---|---:|---|---|---|---|---|
| ariajet.site | 500 | brand_candidate | travel or brand candidate | park_or_rebuild_static | wordpress_install_returning_500 | confirm purpose; if no active plan, deploy parked static placeholder |
| crosbyultimateevents.com | 500 | business_site_candidate | events business candidate | rebuild_static_or_repair_wordpress | wordpress_install_returning_500 | confirm business owner/use; then rebuild static landing page or repair WordPress |
| dadudekc.com | 000 | expired_domain_hold | expired legacy domain; do not repair until reacquired cheaply | hold | - | ignore for deploy/repair until domain is reacquired |
| maskzero.site | 200 | active_product_host | Emergence / Spark Battle Simulator live prototype host | keep_live | - | continue Emergence launch hardening and browser E2E from supported host |
| digitaldreamscape.site | 500 | dreamos_brand_candidate | Dream/DreamOS brand candidate | classify_before_repair | wordpress_install_returning_500 | decide if this becomes Dream.OS portfolio/agency landing page or parked |
| freerideinvestor.com | 200 | active_marketing_site | FreeRideInvestor sales funnel, trading journal workflow, replay proof, early access | keep_live | - | run periodic live static verify and improve funnel copy |
| houstonsipqueen.com | 500 | business_site_candidate | food/beverage brand candidate | rebuild_static_or_repair_wordpress | wordpress_install_returning_500 | confirm brand status; rebuild static landing page if active |
| prismblossom.online | 200 | live_static_or_placeholder_candidate | live brand/project placeholder candidate | keep_live_pending_purpose | - | inspect live copy and decide business use |
| southwestsecret.com | 500 | content_brand_candidate | content or brand candidate | park_or_archive | wordpress_install_returning_500 | confirm purpose; park if no current campaign |
| tradingrobotplug.com | 500 | legacy_project_candidate | Trading Robot Plug / fintech legacy project candidate | classify_before_repair | wordpress_install_returning_500 | decide whether to rebuild as static legacy landing page or archive |
| weareswarm.online | 500 | dreamos_brand_candidate | Dream.OS swarm brand candidate | classify_before_repair | wordpress_install_returning_500 | choose primary swarm domain and park/archive duplicate |
| weareswarm.site | 500 | dreamos_brand_candidate_duplicate | Dream.OS swarm brand duplicate candidate | classify_before_repair | wordpress_install_returning_500 | compare against weareswarm.online and choose canonical |
| xthunder.site | 200 | live_static_or_placeholder_candidate | live brand/project placeholder candidate | keep_live_pending_purpose | - | inspect live copy and decide business use |

## Immediate Operator Order

1. Keep `maskzero.site` live for Emergence work.
2. Keep `freerideinvestor.com` live and monitor static deploy.
3. Hold `dadudekc.com`; expired domain, no repair.
4. Decide canonical Swarm domain before repairing either Swarm WordPress install.
5. For simple business brands, prefer static rebuild over WordPress repair unless there is valuable WP content.
6. Inspect root-cause reports before any destructive action.
