# weareswarm_online_planner_refocus_001

**Lane:** `weareswarm_online_planner_refocus_001`  
**Site:** weareswarm.online  
**Date:** 2026-06-08

## Summary

Refocused weareswarm.online homepage from "Dream.OS Command Center / public proof layer" to **Dream.OS Planner** — planner state first, verified closeouts second.

## Before

| Area | Previous state |
|------|----------------|
| Title / hero | "Dream.OS Command Center" — "The public proof layer for the Swarm." |
| Lead copy | "We build in public. We verify the work. We publish the receipts." |
| Module order | Command board led with Crosby closeout card; portfolio consolidation and Spark lanes prominent |
| Identity | Showcase / command center / proof-first |
| Data wiring | Static HTML cards; no homepage JSON hydration |

## After

| Area | New state |
|------|-----------|
| Title / hero | **Dream.OS Planner** — "One board for active lanes, approved tasks, blockers, repo consolidation, and verified closeouts." |
| Lead copy | Planner-first subtitle; no "public proof layer" lead |
| Module order | 1) Operating State 2) Approved Next Actions 3) Blocked/Review 4) Repo Consolidation 5) Active Lanes 6) Latest Verified Closeouts 7) Capability Unlocks |
| Identity | Planner primary; closeouts demoted to module 6 |
| Data wiring | `planner-home.js` hydrates from `data/planner/*.json` via `focus-dashboard.js` patterns |

## Files changed

- `index.html` — full homepage restructure
- `data/shared/planner-home.js` — new hydration script
- `data/planner/weareswarm_online_identity_lock.json` — identity lock artifact
- `runtime/tasks/weareswarm_online_planner_refocus_001.yaml` — lane task (DreamVault)

## Module structure

1. **Today's Operating State** — `next_lane.json` (mode, next lane, rationale)
2. **Approved Next Actions** — `next_lane.json` → `approved_tasks`
3. **Blocked / Needs Human Review** — `blocked_until` + consolidation recommendations
4. **Repo Consolidation Focus** — `project_consolidation_decision_manifest_001.json` buckets (fallback: domain index)
5. **Active Lanes** — `strategic_active_queue.json`
6. **Latest Verified Closeouts** — static Crosby + feed link (proof secondary)
7. **Capability Unlocks** — `project_capability_index.json`

## Routes (unchanged roles)

| Route | Role |
|-------|------|
| `/` | Planner dashboard |
| `/focus/` | Operating focus |
| `/tasks/` | Approved task queue |
| `/projects/` | Repo/domain board |
| `/feed/` | Closeout receipts |
| `/skill-tree/` | Capability unlocks |
| `/roadmap/` | Future lanes |

## Rules applied

- No weareswarm.site WordPress copy
- No secret-marker file references
- No Spark gameplay merge on homepage
- Repo paths sanitized in public JS copy
- Crosby remains closeout evidence, not homepage identity

## Verification checklist

- [ ] Homepage contains: Dream.OS Planner, active lanes, approved tasks, blockers, repo consolidation
- [ ] Homepage does NOT lead with "public proof layer"
- [ ] `/tasks/`, `/projects/`, `/focus/` render
- [ ] Live smoke test at https://www.weareswarm.online/
