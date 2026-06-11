# Spark City Map Schema (`spark.city_map.v1`)

Single source of truth for Meridian's configurable district grid.

**File:** `assets/data/spark/city_map.json`

## Top-level fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `schema` | string | yes | Must be `spark.city_map.v1` |
| `city_id` | string | no | City slug (e.g. `meridian`) |
| `city_name` | string | no | Display name |
| `grid.cols` | int | yes | Column count (4–120) |
| `grid.rows` | int | yes | Row count (4–120) |
| `map_image` | string | no | Background map image path |
| `quadrants` | array | yes | District definitions |

## Quadrant object

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | string | yes | Snake-case slug (`nw_district`) |
| `label` | string | yes | Player-facing name |
| `bounds.x1/y1` | int | yes | Top-left cell (0-indexed) |
| `bounds.x2/y2` | int | yes | Bottom-right cell (inclusive) |
| `image` | string | no | Relative path under site root |
| `lore` | string | no | Narrative text shown on click/hover |
| `mission_ids` | string[] | no | IDs from `assets/data/spark/missions.json` |
| `tags` | string[] | no | Freeform metadata |

## Bounds rules

- Cells are 0-indexed: `(0,0)` is top-left.
- `x2` and `y2` are **inclusive**.
- Quadrants may overlap in draft data; admin UI warns on overlap.
- Grid resize clamps existing bounds to new dimensions.

## Image workflow (static site)

1. Export quadrant artwork as PNG (recommended 800×600 or larger).
2. Upload to `assets/spark/map/<quadrant_id>.png` on the server (SFTP).
3. Set `image` to `assets/spark/map/<quadrant_id>.png` in JSON.
4. Admin UI supports local FileReader preview before deploy.

## Related files

- Missions SSOT: `assets/data/missions.json` (link quadrants via `mission_ids`)
- JSON Schema: `assets/data/city_map.schema.json`
- Template: `assets/data/spark/templates/quadrant.template.json`
- Admin UI: `/spark-city-admin/` (owner/admin only)
- Player map: `/meridian-map/` (District View tab)
