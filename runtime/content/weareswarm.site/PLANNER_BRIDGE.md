# WeAreSwarm Planner Bridge

Generated public status for `/projects/` and `/tasks/` flows through:

1. `data/reports/planner/next_lane.json` — authoritative planner contract
2. `runtime/scripts/sync_weareswarm_planner_status_001.py` — generator
3. `runtime/content/weareswarm.site/data/swarm-status.generated.json` — generated output
4. `collected/hostinger/wordpress/domains/weareswarm.site/plugins/dreamos-swarm-status/dreamos-swarm-status.php` — merges `swarm-status.generated.json` over fallback
5. `render-static-routes.php` — renders flat static routes before deploy

Refresh contract: `data/reports/planner/refresh_contract.json`
