# Spark Meridian Mission Schema

**SSOT (single source of truth):** `assets/data/missions.json`

Map UIs (`meridian-map/`, `meridian-city/`) load missions from this file. Do not maintain duplicate copies under `meridian-map/missions.json` or `meridian-city/missions.json`.

Machine validation: `assets/data/mission.schema.json`  
Starter template: `missions/mission.template.json`

## File shape

`missions.json` is a **JSON array** of mission objects (not wrapped in a root key).

```json
[
  { "id": "dock_heist_001", "title": "…", … },
  { "id": "guardian_spire_signal_001", "title": "…", … }
]
```

## Required fields

| Field | Type | Notes |
|-------|------|-------|
| `id` | string | Unique snake_case id (`dock_heist_001`) |
| `title` | string | Display headline on map panel |
| `description` | string | 1–4 sentences of mission lore |
| `coord` | string | Grid sector `A1`–`P15` (column A–P, row 1–15) |
| `district` | string | Human district name shown in UI |
| `type` | enum | `mission` · `world-event` · `secret` · `dispatch` |
| `status` | enum | `active` · `hidden` · `completed` · `archived` |
| `reward` | string | Trophy / badge / narrative reward label |

## Recommended fields

| Field | Type | Notes |
|-------|------|-------|
| `district_id` | string | Matches `district_id` in `assets/data/meridian_districts.json` |
| `threat` | enum | `low` · `medium` · `high` · `critical` · `unknown` |
| `tier` | enum | `intro` · `standard` · `elite` · `discovery` |
| `news_id` | string | Cross-link to `meridian-city/news/newsfeed.json` |
| `faction` | string | `aegis`, `sterling_holdings`, `gods_armor`, `undercity`, `independent` |
| `objectives` | string[] | 1–6 player-facing objectives |
| `unlock` | object | Gating rules (see below) |
| `tags` | string[] | Filter / admin labels |

## Unlock object

```json
"unlock": {
  "requires_hero": true,
  "min_notoriety": 0,
  "prerequisite_missions": ["dock_heist_001"]
}
```

| Key | Type | Default | Meaning |
|-----|------|---------|---------|
| `requires_hero` | boolean | `true` | Player must have a locked Spark hero |
| `min_notoriety` | integer | `0` | Minimum hero notoriety to see mission |
| `prerequisite_missions` | string[] | `[]` | Mission ids that must be completed first |

## Coordinate rules

- Columns: `A` through `P` (16 columns)
- Rows: `1` through `15`
- Valid examples: `D7`, `I2`, `A13`
- Invalid: `Q1`, `D16`, `d7` (use uppercase)

## Type semantics

| `type` | Map marker | Player visibility |
|--------|------------|-------------------|
| `mission` | Standard mission pin | Shown when `status` is `active` |
| `world-event` | Event pin | City-wide or district crisis |
| `secret` | Hidden until discovered | Use `status: hidden` until unlocked |
| `dispatch` | Optional | Reserved for Meridian Dispatch headline sync |

## Status semantics

| `status` | Map behavior |
|----------|--------------|
| `active` | Visible on grid; `has-mission` cell class |
| `hidden` | Secret missions; may still render for admins |
| `completed` | Archived; remove from active pool when done |
| `archived` | Kept for history; not shown to players |

## Deploy checklist

1. Edit `assets/data/missions.json` (validate in Mission Admin or with schema).
2. Optionally add matching `newsfeed.json` entry when `news_id` is set.
3. Commit in `websites` repo under `websites/dadudekc.site/`.
4. Deploy: `python ops/deployment/unified_deployer.py --site dadudekc.site`
5. Verify: https://dadudekc.site/meridian-map/?sector=D7

## Related files

| Path | Role |
|------|------|
| `assets/data/missions.json` | **SSOT** mission array |
| `assets/data/meridian_districts.json` | District ids, factions, grid coords |
| `meridian-city/news/newsfeed.json` | Newspaper ledger cross-links |
| `meridian-dispatch/index.html` | Inline dispatch stories (separate format; future sync) |
