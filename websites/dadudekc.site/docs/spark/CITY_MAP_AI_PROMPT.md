# City Map AI Prompt — Copy & Paste

Use this prompt with any LLM to generate valid quadrant data for Meridian City.

---

## Prompt

```
You are a Spark Protocol world-builder for Meridian City (dadudekc.site).

Generate a JSON fragment for spark.city_map.v1 quadrants. Output ONLY valid JSON — no markdown fences.

Context:
- Grid size: {{COLS}} columns × {{ROWS}} rows (0-indexed, inclusive bounds)
- Existing mission IDs (link by id only): {{MISSION_IDS}}
- Tone: comic-book noir, faction politics, hero registry, MaskZero undertones
- Factions: AEGIS, Sterling Holdings, God's Armor, The Undercity, Independent

Requirements per quadrant:
- id: snake_case slug (e.g. nw_district, spindle_ward)
- label: player-facing district name
- bounds: { x1, y1, x2, y2 } within grid, non-overlapping with siblings
- image: "assets/spark/map/<id>.png"
- lore: 2–4 sentences — sensory detail, faction presence, player hook
- mission_ids: array of existing mission IDs that belong here (or [])
- tags: 2–5 snake_case tags

Generate {{QUADRANT_COUNT}} quadrants covering the full grid with no gaps.

Example single quadrant:
{
  "id": "nw_district",
  "label": "Northwest Quarter",
  "bounds": { "x1": 0, "y1": 0, "x2": 19, "y2": 19 },
  "image": "assets/spark/map/nw_district.png",
  "lore": "The Old Tidegate and flood-scarred blocks where Meridian's first walls still stand.",
  "mission_ids": ["secret_tidegate_001"],
  "tags": ["flood_scar", "old_wall"]
}

After generation, paste the quadrants array into spark-city-admin or merge into assets/data/spark/city_map.json.
```

## Quick presets

**4 compass quarters (40×40):**
- NW: `0,0 → 19,19`
- NE: `20,0 → 39,19`
- SW: `0,20 → 19,39`
- SE: `20,20 → 39,39`

**9-grid (30×30, 10-cell districts):**
- Divide into 3×3 blocks of 10×10 cells each.

## Mission linking prompt add-on

```
Also generate matching mission entries for assets/data/missions.json (JSON array):
- id, title, coord (letter+number like D7), district, quadrant_id, type, status, threat, description, reward, news_id
- Link each mission's quadrant_id to the quadrant id above.
```
