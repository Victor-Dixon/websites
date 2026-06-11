# Mission AI Prompt — Copy-Paste Block

Use this prompt when asking an AI to author a new Spark Meridian mission. The output must be **valid JSON** matching `assets/data/mission.schema.json`.

---

## Copy-paste prompt

```
You are authoring a mission for Spark Protocol — Meridian City on dadudekc.site.

OUTPUT RULES (strict):
1. Return ONLY a single JSON object — no markdown fences, no commentary.
2. Match this schema exactly. Required keys: id, title, description, coord, district, type, status, reward.
3. id: unique snake_case, suffix _001, _002, etc.
4. coord: uppercase grid sector A1–P15 (column A–P, row 1–15). Example: D7, I2, A13.
5. district_id: use ids from meridian_districts.json when possible (spindle, irongate, drowned_quarter, ladderways, halcyon_heights, etc.).
6. type: one of mission | world-event | secret | dispatch
7. status: one of active | hidden | completed | archived
8. threat: one of low | medium | high | critical | unknown
9. tier: one of intro | standard | elite | discovery
10. objectives: 2–4 short imperative strings.
11. unlock.requires_hero: true for most missions.
12. faction: aegis | sterling_holdings | gods_armor | undercity | independent

SETTING: Meridian City — masked heroes (Sparks), factions AEGIS, Sterling Holdings, God's Armor, The Undercity. Tone: comic-book noir, newspaper headlines, civic pressure.

MISSION BRIEF FROM OPERATOR:
<describe the crisis, district, desired coord, threat level, and reward name here>

EXAMPLE OUTPUT (format only — create new content):
{
  "id": "ladderways_debt_clash_001",
  "title": "Debt Collectors Spark Market Clash In The Ladderways",
  "description": "Witnesses say the dispute began after a Sterling-linked lender attempted to seize a family stall. Civilians are caught between debt enforcers and market regulars.",
  "coord": "H6",
  "district": "The Ladderways",
  "district_id": "ladderways",
  "type": "mission",
  "status": "active",
  "threat": "medium",
  "tier": "standard",
  "reward": "Ladderways Shield",
  "news_id": "ladderways_debt_clash_001",
  "faction": "independent",
  "objectives": [
    "De-escalate the market confrontation",
    "Identify the Sterling-linked lender behind the seizure"
  ],
  "unlock": {
    "requires_hero": true,
    "min_notoriety": 0,
    "prerequisite_missions": []
  },
  "tags": ["market", "debt", "civilians"]
}
```

---

## Few-shot example 2 (secret mission)

Input brief: *Hidden sewer route under Old Tidegate, coord A13, unlock after dock mission.*

```json
{
  "id": "secret_tidegate_001",
  "title": "Hidden Lair Entrance",
  "description": "A sewer access point beneath the Old Tidegate. This route may unlock underground missions.",
  "coord": "A13",
  "district": "Old Tidegate",
  "district_id": "old_tidegate",
  "type": "secret",
  "status": "hidden",
  "threat": "unknown",
  "tier": "discovery",
  "reward": "Secret Route",
  "news_id": "secret_tidegate_001",
  "faction": "undercity",
  "objectives": [
    "Find the concealed sewer grate",
    "Document the route without alerting patrols"
  ],
  "unlock": {
    "requires_hero": true,
    "min_notoriety": 2,
    "prerequisite_missions": ["dock_heist_001"]
  },
  "tags": ["secret", "undercity", "exploration"]
}
```

## After AI generates JSON

1. Validate in **Mission Admin** → `/spark-mission-admin/` (owner/dev/admin only).
2. Append the object to `assets/data/missions.json` (array).
3. Commit and deploy dadudekc.site.
