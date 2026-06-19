# WeAreSwarm Generated Status Boards

## `/projects/` — project consolidation board

1. `runtime/scripts/build_weareswarm_project_board_001.py` — GitHub + local inventory generator
2. `runtime/content/weareswarm.site/data/project-board.generated.json` — generated project board
3. `dreamos-swarm-status.php` — `dreamos_swarm_apply_project_board()` maps board cards to `$status['projects']`

## `/tasks/` — planner execution queue

1. `data/reports/planner/next_lane.json` — authoritative planner contract
2. `runtime/scripts/sync_weareswarm_planner_status_001.py` — planner status generator
3. `runtime/content/weareswarm.site/data/swarm-status.generated.json` — generated task queue
4. `dreamos-swarm-status.php` — merges planner JSON over fallback (tasks only; projects come from project board)

Refresh contract: `data/reports/planner/refresh_contract.json`

Deploy: `render-static-routes.php` renders flat static routes before upload.
